<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPUnit\Framework\Constraint\Count;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'en_name',
        'password',
        'phone_number',
        'photo',
        'api_token',
        'active',
        'verification_code',
        'email',
        'type',     // 1- user  2- store
        'commercial_register',
        'license',
        'work_times',
        'video_link',
        'contact_number',
        'store_url',
        'logo',
        'store_type_id',
        'latitude',
        'longitude',
        'city_id',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function store_type()
    {
        return $this->belongsTo(StoreType::class , 'store_type_id');
    }
    public function city()
    {
        return $this->belongsTo(City::class , 'city_id');
    }
    public function store_banners()
    {
        return $this->hasMany(StoreBanner::class , 'user_id');
    }
    public function user_offers()
    {
        return $this->hasMany(UserOffer::class  , 'user_id');
    }
}
