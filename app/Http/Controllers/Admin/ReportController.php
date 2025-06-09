<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Report;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Events\NotificationSent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['user', 'reportedBy'])
            ->latest()
            ->paginate(10)
            ->through(function ($report) {
                $report->user->load('profile');
                $report->reportedBy->load('profile');

                return [
                    'id' => $report->id,
                    'user' => [
                        'id' => $report->user->id,
                        'name' => $report->user->profile->full_name,
                    ],
                    'reported_by' => [
                        'id' => $report->reportedBy->id,
                        'name' => $report->reportedBy->profile->full_name,
                    ],
                    'complaint' => $report->complaint,
                    'type' => $report->type,
                    'status' => $report->status,
                    'created_at' => $report->created_at->format('M d, Y'),
                ];
            });

        return Inertia::render('Users/Admin/Report/Index', [
            'reports' => $reports
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'complaint' => 'required|string|max:1000',
            'type' => 'required|in:illegal_transaction,unfinished_booking',
        ]);

        $report = Report::create([
            ...$validated,
            'status' => 'pending',
            'reported_by' => auth()->id(),
        ]);

        // Handle attachments if any
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('reports/attachments', 'public');
                $report->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return back()->with('success', 'Report submitted successfully.');
    }

    public function show(Report $report)
    {
        $report->load(['user', 'user.profile', 'reportedBy', 'attachments', 'remarks.user']);

        return Inertia::render('Users/Admin/Report/Show', [
            'report' => [
                'id' => $report->id,
                'user' => [
                    'id' => $report->user->id,
                    'name' => $report->user->profile->full_name,
                ],
                'reported_by' => [
                    'id' => $report->reportedBy->id,
                    'name' => $report->reportedBy->profile->full_name,
                ],
                'complaint' => $report->complaint,
                'type' => $report->type,
                'status' => $report->status,
                'admin_remarks' => $report->remarks->last()?->content,
                'created_at' => $report->created_at->format('M d, Y'),
                'attachments' => $report->attachments->map(fn($attachment) => [
                    'id' => $attachment->id,
                    'file_name' => $attachment->file_name,
                    'file_path' => asset('storage/' . $attachment->file_path),
                    'file_type' => $attachment->file_type,
                ]),
            ]
        ]);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,resolved,rejected',
            'admin_remarks' => 'required_if:status,rejected|nullable|string|max:1000',
        ]);

        $report->update($validated);

        return back()->with('success', 'Report status updated successfully.');
    }

    public function destroy(Report $report)
    {
        // Delete attachments first
        foreach ($report->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }

        $report->delete();

        return back()->with('success', 'Report deleted successfully.');
    }

    public function approve(Report $report)
    {
        $report->update([
            'status' => 'approved',
        ]);

        // Count approved reports for this user
        $approvedReportsCount = $report->user->reports()->where('status', 'approved')->count();

        // Create notification for the reported user
        $notification = Notification::create([
            'user_id' => $report->user_id,
            'content' => 'Your complaint report #' . $report->id . ' has been approved.',
            'type' => 'report_approved',
            'url' => '/customer/report/received',
            'is_seen' => false
        ]);

        broadcast(new NotificationSent($notification))->toOthers();

        // Send warning notification if user has 2 approved reports
        if ($approvedReportsCount === 2) {
            $notification = Notification::create([
                'user_id' => $report->user_id,
                'content' => 'Warning: You have received 2 approved complaints. One more complaint will result in account suspension.',
                'type' => 'report_warning',
                'url' => '/customer/report/received',
                'is_seen' => false
            ]);

            broadcast(new NotificationSent($notification))->toOthers();
        }
        // Check for suspension threshold
        elseif ($approvedReportsCount >= 3) {
            $report->user()->update(['is_suspended' => true]);

            // Create suspension notification
            $notification = Notification::create([
                'user_id' => $report->user_id,
                'content' => 'Your account has been suspended due to receiving 3 or more approved complaints.',
                'type' => 'account_suspended',
                'url' => '/customer/report/received',
                'is_seen' => false
            ]);

            broadcast(new NotificationSent($notification))->toOthers();
        }

        return back()->with('success', 'Report has been approved.');
    }

    public function reject(Request $request, Report $report)
    {
        $validated = $request->validate([
            'admin_remarks' => 'required|string|max:1000',
        ]);

        $report->update(['status' => 'rejected']);

        // Create remark using polymorphic relationship
        $report->remarks()->create([
            'user_id' => auth()->id(),
            'content' => $validated['admin_remarks']
        ]);

        // Create notification for the user
        $notification = Notification::create([
            'user_id' => $report->user_id,
            'content' => 'Your complaint report #' . $report->id . ' has been rejected. Click to see the details.',
            'type' => 'report_rejected',
            'url' => '/customer/report/' . $report->id,
            'is_seen' => false
        ]);

        broadcast(new NotificationSent($notification))->toOthers();

        return back()->with('success', 'Report has been rejected.');
    }

    public function resolve(Request $request, Report $report)
    {
        // Hindi naman ginagamit to
        // $validated = $request->validate([
        //     'resolution_remarks' => 'required|string|max:1000', :/
        // ]);

        $report->update([
            'status' => 'resolved',
        ]);

        return back()->with('success', 'Report has been resolved successfully.');
    }
}
