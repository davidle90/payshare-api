<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendRequestController extends ApiController
{
    public function sendRequest(Request $request)
    {
        $receiver_reference_id = $request->get('receiver_reference_id');
        $receiver = User::where('reference_id', $receiver_reference_id)->first();

        if(!$receiver){
            return $this->error('User not found', 404);
        }

        $sender = Auth::user();

        if ($sender->id === $receiver->id) {
            return $this->error('You cannot send a friend request to yourself.', 400);
        }

        $existingRequest = FriendRequest::where(function ($query) use ($sender, $receiver) {
            $query->where('sender_id', $sender->id)
                ->where('receiver_id', $receiver->id);
        })->orWhere(function ($query) use ($sender, $receiver) {
            $query->where('sender_id', $receiver->id)
                ->where('receiver_id', $sender->id);
        })->first();

        if ($existingRequest) {
            return $this->error('Friend request already exists.', 400);
        }

        $sender->sendFriendRequest($receiver);

        return $this->ok('Friend request sent.');
    }

    public function acceptRequest(FriendRequest $friendRequest)
    {
        $receiver = Auth::user();

        if ($friendRequest->receiver_id !== $receiver->id) {
            return $this->error('You are not authorized to accept this request.', 403);
        }

        $receiver->acceptFriendRequest($friendRequest);

        return $this->ok('Friend request accepted.');
    }

    public function declineRequest(FriendRequest $friendRequest)
    {
        $receiver = Auth::user();

        if ($friendRequest->receiver_id !== $receiver->id) {
            return $this->error('You are not authorized to decline this request.', 403);
        }

        $receiver->declineFriendRequest($friendRequest);

        return $this->ok('Friend request declined.');
    }

    public function removeFriend(User $friend)
    {
        if(!$friend){
            return $this->error('User not found.', 404);
        }
        $user = Auth::user();

        $user->removeFriend($friend);

        return $this->ok('You are no longer friends :(');
    }
}
