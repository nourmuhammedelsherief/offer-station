<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\StoreCollection;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class StoreController extends Controller
{
    public function get_stores(Request $request)
    {
        $rules = [
            'store_type_id' => 'sometimes|exists:store_types,id',
            'city_id'       => 'sometimes|exists:store_types,id',
            'latitude'      => 'sometimes',
            'longitude'     => 'sometimes',
            'search'        => 'sometimes',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        if ($request->store_type_id == '4')
        {
            $stores = User::where('store_type_id' , $request->store_type_id)
                ->where('active' , '1')
                ->where('type' , '2')
                ->simplePaginate();
        }elseif ($request->search){
            $stores = User::where('name', 'LIKE', "%{$request->search}%")
                ->orWhere('en_name', 'LIKE', "%{$request->search}%")
                ->where('active' , '1')
                ->where('type' , '2')
                ->simplePaginate();
        }elseif($request->city_id != null && $request->store_type_id != null){
            $range = Setting::find(1)->search_range;
            $lat = $request->latitude;
            $lon = $request->longitude;
            $stores = User::with('city')
//                ->whereHas('city' , function ($q) use ($request){
//                    $q->where('city_id' ,  $request->city_id); // driver that have the same truck that user search
//                })
                ->selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude) ) ) ) AS distance', [$lat, $lon, $lat])
                ->having('distance', '<=',  $range)
                ->where('type' , '2')
                ->where('active' , '1')
                ->where('store_type_id' , $request->store_type_id)
                ->where('city_id' , $request->city_id)
                ->orderBy('distance')
                ->simplePaginate();
        }elseif ($request->store_type_id != null){
            $range = Setting::find(1)->search_range;
            $lat = $request->latitude;
            $lon = $request->longitude;
            $stores = User::with('city')
//                ->whereHas('city' , function ($q) use ($request){
//                    $q->where('city_id' ,  $request->city_id); // driver that have the same truck that user search
//                })
                ->selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude) ) ) ) AS distance', [$lat, $lon, $lat])
                ->having('distance', '<=',  $range)
                ->where('type' , '2')
                ->where('active' , '1')
                ->where('store_type_id' , $request->store_type_id)
                ->orderBy('distance')
                ->simplePaginate();
        }elseif ($request->city_id != null){
            $stores = User::where('type' , '2')
                ->where('active' , '1')
                ->where('city_id' , $request->city_id)
                ->simplePaginate();
        }else{
            $stores = User::where('type' , '2')
                ->where('active' , '1')
                ->simplePaginate();
        }
//        $stores = User::where('store_type_id' , $request->store_type_id)
//            ->where('active' , '1')
//            ->paginate();
        if ($stores->count() > 0)
        {

            return (new StoreCollection($stores));
//            return ApiController::respondWithSuccess(new StoreCollection($stores));
        }else{
            $errors = [
                'key'   => 'get_stores',
                'value' => trans('messages.no_stores'),
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }
}
