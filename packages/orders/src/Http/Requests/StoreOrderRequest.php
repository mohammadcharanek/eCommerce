<?php

namespace Ecommerce\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'          => ['nullable', 'integer', 'exists:users,id'],
            'currency'         => ['nullable', 'string', 'size:3'],
            'shipping_amount'  => ['nullable', 'numeric', 'min:0'],
            'discount_amount'  => ['nullable', 'numeric', 'min:0'],
            'notes'            => ['nullable', 'string', 'max:1000'],

            'billing_name'     => ['required', 'string', 'max:255'],
            'billing_email'    => ['required', 'email', 'max:255'],
            'billing_phone'    => ['nullable', 'string', 'max:30'],
            'billing_address'  => ['required', 'string', 'max:500'],
            'billing_city'     => ['required', 'string', 'max:100'],
            'billing_state'    => ['nullable', 'string', 'max:100'],
            'billing_zip'      => ['required', 'string', 'max:20'],
            'billing_country'  => ['required', 'string', 'size:2'],

            'shipping_name'    => ['nullable', 'string', 'max:255'],
            'shipping_address' => ['nullable', 'string', 'max:500'],
            'shipping_city'    => ['nullable', 'string', 'max:100'],
            'shipping_state'   => ['nullable', 'string', 'max:100'],
            'shipping_zip'     => ['nullable', 'string', 'max:20'],
            'shipping_country' => ['nullable', 'string', 'size:2'],

            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ];
    }
}
