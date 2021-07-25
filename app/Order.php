<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'user_id',
        'latitude_from',
        'longitude_from',
        'latitude_to',
        'longitude_to',
        'delivery_price',
        'price',
        'status',
        'commission_status',
        'commission_value',
        'payment_type',
        'type',
        'truck_type_id',
        'driver_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }
    public function driver()
    {
        return $this->belongsTo(User::class , 'driver_id');
    }
    public function truck_type()
    {
        return $this->belongsTo(TruckType::class , 'truck_type_id');
    }
}
