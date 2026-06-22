<?php

namespace App\Http\Controllers;

use App\Models\ChatRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $status,
                    'request_id' => $requestId,
                ];
            });

        return response()->json([
            'success' => true,
            'users' => $users,
        ]);
    }
}
