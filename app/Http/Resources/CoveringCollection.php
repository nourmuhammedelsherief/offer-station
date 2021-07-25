<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CoveringCollection extends ResourceCollection
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
                return [
                    'id'             =>$query->id,
                    'user'           => new User($query->user),
                    'video_link'     => $query->video_link,
                ];
            });
    }
}
