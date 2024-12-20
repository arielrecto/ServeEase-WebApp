<?php

namespace App\Http\Controllers\Customer;

use Inertia\Inertia;
use App\Models\FeedBack;
use App\Models\AvailService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerFeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

        FeedBack::create([
            'user_id' => $request->user()->id,
            'rate' => $safe['rate'],
            'content' => $safe['content'],
            'avail_service_id' => $safe['availServiceId']
        ]);

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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
