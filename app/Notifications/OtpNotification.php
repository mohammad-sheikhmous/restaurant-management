<?php

namespace App\Notifications;

use Ichtrojan\Otp\Otp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $OTP;

    // Create a new notification instance.
    public function __construct()
    {
        $this->OTP = new Otp();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $OTP = $this->OTP->generate($notifiable->email, 'numeric', 6, 20);
        return (new MailMessage)
            ->greeting('Hello ' . $notifiable->name)
            ->subject('OTP Code')
            ->line('Verify Your Email.')
            ->line('Code : ' . $OTP->token)
            ->line('The code is valid for 20 minutes and is used once.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
