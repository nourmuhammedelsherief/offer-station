<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserOffer extends Model
{
    protected $table = 'user_offers';
    protected $fillable = [
        'user_id',
        'offer_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }
    public function offer()
    {
        return $this->belongsTo(Offer::class , 'offer_id');
    }
}
