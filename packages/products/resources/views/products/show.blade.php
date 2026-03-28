@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('products.index') }}" class="hover:underline">Products</a>
        <span>/</span>
        <span class="text-gray-800">{{ $product->name }}</span>
    </div>

    <div class="bg-white rounded-lg shadow p-6 flex flex-col md:flex-row gap-8">
        <div class="md:w-1/2">
            @if ($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                     class="w-full rounded-lg object-cover">
            @else
                <div class="w-full h-72 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-6xl">📦</div>
            @endif
        </div>

        <div class="md:w-1/2 flex flex-col">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $product->name }}</h1>

            @if ($product->category)
                <a href="{{ route('categories.show', $product->category) }}"
                   class="text-sm text-blue-500 hover:underline mb-3">{{ $product->category->name }}</a>
            @endif

            <div class="flex items-center gap-4 mb-4">
                <span class="text-3xl font-bold text-blue-600">${{ number_format($product->price, 2) }}</span>
                @if ($product->compare_price)
                    <span class="text-lg text-gray-400 line-through">${{ number_format($product->compare_price, 2) }}</span>
                    <span class="text-sm bg-red-100 text-red-600 px-2 py-0.5 rounded">
                        {{ $product->discount_percentage }}% OFF
                    </span>
                @endif
            </div>

            <p class="text-gray-600 mb-4">{{ $product->short_description ?? $product->description }}</p>

            <div class="text-sm text-gray-500 space-y-1 mb-6">
                @if ($product->sku)
                    <p>SKU: <span class="font-mono text-gray-700">{{ $product->sku }}</span></p>
                @endif
                <p>Stock:
                    <span class="{{ $product->isInStock() ? 'text-green-600' : 'text-red-500' }} font-medium">
                        {{ $product->isInStock() ? $product->stock_quantity . ' available' : 'Out of Stock' }}
                    </span>
                </p>
            </div>

            <div class="flex gap-3 mt-auto">
                <a href="{{ route('products.edit', $product) }}"
                   class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">Edit</a>
                <form action="{{ route('products.destroy', $product) }}" method="POST"
                      onsubmit="return confirm('Delete this product?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="bg-red-100 text-red-600 px-5 py-2 rounded-lg hover:bg-red-200 transition">Delete</button>
                </form>
            </div>
        </div>
    </div>

    @if ($product->description)
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Description</h2>
            <div class="prose max-w-none text-gray-600">{{ $product->description }}</div>
        </div>
    @endif
</div>
@endsection
