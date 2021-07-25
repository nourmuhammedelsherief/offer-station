<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    protected $table = 'complains';
    protected $fillable = [
        'user_id',
        'complain'
    ];
    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }
}
