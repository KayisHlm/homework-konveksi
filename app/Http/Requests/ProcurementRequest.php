<?php

namespace App\Http\Requests;

use App\Models\Procurement;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProcurementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => 'required|integer|exists:orders,id',
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'material_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'total_cost' => 'nullable|numeric|min:0',
            'ordered_at' => 'required|date',
            'expected_at' => 'nullable|date|after_or_equal:ordered_at',
            'received_at' => 'nullable|date|after_or_equal:ordered_at',
            'status' => 'required|in:' . implode(',', Procurement::statuses()),
            'notes' => 'nullable|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $quantity = (float) $this->input('quantity', 0);
        $unitPrice = (float) $this->input('unit_price', 0);

        $this->merge([
            'total_cost' => $quantity * $unitPrice,
        ]);
    }
}
