<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table = 'offers';
    protected $fillable = [
        'user_id',
        'title',
        'price_type',
        'price',
        'price_after_discount',
        'price_percent',
        'end_date',    // the date to end offer to users
        'offer_time',  // the date entered from control panel to terminate offer
        'max_quantity',
        'code',
        'details',
        'status',  /// 0 , 1
        'active',  /// 0 , 1
        'discriminate',   /// 0 , 1
        'end_discriminate',
        'transfer_photo',
        'photo',               // the default photo if the store don't have a photos
        'invoice_id',
        'external_link',
        'views_count',
//        'views_price',
        'remaining_views',
        'views',
        'discriminate_place_id', //  0-> pop up , 1-> slider , 2 -> category up , 3 -> category down
    ];

    protected $dates = [
        'end_date'  , 'offer_time'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class , 'category_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }
    public function photos()
    {
        return $this->hasMany(OfferPhoto::class , 'offer_id');
    }
    public function discriminate_place()
    {
        return $this->belongsTo(OfferDiscriminatePlaces::class , 'discriminate_place_id');
    }
    public function user_offers()
    {
        return $this->hasMany(UserOffer::class  , 'offer_id');
    }
}
