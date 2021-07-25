<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferPhotoResource extends JsonResource
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
            'id'       => $this->id,
            'offer_id' => $this->offer_id,
            'photo'    => $this->photo == null ? null : asset('/uploads/offers/'.$this->photo),
        ];
    }
}
