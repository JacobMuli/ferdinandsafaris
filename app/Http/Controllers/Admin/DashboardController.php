<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '30'); // days
        $startDate = Carbon::now()->subDays($period);

        // Key Metrics
        $metrics = [
            'total_revenue' => $this->getTotalRevenue($startDate),
            'total_bookings' => $this->getTotalBookings($startDate),
            'pending_bookings' => Booking::pending()->count(),
            'active_tours' => Tour::active()->count(),
            'total_customers' => Customer::count(),
            'revenue_growth' => $this->getRevenueGrowth($period),
            'booking_growth' => $this->getBookingGrowth($period),
            'conversion_rate' => $this->getConversionRate($period),
            'new_bookings_count' => Booking::where('created_at', '>=', $startDate)->count(),
        ];

        // Revenue by Customer Type
        $revenueByCustomerType = $this->getRevenueByCustomerType($startDate);

        // Booking Status Distribution
        $bookingsByStatus = $this->getBookingsByStatus($startDate);

        // Daily Revenue Chart Data
        $dailyRevenue = $this->getDailyRevenue($startDate);

        // Top Performing Tours
        $topTours = $this->getTopTours($startDate, 10);

        // Recent Bookings
        $recentBookings = Booking::with(['tour', 'customer'])
            ->latest()
            ->limit(20)
            ->get();

        // Monthly Trends
        $monthlyTrends = $this->getMonthlyTrends();

        // Customer Type Distribution
        $customerTypeDistribution = $this->getCustomerTypeDistribution($startDate);

        return view('admin.dashboard', compact(
            'metrics',
            'revenueByCustomerType',
            'bookingsByStatus',
            'dailyRevenue',
            'topTours',
            'recentBookings',
            'monthlyTrends',
            'customerTypeDistribution',
            'period'
        ));
    }

    private function getTotalRevenue($startDate)
    {
        return Payment::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->sum('amount');
    }

    private function getTotalBookings($startDate)
    {
        return Booking::where('created_at', '>=', $startDate)->count();
    }

    private function getRevenueGrowth($days)
    {
        $currentPeriod = Payment::where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->sum('amount');

        $previousPeriod = Payment::where('status', 'completed')
            ->whereBetween('created_at', [
                Carbon::now()->subDays($days * 2),
                Carbon::now()->subDays($days)
            ])
            ->sum('amount');

        if ($previousPeriod == 0) {
            return $currentPeriod > 0 ? 100 : 0;
        }

        return (($currentPeriod - $previousPeriod) / $previousPeriod) * 100;
    }

    private function getBookingGrowth($days)
    {
        $currentPeriod = Booking::where('created_at', '>=', Carbon::now()->subDays($days))->count();
        $previousPeriod = Booking::whereBetween('created_at', [
            Carbon::now()->subDays($days * 2),
            Carbon::now()->subDays($days)
        ])->count();

        if ($previousPeriod == 0) {
            return $currentPeriod > 0 ? 100 : 0;
        }

        return (($currentPeriod - $previousPeriod) / $previousPeriod) * 100;
    }

    private function getConversionRate($days)
    {
        $totalViews = Tour::where('updated_at', '>=', Carbon::now()->subDays($days))
            ->sum('views');

        $totalBookings = Booking::where('created_at', '>=', Carbon::now()->subDays($days))
            ->whereIn('status', ['confirmed', 'paid', 'completed'])
            ->count();

        if ($totalViews == 0) return 0;

        return ($totalBookings / $totalViews) * 100;
    }

    private function getRevenueByCustomerType($startDate)
    {
        return Booking::select('customer_type', DB::raw('SUM(total_amount) as revenue'))
            ->where('created_at', '>=', $startDate)
            ->whereIn('payment_status', ['paid'])
            ->groupBy('customer_type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->customer_type => $item->revenue];
            });
    }

    private function getBookingsByStatus($startDate)
    {
        return Booking::select('status', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            });
    }

    private function getDailyRevenue($startDate)
    {
        return Payment::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as revenue')
            )
            ->where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getTopTours($startDate, $limit = 5)
    {
        return Tour::select('tours.*', DB::raw('COUNT(bookings.id) as bookings_count'), DB::raw('SUM(bookings.total_amount) as revenue'))
            ->leftJoin('bookings', 'tours.id', '=', 'bookings.tour_id')
            ->where('bookings.created_at', '>=', $startDate)
            ->whereIn('bookings.payment_status', ['paid'])
            ->groupBy('tours.id')
            ->orderBy('revenue', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getMonthlyTrends()
    {
        return Booking::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as bookings'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getCustomerTypeDistribution($startDate)
    {
        return Booking::select('customer_type', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('customer_type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->customer_type => $item->count];
            });
    }

    public function analytics(Request $request, \App\Services\AiAnalyticsService $aiService)
    {
        $period = $request->get('period', '30');
        $startDate = Carbon::now()->subDays($period);

        // Gather comprehensive data for "Deep Dive"
        $data = [
            'metrics' => [
                'total_revenue' => $this->getTotalRevenue($startDate),
                'total_bookings' => $this->getTotalBookings($startDate),
                'revenue_growth' => $this->getRevenueGrowth($period),
                'conversion_rate' => $this->getConversionRate($period),
                'avg_booking_value' => $this->getAverageBookingValue($startDate),
            ],
            'top_tours' => $this->getTopTours($startDate, 5),
            'monthlyTrends' => $this->getMonthlyTrends(),
            'customer_demographics' => $this->getCustomerTypeDistribution($startDate),
            'revenue_by_tour' => $this->getRevenueByTour($startDate),
        ];

        // Get AI Insights
        $aiInsights = $aiService->generateInsights($data);

        return view('admin.analytics', compact('data', 'aiInsights', 'period'));
    }

    // ... Helper getters ...

    private function getAverageBookingValue($startDate)
    {
        return Booking::where('created_at', '>=', $startDate)
            ->whereIn('payment_status', ['paid'])
            ->avg('total_amount') ?? 0;
    }

    private function getRevenueByTour($startDate)
    {
        return Booking::select('tours.name', DB::raw('SUM(bookings.total_amount) as revenue'))
            ->join('tours', 'bookings.tour_id', '=', 'tours.id')
            ->where('bookings.created_at', '>=', $startDate)
            ->whereIn('bookings.payment_status', ['paid'])
            ->groupBy('tours.id', 'tours.name')
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get();
    }
}