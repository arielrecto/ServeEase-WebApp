<?php

namespace App\Http\Controllers\Customer;

use Inertia\Inertia;
use App\Models\Service;
use App\Models\FeedBack;
use App\Models\AvailService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = Service::with(['user'])->where('id', $id)->first();


        return Inertia::render('Users/Customer/Services/Show', compact(['service']));
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

    public function availCreate(string $id)
    {
        $service = Service::with(['user'])->where('id', $id)->first();



        return Inertia::render('Users/Customer/Services/Avail', compact(['service']));
    }

    public function availStore(Request $request)
    {

        $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'remark' => 'required|string',
            'total' => 'required|numeric',
            'service' => 'required|exists:services,id'
        ]);

        $service = Service::find($request->service);

        if ($service->user_id == Auth::user()->id) {
            return back()->with(['message_error' => 'You cannot avail your own service']);
        }



        $total_hours = Carbon::parse($request->startDate)->diffInDays(Carbon::parse($request->endDate)) * 8;


        if ($service->price_type == 'hr') {
            $total_hours = $request->hours;
        }


        AvailService::create([
            'start_date' => $request->startDate,
            'end_date' => $request->endDate,
            'remarks' => $request->remark,
            'total_price' => $request->total,
            'service_id' => $request->service,
            'total_hours' => $total_hours,
            'user_id' => Auth::user()->id
        ]);


        return to_route('customer.services.show', ['service' => $request->service])->with(['message_success']);
    }

    public function getFeedbackByService(Request $request, Service $service)
    {
        $rate = $request->rating;

        $feedbacks = FeedBack::with(['user', 'user.profile'])
            ->whereHas('availService', function ($query) use ($service) {
                $query->whereServiceId($service->id);
            })
            ->when($rate, function ($query) use ($rate) {
                $query->whereRate($rate);
            })
            ->latest()
            ->get();

        return response()->json($feedbacks, 200);
    }
}
