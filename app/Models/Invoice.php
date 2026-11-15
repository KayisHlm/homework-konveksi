<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Invoice: tagihan bertahap (uang muka / termin / pelunasan) atas satu pesanan.
 * Pelanggan dan item diambil dari pesanan, tidak disimpan ulang.
 */
class Invoice extends Model
{
    public const TYPE_UANG_MUKA = 'uang_muka';

    public const TYPE_TERMIN = 'termin';

    public const TYPE_PELUNASAN = 'pelunasan';

    public const STATUS_BELUM_DIBAYAR = 'belum_dibayar';

    public const STATUS_LUNAS = 'lunas';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const METHOD_TUNAI = 'tunai';

    public const METHOD_TRANSFER = 'transfer';

    protected $fillable = [
        'order_id',
        'type',
        'amount',
        'issued_at',
        'due_date',
        'status',
        'paid_at',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'issued_at' => 'date',
        'due_date' => 'date',
        'paid_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $invoice): void {
            $invoice->invoice_number ??= self::generateInvoiceNumber($invoice->issued_at ?? now());
        });

        static::saving(function (self $invoice): void {
            if ($invoice->status !== self::STATUS_LUNAS) {
                $invoice->paid_at = null;
                $invoice->payment_method = null;
            }
        });

        // Muat ulang relasi agar pesanan yang tersinkron sesuai order_id terbaru.
        static::saved(fn (self $invoice) => $invoice->load('order')->order?->syncPaymentStatus());
        static::deleted(fn (self $invoice) => $invoice->order?->syncPaymentStatus());
    }

    public static function types(): array
    {
        return [self::TYPE_UANG_MUKA, self::TYPE_TERMIN, self::TYPE_PELUNASAN];
    }

    public static function statuses(): array
    {
        return [self::STATUS_BELUM_DIBAYAR, self::STATUS_LUNAS, self::STATUS_DIBATALKAN];
    }

    public static function paymentMethods(): array
    {
        return [self::METHOD_TUNAI, self::METHOD_TRANSFER];
    }

    public static function generateInvoiceNumber(CarbonInterface $date): string
    {
        $prefix = 'INV-'.$date->format('ym').'-';

        $latest = static::query()
            ->where('invoice_number', 'like', $prefix.'%')
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $sequence = $latest ? (int) substr($latest, -4) + 1 : 1;

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }
}
