<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewInvoice extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }
    public string $message;
    public int $automation_id;
    public function __construct($message,$automation_id)
    {
        $this->message = $message;
        $this->automation_id = $automation_id;
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('همیاران شمال شرق')
            ->icon('/images/new_notification.png')
            ->body($this->message)
            ->options(['TTL' => 1000])
            ->data(['action_route' => route("InvoiceAutomation.details",$this->automation_id),'type' => 'NewPerformance']);
    }
}
