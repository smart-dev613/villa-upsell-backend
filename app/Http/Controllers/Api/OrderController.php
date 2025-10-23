<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['property', 'upsell', 'vendor'])
            ->whereHas('property', function($q) {
                $q->where('user_id', auth()->id());
            });

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by property
        if ($request->has('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($orders);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Verify the order belongs to the authenticated user's property
        if ($order->property->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'order' => $order->load(['property', 'upsell', 'vendor']),
        ]);
    }

    /**
     * Update the specified order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Verify the order belongs to the authenticated user's property
        if ($order->property->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,fulfilled,cancelled',
        ]);

        $order->update($validated);

        if ($validated['status'] === 'fulfilled') {
            $order->update(['fulfilled_at' => now()]);
        }

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order->load(['property', 'upsell', 'vendor']),
        ]);
    }

    /**
     * Get recent orders for dashboard.
     */
    public function recent()
    {
        $orders = Order::with(['property', 'upsell', 'vendor'])
            ->whereHas('property', function($q) {
                $q->where('user_id', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($orders);
    }

    /**
     * Get order statistics for dashboard.
     */
    public function stats()
    {
        $userId = auth()->id();
        
        $stats = [
            'total_orders' => Order::whereHas('property', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->count(),
            
            'pending_orders' => Order::whereHas('property', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('status', 'pending')->count(),
            
            'total_revenue' => Order::whereHas('property', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('status', '!=', 'cancelled')->sum('amount'),
            
            'monthly_revenue' => Order::whereHas('property', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('status', '!=', 'cancelled')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount'),
        ];

        return response()->json($stats);
    }
}