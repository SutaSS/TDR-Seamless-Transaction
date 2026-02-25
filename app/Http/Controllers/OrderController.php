<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /** GET /orders — daftar pesanan milik user yang login */
    public function index(): View
    {
        $orders = Order::where('customer_id', auth()->id())
            ->with(['items', 'trackingLogs' => fn ($q) => $q->latest()])
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }
    
    public function track(Request $request, string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('customer_id', auth()->id())
            ->with([
                'items',
                'trackingLogs' => fn ($q) => $q->orderBy('created_at', 'asc'),
            ])
            ->firstOrFail();

        return view('orders.track', compact('order'));
    }
}
