<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Pesanan: objek pusat yang menghubungkan pelanggan, item, pengadaan, pengeluaran, dan invoice.
 */
class Orders extends Model
{
    public const PRODUCTION_STATUS_PESANAN_DITERIMA = 'pesanan_diterima';

    public const PRODUCTION_STATUS_MENUNGGU_BAHAN = 'menunggu_bahan';

    public const PRODUCTION_STATUS_PROSES_PRODUKSI = 'proses_produksi';

    public const PRODUCTION_STATUS_QUALITY_CONTROL = 'quality_control';

    public const PRODUCTION_STATUS_SELESAI = 'selesai';

    public const PAYMENT_STATUS_BELUM_BAYAR = 'belum_bayar';

    public const PAYMENT_STATUS_SEBAGIAN = 'sebagian';

    public const PAYMENT_STATUS_LUNAS = 'lunas';

    protected $table = 'orders';

    protected $fillable = [
        'customer_id',
        'design_notes',
        'due_date',
        'discount_amount',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $order): void {
            $order->order_number ??= self::generateOrderNumber($order->created_at ?? now());
            $order->production_status ??= self::PRODUCTION_STATUS_PESANAN_DITERIMA;
            $order->payment_status ??= self::PAYMENT_STATUS_BELUM_BAYAR;
        });
    }

    /** @return list<string> Urutan status produksi (SRS-HK-008). */
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

    /** @return list<string> */
    public static function paymentStatuses(): array
    {
        return [
            self::PAYMENT_STATUS_BELUM_BAYAR,
            self::PAYMENT_STATUS_SEBAGIAN,
            self::PAYMENT_STATUS_LUNAS,
        ];
    }

    public static function generateOrderNumber(CarbonInterface $date): string
    {
        $prefix = 'ORD-'.$date->format('ym').'-';

        $latest = static::query()
            ->where('order_number', 'like', $prefix.'%')
            ->orderByDesc('order_number')
            ->value('order_number');

        $sequence = $latest ? (int) substr($latest, -4) + 1 : 1;

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function procurements(): HasMany
    {
        return $this->hasMany(Procurement::class, 'order_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'order_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'order_id');
    }

    /** Hitung ulang total pesanan dari item (atribut turunan). */
    public function recalculateTotal(): void
    {
        $subtotal = (float) $this->items()->sum('subtotal');

        $this->total_price = max(0, $subtotal - (float) $this->discount_amount);
        $this->saveQuietly();
    }

    /** Sinkronkan cache status pembayaran dari invoice yang lunas. */
    public function syncPaymentStatus(): void
    {
        $paid = $this->amountPaid();
        $total = (float) $this->total_price;

        $this->payment_status = match (true) {
            $paid <= 0 => self::PAYMENT_STATUS_BELUM_BAYAR,
            $paid >= $total => self::PAYMENT_STATUS_LUNAS,
            default => self::PAYMENT_STATUS_SEBAGIAN,
        };
        $this->saveQuietly();
    }

    /** Total nominal invoice yang sudah lunas. */
    public function amountPaid(): float
    {
        return (float) $this->invoices()->where('status', Invoice::STATUS_LUNAS)->sum('amount');
    }

    /** Total nominal invoice yang masih berlaku (belum dibayar + lunas). */
    public function amountInvoiced(?Invoice $except = null): float
    {
        return (float) $this->invoices()
            ->where('status', '!=', Invoice::STATUS_DIBATALKAN)
            ->when($except?->exists, fn ($query) => $query->whereKeyNot($except->id))
            ->sum('amount');
    }

    /** Sisa tagihan = total pesanan - yang sudah dibayar. */
    public function remainingBalance(): float
    {
        return max(0, (float) $this->total_price - $this->amountPaid());
    }

    /** Nominal yang masih boleh ditagihkan lewat invoice baru. */
    public function billableBalance(?Invoice $except = null): float
    {
        return max(0, (float) $this->total_price - $this->amountInvoiced($except));
    }

    public function totalQuantity(): int
    {
        return (int) $this->items->sum('quantity');
    }

    /** Ringkasan produk untuk tabel daftar, mis. "Kaos Event, Topi". */
    public function productSummary(): string
    {
        return $this->items->pluck('product_name')->unique()->implode(', ') ?: '-';
    }

    public function nextProductionStatus(): ?string
    {
        $statuses = self::productionStatuses();
        $index = array_search($this->production_status, $statuses, true);

        return $index === false ? null : ($statuses[$index + 1] ?? null);
    }
}
