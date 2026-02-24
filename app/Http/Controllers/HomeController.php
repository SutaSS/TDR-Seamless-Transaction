<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
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
}
