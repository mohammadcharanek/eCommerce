@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('categories.index') }}" class="hover:underline">Categories</a>
        <span>/</span>
        <span class="text-gray-800">{{ $category->name }}</span>
    </div>

    <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $category->name }}</h1>
    @if ($category->description)
        <p class="text-gray-500 mb-6">{{ $category->description }}</p>
    @endif

    @if ($products->isEmpty())
        <p class="text-gray-500 text-center py-16">No products in this category.</p>
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
                        <div class="flex items-center justify-between">
                            <span class="text-blue-600 font-bold">${{ number_format($product->price, 2) }}</span>
                        </div>
                        <a href="{{ route('products.show', $product) }}"
                           class="mt-3 block text-center text-sm bg-blue-600 text-white rounded py-1.5 hover:bg-blue-700 transition">
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
