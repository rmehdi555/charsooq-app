<?php

namespace App\Notifications;

use App\Notifications\Channels\SmsRahyabChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SendOtpCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public $code)
    {
        $this->queue = 'sms-queue';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return [SmsRahyabChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toRahyab($notifiable)
    {
        return [
            'text' => __('messages.otp_sms', ['otp' => $this->code])
        ];
    }
}
