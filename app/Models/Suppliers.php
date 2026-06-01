<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suppliers extends Model
{
    //
    protected $table = 'suppliers';
    protected $fillable = [
        'pic_name',
        'store_name',
        'product_category',
        'phone',
        'address',
    ];
}
