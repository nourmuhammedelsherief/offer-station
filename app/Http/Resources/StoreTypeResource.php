<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $locale = $request->header('Content-Language');
        return [
            'id'     => $this->id,
            'name'   => $locale == 'en' ? $this->en_name : $this->ar_name,
            'photo'  => $this->photo == null ? null : asset('/uploads/categories/'.$this->photo)
        ];
    }
}
