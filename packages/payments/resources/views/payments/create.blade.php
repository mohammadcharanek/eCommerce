@extends('layouts.app')

@section('title', 'Pay Order ' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-lg">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('orders.show', $order) }}" class="hover:underline">Order {{ $order->order_number }}</a>
        <span>/</span>
        <span class="text-gray-800">Payment</span>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-xl font-bold text-gray-800 mb-2">Complete Your Payment</h1>
        <p class="text-gray-500 text-sm mb-6">
            You are about to pay <strong>${{ number_format($order->total, 2) }} {{ $order->currency }}</strong>
            for order <span class="font-mono">{{ $order->order_number }}</span>.
        </p>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded p-4 mb-4">
                <ul class="text-sm text-red-600 list-disc list-inside">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('payments.store', $order) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                <div class="space-y-2">
                    @foreach ($methods as $key => $label)
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition
                            {{ old('payment_method') === $key ? 'border-blue-400 bg-blue-50' : '' }}">
                            <input type="radio" name="payment_method" value="{{ $key }}"
                                   {{ old('payment_method', array_key_first($methods)) === $key ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('payment_method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                Pay ${{ number_format($order->total, 2) }}
            </button>
        </form>
    </div>
</div>
@endsection
