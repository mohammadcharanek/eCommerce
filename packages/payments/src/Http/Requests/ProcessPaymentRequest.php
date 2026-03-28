<?php

namespace Ecommerce\Payments\Http\Requests;

use Ecommerce\Payments\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => [
                'required',
                'string',
                Rule::in([
                    Payment::METHOD_CREDIT_CARD,
                    Payment::METHOD_DEBIT_CARD,
                    Payment::METHOD_PAYPAL,
                    Payment::METHOD_BANK_TRANSFER,
                    Payment::METHOD_CASH_ON_DELIVERY,
                ]),
            ],
            'transaction_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
