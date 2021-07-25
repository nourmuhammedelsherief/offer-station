<?php

namespace App\Http\Resources;

use App\OfferDiscriminatePlaces;
use App\Setting;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OfferCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($query){
            if ($query->offer != null)
            {
                return [
                    'id'                   => $query->offer->id,
                    'user'                 => new User($query->offer->user),
                    'price'                => $query->offer->price,
                    'price_after_discount' => $query->offer->price_after_discount,
                    'price_percent'        => $query->offer->price_percent,
                    'title'                => $query->offer->title,
                    'end_date'             => $query->offer->end_date->format('Y-m-d'),
                    'offer_time'           => $query->offer->offer_time == null ? null : $query->offer->offer_time->format('Y-m-d'),
                    'max_quantity'         => $query->offer->max_quantity,
                    'code'                 => $query->offer->code,
                    'details'              => $query->offer->details,
                    'discriminate'         => $query->offer->discriminate,
                    'discriminate_place'   => $query->offer->discriminate_place_id == null ? null : OfferDiscriminatePlaces::find($query->offer->discriminate_place_id)->discriminate_place,
                    'views'                => $query->offer->views,
                    'remaining_views'      => $query->offer->remaining_views,
                    'offer_used_count'     => $query->offer->user_offers->count(),
                    'external_link'        => $query->offer->external_link == null ? null : $query->offer->external_link,
                    'photos'               => $query->offer->photos->count() > 0 ? OfferPhotoResource::collection($query->offer->photos) : asset('/uploads/offers/'.Setting::find(1)->offer_photo),
                    'created_at'           => $query->offer->created_at->diffForHumans(),
                ];
            }else{
                return [
                    'id'                   => $query->id,
                    'user'                 => new User($query->user),
                    'price'                => $query->price,
                    'price_after_discount' => $query->price_after_discount,
                    'price_percent'        => $query->price_percent,
                    'title'                => $query->title,
                    'end_date'             => $query->end_date->format('Y-m-d'),
                    'offer_time'           => $query->offer_time == null ? null : $query->offer_time->format('Y-m-d'),
                    'max_quantity'         => $query->max_quantity,
                    'code'                 => $query->code,
                    'details'              => $query->details,
                    'discriminate'         => $query->discriminate,
                    'discriminate_place'   => $query->discriminate_place_id == null ? null : OfferDiscriminatePlaces::find($query->discriminate_place_id)->discriminate_place,
                    'views'                => $query->views,
                    'remaining_views'      => $query->remaining_views,
                    'offer_used_count'     => $query->user_offers->count(),
                    'external_link'        => $query->external_link == null ? null : $query->external_link,
                    'photos'               => $query->photos->count() > 0 ? OfferPhotoResource::collection($query->photos) : asset('/uploads/offers/'.Setting::find(1)->offer_photo),
                    'created_at'           => $query->created_at->diffForHumans(),
                ];
            }
        });
    }
}
