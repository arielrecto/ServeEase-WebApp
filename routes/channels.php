<?php

use App\Models\User;
use App\Models\Conversation;
use App\Models\Notification;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{conversation}', function ($user, Conversation $conversation) {
    return (int) $user->id === (int) $conversation->owner_id ||
        (int) $user->id === (int) $conversation->participant_id;
});

Broadcast::channel('notifications.{user}', function (User $authUser, User $user) {
    return (int) $authUser->id === (int) $user->id;
});
