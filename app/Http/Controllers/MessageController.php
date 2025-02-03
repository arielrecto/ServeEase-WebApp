<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        // If participant_id is provided, create a conversation if it doesn't exist
        if ($request->has('participant_id')) {
            $participant_id = $request->participant_id;

            // Check if conversation already exists
            $existingConversation = Conversation::where(function($query) use ($participant_id) {
                $query->where('owner_id', Auth::id())
                    ->where('participant_id', $participant_id);
            })->orWhere(function($query) use ($participant_id) {
                $query->where('owner_id', $participant_id)
                    ->where('participant_id', Auth::id());
            })->first();

            // If no conversation exists, create one
            if (!$existingConversation) {
                Conversation::create([
                    'owner_id' => Auth::id(),
                    'participant_id' => $participant_id
                ]);
            }
        }

        // Get all conversations for the current user
        $conversations = Conversation::where('owner_id', Auth::id())
            ->orWhere('participant_id', Auth::id())
            ->with(['owner', 'participant', 'messages' => function($query) {
                $query->latest();
            }])
            ->get();

        return Inertia::render('Messages/Index', [
            'conversations' => $conversations
        ]);
    }

    public function show(Conversation $conversation)
    {
        // Load the conversation with its messages and participants
        $conversation->load(['messages' => function($query) {
            $query->with(['sender', 'receiver'])->orderBy('created_at', 'asc');
        }, 'owner', 'participant']);

        // Mark unseen messages as seen
        Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', Auth::id())
            ->where('is_seen', false)
            ->update(['is_seen' => true]);

        return Inertia::render('Messages/Show', [
            'conversation' => $conversation
        ]);
    }

    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        // Determine receiver ID
        $receiverId = $conversation->owner_id == Auth::id()
            ? $conversation->participant_id
            : $conversation->owner_id;

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'content' => $request->content,
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'is_seen' => false,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return redirect()->back();
    }
}
