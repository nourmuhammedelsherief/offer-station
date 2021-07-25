<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreBannerResource extends JsonResource
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
            'user_id'  => $this->user_id,
            'photo'    => $this->photo == null ? null : asset('/uploads/store_banners/'.$this->photo),
        ];
    }
}
