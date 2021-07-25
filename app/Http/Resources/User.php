<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class User extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $locale = $request->header('Content-Language');
        if ($this->type == 1)
        {
            // User
            return [
                'id'            =>$this->id,
                'name'          =>$this->name,
                'email'         =>$this->email,
                'phone_number'  =>$this->phone_number,
                'active'        =>intval($this->active),
                'type'          =>intval($this->type),
                'photo'         =>$this->photo == null ? asset('/uploads/users/default.png') : asset('/uploads/users/'.$this->photo),
                'api_token'     =>$this->api_token,
                'created_at'    =>$this->created_at->format('Y-m-d'),
            ];
        }elseif ($this->type == 2)
        {
            // Store
            return [
                'id'                   =>$this->id,
                'name'                 =>$locale == 'en' ? $this->en_name : $this->name,
                'phone_number'         =>$this->phone_number,
                'email'                =>$this->email,
                'store_type'           =>new StoreTypeResource($this->store_type),
                'city'                 =>new CityResource($this->city),
                'work_times'           =>$this->work_times,
                'video_link'           =>$this->video_link,
                'contact_number'       =>$this->contact_number,
                'store_url'            =>$this->store_url,
                'active'               =>intval($this->active),
                'type'                 =>intval($this->type),
                'latitude'             => $this->latitude,
                'longitude'            => $this->longitude,
                'photo'                =>$this->photo == null ? asset('/uploads/users/default.png') : asset('/uploads/users/'.$this->photo),
                'logo'                 =>$this->photo == null ? asset('/uploads/users/default.png') : asset('/uploads/logos/'.$this->logo),
                'commercial_register'  =>$this->commercial_register == null ? asset('/uploads/users/default.png') : asset('/uploads/commercial_registers/'.$this->commercial_register),
                'license'              =>$this->license == null ? asset('/uploads/users/default.png') : asset('/uploads/licenses/'.$this->license),
                'store_banners'        =>StoreBannerResource::collection($this->store_banners),
                'api_token'            =>$this->api_token,
                'created_at'           =>$this->created_at->format('Y-m-d'),
            ];
        }
    }
}
