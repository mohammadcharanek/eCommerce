@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Orders</h1>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('orders.index') }}" class="bg-white rounded-lg shadow p-4 mb-6 flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
               placeholder="Order number / email…"
               class="border rounded px-3 py-2 flex-1 min-w-[200px]">
        <select name="status" class="border rounded px-3 py-2">
            <option value="">All Statuses</option>
            @foreach (['pending','processing','shipped','delivered','cancelled','refunded'] as $status)
                <option value="{{ $status }}" {{ ($filters['status'] ?? '') === $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">Filter</button>
        <a href="{{ route('orders.index') }}" class="text-gray-500 px-4 py-2 hover:underline">Reset</a>
    </form>

    @if ($orders->isEmpty())
        <p class="text-gray-500 text-center py-16">No orders found.</p>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 text-gray-600">Order</th>
                        <th class="text-left px-4 py-3 text-gray-600">Customer</th>
                        <th class="text-left px-4 py-3 text-gray-600">Status</th>
                        <th class="text-right px-4 py-3 text-gray-600">Total</th>
                        <th class="text-left px-4 py-3 text-gray-600">Date</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-mono font-semibold text-blue-600">{{ $order->order_number }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $order->billing_name }}</p>
                                <p class="text-gray-500 text-xs">{{ $order->billing_email }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($order->status === 'delivered') bg-green-100 text-green-700
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                                    @elseif($order->status === 'shipped') bg-blue-100 text-blue-700
                                    @else bg-yellow-100 text-yellow-700 @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold">${{ number_format($order->total, 2) }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:underline">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $orders->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
