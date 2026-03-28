<?php

namespace App\Modules\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(['admin', 'vendor']) ?? false;
    }

    public function rules(): array
    {
        return [
            'category_id'   => ['required', 'integer', 'exists:categories,id'],
            'name'          => ['required', 'string', 'max:255'],
            'slug'          => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'description'   => ['nullable', 'string'],
            'price'         => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'sku'           => ['required', 'string', 'unique:products,sku'],
            'is_active'     => ['boolean'],
            'is_featured'   => ['boolean'],
            'images'        => ['nullable', 'array'],
            'images.*'      => ['string', 'url'],
            'meta'          => ['nullable', 'array'],
        ];
    }
}
