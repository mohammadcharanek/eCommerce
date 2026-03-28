<?php

namespace ECommerce\Payment\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'order_id'          => ['required', 'integer', 'exists:orders,id'],
            'payment_method_id' => ['required', 'string'],
            'gateway'           => ['nullable', 'string', 'in:stripe'],
        ];
    }
}
