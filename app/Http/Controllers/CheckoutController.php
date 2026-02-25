<?php

namespace App\Http\Controllers;

use App\Models\AffiliateProfile;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /** Flat shipping cost options. */
    public array $couriers = [
        'jne_reg'   => ['label' => 'JNE Reguler (2–3 hari)',  'cost' => 15_000],
        'jne_yes'   => ['label' => 'JNE YES (1 hari)',         'cost' => 25_000],
        'jnt_reg'   => ['label' => 'J&T Reguler (2–3 hari)',  'cost' => 13_000],
        'sicepat'   => ['label' => 'SiCepat Halu (1–2 hari)', 'cost' => 14_000],
        'pos_biasa' => ['label' => 'Pos Indonesia Biasa',      'cost' => 10_000],
    ];

    public function __construct(protected OrderService $orderService) {}

    /** GET /checkout — reads from session cart */
    public function showForm(Request $request): View|RedirectResponse
    {
        $cart = session('cart', []);

        // Support legacy single-product URL: ?product_id=X&qty=1
        if (empty($cart) && $request->query('product_id')) {
            $product = Product::active()->find($request->query('product_id'));
            if ($product) {
                // affiliate code from URL param only
                $affCode = $request->query('affiliate_code') ?? $request->query('ref');

                if ($affCode) {
                    $valid = AffiliateProfile::where('referral_code', $affCode)
                        ->where('status', 'active')->exists();
                    if (! $valid) $affCode = null;
                }

                $cart[(string) $product->id] = [
                    'product_id'     => $product->id,
                    'product_name'   => $product->name,
                    'product_price'  => (float) $product->price,
                    'product_slug'   => $product->slug,
                    'thumbnail_url'  => $product->thumbnail_url,
                    'stock'          => $product->stock,
                    'quantity'       => max(1, (int) $request->query('qty', 1)),
                    'affiliate_code' => $affCode,
                ];

                session(['cart' => $cart]);
            }
        }

        if (empty($cart)) {
            return redirect()->route('shop')->with('info', 'Tambahkan produk ke keranjang terlebih dahulu.');
        }

        $affiliates = [];
        foreach ($cart as $key => $item) {
            if (! empty($item['affiliate_code'])) {
                $affiliates[$key] = AffiliateProfile::with('user')
                    ->where('referral_code', $item['affiliate_code'])
                    ->where('status', 'active')
                    ->first();
            }
        }

        $user = $request->user();

        return view('checkout.index', [
            'cart'      => $cart,
            'affiliates'=> $affiliates,
            'user'      => $user,
            'couriers'  => $this->couriers,
        ]);
    }

    /** POST /checkout */
    public function process(Request $request): RedirectResponse
    {
        $user = $request->user();
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop')->with('info', 'Keranjang belanja kosong.');
        }

        $data = $request->validate([
            'shipping_courier'     => 'required|string|in:' . implode(',', array_keys($this->couriers)),
            'shipping_address'     => 'required|string',
            'customer_name'        => 'required|string|max:255',
            'customer_phone'       => 'required|string|max:30',
            'customer_email'       => 'nullable|email|max:255',
            'shipping_city'        => 'required|string|max:100',
            'shipping_province'    => 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:10',
            'notes'                => 'nullable|string|max:1000',
        ]);

        $combinedAddress = implode(', ', array_filter([
            $data['customer_name'],
            $data['customer_phone'],
            $data['shipping_address'],
            $data['shipping_city'],
            $data['shipping_province'],
            $data['shipping_postal_code'],
        ]));

        $courierInfo  = $this->couriers[$data['shipping_courier']];
        $shippingCost = $courierInfo['cost'];

        // Build items array from cart
        $items = [];
        foreach ($cart as $item) {
            $items[] = [
                'product_id'     => $item['product_id'],
                'quantity'       => $item['quantity'],
                'affiliate_code' => $item['affiliate_code'] ?? null,
            ];
        }

        $orderData = [
            'items'            => $items,
            'shipping_courier' => $data['shipping_courier'],
            'shipping_cost'    => $shippingCost,
            'shipping_address' => $combinedAddress,
            'notes'            => $data['notes'] ?? null,
            'payment_method'   => 'midtrans',
        ];

        try {
            $order = $this->orderService->createOrder($orderData, $user->id);
        } catch (\Throwable $e) {
            return back()->withErrors(['general' => 'Gagal membuat pesanan: ' . $e->getMessage()]);
        }

        // cart cleared only after Midtrans confirms payment
        return redirect()->away($order->midtrans_snap_token);
    }

    /** GET /checkout/success */
    public function success(Request $request): View|RedirectResponse
    {
        $orderNumber = $request->query('order_number') ?? $request->query('order_id');

        if (! $orderNumber) {
            return redirect()->route('orders.index');
        }

        $order = Order::where('order_number', $orderNumber)
            ->where('customer_id', $request->user()->id)
            ->with('items')
            ->first();

        if (! $order) {
            return redirect()->route('orders.index');
        }

        // Auto-verify: check Midtrans API if not yet confirmed
        if (! $order->payment_verified_at) {
            $this->orderService->checkAndVerifyPayment($order);
            $order->refresh();
        }

        // If STILL not verified (pending / user left without paying) → back to checkout
        if (! $order->payment_verified_at) {
            return redirect()->route('checkout.form')
                ->with('payment_pending', [
                    'order_number' => $order->order_number,
                    'snap_url'     => $order->midtrans_snap_token,
                ]);
        }

        // Payment confirmed — clear cart
        session()->forget('cart');

        return view('checkout.success', compact('order'));
    }

    /** GET /checkout/failed */
    public function failed(Request $request): View
    {
        return view('checkout.failed');
    }
}

