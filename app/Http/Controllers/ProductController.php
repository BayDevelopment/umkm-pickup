<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductModel::query()
            ->where('is_active', true)
            ->whereHas('variants') // 🔥 hanya produk yg punya variant
            ->with(['variants', 'category'])
            ->withMin('variants as lowest_price', 'price');

        // 🔍 SEARCH
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 💰 FILTER PRICE
        if ($request->filled('min_price')) {
            $query->where('lowest_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('lowest_price', '<=', $request->max_price);
        }

        // 🔄 SORT
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('lowest_price', 'asc');
                break;

            case 'price_desc':
                $query->orderBy('lowest_price', 'desc');
                break;

            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        return view('pages.products', [
            'title'    => 'Produk Kami | Trendora',
            'navlink'  => 'produk',
            'products' => $products,
        ]);
    }


    public function productsCustomer(Request $request)
    {
        $baseQuery = ProductModel::query()
            ->where('is_active', true)

            // hanya produk yg punya variant dengan stock > 0
            ->whereHas('variants', function ($q) {
                $q->where('stock', '>', 0);
            })

            // ambil harga termurah dari variant
            ->withMin(['variants' => function ($q) {
                $q->where('stock', '>', 0);
            }], 'price')

            ->with([
                'category',
                'variants' => function ($q) {
                    $q->where('stock', '>', 0)
                        ->with('branch');
                }
            ])

            // 🔍 SEARCH
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })

            // 💰 MIN PRICE
            ->when($request->min_price, function ($q) use ($request) {
                $q->having('variants_min_price', '>=', $request->min_price);
            })

            // 💰 MAX PRICE
            ->when($request->max_price, function ($q) use ($request) {
                $q->having('variants_min_price', '<=', $request->max_price);
            });

        // 🔽 SORTING
        if ($request->sort == 'price_asc') {
            $baseQuery->orderBy('variants_min_price', 'asc');
        } elseif ($request->sort == 'price_desc') {
            $baseQuery->orderBy('variants_min_price', 'desc');
        } else {
            $baseQuery->latest();
        }

        // 🔥 latest product (tidak ikut filter harga & sort)
        $latestProducts = null;

        if (!$request->search) {
            $latestProducts = (clone $baseQuery)
                ->latest()
                ->take(3)
                ->get();
        }

        $products = (clone $baseQuery)
            ->paginate(12)
            ->withQueryString();

        return view('customer.product', [
            'title'   => 'Semua Produk | Trendora',
            'navlink' => 'produk',
            'latestProducts' => $latestProducts,
            'products' => $products,
        ]);
    }

    public function show($categorySlug, $productSlug)
    {
        $product = ProductModel::query()
            ->where('is_active', true)

            ->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            })

            ->whereHas('variants', function ($q) {
                $q->where('stock', '>', 0);
            })

            ->with([
                'category',
                'variants' => function ($q) {
                    $q->where('stock', '>', 0)
                        ->with('branch'); // 🔥 ini penting
                }
            ])

            ->withMin(['variants' => function ($q) {
                $q->where('stock', '>', 0);
            }], 'price')

            ->where('slug', $productSlug)
            ->firstOrFail();

        return view('customer.detail-product', [
            'title' => 'Detail | Fashion & Lifestyle',
            'navlink' => 'Detail',
            'product' => $product,
        ]);
    }
}
