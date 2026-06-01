<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')?->id;

        return [
            'name' => 'required|string',
            'email' => [
                'required',
                'email',
                Rule::unique('customers', 'email')->ignore($customerId),
            ],
            'institute' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
        ];
    }
}

