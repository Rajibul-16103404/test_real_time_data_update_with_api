<?php

namespace App\Http\Controllers;

use App\Events\ChatRequestAccepted;
use App\Events\ChatRequestSent;
use App\Models\ChatRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatRequestController extends Controller
{
    /**
     * Send a new chat request.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
        ]);

        $senderId = Auth::id();
        $receiverId = $validated['receiver_id'];

        if ($senderId === (int) $receiverId) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot send a chat request to yourself.',
            ], 422);
        }

        // Check if request already exists in either direction
        $existing = ChatRequest::query()
            ->where(function ($q) use ($senderId, $receiverId) {
                $q->where('sender_id', $senderId)->where('receiver_id', $receiverId);
            })
            ->orWhere(function ($q) use ($senderId, $receiverId) {
                $q->where('sender_id', $receiverId)->where('receiver_id', $senderId);
            })
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'A connection request already exists between you and this user.',
                'status' => $existing->status,
            ], 422);
        }

        $chatRequest = ChatRequest::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'status' => 'pending',
        ]);

        // Load the sender relationship for the broadcast
        $chatRequest->load('sender');

        ChatRequestSent::dispatch($chatRequest);

        return response()->json([
            'success' => true,
            'request' => $chatRequest,
        ], 201);
    }

    /**
     * Accept a pending chat request.
     */
    public function accept(ChatRequest $chatRequest): JsonResponse
    {
        $authUserId = Auth::id();

        if ($chatRequest->receiver_id !== $authUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        if ($chatRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This request is not pending.',
            ], 422);
        }

        $chatRequest->update([
            'status' => 'accepted',
        ]);

        // Load relations for broadcast payload
        $chatRequest->load(['sender', 'receiver']);

        ChatRequestAccepted::dispatch($chatRequest);

        return response()->json([
            'success' => true,
            'request' => $chatRequest,
        ]);
    }

    /**
     * Decline or cancel a chat request.
     */
    public function decline(ChatRequest $chatRequest): JsonResponse
    {
        $authUserId = Auth::id();

        if ($chatRequest->sender_id !== $authUserId && $chatRequest->receiver_id !== $authUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        $chatRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Chat request deleted successfully.',
        ]);
    }
}
