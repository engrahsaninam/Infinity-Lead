<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage as LaravelMailMessage;

class CustomPasswordReset extends Notification
{
    use Queueable;

    protected $customToken;

    public function __construct($customToken)
    {
        $this->customToken = $customToken;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = url('password/reset', $this->customToken);

        return (new LaravelMailMessage)
            ->subject('Forgot Password')
            ->view('emails.custom_password_reset', [
                'resetUrl' => $resetUrl,
                'notifiable' => $notifiable,
            ]);
    }
}
