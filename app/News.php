<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';
    protected $fillable = [
        'ar_title',
        'en_title',
        'ar_details',
        'en_details',
        'photo',
    ];
}
