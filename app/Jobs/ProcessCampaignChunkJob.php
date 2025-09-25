<?php

namespace App\Jobs;

use App\Models\Analytics;
use App\Models\Campaign;
use App\Models\EmailAccount;
use App\Services\CampaignService;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;

class ProcessCampaignChunkJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;
    public $tries = 5;
    public $timeout = 300;
    public $maxExceptions = 3;
    public $campaignId;
    public $rowIds;
    public function __construct($campaignId, $rowIds)
    {
        $this->campaignId = $campaignId;
        $this->rowIds = $rowIds;
    }

    public function handle(CampaignService $campaignService)
    {
        try {
            $campaign = Campaign::findOrFail($this->campaignId);
            if ($campaign->status !== Campaign::STATUS_LAUNCHED)
                return;

            $now = Carbon::now($campaign->timezone);
            $currentDay = strtolower($now->format('D'));
            $hasProcessed = false;

            foreach ($this->rowIds as $rowId) {
                $row = Analytics::with('subscriber')->find($rowId);
                if (!$row || $row->sent_at || $row->skipped || $row->blacklisted)
                    continue;

                if (!in_array($currentDay, $campaign->days ?? []))
                    continue;

                $startTime = Carbon::createFromFormat('H:i:s', $campaign->start, $campaign->timezone);
                $endTime = Carbon::createFromFormat('H:i:s', $campaign->end, $campaign->timezone);
                if (!$now->between($startTime, $endTime)) {
                    $row->update(['error' => 'Outside time window']);
                    continue;
                }

                $receiver = $this->getReceiverFromRow($row);
                if (!$receiver) {
                    $row->update(['error' => 'No receiver']);
                    continue;
                }

                $blacklist = $campaign->user->blacklists()->pluck('email')->toArray();
                if (in_array($receiver, $blacklist)) {
                    $row->update(['skipped' => 1, 'error' => 'Found in blacklist']);
                    continue;
                }

                $senderAccount = Campaign::selectEmailAccount($campaign);
                if (!$senderAccount) {
                    $row->update(['error' => 'No sender account']);
                    continue;
                }

                // lock to prevent duplicates
                $lock = Cache::lock("campaign_analytics_{$row->id}", 60)->block(5);
                try {
                    $this->sendEmail($campaign, $row, $receiver, $senderAccount, $campaignService);
                    $hasProcessed = true;
                } catch (\Exception $e) {
                    Log::error("Send error: " . $e->getMessage());
                } finally {
                    optional($lock)->release();
                }

                unset($row); // Free memory
            }

            if (!$campaign->analytics()->whereNull('sent_at')->where('skipped', 0)->exists()) {
                $campaign->update(['status' => Campaign::STATUS_COMPLETED]);
            }

        } catch (\Throwable $e) {
            Log::error("ChunkJob crashed: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'campaign_id' => $this->campaignId,
            ]);
            throw $e;
        }
    }

    public function getReceiverFromRow($row)
    {
        $mapping = $row->subscriber->csv->mapping;
        return $row->subscriber->{$mapping} ?? null;
    }
    public function getReceiverKeyFromRow($row)
    {
        $mapping = $row->subscriber->csv->mapping;
        return $mapping ?? null;
    }


    protected function shouldSkipRow($row, $receiver, $campaign): bool
    {
        $controls = $campaign->controls;
        $emailDomain = strtolower(substr(strrchr($receiver, "@"), 1));
        $receiverName = explode("@", $receiver)[0];


        // Skip leads that exist in another campaign. This doesn't apply to campaigns that have been deleted.	.
        // Skip leads that have already responded. This does not apply to leads that have responded in other sending tools.	



        // Skip leads that have “invalid” or “catch-all” email addresses. Activating this will ensure that you only send emails to leads with “valid” email addresses.	
        if (!filter_var($receiver, FILTER_VALIDATE_EMAIL)) {
            $row->update(['error' => 'Invalid RFC email format', 'skipped' => 1]);
            return true;
        }
        // Skip duplicate leads. If your lead list contains the same lead more than once, then we'll only include the lead once in this campaign
        if (in_array('skip_duplicates',  $controls)) {
            $emailColName=$this->getReceiverKeyFromRow($row);
            if ($campaign->list->csv->subscribers()
                ->where('id', '!=', $row->subscriber_id)
                ->where($emailColName, $receiver)->exists()) {
                $row->update(['error' => 'Duplicate email', 'skipped' => 1]);
                return true;
            }
        }

        // Skip personal email addresses (like @gmail.com). Contacting personal email addresses can negatively impact your deliverability.	
        if (in_array('skip_personal', $controls)) {
            $personalDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
            if (in_array($emailDomain, $personalDomains)) {
                $row->update(['error' => 'Personal email', 'skipped' => 1]);
                return true;
            }
        }

        if (in_array('skip_invalid', $controls)) {
            if (in_array($receiverName, ['invalid', 'catch-all'])) {
                $row->update(['error' => 'Invalid email', 'skipped' => 1]);
                return true;
            }
        }

        return false;
    }

    public function sendEmail($campaign, $row, $receiver, $senderAccount, $campaignService)
    {
        $row = Analytics::find($row->id);
        if ($row->sent || $row->sent_at) {
            Log::error("Email already sent.");
            return ;
        }
        $signature = $campaignService->filter_signature($campaign->template->signature, $senderAccount->email);
        $bodyMessage = $campaignService->filter_message($row);
        $body = $bodyMessage . "<br><br>" . $signature;

        try {
            if ($senderAccount->type === EmailAccount::TYPE_SMTP) {
                $retryCount = 0;
                $maxRetries = 3;

                while ($retryCount < $maxRetries) {
                    try {
                        Config::set('mail.mailers.dynamic', [
                            'transport' => 'smtp',
                            'host' => $senderAccount->host,
                            'port' => $senderAccount->port,
                            'encryption' => $senderAccount->encryption ?? 'tls',
                            'username' => $senderAccount->username,
                            'password' => $senderAccount->password,
                        ]);
                        Config::set('mail.from.address', $senderAccount->email);
                        Config::set('mail.from.name', $senderAccount->name ?? '');
                        try {
                            $mailer = Mail::mailer('dynamic');
                        } catch (\Throwable $e) {
                            Log::error("Failed to create dynamic mailer: " . $e->getMessage());
                            throw $e;
                        }
                        $email = (new Email())
                            ->from(new \Symfony\Component\Mime\Address($senderAccount->email, $senderAccount->name??''))
                            ->to($receiver)
                            ->subject($campaign->subject);
                        if ($campaign->template->type === 'html') {
                            $email->html($body);
                        } else {
                            $email->text(strip_tags($body));
                        }


                        if ($campaign->attachment && file_exists(public_path($campaign->attachment))) {
                            $email->attachFromPath(public_path($campaign->attachment), $campaign->o_attachment);
                        }
                        $references = $email->getHeaders()->get('References');
                        $references = $references ? $references->toString() : null;
                        $sentMessage = $mailer->getSymfonyTransport()->send($email);
                        Log::info("SMTP Email sent to {$receiver} " . $campaign->name);
                        $row->update([
                            'sent' => true,
                            'sent_at' => now(),
                            'error' => null,
                            'email_account_id' => $senderAccount->id,
                            'response' => json_encode([
                                'message' => 'Sent via SMTP',
                                'message_id' => $sentMessage->getMessageId(),
                                'references' => $references,
                            ]),
                        ]);
                        break;
                    } catch (\Exception $e) {
                        $retryCount++;
                        Log::error("SMTP retry {$retryCount} failed: " . $e->getMessage());
                        sleep(5); // Wait before retrying
                    }
                }



            }

            if ($senderAccount->type === EmailAccount::TYPE_GOOGLE) {
                $client = new \Google_Client();
                $client->setAccessToken($senderAccount->access_token);

                if ($client->isAccessTokenExpired()) {
                    $campaignService->refreshGmailAccessToken($senderAccount);
                }

                $service = new \Google_Service_Gmail($client);
                $rawMessage = $campaignService->encodeEmail($receiver, $campaign->subject, $body, $campaign->attachment,null,null,$campaign->template->type);

                $message = new \Google_Service_Gmail_Message();
                $message->setRaw($rawMessage);

                $response = $service->users_messages->send('me', $message);

                $row->update([
                    'sent' => true,
                    'sent_at' => now(),
                    'error' => null,
                    'email_account_id' => $senderAccount->id,
                    'response' => json_encode($response),
                ]);
                Log::info("GOOGLE Email sent to {$receiver} " . $campaign->name);
            }
            $row->update(['error' =>null]);
        } catch (\Exception $e) {
            $row->update(['sent' => false, 'error' => $e->getMessage()]);
            Log::error("Failed to send email to {$receiver} for campaign {$campaign->id}: " . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
            ]);
        }
    }
}
