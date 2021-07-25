<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationCollection extends ResourceCollection
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
        return
            $this->collection->transform(function ($query){
                $locale = $this->lang;
                return [
                    'id'          =>$query->id,
                    'user'        =>$query->user->id,
                    'offer'       =>$query->offer == null ? null : $query->offer->id,
                    'title'       =>$locale == 'en' ? $query->en_title : $query->ar_title,
                    'message'     =>$locale == 'en' ? $query->en_message : $query->ar_message,
                    'type'        =>$query->type,
                    'created_at'  => $query->created_at->format('Y-m-d'),
                ];
            });
    }
}
