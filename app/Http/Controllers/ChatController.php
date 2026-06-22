<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Retrieve the latest 50 chat messages in chronological order.
     */
    public function index(): JsonResponse
    {
        $messages = ChatMessage::query()
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Store a new chat message and broadcast it.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:1000'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $message = ChatMessage::create([
            'username' => $validated['username'],
            'message' => $validated['message'],
            'color' => $validated['color'] ?? '#a78bfa', // Default violet
        ]);

        MessageSent::dispatch($message);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }
}
