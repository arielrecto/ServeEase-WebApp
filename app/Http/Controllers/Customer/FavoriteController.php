<?php

namespace App\Http\Controllers\Customer;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Service;
use App\Models\ServiceUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $favorites = auth()->user()
            ->favorites()
            ->with(['user'])
            ->withCount(['availService as avail_service_count'])
            ->latest()
            ->paginate(20)
            ->through(function ($favorite) {
                return [
                    'id' => $favorite->id,
                    'user_id' => $favorite->user_id,
                    'user' => $favorite->user->name,
                    'name' => $favorite->name,
                    'price' => $favorite->price,
                    'price_type' => $favorite->price_type,
                    'thumbnail' => $favorite->thumbnail,
                    'avg_rate' => $favorite->avg_rate,
                    'avail_service_count' => $favorite->avail_service_count,
                ];
            });

        return Inertia::render("Users/Customer/Favorites/Index", compact(["favorites"]));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function add(Service $favorite)
    {
        auth()->user()->favorites()->attach($favorite->id);

        return back()->with(['message_success' => 'Added to favorites']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        // dd(auth()->user()->favorites);
        auth()->user()->favorites()->detach($service->id);
        // dd(auth()->user()->favorites);

        return back();
    }
}
