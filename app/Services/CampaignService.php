<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Mapping;
use App\Models\Record;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;


use Google_Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use OpenAI;
use App\Models\ApiKey;

class CampaignService
{
    public function refreshGmailAccessToken($emailAccount)
    {
        $client = new Google_Client();
        $client->setAccessToken($emailAccount->access_token);
        $refreshToken = $emailAccount->refresh_token;
        if (!$refreshToken) {
            return ['success' => false, 'error' => 'Failed to refresh access token. Re-authentication required.'];
        }
        config([
            'services.google' => config('services.google_gmail')
        ]);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ]);

        $newAccessToken = $response->json();
        if (isset($newAccessToken['access_token'])) {
            $emailAccount->update([
                'access_token' => $newAccessToken['access_token'],
                'expires_in' => now()->addSeconds($newAccessToken['expires_in']),
            ]);
            $client->setAccessToken($newAccessToken['access_token']);
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Failed to refresh access token'];
        }
    }
    public function filter_signature($signature, $email)
    {
        $explode = explode('@', $email);
        $domain = array_key_exists(1, $explode) ? $explode[1] : '';
        $signature = str_replace('{Domain}', $domain, $signature);
        $signature = str_replace('{Email}', $email, $signature);
        $signature = preg_replace(
            '/<p[^>]*>\s*Powered by\s*<a[^>]*>Froala Editor<\/a>\s*<\/p>/i',
            '',
            $signature
        );
        return $signature;
    }
    public function filter_message($lead)
    {
        $campaign = $lead->campaign;
        $message = $campaign->template->body;
        $message = preg_replace(
            '/<p[^>]*>\s*Powered by\s*<a[^>]*>Froala Editor<\/a>\s*<\/p>/i',
            '',
            $message
        );
        
        $tags = Tag::where('user_id', $campaign->user_id)->pluck('name', 'id');
        $headers = $campaign->list->csv->headers;

        foreach ($tags as $id => $tag) {
            $placeholder = '{' . $tag . '}';
            if (Str::contains($message, $placeholder)) {
            $mapping = Mapping::where('tag_id', $id)
                ->where('list_id', $campaign->list_id)
                ->first();

            if ($mapping) {
                $csvColName = $mapping->mappings;
                $colId = array_search($csvColName, $headers);
                if ($colId !== false && isset($lead->subscriber->{'column_' . $colId})) {
                $replaceValue = $lead->subscriber->{'column_' . $colId};
                $message = str_replace($placeholder, $replaceValue, $message);
                }
            }
            }
        }

        // Find and replace purple-colored text using ChatGPT API
        if (str_contains($message, '<span style="color: purple;">')) {
            preg_match_all('/<span style="color: purple;">(.*?)<\/span>/', $message, $matches);
            $spanTexts = $matches[1] ?? [];
            foreach ($spanTexts as $originalText) {
                $similarText = $this->getSimilarTextFromChatGPT($originalText, $campaign->user_id);
                $message = str_replace("<span style=\"color: purple;\">{$originalText}</span>", $similarText, $message);
            }
        }
        $encryptedId = Crypt::encryptString($lead->id);
        //$message .= '<br><br><br><div style="width: 100%;background: #efefef!important;padding: 10px!important;text-align: center!important;"><a style="color: #0a53be!important;" href="#">Unsubscribe.</a></div>';
        $message .= '<br><br><br><div style="width: 100%;background: #efefef!important;padding: 10px!important;text-align: center!important;"><a style="color: #0a53be!important;" href="' . url('unsubscribe/row/' . urlencode($encryptedId)) . '">Unsubscribe.</a></div>';
        return $message;
    }
    public function filter_followup_message($followup)
    {
        $campaign = $followup->campaign;
        $headers = $campaign->header->headers;
        $mapping = array_flip($headers);
        $message = $followup->message;
        preg_match_all('/\{(.*?)\}/', $message, $matches);
        $placeholders = $matches[1] ?? [];
        foreach ($headers as $header) {
            if (in_array($header, $placeholders) && isset($mapping[$header])) {
                $column = $mapping[$header];
                $col = 'column_' . $column;
                if (isset($lead[$col])) {
                    $message = str_replace("{{$header}}", $lead[$col], $message);
                }
            }
        }
        // Find and replace purple-colored text using ChatGPT API
        if (str_contains($message, '<span style="color: purple;">')) {
            preg_match_all('/<span style="color: purple;">(.*?)<\/span>/', $message, $matches);
            $spanTexts = $matches[1] ?? [];
            foreach ($spanTexts as $originalText) {
                $similarText = $this->getSimilarTextFromChatGPT($originalText, $campaign->user_id);
                $message = str_replace("<span style=\"color: purple;\">{$originalText}</span>", $similarText, $message);
            }
        }

        return $message;
    }

    public function encodeEmail($to, $subject, $body, $attachmentPath = null, $threadId = null, $inReplyTo = null)
    {
        $boundary = uniqid(rand(), true);
        $messageId = "<" . uniqid() . "@yourdomain.com>";

        $rawMessage = "To: $to\r\n";
        $rawMessage .= "Subject: $subject\r\n";
        $rawMessage .= "MIME-Version: 1.0\r\n";
        $rawMessage .= "Message-ID: $messageId\r\n";

        if ($threadId && $inReplyTo) {
            $rawMessage .= "In-Reply-To: <$inReplyTo>\r\n";
            $rawMessage .= "References: <$inReplyTo>\r\n";
        }
        if ($attachmentPath && file_exists(public_path($attachmentPath))) {
            $filename = Campaign::clear_stamp_from_attachment($attachmentPath) ?? basename($attachmentPath);
            $fileData = file_get_contents(public_path($attachmentPath));
            $fileDataEncoded = base64_encode($fileData);
            $rawMessage .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
            $rawMessage .= "\r\n--$boundary\r\n";
            $rawMessage .= "Content-Type: text/html; charset=UTF-8\r\n";
            $rawMessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $rawMessage .= "$body\r\n";
            $rawMessage .= "--$boundary\r\n";
            $rawMessage .= "Content-Type: application/octet-stream; name=\"$filename\"\r\n";
            $rawMessage .= "Content-Disposition: attachment; filename=\"$filename\"\r\n";
            $rawMessage .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $rawMessage .= chunk_split($fileDataEncoded) . "\r\n";
            $rawMessage .= "--$boundary--";
        } else {
            $rawMessage .= "Content-Type: text/html; charset=UTF-8\r\n";
            $rawMessage .= "\r\n$body";
        }

        return base64_encode($rawMessage);
    }


    private function getSimilarTextFromChatGPT($text, $userId)
    {
        $apiKeyModel = ApiKey::where('user_id', $userId)
            ->where('type', ApiKey::TYPE_AI)
            ->first();
        if (!$apiKeyModel) {
            return $text;
        }

        $apiKey = $apiKeyModel->key;
        if ($text) {
            try {
                $client = OpenAI::factory()
                    ->withApiKey($apiKey)
                    ->make();

                $messages = [
                    [
                        'role' => 'user',
                        'content' => 'Rephrase this for reducing spam in email: ' . $text,
                    ],
                ];

                $response = $client->chat()->create([
                    'model' => 'gpt-4o-mini',
                    'store' => true,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 1024,
                ]);

                return (string) Arr::get($response, 'choices.0.message.content', $text);
            } catch (\Exception $e) {
                return $text; // fallback to original text
            }
        }

        return $text;
    }
    
}
