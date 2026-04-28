<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CallbackEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message;

    public function __construct(
        array|string    $output,
        protected string $status,
        protected string $broadcastOn,
    )
    {
        $this->message = is_string($output) ? $output : json_encode($output);
    }

    /**
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel($this->broadcastOn);
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return $this->status;
    }
}

