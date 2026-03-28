@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Shop by Category</h1>

    @if ($categories->isEmpty())
        <p class="text-gray-500 text-center py-16">No categories available.</p>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($categories as $category)
                <a href="{{ route('categories.show', $category) }}"
                   class="bg-white rounded-lg shadow hover:shadow-md transition p-5 flex flex-col items-center text-center">
                    <span class="text-4xl mb-3">🗂️</span>
                    <h3 class="font-semibold text-gray-800">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $category->products_count }} products</p>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
