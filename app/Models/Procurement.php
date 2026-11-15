<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Pengadaan bahan baku untuk satu pesanan dari satu pemasok.
 * Menghasilkan paling banyak satu pengeluaran kategori bahan baku.
 */
class Procurement extends Model
{
    public const STATUS_DIPESAN = 'dipesan';

    public const STATUS_DITERIMA = 'diterima';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    protected $fillable = [
        'order_id',
        'supplier_id',
        'material_name',
        'quantity',
        'unit',
        'unit_price',
        'ordered_at',
        'expected_at',
        'received_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'ordered_at' => 'date',
        'expected_at' => 'date',
        'received_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $procurement): void {
            $procurement->total_cost = (float) $procurement->quantity * (float) $procurement->unit_price;
        });

        static::saved(fn (self $procurement) => $procurement->syncExpense());
    }

    public static function statuses(): array
    {
        return [self::STATUS_DIPESAN, self::STATUS_DITERIMA, self::STATUS_DIBATALKAN];
    }

    /** Pengeluaran bahan baku mengikuti pengadaan; batal atau nol berarti tidak ada pengeluaran. */
    public function syncExpense(): void
    {
        if ($this->status === self::STATUS_DIBATALKAN || (float) $this->total_cost <= 0) {
            $this->expense()->delete();

            return;
        }

        $this->expense()->updateOrCreate([], [
            'order_id' => $this->order_id,
            'category' => Expense::CATEGORY_BAHAN_BAKU,
            'description' => 'Pengadaan bahan: '.$this->material_name,
            'amount' => $this->total_cost,
            'spent_at' => $this->ordered_at,
        ]);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id');
    }

    public function expense(): HasOne
    {
        return $this->hasOne(Expense::class, 'procurement_id');
    }
}
