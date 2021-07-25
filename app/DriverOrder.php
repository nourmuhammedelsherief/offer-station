<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverOrder extends Model
{
    protected $table = 'driver_orders';
    protected $fillable = [
        'driver_id',
        'order_id',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class , 'order_id');
    }
    public function driver()
    {
        return $this->belongsTo(User::class , 'driver_id');
    }
}
