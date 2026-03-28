@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('orders.index') }}" class="hover:underline">Orders</a>
        <span>/</span>
        <span class="text-gray-800">{{ $order->order_number }}</span>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded p-3 mb-4">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Order Summary --}}
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Order Items</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        @if($order->status === 'delivered') bg-green-100 text-green-700
                        @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                        @elseif($order->status === 'shipped') bg-blue-100 text-blue-700
                        @else bg-yellow-100 text-yellow-700 @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <table class="w-full text-sm">
                    <thead class="border-b">
                        <tr>
                            <th class="text-left py-2 text-gray-600">Product</th>
                            <th class="text-center py-2 text-gray-600">Qty</th>
                            <th class="text-right py-2 text-gray-600">Unit</th>
                            <th class="text-right py-2 text-gray-600">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="py-3">
                                    <p class="font-medium text-gray-800">{{ $item->product_name }}</p>
                                    @if ($item->product_sku)
                                        <p class="text-xs text-gray-400 font-mono">{{ $item->product_sku }}</p>
                                    @endif
                                </td>
                                <td class="py-3 text-center">{{ $item->quantity }}</td>
                                <td class="py-3 text-right">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-3 text-right font-semibold">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t">
                        <tr><td colspan="3" class="py-2 text-right text-gray-500">Subtotal</td><td class="py-2 text-right">${{ number_format($order->subtotal, 2) }}</td></tr>
                        <tr><td colspan="3" class="py-2 text-right text-gray-500">Tax</td><td class="py-2 text-right">${{ number_format($order->tax_amount, 2) }}</td></tr>
                        <tr><td colspan="3" class="py-2 text-right text-gray-500">Shipping</td><td class="py-2 text-right">${{ number_format($order->shipping_amount, 2) }}</td></tr>
                        @if ($order->discount_amount > 0)
                            <tr><td colspan="3" class="py-2 text-right text-green-600">Discount</td><td class="py-2 text-right text-green-600">-${{ number_format($order->discount_amount, 2) }}</td></tr>
                        @endif
                        <tr class="font-bold text-gray-800 border-t">
                            <td colspan="3" class="pt-3 text-right">Total</td>
                            <td class="pt-3 text-right text-blue-600">${{ number_format($order->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Payment info --}}
            @if ($order->payment)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3">Payment</h2>
                    <dl class="text-sm space-y-1">
                        <div class="flex justify-between"><dt class="text-gray-500">Method</dt><dd>{{ ucwords(str_replace('_', ' ', $order->payment->payment_method)) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Status</dt>
                            <dd class="{{ $order->payment->isPaid() ? 'text-green-600' : 'text-red-500' }} font-medium">{{ ucfirst($order->payment->status) }}</dd>
                        </div>
                        @if ($order->payment->paid_at)
                            <div class="flex justify-between"><dt class="text-gray-500">Paid at</dt><dd>{{ $order->payment->paid_at->format('M d, Y H:i') }}</dd></div>
                        @endif
                    </dl>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 flex items-center justify-between">
                    <p class="text-yellow-700 text-sm">This order has not been paid yet.</p>
                    <a href="{{ route('payments.create', $order) }}"
                       class="bg-yellow-500 text-white text-sm px-4 py-2 rounded hover:bg-yellow-600 transition">Pay Now</a>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-base font-semibold text-gray-800 mb-3">Billing Address</h2>
                <address class="text-sm text-gray-600 not-italic leading-relaxed">
                    {{ $order->billing_name }}<br>
                    {{ $order->billing_address }}<br>
                    {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_zip }}<br>
                    {{ $order->billing_country }}<br>
                    {{ $order->billing_email }}
                </address>
            </div>

            @if ($order->isCancellable())
                <form action="{{ route('orders.cancel', $order) }}" method="POST"
                      onsubmit="return confirm('Cancel this order?')">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="w-full bg-red-100 text-red-600 rounded-lg py-2 hover:bg-red-200 transition text-sm font-medium">
                        Cancel Order
                    </button>
                </form>
            @endif

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-base font-semibold text-gray-800 mb-3">Update Status</h2>
                <form action="{{ route('orders.updateStatus', $order) }}" method="POST">
                    @csrf @method('PATCH')
                    <select name="status" class="w-full border rounded px-3 py-2 text-sm mb-3">
                        @foreach (['pending','processing','shipped','delivered','cancelled','refunded'] as $s)
                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="w-full bg-blue-600 text-white rounded-lg py-2 hover:bg-blue-700 transition text-sm">
                        Update
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
