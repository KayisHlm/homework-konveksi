<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $fillable = [
        'customer_id',
        'product_name',
        'payment_type',
        'payment_status',
        'name',
        'email',
        'phone',
        'address',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];  

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

}
