<?php

namespace App\Http\Resources;

use App\OfferDiscriminatePlaces;
use App\Setting;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                   => $this->id,
            'user'                 => new User($this->user),
            'price'                => $this->price,
            'price_after_discount' => $this->price_after_discount,
            'price_percent'        => $this->price_percent,
            'title'                => $this->title,
            'end_date'             => $this->end_date->format('Y-m-d'),
            'offer_time'           => $this->offer_time == null ? null : $this->offer_time->format('Y-m-d'),
            'max_quantity'         => $this->max_quantity,
            'code'                 => $this->code,
            'details'              => $this->details,
            'discriminate'         => $this->discriminate,
            'discriminate_place'   => $this->discriminate_place_id == null ? null : OfferDiscriminatePlaces::find($this->discriminate_place_id)->discriminate_place,
            'views'                => $this->views,
            'remaining_views'      => $this->remaining_views,
            'offer_used_count'     => $this->user_offers->count(),
            'external_link'        => $this->external_link == null ? null : $this->external_link,
            'photos'               => $this->photos->count() > 0 ? OfferPhotoResource::collection($this->photos) : asset('/uploads/offers/'.Setting::find(1)->offer_photo),
            'created_at'           => $this->created_at->diffForHumans(),
        ];
    }
}
