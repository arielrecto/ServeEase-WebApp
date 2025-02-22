<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Service;
use App\Models\Barangay;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function redirectAuthUser()
    {
        $brgys = Barangay::orderBy('name')->get();
        $services = ServiceType::orderBy('name')->get();
        return Inertia::render('Users/Search', compact(['brgys', 'services']));
    }

    // TODO: Add filtering logic
    public function servicesFilter(Request $request)
    {
        $rating = $request->byRating;
        $price = $request->byPrice;
        $more = $request->more ?? 0;
        $transactions = $request->byTransaction;

        // TODO: Add filter by rating & no. of transactions
        $services = Service::with(['user'])
            ->withCount(['availService as avail_service_count'])
            ->when($request->search, function ($q) use ($request) {
                $rating = null;
                $price = null;
                $transactions = null;
                $request->service = null;
                $request->brgy = null;

                $q->where('name', 'LIKE', "%{$request->search}%");
            })
            ->when($request->service, function ($q) use ($request) {
                $q->where('service_type_id', $request->service);
            })
            ->when($request->brgy, function ($q) use ($request) {
                $q->where('barangay_id', $request->brgy);
            })
            ->when($price, function ($q) use ($price) {
                if ($price === 'High') {
                    $q->orderBy('price', 'DESC');
                } else if ($price === 'Low') {
                    $q->orderBy('price', 'ASC');
                }
            })
            ->when($transactions, function ($q) use ($transactions) {
                $transactionsOrder = $transactions === 'High' ? 'DESC' : 'ASC';

                $q->withCount('availService')->orderBy('avail_service_count', $transactionsOrder);
            })
            // ->when($rating, function ($q) use ($rating) {
            //     if ($rating === 'Highest') {
            //         $q->sortBy(function ($service) {
            //             return $service->avg_rate;
            //         });
            //     }
            //     if ($rating === 'Lowest') {
            //         $q->sortByDesc(function ($service) {
            //             return $service->avg_rate;
            //         });
            //     }
            // })
            ->offset($more)
            ->limit(15)
            ->get();
        // ->values();

        // if ($rating === 'Highest') {
        //     $services->sortBy(function ($service) {
        //         return $service->avg_rate;
        //     });
        // }
        // if ($rating === 'Lowest') {
        //     $services->sortByDesc(function ($service) {
        //         return $service->avg_rate;
        //     });
        // }

        return response()->json(['services' => $services], 200);
    }
}
