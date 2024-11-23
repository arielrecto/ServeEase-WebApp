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
            ->where('service_type', $request->service)
            ->where('barangay_id', $request->brgy)
            ->when($price, function ($q) use ($price) {
                if ($price === 'High') {
                    $q->orderBy('price', 'DESC');
                } else if ($price === 'Low') {
                    $q->orderBy('price', 'ASC');
                }
            })
            // ->when($rating, function ($q) use ($rating) {
            //     if ($rating === 'Highest') {

            //     } else if ($rating === 'Lowest') {

            //     }
            // })
            // ->when($transactions, function ($q) use ($transactions) {
            //     if ($transactions === 'High') {

            //     } else if ($transactions === 'Low') {

            //     }
            // })
            ->offset($more)
            ->limit(15)
            ->get();

        return response()->json(['services' => $services], 200);
    }
}
