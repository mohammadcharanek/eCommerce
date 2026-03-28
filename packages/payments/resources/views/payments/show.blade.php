@extends('layouts.app')

@section('title', 'Payment #' . $payment->id)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('payments.index') }}" class="hover:underline">Payments</a>
        <span>/</span>
        <span class="text-gray-800">Payment #{{ $payment->id }}</span>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded p-3 mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-bold text-gray-800">Payment Details</h1>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                @if($payment->status === 'paid') bg-green-100 text-green-700
                @elseif($payment->status === 'failed') bg-red-100 text-red-700
                @elseif($payment->status === 'refunded') bg-purple-100 text-purple-700
                @else bg-yellow-100 text-yellow-700 @endif">
                {{ ucfirst($payment->status) }}
            </span>
        </div>

        <dl class="space-y-3 text-sm">
            <div class="flex justify-between border-b pb-2">
                <dt class="text-gray-500">Order</dt>
                <dd>
                    <a href="{{ route('orders.show', $payment->order) }}"
                       class="text-blue-600 hover:underline font-mono">
                        {{ $payment->order->order_number }}
                    </a>
                </dd>
            </div>
            <div class="flex justify-between border-b pb-2">
                <dt class="text-gray-500">Transaction ID</dt>
                <dd class="font-mono text-gray-700">{{ $payment->transaction_id ?? '—' }}</dd>
            </div>
            <div class="flex justify-between border-b pb-2">
                <dt class="text-gray-500">Payment Method</dt>
                <dd>{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</dd>
            </div>
            <div class="flex justify-between border-b pb-2">
                <dt class="text-gray-500">Amount</dt>
                <dd class="font-bold text-gray-800">${{ number_format($payment->amount, 2) }} {{ $payment->currency }}</dd>
            </div>
            @if ($payment->paid_at)
                <div class="flex justify-between border-b pb-2">
                    <dt class="text-gray-500">Paid At</dt>
                    <dd>{{ $payment->paid_at->format('M d, Y H:i') }}</dd>
                </div>
            @endif
            @if ($payment->refunded_at)
                <div class="flex justify-between border-b pb-2">
                    <dt class="text-gray-500">Refunded At</dt>
                    <dd>{{ $payment->refunded_at->format('M d, Y H:i') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Refund Amount</dt>
                    <dd class="text-purple-600 font-semibold">${{ number_format($payment->refund_amount, 2) }}</dd>
                </div>
            @endif
        </dl>
    </div>

    @if ($payment->isRefundable())
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-3">Process Refund</h2>
            <form action="{{ route('payments.refund', $payment) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to refund this payment?')">
                @csrf
                <div class="flex gap-3">
                    <div class="flex-1">
                        <input type="number" name="amount" step="0.01" min="0.01"
                               max="{{ $payment->amount }}"
                               placeholder="Refund amount (leave blank for full refund)"
                               class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <button type="submit"
                            class="bg-purple-600 text-white px-5 py-2 rounded-lg hover:bg-purple-700 transition text-sm font-medium">
                        Refund
                    </button>
                </div>
                @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </form>
        </div>
    @endif
</div>
@endsection
