<?php

namespace App\Http\Controllers;

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
}
