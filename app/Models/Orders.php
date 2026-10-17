<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    public const PRODUCTION_STATUS_PESANAN_DITERIMA = 'pesanan_diterima';
    public const PRODUCTION_STATUS_MENUNGGU_BAHAN = 'menunggu_bahan';
    public const PRODUCTION_STATUS_PROSES_PRODUKSI = 'proses_produksi';
    public const PRODUCTION_STATUS_QUALITY_CONTROL = 'quality_control';
    public const PRODUCTION_STATUS_SELESAI = 'selesai';

    protected $table = 'orders';
    protected $primaryKey = 'id';

    protected $fillable = [
        'customer_id',
        'order_number',
        'product_name',
        'material',
        'quantity',
        'size_details',
        'unit_price',
        'total_price',
        'down_payment',
        'due_date',
        'production_status',
        'notes',
        'payment_type',
        'payment_status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];  

    protected static function booted(): void
    {
        static::creating(function (self $order): void {
            if ($order->total_price === null) {
                $order->total_price = ((float) $order->quantity) * ((float) $order->unit_price);
            }

            if (!$order->production_status) {
                $order->production_status = self::PRODUCTION_STATUS_PESANAN_DITERIMA;
            }
        });

        static::created(function (self $order): void {
            if ($order->order_number) {
                return;
            }

            $order->forceFill([
                'order_number' => self::generateOrderNumber($order->created_at ?? now()),
            ])->saveQuietly();
        });
    }

    public static function productionStatuses(): array
    {
        return [
            self::PRODUCTION_STATUS_PESANAN_DITERIMA,
            self::PRODUCTION_STATUS_MENUNGGU_BAHAN,
            self::PRODUCTION_STATUS_PROSES_PRODUKSI,
            self::PRODUCTION_STATUS_QUALITY_CONTROL,
            self::PRODUCTION_STATUS_SELESAI,
        ];
    }

    protected static function generateOrderNumber(Carbon $date): string
    {
        $prefix = 'ORD-' . $date->format('ym') . '-';

        $latestOrderNumber = static::query()
            ->where('order_number', 'like', $prefix . '%')
            ->orderByDesc('order_number')
            ->value('order_number');

        $lastSequence = $latestOrderNumber ? (int) substr($latestOrderNumber, -4) : 0;

        return $prefix . str_pad((string) ($lastSequence + 1), 4, '0', STR_PAD_LEFT);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function procurements()
    {
        return $this->hasMany(Procurement::class, 'order_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'order_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'order_id');
    }

    public function getRemainingBalanceAttribute(): float
    {
        return max(0, (float) $this->total_price - (float) $this->down_payment);
    }
}
