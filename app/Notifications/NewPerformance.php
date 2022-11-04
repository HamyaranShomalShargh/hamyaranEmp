<?php

namespace App\Notifications;

use App\Models\ContractSubset;
use App\Models\PerformanceAutomation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewPerformance extends Notification
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
            ->data(['action_route' => route("PerformanceAutomation.details",$this->automation_id),'type' => 'NewPerformance']);
    }
}
