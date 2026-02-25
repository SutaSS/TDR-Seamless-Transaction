<?php

namespace App\Http\Controllers;

use App\Models\AffiliateClick;
use App\Models\AffiliateProfile;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /** GET / */
    public function index(Request $request): View
    {
        $featuredProducts = Product::active()->latest()->take(6)->get();

        return view('welcome', compact('featuredProducts'));
    }

    /** GET /shop */
    public function shop(Request $request): View
    {
        $search = $request->input('search', '');

        $products = Product::active()
            ->when($request->category, fn ($q, $v) => $q->where('category', $v))
            ->when($request->brand,    fn ($q, $v) => $q->where('brand', 'LIKE', "%{$v}%"))
            ->when($search,            fn ($q, $v) => $q->where('name', 'LIKE', "%{$v}%"))
            ->orderBy(
                in_array($request->sort, ['price', 'name', 'created_at']) ? $request->sort : 'created_at',
                $request->order === 'asc' ? 'asc' : 'desc'
            )
            ->paginate(12);

        return view('shop', compact('products', 'search'));
    }

    /** GET /products/{slug} — detail produk & share link */
    public function showProduct(Request $request, string $slug): View
    {
        $product = Product::active()->where('slug', $slug)->firstOrFail();

        $affCode = $request->query('affiliate_code') ?? $request->query('ref');
        $affiliate = null;

        if ($affCode) {
            $affiliate = AffiliateProfile::with('user')
                ->where('referral_code', $affCode)
                ->where('status', 'active')
                ->first();
            if (! $affiliate) $affCode = null;
        }

        // Track click once per session per affiliate+product
        if ($affiliate) {
            $sessionKey = 'aff_click_' . $affiliate->user_id . '_' . $product->id;
            if (! session()->has($sessionKey)) {
                AffiliateClick::create([
                    'affiliate_id' => $affiliate->user_id,
                    'ip_address'   => $request->ip(),
                    'user_agent'   => substr($request->userAgent() ?? '', 0, 500),
                    'referrer_url' => substr($request->headers->get('referer', ''), 0, 500),
                    'clicked_at'   => now(),
                ]);
                session([$sessionKey => true]);
            }
        }

        $shareUrl = url('/products/' . $product->slug);

        return view('products.show', compact('product', 'affiliate', 'affCode', 'shareUrl'));
    }
}
