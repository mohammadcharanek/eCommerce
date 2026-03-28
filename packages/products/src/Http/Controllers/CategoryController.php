<?php

namespace Ecommerce\Products\Http\Controllers;

use Ecommerce\Products\Models\Category;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::withCount('products')
            ->active()
            ->orderBy('name')
            ->get();

        return view('products::categories.index', compact('categories'));
    }

    /**
     * Display products in a given category.
     */
    public function show(Category $category)
    {
        $products = $category->products()
            ->active()
            ->inStock()
            ->with('category')
            ->paginate(config('products.per_page', 15));

        return view('products::categories.show', compact('category', 'products'));
    }
}
