<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreBanner extends Model
{
    protected $table = 'store_banners';
    protected $fillable = [
        'user_id',
        'photo',
    ];
    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }
}
