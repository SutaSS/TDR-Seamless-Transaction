<?php

namespace App\Http\Controllers;

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

        // Resolve affiliate from ?affiliate_code=CODE or ?ref=CODE
        $affCode = $request->query('affiliate_code') ?? $request->query('ref');
        $affiliate = null;

        if ($affCode) {
            $affiliate = AffiliateProfile::with('user')
                ->where('referral_code', $affCode)
                ->where('status', 'active')
                ->first();
            // Store in cookie for existing referral flow
            if ($affiliate) {
                cookie()->queue('affiliate_ref', $affCode, 60 * 24 * 30);
            }
        }

        // Share URL for this product
        $shareUrl = url('/products/' . $product->slug);

        return view('products.show', compact('product', 'affiliate', 'affCode', 'shareUrl'));
    }
}
