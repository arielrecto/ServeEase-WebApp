<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Service;
use App\Models\AvailService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user =  User::where('id', Auth::user()->id)->first();

        $stats = [
            'totalBookings' => AvailService::whereHas('service', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
            'pendingBookings' => AvailService::whereHas('service', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'pending')->count(),
            'completedBookings' => AvailService::whereHas('service', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'completed')->count(),
            'totalRevenue' => AvailService::whereHas('service', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'completed')->sum('amount'),
        ];

        $recentBookings = AvailService::with(['user', 'service'])->whereHas('service', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->latest()
            ->take(5)
            ->get();

        $services = Service::where('user_id', $user->id)->get();
        $availServicePending = AvailService::where('status', 'pending')
        ->whereHas('service', function($q) use($user) {
            $q->where('user_id', $user->id);
        })->get();
        $chartData = $this->getMonthlyRevenueChartData();

        return inertia('Users/ServiceProvider/Dashboard', [
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'chartData' => [
                'monthlyRevenue' => $chartData['monthlyRevenue'],
                'bookingStatus' => $this->getBookingStatusChartData()
            ],
            'services' => $services,
            'availServicePending' => $availServicePending
        ]);
    }

    // public function index()
    // {
    //     $user = Auth::user();

    //     $services = Service::where('user_id', $user->id)->get();

    //     $availServicePending = AvailService::where('status', 'pending')
    //     ->whereHas('service', function($q) use($user) {
    //         $q->where('user_id', $user->id);
    //     })->get();

    //     return Inertia::render('Users/ServiceProvider/Dashboard', compact(['services', 'availServicePending']));
    // }

    private function getMonthlyRevenueChartData()
    {
        $user = Auth::user();

        $monthlyRevenue = AvailService::whereHas('service', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('status', 'completed')
            ->selectRaw("
            strftime('%Y-%m', created_at) as month_start,
            SUM(total_price) as total_revenue
        ")
            ->groupBy('month_start')
            ->orderBy('month_start', 'asc')
            ->get();

        // Format months as "Jan 2024" and convert to numbers
        $labels = $monthlyRevenue->map(function ($item) {
            return Carbon::parse($item->month_start)->format('M Y');
        });

        $data = $monthlyRevenue->pluck('total_revenue')->map(function ($value) {
            return (float) $value;
        });

        return [
            'monthlyRevenue' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Monthly Revenue',
                        'data' => $data,
                        'backgroundColor' => '#4F46E5',
                        'borderColor' => '#4F46E5',
                        'borderWidth' => 1
                    ]
                ]
            ]
        ];
    }

    private function getBookingStatusChartData()
    {
        $user = Auth::user();

        $statusDistribution = AvailService::whereHas('service', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->selectRaw('status, count(*) as count')
        ->groupBy('status')
        ->get()
        ->mapWithKeys(function($item) {
            return [$item->status => $item->count];
        });

        // Default values for all possible statuses
        $labels = ['pending', 'completed', 'cancelled'];
        $data = collect($labels)->map(fn($status) => $statusDistribution[$status] ?? 0);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => [
                        '#F59E0B', // pending - amber
                        '#10B981', // completed - emerald
                        '#EF4444'  // cancelled - red
                    ],
                    'hoverOffset' => 4
                ]
            ]
        ];
    }
}
