<?php

namespace Ecommerce\Products\Http\Controllers;

use Ecommerce\Products\Http\Requests\StoreProductRequest;
use Ecommerce\Products\Http\Requests\UpdateProductRequest;
use Ecommerce\Products\Models\Product;
use Ecommerce\Products\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $filters  = $request->only(['category_id', 'search', 'is_active', 'is_featured', 'in_stock', 'sort_by', 'sort_dir']);
        $products = $this->productService->list($filters);
        $categories = $this->productService->allCategories();

        return view('products::products.index', compact('products', 'categories', 'filters'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = $this->productService->allCategories();

        return view('products::products.create', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create($request->validated());

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load('category');

        return view('products::products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = $this->productService->allCategories();

        return view('products::products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product = $this->productService->update($product, $request->validated());

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        $this->productService->delete($product);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
