<?php

namespace App\Http\Requests;

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
            'quantity' => 'required|numeric|min:0.01|max:1000000',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0|max:100000000',
            'ordered_at' => 'required|date',
            'expected_at' => 'nullable|date|after_or_equal:ordered_at',
            'received_at' => 'nullable|date|after_or_equal:ordered_at',
            'notes' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'order_id' => 'pesanan',
            'supplier_id' => 'pemasok',
            'material_name' => 'nama bahan',
            'quantity' => 'jumlah',
            'unit' => 'satuan',
            'unit_price' => 'harga satuan',
            'ordered_at' => 'tanggal pesan',
            'expected_at' => 'perkiraan datang',
        ];
    }
}
