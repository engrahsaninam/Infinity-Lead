<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage as LaravelMailMessage;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable): LaravelMailMessage
    {
        return (new LaravelMailMessage)
            ->subject('Verify Your Email Address')
            ->view('emails.verifyRegisterEmail', [
                'user' => $this->user,
            ]);
    }
}
