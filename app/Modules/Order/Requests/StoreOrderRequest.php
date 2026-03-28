<?php

namespace App\Modules\Order\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'cart_id'                           => ['required', 'integer', 'exists:carts,id'],
            'currency'                          => ['nullable', 'string', 'size:3'],
            'shipping_address'                  => ['required', 'array'],
            'shipping_address.name'             => ['required', 'string'],
            'shipping_address.address_line_1'   => ['required', 'string'],
            'shipping_address.city'             => ['required', 'string'],
            'shipping_address.state'            => ['required', 'string'],
            'shipping_address.postal_code'      => ['required', 'string'],
            'shipping_address.country'          => ['required', 'string', 'size:2'],
            'billing_address'                   => ['nullable', 'array'],
            'notes'                             => ['nullable', 'string', 'max:1000'],
        ];
    }
}
