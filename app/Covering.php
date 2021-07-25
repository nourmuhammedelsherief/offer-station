<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Covering extends Model
{
    protected $table = 'coverings';
    protected $fillable = [
        'user_id',
        'video_link',
        'end_date',
        'days',
        'price',
        'status',
        'invoice_id',
        'transfer_photo',
    ];
    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }
}
