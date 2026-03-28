<?php

namespace App\Modules\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(['admin', 'vendor']) ?? false;
    }

    public function rules(): array
    {
        $productId = $this->route('id');
        return [
            'category_id'   => ['nullable', 'integer', 'exists:categories,id'],
            'name'          => ['nullable', 'string', 'max:255'],
            'slug'          => ['nullable', 'string', 'max:255', "unique:products,slug,{$productId}"],
            'description'   => ['nullable', 'string'],
            'price'         => ['nullable', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'sku'           => ['nullable', 'string', "unique:products,sku,{$productId}"],
            'is_active'     => ['boolean'],
            'is_featured'   => ['boolean'],
            'images'        => ['nullable', 'array'],
            'images.*'      => ['string', 'url'],
            'meta'          => ['nullable', 'array'],
        ];
    }
}
