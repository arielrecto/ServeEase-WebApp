<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Models\Barangay;
use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return Inertia::render('Users/ServiceProvider/Services/Index', compact(['services']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $serviceTypes = ServiceType::get();

        $barangays = Barangay::get();

        return Inertia::render('Users/ServiceProvider/Services/Create', compact(['serviceTypes', 'barangays']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
        ]);

        $imageName = 'thumbnail-' . uniqid() . '.' . $request->thumbnail->extension();
        $dir = $request->thumbnail->storeAs('/service_thumbnails', $imageName, 'public');

        Service::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'price_type' => $request->priceType,
            'terms_and_conditions' => $request->termAndCondition,
            'service_type_id' => $request->serviceType,
            'is_approved' => false,
            'barangay_id' => $request->barangay,
            'user_id' => $request->user()->id,
            'thumbnail' => asset('/storage/' . $dir),
        ]);

        return to_route('profile.provider')->with([
            'message_success' => 'Service Added',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = Service::with(['user.profile.providerProfile'])
            ->where('id', $id)
            ->first();

        $availServices = $service->AvailService()
            ->with(['service.user', 'user'])
            ->latest()
            ->paginate(10);

        // Get chart data
        $chartData = $this->getServiceChartData($service);

        return Inertia::render('Users/ServiceProvider/Services/Show', compact([
            'service',
            'availServices',
            'chartData'
        ]));
    }

    /**
     * Get chart data for service analytics
     */
    private function getServiceChartData(Service $service)
    {
        // Get all avail services for this service
        $availServices = $service->availService()->get();

        // Initialize monthly sales data
        $monthlySales = array_fill(0, 12, 0);

        // Initialize ratings data
        $ratings = [
            '5 Stars' => 0,
            '4 Stars' => 0,
            '3 Stars' => 0,
            '2 Stars' => 0,
            '1 Star' => 0
        ];

        foreach ($availServices as $booking) {
            // Calculate monthly sales
            if ($booking->start_date) {
                $month = date('n', strtotime($booking->start_date)) - 1; // 0-based month index
                $monthlySales[$month] += $booking->total_price;
            }

            // Calculate ratings distribution
            if ($booking->rate) {
                $ratingKey = $booking->rate . ($booking->rate === 1 ? ' Star' : ' Stars');
                $ratings[$ratingKey]++;
            }
        }

        return [
            'monthlySales' => [
                'labels' => [
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December'
                ],
                'datasets' => [
                    [
                        'label' => 'Monthly Sales',
                        'backgroundColor' => '#f87979',
                        'data' => array_values($monthlySales)
                    ]
                ]
            ],
            'ratings' => [
                'labels' => array_keys($ratings),
                'datasets' => [
                    [
                        'backgroundColor' => ['#41B883', '#00D8FF', '#E46651', '#DD1B16', '#FD8008'],
                        'data' => array_values($ratings)
                    ]
                ]
            ]
        ];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $service = Service::findOrFail($id);
        $serviceTypes = ServiceType::get();
        $barangays = Barangay::get();

        return Inertia::render('Users/ServiceProvider/Services/Edit', compact(['service', 'serviceTypes', 'barangays']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $service = Service::findOrFail($id);

        $fields = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'price_type' => 'required',
            'description' => 'required',
            'terms_and_conditions' => 'required',
            'barangay_id' => 'required|exists:barangays,id',
            'thumbnail' => 'nullable|sometimes|file|mimes:png,jpg,jpeg|max:5000',
        ]);

        if ($request->hasFile('thumbnail')) {
            $imageName = 'thumbnail-' . uniqid() . '.' . $request->thumbnail->extension();
            $dir = $request->thumbnail->storeAs('/service_thumbnails', $imageName, 'public');
            $fields['thumbnail'] = asset('/storage/' . $dir);
        }

        $service->update($fields);

        return to_route('service-provider.services.index')->with('message_success', 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = Service::find($id);

        $service->delete();
    }
}
