<?php

namespace App\Http\Controllers;

use App\Models\AffiliateProfile;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /** Flat shipping cost options. */
    private array $couriers = [
        'jne_reg'  => ['label' => 'JNE Reguler (2–3 hari)',  'cost' => 15_000],
        'jne_yes'  => ['label' => 'JNE YES (1 hari)',         'cost' => 25_000],
        'jnt_reg'  => ['label' => 'J&T Reguler (2–3 hari)',  'cost' => 13_000],
        'sicepat'  => ['label' => 'SiCepat Halu (1–2 hari)', 'cost' => 14_000],
        'pos_biasa'=> ['label' => 'Pos Indonesia Biasa',      'cost' => 10_000],
    ];

    public function __construct(protected OrderService $orderService) {}

    /** GET /checkout */
    public function showForm(Request $request): View|RedirectResponse
    {
        $productId = $request->query('product_id');

        if (! $productId) {
            return redirect()->route('shop')->with('info', 'Pilih produk terlebih dahulu.');
        }

        $product   = Product::active()->findOrFail($productId);
        $user      = $request->user();
        $qty       = max(1, (int) $request->query('qty', 1));
        $affiliate = null;

        // Load affiliate from referral cookie
        $refCode = Cookie::get('affiliate_ref');
        if ($refCode) {
            $affiliate = AffiliateProfile::with('user')
                ->where('referral_code', $refCode)
                ->where('status', 'active')
                ->first();
        }

        return view('checkout.index', [
            'product'  => $product,
            'user'     => $user,
            'qty'      => $qty,
            'couriers' => $this->couriers,
            'affiliate'=> $affiliate,
        ]);
    }

    /** POST /checkout */
    public function process(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'product_id'       => 'required|integer|exists:products,id',
            'qty'              => 'required|integer|min:1',
            'shipping_courier' => 'required|string|in:' . implode(',', array_keys($this->couriers)),
            'shipping_address' => 'required|string',
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:30',
            'shipping_city'    => 'required|string|max:100',
            'shipping_province'=> 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:10',
            'notes'            => 'nullable|string|max:1000',
        ]);

        // Combine shipping address fields into a single string
        $combinedAddress = implode(', ', array_filter([
            $data['customer_name'],
            $data['customer_phone'],
            $data['shipping_address'],
            $data['shipping_city'],
            $data['shipping_province'],
            $data['shipping_postal_code'],
        ]));

        $orderData = [
            'product_id'       => $data['product_id'],
            'quantity'         => $data['qty'],
            'shipping_courier' => $data['shipping_courier'],
            'shipping_address' => $combinedAddress,
            'notes'            => $data['notes'] ?? null,
            'payment_method'   => 'midtrans',
            'affiliate_code'   => Cookie::get('affiliate_ref'),
        ];

        try {
            $order = $this->orderService->createOrder($orderData, $user->id);
        } catch (\Throwable $e) {
            return back()->withErrors(['general' => 'Gagal membuat pesanan: ' . $e->getMessage()]);
        }

        // Clear affiliate cookie after order
        Cookie::expire('affiliate_ref');

        return redirect()->away($order->midtrans_snap_token);
    }

    /** GET /checkout/success */
    public function success(Request $request): View
    {
        $order = null;

        // support both ?order_number=TDR-xxx (internal) and ?order_id=TDR-xxx (Midtrans redirect)
        $orderNumber = $request->query('order_number') ?? $request->query('order_id');

        if ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)
                ->where('customer_id', $request->user()->id)
                ->first();
        }

        // Auto-verify: if order still unpaid, check Midtrans API directly
        if ($order && ! $order->payment_verified_at) {
            $status = $this->orderService->checkAndVerifyPayment($order);
            if ($status) {
                $order->refresh();
            }
        }

        return view('checkout.success', compact('order'));
    }

    /** GET /checkout/failed */
    public function failed(Request $request): View
    {
        return view('checkout.failed');
    }
}
