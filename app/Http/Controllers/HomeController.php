<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * GET / — Landing page (brand hero, features, CTA)
     */
    public function index(Request $request): View
    {
        $featuredProducts = Product::active()->limit(6)->get();

        return view('welcome', compact('featuredProducts'));
    }

    /**
     * GET /shop — Full product catalog with search
     */
    public function shop(Request $request): View
    {
        $search   = $request->query('search', '');
        $products = Product::active()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%"))
            ->paginate(12);

        return view('shop', compact('products', 'search'));
    }

    /**
     * GET /my-orders — Customer purchase history
     */
    public function myOrders(Request $request): View
    {
        $orders = Order::where('customer_user_id', Auth::id())
            ->with(['items'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * GET /my-orders/{order} — Customer single order detail
     */
    public function myOrderDetail(Order $order): View
    {
        // Ensure the order belongs to the authenticated user
        abort_if($order->customer_user_id !== Auth::id(), 403, 'Bukan pesanan Anda.');

        $order->load(['items', 'payment']);

        return view('orders.show', compact('order'));
    }
}
