@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Products</h1>
        <a href="{{ route('products.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            + Add Product
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('products.index') }}" class="bg-white rounded-lg shadow p-4 mb-6 flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
               placeholder="Search products…"
               class="border rounded px-3 py-2 flex-1 min-w-[200px]">
        <select name="category_id" class="border rounded px-3 py-2">
            <option value="">All Categories</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        <select name="sort_by" class="border rounded px-3 py-2">
            <option value="created_at" {{ ($filters['sort_by'] ?? '') === 'created_at' ? 'selected' : '' }}>Newest</option>
            <option value="price"      {{ ($filters['sort_by'] ?? '') === 'price'      ? 'selected' : '' }}>Price</option>
            <option value="name"       {{ ($filters['sort_by'] ?? '') === 'name'       ? 'selected' : '' }}>Name</option>
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">Filter</button>
        <a href="{{ route('products.index') }}" class="text-gray-500 px-4 py-2 hover:underline">Reset</a>
    </form>

    @if ($products->isEmpty())
        <p class="text-gray-500 text-center py-16">No products found.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <div class="bg-white rounded-lg shadow hover:shadow-md transition overflow-hidden">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                             class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400 text-4xl">📦</div>
                    @endif
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-800 mb-1 truncate">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 mb-2">{{ $product->category->name ?? '—' }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-blue-600 font-bold">${{ number_format($product->price, 2) }}</span>
                            <span class="text-xs {{ $product->isInStock() ? 'text-green-600' : 'text-red-500' }}">
                                {{ $product->isInStock() ? 'In Stock' : 'Out of Stock' }}
                            </span>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('products.show', $product) }}"
                               class="text-sm text-blue-600 hover:underline">View</a>
                            <a href="{{ route('products.edit', $product) }}"
                               class="text-sm text-gray-600 hover:underline">Edit</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $products->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
