<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreType extends Model
{
    protected $table = 'store_types';
    protected $fillable = [
        'ar_name',
        'en_name',
        'photo'
    ];
    public function users()
    {
        return $this->hasMany(User::class , 'store_type_id');
    }
}
