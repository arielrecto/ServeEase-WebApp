<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Models\PersonalEvent;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PersonalEventController extends Controller
{
    public function index()
    {
        $events = PersonalEvent::where('user_id', Auth::id())
            ->orderBy('start_date')
            ->get()
            ->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->event_name,
                    'start' => $event->start_date,
                    'end' => $event->end_date,
                    'type' => $event->event_type,
                    'description' => $event->description
                ];
            });

        return Inertia::render('Users/ServiceProvider/PersonalEvents/Index', [
            'events' => $events
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_type' => 'required|string|in:holiday,meeting,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string|max:1000',
        ]);

        PersonalEvent::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        return back()->with('message', 'Event created successfully');
    }

    public function edit(PersonalEvent $personalEvent)
    {
        return Inertia::render('Users/ServiceProvider/PersonalEvents/Edit', [
            'event' => $personalEvent
        ]);
    }

    public function update(Request $request, PersonalEvent $personalEvent)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_type' => 'required|string|in:holiday,meeting,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string|max:1000',
        ]);

        $personalEvent->update($validated);

        return redirect()->route('service-provider.personal-events.index')
            ->with('message', 'Event updated successfully');
    }

    public function destroy(PersonalEvent $personalEvent)
    {
        $personalEvent->delete();
        return back()->with('message', 'Event deleted successfully');
    }
}
