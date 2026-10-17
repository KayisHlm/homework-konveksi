<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'total_cost',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $procurement): void {
            $procurement->total_cost = ((float) $procurement->quantity) * ((float) $procurement->unit_price);
        });

        static::saved(function (self $procurement): void {
            if ((float) $procurement->total_cost <= 0) {
                $procurement->expense()?->delete();

                return;
            }

            $procurement->expense()->updateOrCreate(
                ['procurement_id' => $procurement->id],
                [
                    'order_id' => $procurement->order_id,
                    'category' => 'bahan_baku',
                    'description' => 'Pengadaan bahan: ' . $procurement->material_name,
                    'amount' => $procurement->total_cost,
                    'spent_at' => $procurement->ordered_at,
                ],
            );
        });
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DIPESAN,
            self::STATUS_DITERIMA,
            self::STATUS_DIBATALKAN,
        ];
    }

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id');
    }

    public function expense()
    {
        return $this->hasOne(Expense::class, 'procurement_id');
    }
}
