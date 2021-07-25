<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->order != null)
        {
            return [
                'id'                    => $this->order->id,
                'status'                => $this->order->status,
                'user'                  => new User($this->order->user),
                'driver'                => $this->order->driver_id == null ? null : new User($this->order->driver),
                'latitude_from'         => $this->order->latitude_from,
                'longitude_from'        => $this->order->longitude_from,
                'latitude_to'           => $this->order->latitude_to,
                'longitude_to'          => $this->order->longitude_to,
                'type'                  => $this->order->type,
                'truck_type_id'         => new TruckType($this->order->truck_type),
                'created_at'            => $this->order->created_at->DiffForHumans(),
            ];
        }else{
            return [
                'id'                    => $this->id,
                'status'                => $this->status,
                'user'                  => new User($this->user),
                'driver'                => $this->driver_id == null ? null : new User($this->driver),
                'latitude_from'         => $this->latitude_from,
                'longitude_from'        => $this->longitude_from,
                'latitude_to'           => $this->latitude_to,
                'longitude_to'          => $this->longitude_to,
                'type'                  => $this->type,
                'truck_type_id'         => new TruckType($this->truck_type),
                'created_at'            => $this->created_at->DiffForHumans(),
            ];
        }
    }
}
