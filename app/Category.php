<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'ar_name',
        'en_name',
        'photo',
    ];
    public function offers ()
    {
        return $this->hasMany(Offer::class , 'category_id');
    }
}
