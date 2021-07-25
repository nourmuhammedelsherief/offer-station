<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StoreCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->lang = $request->header('Content-Language');
        $this->latitudeFrom = $request->latitude;
        $this->longitudeFrom = $request->longitude;
        return
            $this->collection->transform(function ($query){
                $locale = $this->lang;
                $latFrom = $this->latitudeFrom;
                $lonFrom = $this->longitudeFrom;
                $latTo   = $query->latitude;
                $lonTo   = $query->longitude;
                return [
                    'id'                   =>$query->id,
                    'name'                 =>$locale == 'en' ? $query->en_name : $query->name,
                    'phone_number'         =>$query->phone_number,
                    'email'                =>$query->email,
                    'store_type'           =>new StoreTypeResource($query->store_type),
                    'city'                 =>new CityResource($query->city),
                    'work_times'           =>$query->work_times,
                    'video_link'           =>$query->video_link,
                    'contact_number'       =>$query->contact_number,
                    'store_url'            =>$query->store_url,
                    'distance'             =>$query->store_type_id == 4 ? null :distanceBetweenTowPlaces($latFrom , $lonFrom , $latTo , $lonTo),
                    'photo'                =>$query->photo == null ? asset('/uploads/users/default.png') : asset('/uploads/users/'.$query->photo),
                    'logo'                 =>$query->photo == null ? asset('/uploads/users/default.png') : asset('/uploads/logos/'.$query->logo),
                    'commercial_register'  =>$query->commercial_register == null ? asset('/uploads/users/default.png') : asset('/uploads/commercial_registers/'.$query->commercial_register),
                    'license'              =>$query->license == null ? asset('/uploads/users/default.png') : asset('/uploads/licenses/'.$query->license),
                    'store_banners'        =>StoreBannerResource::collection($query->store_banners),
                ];
            });
    }
}
