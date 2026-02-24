<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateReferralClick;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\MidtransService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(protected MidtransService $midtrans) {}

    /**
     * GET /checkout
     * Tampilkan form checkout dengan daftar produk aktif.
     */
    public function showForm(Request $request): View
    {
        $products = Product::active()->get();

        // Affiliate dari cookie
        $affiliateCode = $request->cookie('affiliate_ref');
        $affiliate     = $affiliateCode
            ? Affiliate::where('referral_code', $affiliateCode)->first()
            : null;

        $user = Auth::user();
        return view('checkout.index', compact('products', 'affiliate', 'user'));
    }

    /**
     * POST /checkout
     * Buat pending order lalu redirect ke Midtrans Snap.
     */
    public function process(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
        ]);

        try {
            $redirectUrl = DB::transaction(function () use ($validated, $request) {
                // Resolve affiliate dari cookie
                $affiliateCode = $request->cookie('affiliate_ref');
                $affiliate     = $affiliateCode
                    ? Affiliate::where('referral_code', $affiliateCode)->first()
                    : null;

                $referralClick = null;
                if ($affiliate) {
                    $referralClick = AffiliateReferralClick::where('affiliate_id', $affiliate->id)
                        ->where('is_attributed', false)
                        ->latest()
                        ->first();
                }

                // Hitung total
                $orderItems = [];
                $subtotal   = 0;

                foreach ($validated['items'] as $item) {
                    $product  = Product::findOrFail($item['product_id']);
                    $qty      = (int) $item['qty'];
                    $price    = (float) $product->price;
                    $lineTotal = $price * $qty;
                    $subtotal += $lineTotal;

                    $orderItems[] = [
                        'product'   => $product,
                        'qty'       => $qty,
                        'price'     => $price,
                        'lineTotal' => $lineTotal,
                    ];
                }

                $orderNumber = 'ORD-' . strtoupper(Str::random(10)) . '-' . time();

                // Buat order (pending, akan di-update webhook ke paid)
                $order = Order::create([
                    'order_number'     => $orderNumber,
                    'customer_user_id' => Auth::id(),
                    'affiliate_id'     => $affiliate?->id,
                    'referral_click_id'=> $referralClick?->id,
                    'subtotal_amount'  => $subtotal,
                    'discount_amount'  => 0,
                    'total_amount'     => $subtotal,
                    'currency'         => 'IDR',
                    'order_status'     => 'pending',
                    'payment_status'   => 'unpaid',
                    'customer_name'    => $validated['customer_name'],
                    'customer_phone'   => $validated['customer_phone'] ?? null,
                ]);

                // Simpan order items
                foreach ($orderItems as $oi) {
                    OrderItem::create([
                        'order_id'              => $order->id,
                        'product_id'            => $oi['product']->id,
                        'product_name_snapshot' => $oi['product']->name,
                        'qty'                   => $oi['qty'],
                        'unit_price'            => $oi['price'],
                        'line_total'            => $oi['lineTotal'],
                    ]);
                }

                // Buat Midtrans Snap token
                $midtransItems = array_map(fn ($oi) => [
                    'id'       => (string) $oi['product']->id,
                    'price'    => (int) $oi['price'],
                    'quantity' => $oi['qty'],
                    'name'     => $oi['product']->name,
                ], $orderItems);

                $params = [
                    'transaction_details' => [
                        'order_id'     => $orderNumber,
                        'gross_amount' => (int) $subtotal,
                    ],
                    'customer_details' => [
                        'first_name' => $validated['customer_name'],
                        'email'      => $validated['customer_email'],
                        'phone'      => $validated['customer_phone'] ?? '',
                    ],
                    'item_details' => $midtransItems,
                ];

                return $this->midtrans->createSnapToken($params);
            });

            return redirect($redirectUrl);
        } catch (\Throwable $e) {
            Log::error('[CheckoutController] Failed to create order', ['error' => $e->getMessage()]);
            return back()->withErrors(['general' => 'Gagal membuat pesanan: ' . $e->getMessage()]);
        }
    }

    /**
     * GET /checkout/success
     */
    public function success(Request $request): View
    {
        $orderId = $request->query('order_id');
        $order   = $orderId ? Order::where('order_number', $orderId)->with('items')->first() : null;
        return view('checkout.success', compact('order'));
    }

    /**
     * GET /checkout/failed
     */
    public function failed(): View
    {
        return view('checkout.failed');
    }
}
