<?php

namespace App\Models;

use App\Support\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Item Pesanan: satu produk (nama, bahan, warna, harga dasar) dengan rincian jumlah per ukuran.
 * quantity & subtotal adalah nilai turunan dari rincian ukuran.
 */
class OrderItem extends Model
{
    public const SIZES = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', 'All Size'];

    protected $fillable = [
        'order_id',
        'product_name',
        'material',
        'color',
        'unit_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function sizes(): HasMany
    {
        // Urutkan sesuai urutan ukuran baku, bukan urutan input.
        return $this->hasMany(OrderItemSize::class, 'order_item_id')
            ->orderByRaw('CASE size '.collect(self::SIZES)->map(fn ($size, $i) => "WHEN '{$size}' THEN {$i}")->implode(' ').' END');
    }

    /** Hitung ulang qty & subtotal dari rincian ukuran. */
    public function recalculate(): void
    {
        $sizes = $this->sizes()->get()->each->setRelation('item', $this);

        $this->quantity = (int) $sizes->sum('quantity');
        $this->subtotal = $sizes->sum(fn (OrderItemSize $size) => $size->subtotal());
        $this->save();
    }

    /** Ringkasan ukuran untuk tampilan, mis. "S 10 · M 20 · XXL 2 (+Rp 5.000)". */
    public function sizeSummary(): string
    {
        return $this->sizes
            ->map(fn (OrderItemSize $size) => $size->size.' '.$size->quantity
                .((float) $size->extra_price > 0 ? ' (+'.Status::rupiah($size->extra_price).')' : ''))
            ->implode(' · ');
    }
}
