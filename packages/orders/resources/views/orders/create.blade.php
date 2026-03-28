@extends('layouts.app')

@section('title', 'New Order')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('orders.index') }}" class="hover:underline">Orders</a>
        <span>/</span>
        <span class="text-gray-800">New Order</span>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-xl font-bold text-gray-800 mb-6">Create New Order</h1>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded p-4 mb-4">
                <ul class="text-sm text-red-600 list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('orders.store') }}" method="POST">
            @csrf

            <h2 class="text-base font-semibold text-gray-700 mb-3 mt-2">Billing Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="billing_name" value="{{ old('billing_name') }}"
                           class="w-full border rounded-lg px-3 py-2 @error('billing_name') border-red-400 @enderror" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="billing_email" value="{{ old('billing_email') }}"
                           class="w-full border rounded-lg px-3 py-2 @error('billing_email') border-red-400 @enderror" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                    <input type="text" name="billing_address" value="{{ old('billing_address') }}"
                           class="w-full border rounded-lg px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City <span class="text-red-500">*</span></label>
                    <input type="text" name="billing_city" value="{{ old('billing_city') }}"
                           class="w-full border rounded-lg px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ZIP <span class="text-red-500">*</span></label>
                    <input type="text" name="billing_zip" value="{{ old('billing_zip') }}"
                           class="w-full border rounded-lg px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Country (2-letter ISO) <span class="text-red-500">*</span></label>
                    <input type="text" name="billing_country" value="{{ old('billing_country', 'US') }}"
                           maxlength="2" class="w-full border rounded-lg px-3 py-2" required>
                </div>
            </div>

            <h2 class="text-base font-semibold text-gray-700 mb-3">Items</h2>
            <div id="items-container" class="space-y-2 mb-4">
                <div class="flex gap-2 items-center">
                    <input type="number" name="items[0][product_id]" placeholder="Product ID" min="1"
                           class="border rounded px-3 py-2 w-1/2" required>
                    <input type="number" name="items[0][quantity]" placeholder="Qty" min="1" value="1"
                           class="border rounded px-3 py-2 w-1/4" required>
                </div>
            </div>
            <button type="button" onclick="addItem()"
                    class="text-sm text-blue-600 hover:underline mb-6">+ Add Item</button>

            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Place Order
                </button>
                <a href="{{ route('orders.index') }}"
                   class="text-gray-600 px-6 py-2 rounded-lg border hover:bg-gray-50 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
let itemIndex = 1;
function addItem() {
    const container = document.getElementById('items-container');
    const div = document.createElement('div');
    div.className = 'flex gap-2 items-center';
    div.innerHTML = `
        <input type="number" name="items[${itemIndex}][product_id]" placeholder="Product ID" min="1"
               class="border rounded px-3 py-2 w-1/2" required>
        <input type="number" name="items[${itemIndex}][quantity]" placeholder="Qty" min="1" value="1"
               class="border rounded px-3 py-2 w-1/4" required>
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">✕</button>
    `;
    container.appendChild(div);
    itemIndex++;
}
</script>
@endsection
