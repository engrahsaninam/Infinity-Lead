<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\EmailAccount;
use App\Models\Lead;
use App\Services\CampaignService;
use App\Traits\CommonTraits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;
class ChatController extends Controller
{
    //
    use CommonTraits;
    private function encodeEmail($to, $subject, $body, $from = '', $threadId = null, $inReplyTo = null, $references = null)
    {
        $headers = [];
        $headers[] = "From: {$from}";
        $headers[] = "To: {$to}";
        $headers[] = "Subject: {$subject}";
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: text/html; charset=UTF-8";

        if ($inReplyTo) {
            $headers[] = "In-Reply-To: <{$inReplyTo}>";
        }
        if ($references) {
            $headers[] = "References: {$references}";
        }

        $rawMessageString = implode("\r\n", $headers) . "\r\n\r\n" . $body;

        // Gmail expects URL safe base64 encoded string without trailing '='
        return rtrim(strtr(base64_encode($rawMessageString), '+/', '-_'), '=');
    }

    public function compose(Request $request, CampaignService $campaignService)
    {
        $validators = Validator($request->all(), [
            'lead_id' => 'required|exists:leads,id',
            'from' => 'required|exists:email_accounts,id',
            'to' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);

        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $lead = Lead::find($request->lead_id);
        $messageId = '';
        $response = '';
        $account = EmailAccount::find($request->from);
        if ($account->type === EmailAccount::TYPE_SMTP) {
            $inReplyTo = null;
            $references = null;
            if ($lead->chat->count() > 0) {
                $lastMessage = $lead->chat->sortByDesc('id')->first();
                $inReplyTo = $lastMessage->message_id;
                $refs = $lead->chat->pluck('message_id')->map(fn($m) => "<{$m}>")->toArray();
                $references = implode(' ', $refs);
            }
            Config::set('mail.mailers.dynamic', [
                'transport' => 'smtp',
                'host' => $account->host,
                'port' => $account->port,
                'encryption' => $account->encryption ?? 'tls',
                'username' => $account->username,
                'password' => $account->password,
            ]);
            Config::set('mail.from.address', $account->email);
            Config::set('mail.from.name', $senderAccount->name ?? $account->email);
            $mailer = Mail::mailer('dynamic');

            $email = (new Email())
                ->from($account->email)
                ->to($request->to)
                ->subject($request->subject)
                ->html($request->message);

            if ($inReplyTo) {
                $email->getHeaders()->addTextHeader('In-Reply-To', "<{$inReplyTo}>");
            }
            if ($references) {
                $email->getHeaders()->addTextHeader('References', $references);
            }

            $sentMessage = $mailer->getSymfonyTransport()->send($email);
            $messageId = $sentMessage->getMessageId();

            $response = [
                'message_id' => $messageId,
                'in_reply_to' => $inReplyTo,
                'references' => $references,
            ];
        }


        if ($account->type === EmailAccount::TYPE_GOOGLE) {
            $inReplyTo = null;
            $references = null;
            $firstThreadId = null;

            if ($lead->chat->count() > 0) {
                $firstMessage = $lead->chat->first();
                $lastMessage = $lead->chat->sortByDesc('id')->first();

                if ($firstMessage) {
                    $firstThreadId = $firstMessage->response['threadId'] ?? null;
                }
                if ($lastMessage) {
                    $inReplyTo = $lastMessage->message_id;
                    $refs = $lead->chat->pluck('message_id')->map(fn($m) => "<{$m}>")->toArray();
                    $references = implode(' ', $refs);
                }
            }

            $client = new \Google_Client();
            $client->setAccessToken($account->access_token);

            if ($client->isAccessTokenExpired()) {
                $campaignService->refreshGmailAccessToken($account);
                // Refresh token, then reset access token
                $client->setAccessToken($account->access_token);
            }

            $service = new \Google_Service_Gmail($client);

            // Pass the 'from' email to encodeEmail to set From header properly
            $rawMessage = $this->encodeEmail(
                $request->to,
                $request->subject,
                $request->message,
                $account->email,
                $firstThreadId,
                $inReplyTo,
                $references
            );

            $gmailMessage = new \Google_Service_Gmail_Message();
            $gmailMessage->setRaw($rawMessage);

            if ($firstThreadId) {
                $gmailMessage->setThreadId($firstThreadId);
            }

            $sent = $service->users_messages->send('me', $gmailMessage);
            $sentMessage = $service->users_messages->get('me', $sent->id);

            $headers = collect($sentMessage->getPayload()->getHeaders());
            $messageId = trim(optional(
                $headers->first(fn($header) => strtolower($header->getName()) === 'message-id')
            )->getValue(), '<>');

            $response = [
                'threadId' => $sent['threadId'],
                'message_id' => $messageId,
                'in_reply_to' => $inReplyTo,
                'references' => $references,
            ];
        }



        $chat = Chat::create([
            'lead_id' => $request->lead_id,
            'from' => $account->email,
            'to' => $request->to,
            'subject' => $request->subject,
            'message' => $request->message,
            'user_id' => Auth::id(),
            'message_id' => $messageId,
            'response' => json_encode($response),
        ]);
        $lead = Lead::find($lead->id);

        if ($lead->chat->count() === 1) {
            $lead->update(['status' => Lead::STATUS_CONTACTED, 'sender_email_account_id' => $account->id]);
        }
        return $this->sendSuccess('Email sent successfully!', ['chat_id' => $chat->id]);
    }






    public function replies(Request $request)
    {
        $lead = Lead::find($request->id);
        $message = $lead->chat->first();

        if (!$message) {
            return $this->sendError('No messages found for this lead.', 421);
        }

        $threadId = $message->response['threadId'] ?? null;
        if (!$threadId) {
            return $this->sendError('Thread ID not found.', 421);
        }

        $account = $message->account;
        if (!$account) {
            return $this->sendError('Email account not found.', 421);
        }

        $client = new \Google_Client();
        $client->setAccessToken($account->access_token);
        $emailController = new EmailController();

        if ($client->isAccessTokenExpired()) {
            $emailController->refreshToken($account);
        }

        $service = new \Google_Service_Gmail($client);

        try {
            $thread = $service->users_threads->get('me', $threadId);
            $messages = $thread->getMessages();

            foreach ($messages as $gmailMessage) {
                $payload = $gmailMessage->getPayload();
                $headers = collect($payload->getHeaders());

                $from = $headers->where('name', 'From')->first()->getValue();
                $to = $headers->where('name', 'To')->first()->getValue();
                $subject = $headers->where('name', 'Subject')->first()->getValue();
                $messageId = collect($payload->getHeaders())
                    ->firstWhere('name', 'Message-ID')
                        ?->getValue();  // Store this instead

                $body = '';

                if ($payload->getBody() && $payload->getBody()->size > 0) {
                    $body = base64_decode(str_replace(['-', '_'], ['+', '/'], $payload->getBody()->getData()));
                } elseif ($payload->getParts()) {
                    foreach ($payload->getParts() as $part) {
                        if ($part->getBody() && $part->getBody()->size > 0) {
                            $body = base64_decode(str_replace(['-', '_'], ['+', '/'], $part->getBody()->getData()));
                            break;
                        }
                    }
                }

                // Clean the extracted email body
                $cleanedBody = $this->cleanEmailBody($body);

                if (!Chat::where('message_id', $messageId)->exists()) {
                    Chat::create([
                        'lead_id' => $request->id,
                        'from' => $from,
                        'to' => $to,
                        'subject' => $subject,
                        'message' => $cleanedBody, // Store cleaned message
                        'user_id' => auth()->id(),
                        'message_id' => $messageId,
                        'response' => json_encode($gmailMessage),
                    ]);
                }
            }

            return $this->sendSuccess('Replies fetched and stored successfully!');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * Remove unwanted quoted text and metadata from email body.
     */
    private function cleanEmailBody($body)
    {
        $body = preg_replace('/On.*wrote:.*/s', '', $body);
        $body = preg_replace('/(From:.*)|(To:.*)|(Sent:.*)|(Subject:.*)/s', '', $body);
        return trim($body);
    }
}
