<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use App\Models\OrderItem;
use App\Models\Orders;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class OrderRequest extends FormRequest
{
    /** Batas wajar nominal per isian (Rp 100 juta), jauh di bawah kapasitas kolom decimal(12,2). */
    private const MAX_MONEY = 100000000;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isCreate = ! $this->route('order');

        return [
            'customer_id' => 'nullable|integer|exists:customers,id',
            'name' => 'required|string|max:255',
            'email' => array_filter([
                'required', 'email', 'max:255',
                $this->filled('customer_id') ? Rule::unique('customers', 'email')->ignore($this->input('customer_id')) : null,
            ]),
            'phone' => 'required|string|max:50',
            'address' => 'required|string',
            'institute' => 'required|string|max:255',

            'due_date' => 'nullable|date',
            'design_notes' => 'nullable|string',
            'notes' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0|max:'.self::MAX_MONEY,

            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.material' => 'required|string|max:255',
            'items.*.color' => 'nullable|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0|max:'.self::MAX_MONEY,
            // Rincian ukuran dikunci per nama ukuran; ukuran dengan qty kosong/0 sudah dibuang di prepareForValidation.
            'items.*.sizes' => [
                'required', 'array', 'min:1',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $unknown = array_diff(array_keys($value), OrderItem::SIZES);
                    if ($unknown !== []) {
                        $fail('Ukuran tidak dikenal: '.implode(', ', $unknown).'.');
                    }
                },
            ],
            'items.*.sizes.*.quantity' => 'required|integer|min:1|max:100000',
            'items.*.sizes.*.extra_price' => 'nullable|numeric|min:0|max:'.self::MAX_MONEY,

            // Uang muka hanya dicatat saat pesanan dibuat; setelah itu lewat modul invoice.
            'down_payment' => $isCreate ? 'nullable|numeric|min:0|max:'.self::MAX_MONEY : 'exclude',
            'down_payment_method' => [
                $isCreate ? 'nullable' : 'exclude',
                Rule::requiredIf(fn () => $isCreate && (float) $this->input('down_payment', 0) > 0),
                Rule::in(Invoice::paymentMethods()),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'items.*.sizes.required' => 'Isi jumlah minimal satu ukuran untuk setiap produk.',
            'items.*.sizes.min' => 'Isi jumlah minimal satu ukuran untuk setiap produk.',
        ];
    }

    public function attributes(): array
    {
        return [
            'items' => 'item pesanan',
            'items.*.product_name' => 'nama produk',
            'items.*.material' => 'bahan',
            'items.*.unit_price' => 'harga dasar',
            'items.*.sizes' => 'rincian ukuran',
            'items.*.sizes.*.quantity' => 'jumlah',
            'items.*.sizes.*.extra_price' => 'tambahan harga',
            'down_payment' => 'uang muka',
            'down_payment_method' => 'metode uang muka',
            'discount_amount' => 'diskon',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'customer_id' => $this->filled('customer_id') ? (int) $this->input('customer_id') : null,
            'discount_amount' => (float) ($this->input('discount_amount') ?: 0),
            'items' => collect((array) $this->input('items', []))
                ->map(function ($item) {
                    $item = (array) $item;
                    $item['sizes'] = collect((array) ($item['sizes'] ?? []))
                        ->reject(fn ($size) => in_array(trim((string) ($size['quantity'] ?? '')), ['', '0'], true))
                        ->map(fn ($size) => ['quantity' => $size['quantity'], 'extra_price' => ($size['extra_price'] ?? '') === '' ? 0 : $size['extra_price']])
                        ->all();

                    return $item;
                })
                ->values()
                ->all(),
        ]);

        if (! $this->route('order')) {
            $this->merge(['down_payment' => (float) ($this->input('down_payment') ?: 0)]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $total = $this->computedTotal();

            // Kapasitas kolom decimal(12,2) adalah 9.999.999.999,99.
            if ($this->computedSubtotal() > 9999999999) {
                $validator->errors()->add('items', 'Total pesanan terlalu besar. Periksa kembali harga dan jumlah.');

                return;
            }

            if ((float) $this->input('discount_amount') > $this->computedSubtotal()) {
                $validator->errors()->add('discount_amount', 'Diskon tidak boleh melebihi subtotal item.');
            }

            if ((float) $this->input('down_payment', 0) > $total) {
                $validator->errors()->add('down_payment', 'Uang muka tidak boleh melebihi total pesanan.');
            }

            /** @var Orders|null $order */
            $order = $this->route('order');
            if ($order && round($total, 2) < round($order->amountInvoiced(), 2)) {
                $validator->errors()->add('items', 'Total pesanan tidak boleh lebih kecil dari total invoice yang sudah diterbitkan (Rp '.number_format($order->amountInvoiced(), 0, ',', '.').').');
            }
        });
    }

    public function computedSubtotal(): float
    {
        return (float) collect($this->input('items', []))->sum(
            fn (array $item) => collect($item['sizes'] ?? [])->sum(
                fn (array $size) => (int) $size['quantity'] * ((float) ($item['unit_price'] ?? 0) + (float) $size['extra_price'])
            )
        );
    }

    public function computedTotal(): float
    {
        return max(0, $this->computedSubtotal() - (float) $this->input('discount_amount'));
    }
}
