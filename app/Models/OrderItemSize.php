<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Rincian Ukuran: jumlah pcs satu ukuran dalam sebuah item pesanan.
 */
class OrderItemSize extends Model
{
    protected $fillable = [
        'order_item_id',
        'size',
        'quantity',
        'extra_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'extra_price' => 'decimal:2',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    /** Harga per pcs ukuran ini = harga dasar item + tambahan ukuran. */
    public function unitPrice(): float
    {
        return (float) $this->item->unit_price + (float) $this->extra_price;
    }

    public function subtotal(): float
    {
        return $this->quantity * $this->unitPrice();
    }
}
