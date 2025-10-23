<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Upsell;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics.
     */
    public function stats()
    {
        $userId = auth()->id();
        
        $stats = [
            'total_properties' => Property::where('user_id', $userId)->count(),
            'total_upsells' => Upsell::whereHas('property', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('is_active', true)->count(),
            'total_orders' => Order::whereHas('property', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->count(),
            'total_revenue' => Order::whereHas('property', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('status', '!=', 'cancelled')->sum('amount'),
            'monthly_revenue' => Order::whereHas('property', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('status', '!=', 'cancelled')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount'),
            'conversion_rate' => $this->calculateConversionRate($userId),
            'active_vendors' => Vendor::where('is_active', true)->count(),
            'pending_orders' => Order::whereHas('property', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('status', 'pending')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get recent orders for dashboard.
     */
    public function recentOrders()
    {
        $orders = Order::with(['property', 'upsell', 'vendor'])
            ->whereHas('property', function($q) {
                $q->where('user_id', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json($orders);
    }

    /**
     * Get revenue analytics.
     */
    public function revenueAnalytics(Request $request)
    {
        $userId = auth()->id();
        $period = $request->get('period', '30'); // days
        
        $revenue = Order::whereHas('property', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', now()->subDays($period))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($revenue);
    }

    /**
     * Get upsell performance analytics.
     */
    public function upsellAnalytics()
    {
        $userId = auth()->id();
        
        $upsells = Upsell::withCount(['orders as total_orders'])
            ->withSum(['orders as total_revenue' => function($query) {
                $query->where('status', '!=', 'cancelled');
            }])
            ->whereHas('property', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('is_active', true)
            ->orderBy('total_revenue', 'desc')
            ->get();

        return response()->json($upsells);
    }

    /**
     * Calculate conversion rate.
     */
    private function calculateConversionRate($userId)
    {
        $totalVisitors = 1000; // This would come from analytics in a real app
        $totalOrders = Order::whereHas('property', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->count();

        return $totalVisitors > 0 ? round(($totalOrders / $totalVisitors) * 100, 2) : 0;
    }
}