<?php

namespace App\Notifications\Client;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyOtpNotification extends Notification
{
    use Queueable;

    public function __construct(public string $otp)
    {
        $this->locale = app()->getLocale();
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Visa Egypt verification code')
            ->greeting(__('messages.notifications.greeting', ['name' => $notifiable->name]))
            ->line('Use this verification code to confirm your account:')
            ->line("**{$this->otp}**")
            ->line('This code expires in 10 minutes.')
            ->line(__('messages.notifications.thanks_for_using_our_app'));
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
