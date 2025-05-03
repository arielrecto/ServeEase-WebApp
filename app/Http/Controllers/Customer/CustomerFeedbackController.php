<?php

namespace App\Http\Controllers\Customer;

use Inertia\Inertia;
use App\Models\FeedBack;
use App\Models\AvailService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CustomerFeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $feedbacks = FeedBack::with(['availService', 'availService.service', 'availService.service.user'])
            ->whereUserId(Auth::user()->id)
            ->latest()
            ->paginate(20)
            ->through(function ($feedback) {
                return [
                    'id' => $feedback->id,
                    'rate' => $feedback->rate,
                    'service' => $feedback->availService->service->name,
                    'provider' => $feedback->availService->service->user->name,
                    'created_at' => $feedback->created_at
                ];
            });

        return Inertia::render("Users/Customer/Feedback/Index", compact('feedbacks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // dd($request->all());
        $availService = AvailService::findOrFail($request->id);
        return Inertia::render('Users/Customer/Feedback/Create', compact(['availService']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $safe = $request->validate([
            'availServiceId' => ['required', 'exists:avail_services,id'],
            'rate' => ['required', 'integer', 'between:1,5'],
            'content' => ['required', 'string']
        ]);

        $feedback = FeedBack::create([
            'user_id' => $request->user()->id,
            'rate' => $safe['rate'],
            'content' => $safe['content'],
            'avail_service_id' => $safe['availServiceId']
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('feedback-attachments', 'public');

                $feedback->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]);
            }
        }

        return back()->with('message_success', 'Feedback submitted successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FeedBack $feedback)
    {
        return Inertia::render("Users/Customer/Feedback/Edit", compact('feedback'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FeedBack $feedback)
    {
        $safe = $request->validate([
            'rate' => ['required', 'integer', 'between:1,5'],
            'content' => ['required', 'string']
        ]);

        $feedback->update([
            'rate' => $safe['rate'],
            'content' => $safe['content']
        ]);

        return to_route('customer.feedbacks.index')->with('message_success', 'Feedback updated successfully');
    }

    public function delete(FeedBack $feedback)
    {
        return Inertia::render('Users/Customer/Feedback/Delete', compact('feedback'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeedBack $feedback)
    {
        $feedback->delete();

        return to_route('customer.feedbacks.index')->with('message_success', 'Feedback deleted successfully');
    }
}
