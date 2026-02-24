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
    // Daftar kurir + ongkos kirim tetap (bisa diganti dengan API RajaOngkir kelak)
    public const COURIERS = [
        'jne_reg'      => ['label' => 'JNE REG (2-3 hari)',     'cost' => 15000],
        'jne_yes'      => ['label' => 'JNE YES (1-2 hari)',     'cost' => 25000],
        'sicepat_reg'  => ['label' => 'SiCepat REG (2-3 hari)', 'cost' => 12000],
        'sicepat_halu' => ['label' => 'SiCepat HALU (1-2 hari)','cost' => 18000],
        'tiki_reg'     => ['label' => 'TIKI REG (3-5 hari)',    'cost' => 14000],
        'pickup'       => ['label' => 'Ambil Sendiri',           'cost' => 0],
    ];

    public function __construct(protected MidtransService $midtrans) {}

    /**
     * GET /checkout?product_id=X&qty=1
     * Tampilkan halaman checkout bergaya Shopee.
     */
    public function showForm(Request $request): View|RedirectResponse
    {
        $productId = $request->query('product_id');
        $qty       = max(1, (int) $request->query('qty', 1));

        if (! $productId) {
            return redirect()->route('home')
                ->with('info', 'Pilih produk terlebih dahulu.');
        }

        $product = Product::active()->findOrFail($productId);

        $affiliateCode = $request->cookie('affiliate_ref');
        $affiliate     = $affiliateCode
            ? Affiliate::where('referral_code', $affiliateCode)->first()
            : null;

        $user    = Auth::user();
        $couriers = self::COURIERS;

        return view('checkout.index', compact('product', 'qty', 'affiliate', 'user', 'couriers'));
    }

    /**
     * POST /checkout
     */
    public function process(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id'        => 'required|exists:products,id',
            'qty'               => 'required|integer|min:1|max:100',
            'customer_name'     => 'required|string|max:255',
            'customer_email'    => 'required|email|max:255',
            'customer_phone'    => 'required|string|max:30',
            'shipping_address'  => 'required|string|max:500',
            'shipping_city'     => 'required|string|max:100',
            'shipping_province' => 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:10',
            'shipping_courier'  => 'required|in:' . implode(',', array_keys(self::COURIERS)),
            'note'              => 'nullable|string|max:500',
        ]);

        try {
            $redirectUrl = DB::transaction(function () use ($validated, $request) {
                $product      = Product::findOrFail($validated['product_id']);
                $qty          = (int) $validated['qty'];
                $unitPrice    = (int) round((float) $product->price);
                $subtotal     = $unitPrice * $qty;
                $courierData  = self::COURIERS[$validated['shipping_courier']];
                $shippingCost = (int) $courierData['cost'];
                $total        = $subtotal + $shippingCost;

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

                $orderNumber = 'ORD-' . strtoupper(Str::random(10)) . '-' . time();

                $order = Order::create([
                    'order_number'        => $orderNumber,
                    'customer_user_id'    => Auth::id(),
                    'affiliate_id'        => $affiliate?->id,
                    'referral_click_id'   => $referralClick?->id,
                    'subtotal_amount'     => $subtotal,
                    'discount_amount'     => 0,
                    'total_amount'        => $total,
                    'currency'            => 'IDR',
                    'order_status'        => 'pending',
                    'payment_status'      => 'unpaid',
                    'customer_name'       => $validated['customer_name'],
                    'customer_phone'      => $validated['customer_phone'],
                    'shipping_address'    => $validated['shipping_address'],
                    'shipping_city'       => $validated['shipping_city'],
                    'shipping_province'   => $validated['shipping_province'],
                    'shipping_postal_code'=> $validated['shipping_postal_code'],
                    'shipping_courier'    => $validated['shipping_courier'],
                    'shipping_cost'       => $shippingCost,
                    'shipping_provider'   => $courierData['label'],
                    'note'                => $validated['note'] ?? null,
                ]);

                OrderItem::create([
                    'order_id'              => $order->id,
                    'product_id'            => $product->id,
                    'product_name_snapshot' => $product->name,
                    'qty'                   => $qty,
                    'unit_price'            => $unitPrice,
                    'line_total'            => $subtotal,
                ]);

                // Midtrans: nama produk max 50 karakter, semua amount harus int bulat
                $itemDetails = [
                    [
                        'id'       => (string) $product->id,
                        'price'    => $unitPrice,
                        'quantity' => $qty,
                        'name'     => substr($product->name, 0, 50),
                    ],
                ];

                if ($shippingCost > 0) {
                    $itemDetails[] = [
                        'id'       => 'SHIPPING',
                        'price'    => $shippingCost,
                        'quantity' => 1,
                        'name'     => 'Ongkos Kirim',
                    ];
                }

                $params = [
                    'transaction_details' => [
                        'order_id'     => $orderNumber,
                        'gross_amount' => $total,
                    ],
                    'callbacks' => [
                        'finish'  => route('checkout.success') . '?order_id=' . $orderNumber,
                        'error'   => route('checkout.failed'),
                        'pending' => route('checkout.success') . '?order_id=' . $orderNumber,
                    ],
                    'customer_details' => [
                        'first_name' => substr($validated['customer_name'], 0, 255),
                        'email'      => $validated['customer_email'],
                        'phone'      => $validated['customer_phone'],
                        'billing_address' => [
                            'address'   => $validated['shipping_address'],
                            'city'      => $validated['shipping_city'],
                            'postal_code' => $validated['shipping_postal_code'],
                            'country_code' => 'IDN',
                        ],
                        'shipping_address' => [
                            'address'   => $validated['shipping_address'],
                            'city'      => $validated['shipping_city'],
                            'postal_code' => $validated['shipping_postal_code'],
                            'country_code' => 'IDN',
                        ],
                    ],
                    'item_details' => $itemDetails,
                ];

                return $this->midtrans->createSnapToken($params);
            });

            return redirect($redirectUrl);

        } catch (\Throwable $e) {
            Log::error('[CheckoutController] Failed to create order', ['error' => $e->getMessage()]);
            return back()->withErrors(['general' => 'Gagal membuat pesanan: ' . $e->getMessage()])->withInput();
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
