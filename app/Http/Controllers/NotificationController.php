<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsSeen(Request $request)
    {
        $request->validate(['notifications' => ['required', 'array']]);

        foreach ($request->notifications as $notification) {
            $notification = Notification::find($notification['id']);
            $notification->update(['is_seen' => true]);
        }

        return response()->json(['message' => 'Marked all as seen'], 200);
    }
}
