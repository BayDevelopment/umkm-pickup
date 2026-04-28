<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = ProductModel::where('is_active', true)
            ->with(['variants.branch', 'category', 'mainImage', 'umkm']) // ✅
            ->latest()
            ->take(3)
            ->get();

        // total produk aktif
        $totalProducts = ProductModel::where('is_active', true)->count();

        // total kategori aktif
        $totalCategories = CategoryModel::where('is_active', true)->count();

        return view('pages.home', [
            'title' => 'Trendora | Fashion & Lifestyle',
            'navlink' => 'beranda',
            'products' => $products,
            'totalProducts' => $totalProducts,
            'totalCategories' => $totalCategories,
        ]);
    }

    public function show($categorySlug, $productSlug)
    {
        $product = ProductModel::with(['variants', 'category'])
            ->where('slug', $productSlug)
            ->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            })
            ->whereHas('variants') // 🔥 BLOCK jika tidak punya variant
            ->firstOrFail();

        return view('pages.detail-product', [
            'title' => 'Detail | Fashion & Lifestyle',
            'navlink' => 'Detail',
            'product' => $product,
        ]);
    }
}
