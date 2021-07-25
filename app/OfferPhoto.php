<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfferPhoto extends Model
{
    protected $table = 'offer_photos';
    protected $fillable = [
        'offer_id',
        'photo',
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class , 'offer_id');
    }
}
