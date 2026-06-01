<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pic_name' => 'required|string',
            'store_name' => 'required|string',
            'product_category' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
        ];
    }
}

