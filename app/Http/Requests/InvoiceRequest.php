<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use App\Models\Orders;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isPaid = $this->input('status') === Invoice::STATUS_LUNAS;

        return [
            'order_id' => 'required|integer|exists:orders,id',
            'type' => ['required', Rule::in(Invoice::types())],
            'amount' => 'required|numeric|min:1|max:9999999999',
            'issued_at' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issued_at',
            'status' => ['required', Rule::in(Invoice::statuses())],
            'paid_at' => [$isPaid ? 'required' : 'nullable', 'date', 'after_or_equal:issued_at'],
            'payment_method' => [$isPaid ? 'required' : 'nullable', Rule::in(Invoice::paymentMethods())],
            'notes' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'order_id' => 'pesanan',
            'type' => 'jenis tagihan',
            'amount' => 'nominal',
            'issued_at' => 'tanggal terbit',
            'due_date' => 'jatuh tempo',
            'paid_at' => 'tanggal bayar',
            'payment_method' => 'metode pembayaran',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty() || $this->input('status') === Invoice::STATUS_DIBATALKAN) {
                return;
            }

            $order = Orders::query()->find($this->input('order_id'));
            $billable = $order->billableBalance($this->route('invoice'));

            if (round((float) $this->input('amount'), 2) > round($billable, 2)) {
                $validator->errors()->add(
                    'amount',
                    'Nominal melebihi sisa yang bisa ditagihkan (Rp '.number_format($billable, 0, ',', '.').').'
                );
            }
        });
    }
}
