<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /** GET /orders/{orderNumber} — halaman tracking pesanan */
    public function track(Request $request, string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('customer_id', auth()->id())
            ->with([
                'items.product',
                'trackingLogs' => fn ($q) => $q->orderBy('created_at', 'asc'),
            ])
            ->firstOrFail();

        return view('orders.track', compact('order'));
    }
}
