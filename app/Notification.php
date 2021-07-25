<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    protected $fillable = [
        'user_id',
        'type',
        'ar_message',
        'en_message',
        'ar_title',
        'en_title',
        'offer_id'
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
