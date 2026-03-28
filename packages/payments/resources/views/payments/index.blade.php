@extends('layouts.app')

@section('title', 'Payments')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Payments</h1>

    <form method="GET" action="{{ route('payments.index') }}" class="bg-white rounded-lg shadow p-4 mb-6 flex flex-wrap gap-3">
        <select name="status" class="border rounded px-3 py-2">
            <option value="">All Statuses</option>
            @foreach (['pending','paid','failed','refunded','cancelled'] as $status)
                <option value="{{ $status }}" {{ ($filters['status'] ?? '') === $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
        <select name="payment_method" class="border rounded px-3 py-2">
            <option value="">All Methods</option>
            @foreach (config('payments.methods', []) as $key => $label)
                <option value="{{ $key }}" {{ ($filters['payment_method'] ?? '') === $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">Filter</button>
        <a href="{{ route('payments.index') }}" class="text-gray-500 px-4 py-2 hover:underline">Reset</a>
    </form>

    @if ($payments->isEmpty())
        <p class="text-gray-500 text-center py-16">No payments found.</p>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 text-gray-600">Transaction</th>
                        <th class="text-left px-4 py-3 text-gray-600">Order</th>
                        <th class="text-left px-4 py-3 text-gray-600">Method</th>
                        <th class="text-left px-4 py-3 text-gray-600">Status</th>
                        <th class="text-right px-4 py-3 text-gray-600">Amount</th>
                        <th class="text-left px-4 py-3 text-gray-600">Date</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($payments as $payment)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $payment->transaction_id ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('orders.show', $payment->order) }}"
                                   class="text-blue-600 hover:underline font-mono">
                                    {{ $payment->order->order_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($payment->status === 'paid') bg-green-100 text-green-700
                                    @elseif($payment->status === 'failed') bg-red-100 text-red-700
                                    @elseif($payment->status === 'refunded') bg-purple-100 text-purple-700
                                    @else bg-yellow-100 text-yellow-700 @endif">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold">${{ number_format($payment->amount, 2) }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $payment->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:underline">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $payments->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
