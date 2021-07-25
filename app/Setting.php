<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
    protected $fillable = [
        'search_range',
        'bearer_token',
        'sender_name',
        'sender_email',
        'user_verify',
        'store_verify',
        'contact_number',
        'offer_time',
        'views_count',
        'views_price',
        'account_number',
        'bank_name',
        'coverings_day_price',  // the day price  for covering section
        'IBAN_number',
        'offer_photo',
        'logo',
        'offer_activated',  // review  , not review
    ];
}
