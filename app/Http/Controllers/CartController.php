<?php

namespace App\Http\Controllers;

use App\Models\AffiliateProfile;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class CartController extends Controller
{
    /** GET /cart */
    public function index(Request $request): View
    {
        $cart = $this->getCart($request);

        return view('cart.index', compact('cart'));
    }

    /** POST /cart/add */
    public function add(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'product_id'     => 'required|integer|exists:products,id',
            'quantity'       => 'nullable|integer|min:1|max:999',
            'affiliate_code' => 'nullable|string|max:20',
        ]);

        $product = Product::active()->findOrFail($data['product_id']);
        $qty     = (int) ($data['quantity'] ?? 1);

        // Resolve affiliate_code: request param > ?ref cookie > nothing
        $affCode = $data['affiliate_code']
            ?? $request->query('affiliate_code')
            ?? Cookie::get('affiliate_ref')
            ?? null;

        // Validate that the affiliate code exists and is active
        if ($affCode) {
            $valid = AffiliateProfile::where('referral_code', $affCode)
                ->where('status', 'active')
                ->exists();
            if (! $valid) {
                $affCode = null;
            }
        }

        $cart = session('cart', []);
        $key  = (string) $product->id;

        if (isset($cart[$key])) {
            // Increment qty if item already in cart
            $cart[$key]['quantity'] = min(
                $cart[$key]['quantity'] + $qty,
                $product->stock ?? 999
            );
            // Update affiliate code if a new one is provided
            if ($affCode) {
                $cart[$key]['affiliate_code'] = $affCode;
            }
        } else {
            $cart[$key] = [
                'product_id'     => $product->id,
                'product_name'   => $product->name,
                'product_price'  => (float) $product->price,
                'product_slug'   => $product->slug,
                'thumbnail_url'  => $product->thumbnail_url,
                'stock'          => $product->stock,
                'quantity'       => $qty,
                'affiliate_code' => $affCode,
            ];
        }

        session(['cart' => $cart]);

        if ($request->wantsJson()) {
            return response()->json([
                'message'    => 'Produk ditambahkan ke keranjang.',
                'cart_count' => count($cart),
            ]);
        }

        return back()->with('cart_success', "\"{$product->name}\" berhasil ditambahkan ke keranjang.");
    }

    /** PATCH /cart/{productId} */
    public function update(Request $request, int $productId): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1|max:999',
        ]);

        $cart = session('cart', []);
        $key  = (string) $productId;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $data['quantity'];
            session(['cart' => $cart]);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Keranjang diperbarui.', 'cart_count' => count($cart)]);
        }

        return back();
    }

    /** DELETE /cart/{productId} */
    public function remove(Request $request, int $productId): RedirectResponse|JsonResponse
    {
        $cart = session('cart', []);
        unset($cart[(string) $productId]);
        session(['cart' => $cart]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Item dihapus.', 'cart_count' => count($cart)]);
        }

        return back()->with('cart_success', 'Item dihapus dari keranjang.');
    }

    /** DELETE /cart */
    public function clear(Request $request): RedirectResponse
    {
        session()->forget('cart');

        return redirect()->route('shop')->with('info', 'Keranjang dikosongkan.');
    }

    // Helpers

    public static function getCart(Request $request): array
    {
        return session('cart', []);
    }

    public static function cartCount(): int
    {
        return count(session('cart', []));
    }

    public static function cartTotal(): float
    {
        return collect(session('cart', []))
            ->sum(fn($i) => $i['product_price'] * $i['quantity']);
    }
}
