<?php

namespace App\Http\Controllers\Customer;

use App\Models\Report;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['user'])
            ->where('reported_by', auth()->id())
            ->latest()
            ->paginate(10)
            ->through(function ($report) {
                $report->load('user.profile');

                return [
                    'id' => $report->id,
                    'user' => [
                        'name' => $report->user->profile->full_name,
                    ],
                    'complaint' => $report->complaint,
                    'type' => $report->type,
                    'status' => $report->status,
                    'created_at' => $report->created_at->format('M d, Y'),
                ];
            });

        return Inertia::render('Users/Customer/Report/Index', [
            'reports' => $reports
        ]);
    }

    public function create()
    {

        return Inertia::render('Users/Customer/Report/Create', [
            'providers' => User::role('service provider')
                ->orWhereHas('profile', function ($query) {
                    $query->whereNotNull('provider_profile_id');
                })
                ->with('profile')
                ->get()
                ->map(fn($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'service' => $user->profile->providerProfile?->serviceType?->name ?? 'N/A'
                ])
        ]);
    }

    public function store(Request $request)
    {


        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'complaint' => 'required|string|max:1000',
            'type' => 'required|in:illegal_transaction,unfinished_booking',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        $report = Report::create([
            ...$validated,
            'status' => 'pending',
            'reported_by' => auth()->id(),
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('reports/attachments', 'public');
                $report->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        return redirect()
            ->route('customer.report.index')
            ->with('success', 'Report submitted successfully.');
    }

    public function show(Report $report)
    {
        // Ensure user can only view their own reports
        if ($report->reported_by !== auth()->id()) {
            abort(403);
        }

        $report->load(['user.profile', 'attachments', 'remarks.user']);

        return Inertia::render('Users/Customer/Report/Show', [
            'report' => [
                'id' => $report->id,
                'user' => [
                    'name' => $report->user->profile->full_name,
                ],
                'complaint' => $report->complaint,
                'type' => $report->type,
                'status' => $report->status,
                'created_at' => $report->created_at->format('M d, Y'),
                'attachments' => $report->attachments->map(fn($attachment) => [
                    'id' => $attachment->id,
                    'file_name' => $attachment->file_name,
                    'file_path' => asset('storage/' . $attachment->file_path),
                    'file_type' => $attachment->file_type,
                ]),
                'remarks' => $report->remarks->map(fn($remark) => [
                    'id' => $remark->id,
                    'content' => $remark->content,
                    'created_at' => $remark->created_at,
                    'user' => [
                        'name' => $remark->user->name
                    ]
                ]),
            ]
        ]);
    }

    public function destroy(Report $report)
    {
        // Ensure user can only delete their own pending reports
        if ($report->reported_by !== auth()->id() || $report->status !== 'pending') {
            abort(403);
        }

        // Delete attachments
        foreach ($report->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }

        $report->delete();

        return back()->with('success', 'Report cancelled successfully.');
    }

    public function receivedComplaints()
    {
        $reports = Report::with(['reportedBy'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10)
            ->through(function ($report) {
                return [
                    'id' => $report->id,
                    'type' => $report->type,
                    'status' => $report->status,
                    'created_at' => $report->created_at->format('M d, Y'),
                ];
            });

        return Inertia::render('Users/Customer/Report/ReceivedComplaints', [
            'reports' => $reports
        ]);
    }
}
