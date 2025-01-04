<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\FeedBack;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerFeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rating = $request->rate;
        $feedbacks = FeedBack::with(['availService', 'availService.service', 'availService.service.user'])
            ->latest()
            ->when($rating, function ($query) use ($rating) {
                $query->whereRate((int) $rating);
            })
            ->paginate(20)
            ->through(function ($feedback) {
                return [
                    'id' => $feedback->id,
                    'rate' => $feedback->rate,
                    'customer' => $feedback->user->name,
                    'service' => $feedback->availService->service->name,
                    'provider' => $feedback->availService->service->user->name,
                    'created_at' => $feedback->created_at
                ];
            });

        return Inertia::render("Users/Admin/Feedback/Index", compact('feedbacks'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $feedback = Feedback::whereId($id)
            ->with(["availService", "availService.service", "availService.service.user", "user"])
            ->first();

        return Inertia::render("Users/Admin/Feedback/Show", compact("feedback"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $feedback = FeedBack::with(['user', 'user.profile'])->whereId($id)->first();
        return Inertia::render("Users/Admin/Feedback/Edit", compact("feedback"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FeedBack $feedback)
    {
        $safe = $request->validate(['content' => ['required', 'string', 'max:255']]);

        $feedback->update(['content' => $safe['content']]);

        return to_route('admin.feedbacks.index')->with('message_success', 'Feedback has been updated');
    }

    /**
     * Display delete modal
     */

    public function delete(FeedBack $feedback)
    {
        return Inertia::render("Users/Admin/Feedback/Delete", compact("feedback"));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeedBack $feedback)
    {
        $feedback->delete();
        return to_route('admin.feedbacks.index')->with('message_success', 'Feedback has been updated');
    }
}
