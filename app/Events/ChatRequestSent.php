<?php

namespace App\Events;

use App\Models\ChatRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatRequestSent implements ShouldBroadcastNow
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
            new PrivateChannel('user.'.$this->chatRequest->receiver_id),
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
            'sender' => [
                'id' => $this->chatRequest->sender->id,
                'name' => $this->chatRequest->sender->name,
                'email' => $this->chatRequest->sender->email,
            ],
            'created_at' => $this->chatRequest->created_at->toIso8601String(),
        ];
    }
}
