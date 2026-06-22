<?php

namespace App\Events;

use App\Models\ChatRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatRequestAccepted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public ChatRequest $chatRequest)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->chatRequest->sender_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->chatRequest->id,
            'status' => $this->chatRequest->status,
            'receiver' => [
                'id' => $this->chatRequest->receiver->id,
                'name' => $this->chatRequest->receiver->name,
                'email' => $this->chatRequest->receiver->email,
            ],
            'updated_at' => $this->chatRequest->updated_at->toIso8601String(),
        ];
    }
}
