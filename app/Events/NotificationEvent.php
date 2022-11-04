<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JetBrains\PhpStorm\ArrayShape;

class NotificationEvent implements shouldBroadCast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $user_id;
    public array $data;

    public function __construct($user_id,$data)
    {
        $this->user_id = $user_id;
        $this->data = $data;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('notifications.' . $this->user_id);
    }

    #[ArrayShape(["message" => "mixed", 'action' => "mixed", "type" => "mixed"])] public function broadcastWith(): array
    {
        return ["message" => $this->data["message"],'action' => $this->data["action"],"type" => $this->data["type"]];
    }
}
