<?php

namespace Ecommerce\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:255'],
            'slug'                => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'description'         => ['nullable', 'string'],
            'short_description'   => ['nullable', 'string', 'max:500'],
            'price'               => ['required', 'numeric', 'min:0'],
            'compare_price'       => ['nullable', 'numeric', 'min:0'],
            'cost_price'          => ['nullable', 'numeric', 'min:0'],
            'stock_quantity'      => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'sku'                 => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'category_id'         => ['nullable', 'integer', 'exists:categories,id'],
            'image'               => ['nullable', 'image', 'max:2048'],
            'images'              => ['nullable', 'array'],
            'images.*'            => ['image', 'max:2048'],
            'is_active'           => ['boolean'],
            'is_featured'         => ['boolean'],
            'weight'              => ['nullable', 'numeric', 'min:0'],
            'meta_title'          => ['nullable', 'string', 'max:255'],
            'meta_description'    => ['nullable', 'string', 'max:500'],
        ];
    }
}
