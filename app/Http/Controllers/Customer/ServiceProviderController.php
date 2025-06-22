<?php

namespace App\Http\Controllers\Customer;

use App\Models\Remark;
use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Service;
use App\Enums\ServicesType;
use App\Models\ServiceType;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ProviderProfile;
use App\Events\NotificationSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Actions\GenerateNotificationAction;

class ServiceProviderController extends Controller
{
    public function create()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $rejectedCount = Remark::where('remarkable_type', ProviderProfile::class)
            ->where('remarkable_id', auth()->user()->profile->providerProfile->id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        if ($rejectedCount >= 3) {
            return back()->with('message_error', 'You have reached the maximum number of rejected bookings for this month. Please try again later.');
        }

        // dd($rejectedCount, ProviderProfile::whereHas('profile', function ($query) use ($now) {
        //     $query->where('user_id', Auth::user()->id);
        // })
        //     ->where('status', 'rejected')
        //     ->whereBetween('created_at', [$startOfMonth, $endOfMonth]));

        $serviceTypes = ServiceType::all();
        $providerProfile = auth()->user()->profile->providerProfile;

        if ($providerProfile) {
            $providerProfile->load(['serviceType', 'remarks.user']);
        }

        if ($providerProfile?->verified_at) {
            return back();
        }

        return Inertia::render('Users/Customer/ServiceProvider/Create', [
            'serviceTypes' => $serviceTypes,
            'providerProfile' => $providerProfile
        ]);
    }


    public function store(Request $request)
    {
        try {
            $notification = DB::transaction(function () use ($request) {
                $request->validate([
                    'service' => 'required',
                    'experience' => 'required',
                    'experience_duration' => 'required',
                    'contact' => 'required',
                    'valid_id_type' => 'required|string',
                    'citizenship_document_type' => 'required|string',
                    'valid_id_image' => 'required|mimes:png,jpg',
                    'proof_document_image' => 'required|mimes:png,jpg',
                    'citizenship_document_image' => 'required|mimes:png,jpg',
                    'additional_documents.*' => 'nullable|file|max:10240', // 10MB max per file
                ]);

                $validIdImageName = 'valid-id-' . uniqid() . '.' . $request->valid_id_image->extension();
                $validIdImage = $request->file('valid_id_image')->storeAs('provider/valid-id', $validIdImageName, 'public');

                $proofDocumentImageName = 'proof-document-' . uniqid() . '.' . $request->proof_document_image->extension();
                $proofDocumentImage = $request->file('proof_document_image')->storeAs('provider/proof-document', $proofDocumentImageName, 'public');

                $citizenshipDocumentImageName = 'certificate-' . uniqid() . '.' . $request->citizenship_document_image->extension();
                $citizenshipDocumentImage = $request->file('citizenship_document_image')->storeAs('provider/certificate', $citizenshipDocumentImageName, 'public');

                // Create provider profile
                $providerProfile = ProviderProfile::create([
                    'service_type_id' => $request->service,
                    'experience' => $request->experience,
                    'experience_duration' => $request->experience_duration,
                    'contact' => $request->contact,
                    'valid_id_type' => $request->valid_id_type,
                    'valid_id_image' => asset('/storage/' . $validIdImage),
                    'citizenship_document_type' => $request->citizenship_document_type,
                    'citizenship_document_image' => asset('/storage/' . $citizenshipDocumentImage),
                    'proof_document_image' => asset('/storage/' . $proofDocumentImage),
                    'profile_id' => $request->user()->profile->id,
                    // 'verified_at' => now()
                ]);

                // Handle additional documents
                if ($request->hasFile('additional_documents')) {
                    foreach ($request->file('additional_documents') as $file) {
                        $fileName = 'additional-' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $filePath = $file->storeAs('provider/additional-documents', $fileName, 'public');

                        $providerProfile->attachments()->create([
                            'file_name' => $file->getClientOriginalName(),
                            'file_path' => $filePath,
                            'file_type' => $file->getClientOriginalExtension(),
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                        ]);
                    }
                }

                $notification = Notification::create([
                    'user_id' => User::role('admin')->first()->id,
                    'content' => GenerateNotificationAction::handle('application', 'application-created', auth()->user()),
                    'type' => 'application',
                    'url' => route('admin.applications.index')
                ]);

                return $notification;

            });

            broadcast(new NotificationSent($notification))->toOthers();

            return to_route('customer.dashboard')->with('message_success', 'Application submitted successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('message_error', 'Something went wrong. Please try again.');
        }
    }

    public function update(Request $request, ProviderProfile $providerProfile)
    {
        // dd($request->all());
        $request->validate([
            'service' => 'required',
            'experience' => 'required',
            'experience_duration' => 'required',
            'contact' => 'required',
            'valid_id_type' => 'required|string',
            'citizenship_document_type' => 'required|string',
            'valid_id_image' => 'required|mimes:png,jpg',
            'proof_document_image' => 'required|mimes:png,jpg',
            'citizenship_document_image' => 'required|mimes:png,jpg',
            'additional_documents.*' => 'nullable|file|max:10240',
        ]);

        $validIdImageName = 'valid-id-' . uniqid() . '.' . $request->valid_id_image->extension();
        $validIdImage = $request->valid_id_image->storeAs('/provider/valid-id', $validIdImageName, 'public');

        $proofDocumentImageName = 'proof-document-' . uniqid() . '.' . $request->proof_document_image->extension();
        $proofDocumentImage = $request->proof_document_image->storeAs('/provider/proof-document', $proofDocumentImageName, 'public');

        $citizenshipDocumentImageName = 'certificate-' . uniqid() . '.' . $request->citizenship_document_image->extension();
        $citizenshipDocumentImage = $request->citizenship_document_image->storeAs('/provider/certificate', $citizenshipDocumentImageName, 'public');

        $providerProfile->update([
            'service_type_id' => $request->service,
            'experience' => $request->experience,
            'experience_duration' => $request->experience_duration,
            'contact' => $request->contact,
            'valid_id_type' => $request->valid_id_type,
            'valid_id_image' => asset('/storage/' . $validIdImage),
            'citizenship_document_type' => $request->citizenship_document_type,
            'citizenship_document_image' => asset('/storage/' . $citizenshipDocumentImage),
            'proof_document_image' => asset('/storage/' . $proofDocumentImage),
            'profile_id' => $request->user()->profile->id,
            'status' => "pending",
            // 'verified_at' => now()
        ]);

        // Handle additional documents
        if ($request->hasFile('additional_documents')) {
            // Delete existing attachments
            $providerProfile->attachments()->delete();

            // Add new attachments
            foreach ($request->file('additional_documents') as $file) {
                $fileName = 'additional-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('provider/additional-documents', $fileName, 'public');

                $providerProfile->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        return to_route('customer.dashboard')->with('message_success', 'Application updated successfully.');

    }
}
