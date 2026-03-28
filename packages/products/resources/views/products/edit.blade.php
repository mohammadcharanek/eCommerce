@extends('layouts.app')

@section('title', 'Edit ' . $product->name)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('products.index') }}" class="hover:underline">Products</a>
        <span>/</span>
        <a href="{{ route('products.show', $product) }}" class="hover:underline">{{ $product->name }}</a>
        <span>/</span>
        <span class="text-gray-800">Edit</span>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-xl font-bold text-gray-800 mb-6">Edit Product</h1>

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('products::products._form', ['categories' => $categories, 'product' => $product])

            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Save Changes
                </button>
                <a href="{{ route('products.show', $product) }}"
                   class="text-gray-600 px-6 py-2 rounded-lg border hover:bg-gray-50 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
