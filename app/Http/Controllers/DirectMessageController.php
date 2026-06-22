<?php

namespace App\Http\Controllers;

use App\Events\DirectMessageSent;
use App\Models\ChatRequest;
use App\Models\DirectMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirectMessageController extends Controller
{
    /**
     * Helper to verify if two users are accepted chat partners.
     */
    private function areConnected(int $userA, int $userB): bool
    {
        return ChatRequest::query()
            ->where('status', 'accepted')
            ->where(function ($q) use ($userA, $userB) {
                $q->where(function ($q2) use ($userA, $userB) {
                    $q2->where('sender_id', $userA)->where('receiver_id', $userB);
                })->orWhere(function ($q2) use ($userA, $userB) {
                    $q2->where('sender_id', $userB)->where('receiver_id', $userA);
                });
            })
            ->exists();
    }

    /**
     * Retrieve conversation history with a user.
     */
    public function index(int $userId): JsonResponse
    {
        $authUserId = Auth::id();

        if (! $this->areConnected($authUserId, $userId)) {
            return response()->json([
                'success' => false,
                'message' => 'You can only message users who have accepted your chat request.',
            ], 403);
        }

        $messages = DirectMessage::query()
            ->where(function ($q) use ($authUserId, $userId) {
                $q->where('sender_id', $authUserId)->where('receiver_id', $userId);
            })
            ->orWhere(function ($q) use ($authUserId, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $authUserId);
            })
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a direct message.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $authUserId = Auth::id();
        $receiverId = (int) $validated['receiver_id'];

        if (! $this->areConnected($authUserId, $receiverId)) {
            return response()->json([
                'success' => false,
                'message' => 'You can only message users who have accepted your chat request.',
            ], 403);
        }

        $directMessage = DirectMessage::create([
            'sender_id' => $authUserId,
            'receiver_id' => $receiverId,
            'message' => $validated['message'],
        ]);

        DirectMessageSent::dispatch($directMessage);

        return response()->json([
            'success' => true,
            'message' => $directMessage,
        ], 201);
    }
}
