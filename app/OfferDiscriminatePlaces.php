<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfferDiscriminatePlaces extends Model
{
    protected $table = 'offer_discriminate_places';
    protected $fillable = [
        'views_count',
        'views_price',
        'discriminate_place', //  0-> pop up , 1-> slider , 2 -> category up , 3 -> category down
    ];

}
