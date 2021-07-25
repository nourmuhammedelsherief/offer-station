<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';
    protected $fillable  = [
        'user_id' ,
        'offer_id',
        'report'
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
