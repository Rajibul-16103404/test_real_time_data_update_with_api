<?php

namespace Tests\Feature;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ChatMessageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test retrieving the latest chat messages history.
     */
    public function test_can_get_latest_chat_messages(): void
    {
        // Seed some messages
        ChatMessage::create([
            'username' => 'User A',
            'message' => 'Hello first',
            'color' => '#a78bfa',
        ]);

        ChatMessage::create([
            'username' => 'User B',
            'message' => 'Hello second',
            'color' => '#f472b6',
        ]);

        $response = $this->getJson(route('api.messages.index'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(2, 'messages');

        // Check chronological ordering (oldest first)
        $messages = $response->json('messages');
        $this->assertEquals('User A', $messages[0]['username']);
        $this->assertEquals('User B', $messages[1]['username']);
    }

    /**
     * Test sending a chat message dispatches the broadcast event and stores in DB.
     */
    public function test_can_send_chat_message_and_broadcasts(): void
    {
        Event::fake();

        $payload = [
            'username' => 'Explorer_402',
            'message' => 'Hello WebSocket world!',
            'color' => '#38bdf8',
        ];

        $response = $this->postJson(route('api.messages.store'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => [
                    'username' => 'Explorer_402',
                    'message' => 'Hello WebSocket world!',
                    'color' => '#38bdf8',
                ],
            ]);

        $this->assertDatabaseHas('chat_messages', [
            'username' => 'Explorer_402',
            'message' => 'Hello WebSocket world!',
            'color' => '#38bdf8',
        ]);

        Event::assertDispatched(MessageSent::class, function (MessageSent $event) {
            return $event->message->username === 'Explorer_402' &&
                $event->message->message === 'Hello WebSocket world!';
        });
    }

    /**
     * Test validating input payload when creating a chat message.
     */
    public function test_validates_chat_message_payload(): void
    {
        // Missing username and message, invalid color format
        $response = $this->postJson(route('api.messages.store'), [
            'color' => 'invalid-color',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username', 'message', 'color']);
    }
}
