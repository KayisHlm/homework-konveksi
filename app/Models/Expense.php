<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    public const CATEGORY_BAHAN_BAKU = 'bahan_baku';
    public const CATEGORY_SABLON = 'sablon';
    public const CATEGORY_BORDIR = 'bordir';
    public const CATEGORY_JAHIT = 'jahit';
    public const CATEGORY_TRANSPORTASI = 'transportasi';
    public const CATEGORY_OPERASIONAL = 'operasional';
    public const CATEGORY_LAINNYA = 'lainnya';

    protected $fillable = [
        'order_id',
        'procurement_id',
        'category',
        'description',
        'amount',
        'spent_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'spent_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function categories(): array
    {
        return [
            self::CATEGORY_BAHAN_BAKU,
            self::CATEGORY_SABLON,
            self::CATEGORY_BORDIR,
            self::CATEGORY_JAHIT,
            self::CATEGORY_TRANSPORTASI,
            self::CATEGORY_OPERASIONAL,
            self::CATEGORY_LAINNYA,
        ];
    }

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function procurement()
    {
        return $this->belongsTo(Procurement::class, 'procurement_id');
    }
}
