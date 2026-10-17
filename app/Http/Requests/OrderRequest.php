<?php

namespace App\Http\Requests;

use App\Models\Orders;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'nullable|integer|exists:customers,id',
            'product_name' => 'required|string|max:255',
            'material' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'size_details' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'total_price' => 'nullable|numeric|min:0',
            'down_payment' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'production_status' => 'required|in:' . implode(',', Orders::productionStatuses()),
            'notes' => 'nullable|string',
            'payment_type' => 'required|in:cash,transfer,instalment',
            'payment_status' => 'required|in:pending,paid,cancelled',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string',
            'institute' => 'required|string|max:255',
        ];
    }

    protected function prepareForValidation(): void
    {
        $quantity = (int) $this->input('quantity', 0);
        $unitPrice = (float) $this->input('unit_price', 0);

        $this->merge([
            'customer_id' => $this->filled('customer_id') ? (int) $this->input('customer_id') : null,
            'total_price' => $quantity * $unitPrice,
            'down_payment' => $this->filled('down_payment') ? (float) $this->input('down_payment') : 0,
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $totalPrice = (float) $this->input('total_price', 0);
            $downPayment = (float) $this->input('down_payment', 0);

            if ($downPayment > $totalPrice) {
                $validator->errors()->add('down_payment', 'Uang muka tidak boleh melebihi total harga.');
            }
        });
    }
}
