<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\AvailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function dashboard()
    {

        return Inertia::render('Users/Admin/Dashboard', [
            'stats' => $this->getDashboardStats(),
            'chartData' => $this->getChartData()
        ]);
    }

    protected function getDashboardStats()
    {
        return [
            'totalServices' => AvailService::count(),
            'activeUsers' => User::count(),
            'revenue' => AvailService::sum('total_price'),
            'ongoingServices' => AvailService::where('status', 'in_progress')->count()
        ];
    }

    protected function getChartData()
    {
        return [
            'monthlyServices' => $this->getMonthlyAvailServices(),
            'serviceStatusDistribution' => $this->getServiceStatusDistribution()
        ];
    }

    protected function getMonthlyAvailServices()
    {
        $oneYearAgo = Carbon::now()->subYear()->startOfDay();

        $services = AvailService::where('created_at', '>=', '2024-02-09 00:00:00')
            ->select(DB::raw('strftime("%Y", created_at) as year, strftime("%m", created_at) as month, COUNT(*) as count'))
            ->groupBy(DB::raw('strftime("%Y", created_at), strftime("%m", created_at)'))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $monthlyData = array_fill(0, 12, 0);

        foreach ($services as $service) {
            $monthIndex = (int) $service->month - 1; // Cast to int for correct indexing
            $monthlyData[$monthIndex] = $service->count;
        }

        return [
            'id' => 'monthly-services',
            'labels' => [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ],
            'datasets' => [
                [
                    'label' => 'Monthly Services',
                    'data' => $monthlyData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    protected function getServiceStatusDistribution()
    {
        $statuses = AvailService::select('status', \DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $labels = [];
        $data = [];
        $colors = [
            '#EF4444',
            '#10B981',
            '#FACC15',
            '#F97316',
            '#3B82F6'
        ];

        foreach ($statuses as $index => $status) {
            $labels[] = ucfirst(str_replace('_', ' ', $status->status));
            $data[] = $status->count;
        }

        return [
            'id' => 'service-status',
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($labels)),
                    'hoverOffset' => 4
                ]
            ]
        ];
    }
}
