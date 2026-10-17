<?php

namespace App\Http\Requests;

use App\Models\Expense;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => 'nullable|integer|exists:orders,id',
            'category' => 'required|in:' . implode(',', Expense::categories()),
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'spent_at' => 'required|date',
        ];
    }
}
