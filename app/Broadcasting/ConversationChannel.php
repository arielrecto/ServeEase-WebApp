<?php

namespace App\Broadcasting;

use App\Models\User;
use App\Models\Conversation;

class ConversationChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, Conversation $conversation): bool
    {
        return $user->id === $conversation->owner_id ||
               $user->id === $conversation->participant_id;
    }
}
