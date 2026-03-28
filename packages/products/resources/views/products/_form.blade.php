{{-- Shared form fields for create/edit product --}}

@if ($errors->any())
    <div class="bg-red-50 border border-red-200 rounded p-4 mb-4">
        <ul class="text-sm text-red-600 list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
               class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-300 @error('name') border-red-400 @enderror"
               required>
        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
        <select name="category_id"
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-300">
            <option value="">— No Category —</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}"
                    {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Price <span class="text-red-500">*</span></label>
            <input type="number" name="price" step="0.01" min="0"
                   value="{{ old('price', $product->price ?? '') }}"
                   class="w-full border rounded-lg px-3 py-2 @error('price') border-red-400 @enderror"
                   required>
            @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Compare Price</label>
            <input type="number" name="compare_price" step="0.01" min="0"
                   value="{{ old('compare_price', $product->compare_price ?? '') }}"
                   class="w-full border rounded-lg px-3 py-2">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity <span class="text-red-500">*</span></label>
            <input type="number" name="stock_quantity" min="0"
                   value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}"
                   class="w-full border rounded-lg px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
            <input type="text" name="sku"
                   value="{{ old('sku', $product->sku ?? '') }}"
                   class="w-full border rounded-lg px-3 py-2 font-mono">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
        <input type="text" name="short_description"
               value="{{ old('short_description', $product->short_description ?? '') }}"
               maxlength="500"
               class="w-full border rounded-lg px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea name="description" rows="5"
                  class="w-full border rounded-lg px-3 py-2">{{ old('description', $product->description ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
        <input type="file" name="image" accept="image/*" class="w-full border rounded-lg px-3 py-2">
        @if (!empty($product->image))
            <img src="{{ asset('storage/' . $product->image) }}" alt="Current image"
                 class="mt-2 h-20 rounded object-cover">
        @endif
    </div>

    <div class="flex gap-6">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1"
                   {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
            <span class="text-sm text-gray-700">Active</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_featured" value="1"
                   {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
            <span class="text-sm text-gray-700">Featured</span>
        </label>
    </div>
</div>
