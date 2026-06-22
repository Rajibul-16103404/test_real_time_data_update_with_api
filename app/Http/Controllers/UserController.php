<?php

namespace App\Http\Controllers;

use App\Models\ChatRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    /**
     * Get a list of all other users with connection statuses.
     */
    public function index(): JsonResponse
    {
        $authUserId = Auth::id();

        $users = User::query()
            ->where('id', '!=', $authUserId)
            ->get()
            ->map(function (User $user) use ($authUserId) {
                // Find request between current user and this user
                $req = ChatRequest::query()
                    ->where(function ($q) use ($authUserId, $user) {
                        $q->where('sender_id', $authUserId)->where('receiver_id', $user->id);
                    })
                    ->orWhere(function ($q) use ($authUserId, $user) {
                        $q->where('sender_id', $user->id)->where('receiver_id', $authUserId);
                    })
                    ->first();

                $status = 'none'; // none, pending_sent, pending_received, accepted
                $requestId = null;

                if ($req) {
                    $requestId = $req->id;
                    if ($req->status === 'accepted') {
                        $status = 'accepted';
                    } else {
                        $status = $req->sender_id === $authUserId ? 'pending_sent' : 'pending_received';
                    }
                }

                $lastSeen = Cache::get('user-last-seen-'.$user->id);
                $isOnline = $lastSeen ? $lastSeen->greaterThanOrEqualTo(now()->subMinutes(5)) : false;

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $status,
                    'request_id' => $requestId,
                    'is_online' => $isOnline,
                    'last_seen' => $lastSeen ? $lastSeen->toIso8601String() : null,
                ];
            });

        return response()->json([
            'success' => true,
            'users' => $users,
        ]);
    }

    /**
     * Get a specific user's online activity status.
     */
    public function activity(User $user): JsonResponse
    {
        $lastSeen = Cache::get('user-last-seen-'.$user->id);
        $isOnline = false;

        if ($lastSeen) {
            $isOnline = $lastSeen->greaterThanOrEqualTo(now()->subMinutes(5));
        }

        return response()->json([
            'success' => true,
            'user_id' => $user->id,
            'is_online' => $isOnline,
            'last_seen' => $lastSeen ? $lastSeen->toIso8601String() : null,
        ]);
    }
}
