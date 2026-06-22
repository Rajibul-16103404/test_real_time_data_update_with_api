<?php

namespace Tests\Feature;

use App\Events\ChatRequestAccepted;
use App\Events\ChatRequestSent;
use App\Events\DirectMessageSent;
use App\Models\ChatRequest;
use App\Models\DirectMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PrivateChatTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test API user registration.
     */
    public function test_api_user_can_register(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson(route('api.register'), $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'user' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    /**
     * Test API user login.
     */
    public function test_api_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password123'),
        ]);

        $payload = [
            'email' => 'login@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson(route('api.login'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure(['token', 'user']);
    }

    /**
     * Test web user registration.
     */
    public function test_web_user_can_register(): void
    {
        $payload = [
            'name' => 'Web User',
            'email' => 'web@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $payload);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'web@example.com',
        ]);
        $this->assertAuthenticated();
    }

    /**
     * Test web user login.
     */
    public function test_web_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'weblogin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $payload = [
            'email' => 'weblogin@example.com',
            'password' => 'password123',
        ];

        $response = $this->post('/login', $payload);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test sending connection request.
     */
    public function test_user_can_send_chat_request_and_broadcasts(): void
    {
        Event::fake();

        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $response = $this->actingAs($sender)
            ->postJson(route('api.chat-requests.store'), [
                'receiver_id' => $receiver->id,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'request' => [
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'status' => 'pending',
                ],
            ]);

        $this->assertDatabaseHas('chat_requests', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => 'pending',
        ]);

        Event::assertDispatched(ChatRequestSent::class, function (ChatRequestSent $event) use ($sender, $receiver) {
            return $event->chatRequest->sender_id === $sender->id &&
                $event->chatRequest->receiver_id === $receiver->id;
        });
    }

    /**
     * Test accepting connection request.
     */
    public function test_user_can_accept_chat_request_and_broadcasts(): void
    {
        Event::fake();

        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $chatRequest = ChatRequest::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($receiver)
            ->postJson(route('api.chat-requests.accept', $chatRequest));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => [
                    'id' => $chatRequest->id,
                    'status' => 'accepted',
                ],
            ]);

        $this->assertDatabaseHas('chat_requests', [
            'id' => $chatRequest->id,
            'status' => 'accepted',
        ]);

        Event::assertDispatched(ChatRequestAccepted::class, function (ChatRequestAccepted $event) use ($chatRequest) {
            return $event->chatRequest->id === $chatRequest->id;
        });
    }

    /**
     * Test declining chat request.
     */
    public function test_user_can_decline_chat_request(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $chatRequest = ChatRequest::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($receiver)
            ->postJson(route('api.chat-requests.decline', $chatRequest));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('chat_requests', [
            'id' => $chatRequest->id,
        ]);
    }

    /**
     * Test direct messaging when connection is accepted.
     */
    public function test_user_can_send_direct_message_when_accepted(): void
    {
        Event::fake();

        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        // Connect them
        ChatRequest::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => 'accepted',
        ]);

        $response = $this->actingAs($sender)
            ->postJson(route('api.direct-messages.store'), [
                'receiver_id' => $receiver->id,
                'message' => 'Hello private friend!',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => [
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'message' => 'Hello private friend!',
                ],
            ]);

        $this->assertDatabaseHas('direct_messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'Hello private friend!',
        ]);

        Event::assertDispatched(DirectMessageSent::class, function (DirectMessageSent $event) use ($sender) {
            return $event->directMessage->sender_id === $sender->id &&
                $event->directMessage->message === 'Hello private friend!';
        });
    }

    /**
     * Test user cannot message if not connected.
     */
    public function test_user_cannot_send_direct_message_when_not_accepted(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        // No connection request
        $response = $this->actingAs($sender)
            ->postJson(route('api.direct-messages.store'), [
                'receiver_id' => $receiver->id,
                'message' => 'Sneaky message',
            ]);

        $response->assertStatus(403);

        // Connection exists but is only pending
        ChatRequest::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($sender)
            ->postJson(route('api.direct-messages.store'), [
                'receiver_id' => $receiver->id,
                'message' => 'Still pending message',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test retrieving conversation history.
     */
    public function test_user_can_retrieve_message_history_when_accepted(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        // Connect them
        ChatRequest::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => 'accepted',
        ]);

        // Create some messages
        DirectMessage::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'First message',
        ]);

        DirectMessage::create([
            'sender_id' => $receiver->id,
            'receiver_id' => $sender->id,
            'message' => 'Second reply',
        ]);

        $response = $this->actingAs($sender)
            ->getJson(route('api.direct-messages.show', $receiver->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(2, 'messages');

        $messages = $response->json('messages');
        $this->assertEquals('First message', $messages[0]['message']);
        $this->assertEquals('Second reply', $messages[1]['message']);
    }
}
