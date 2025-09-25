<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details; // Data jo email me use hoga

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        // This is only for testing purpose and call with '/test-send-email' route 
        return $this->subject($this->details['subject'])
                    ->view('emails.campaign') // Blade view create karna hai
                    ->with(['body' => $this->details['body']]);
    }
}
