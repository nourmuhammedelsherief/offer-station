<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';
    protected $fillable = [
        'ar_name',
        'en_name',
    ];
    public function users()
    {
        return $this->hasMany(User::class , 'city_id');
    }
}
