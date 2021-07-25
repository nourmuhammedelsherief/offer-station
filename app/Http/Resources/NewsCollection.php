<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class NewsCollection extends ResourceCollection
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
                    'title'       =>$locale == 'en' ? $query->en_title : $query->ar_title,
                    'details'     =>$locale == 'en' ? $query->en_details : $query->ar_details,
                    'photo'       =>$query->photo == null ? null : asset('/uploads/news/'.$query->photo),
                    'created_at'  => $query->created_at->format('Y-m-d'),
                ];
            });
    }
}
