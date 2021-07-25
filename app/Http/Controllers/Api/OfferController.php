<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\DriverOrder;
use App\Favorite;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\NewsCollection;
use App\Http\Resources\OfferCollection;
use App\Http\Resources\OfferResource;
use App\News;
use App\Offer;
use App\OfferDiscriminatePlaces;
use App\OfferPhoto;
use App\Order;
use App\Report;
use App\Setting;
use App\User;
use App\UserDevice;
use App\UserOffer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class OfferController extends Controller
{
//    public function categories()
//    {
//        $categories = Category::all();
//        if ($categories->count() > 0)
//        {
//            return ApiController::respondWithSuccess(CategoryResource::collection($categories));
//        }else{
//            $errors = [
//                'key'    => 'categories',
//                'value'  => trans('messages.no_categories')
//            ];
//            return ApiController::respondWithErrorAuthArray(array($errors));
//        }
//    }
    public function create_offer(Request $request)
    {
        $rules = [
            'title' => 'required',
            'price_type' => 'required|in:0,1',
            'price' => 'required',
            'price_after_discount' => 'required_without:price_percent',
            'price_percent' => 'required_without:price_after_discount',
            'end_date' => 'required|date',
            'max_quantity' => 'required|numeric',
            'code' => 'required',
            'external_link' => 'sometimes',
            'details' => 'sometimes',
            'photos*' => 'sometimes|mimes:jpg,jpeg,png,gif,tif,bmp,bsd|max:5000',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        // create new offer
        $status = Setting::find(1)->offer_activated;
        if ($status == 'review') {
            $end_date = null;
            $is_active = '0';
        } else {
            $offer_time = Setting::find(1)->offer_time;
            $end_date = Carbon::now()->addDays($offer_time);
            $is_active = '1';
        }

        $offer = Offer::create([
            'user_id' => $request->user()->id,
            'price_type' => $request->price_type,
            'price' => $request->price == null ? null : $request->price,
            'price_after_discount' => $request->price_after_discount == null ? null : $request->price_after_discount,
            'price_percent' => $request->price_percent == null ? null : $request->price_percent,
            'title' => $request->title,
            'end_date' => $request->end_date,
            'offer_time' => $end_date,
            'external_link' => $request->external_link == null ? null : $request->external_link,
            'max_quantity' => $request->max_quantity,
            'code' => $request->code,
            'details' => $request->details,
            'status' => '0',
            'active' => $is_active,
        ]);
        // create offer photos
        $name = $request->file('photos');
        $fileFinalName_ar = "";
        if ($name != "") {
            if ($files = $name) {
                foreach ($files as $file) {
                    $images = new OfferPhoto();
                    $fileFinalName_ar = time() . rand(1111,
                            9999) . '.' . $file->getClientOriginalExtension();
                    $path = base_path() . "/public/uploads/offers";
                    $images->offer_id = $offer->id;
                    $images->photo = $fileFinalName_ar;
                    $images->save();
                    $file->move($path, $fileFinalName_ar);
                }
            }
        } else {
            $offer->update([
                'photo' => Setting::find(1)->offer_photo,
            ]);
        }
        // send Notification to users
        $users = User::whereType('1')
            ->where('active', '1')
            ->get();
        if ($users->count() > 0) {
            foreach ($users as $user) {
                $ar_title = 'العروض';
                $en_title = 'Offers';
                $ar_message = 'تم أضافه عرض جديد من قبل أحد المتاجر';
                $en_message = 'A new offer has been added by a store';
                $devicesTokens = UserDevice::where('user_id', $user->id)
                    ->get()
                    ->pluck('device_token')
                    ->toArray();
                if ($devicesTokens) {
                    sendMultiNotification($ar_title, $ar_message, $devicesTokens);
                }
                saveNotification($user->id, $ar_title, $en_title, $ar_message, $en_message, '1', $offer->id);
            }
        }
        $success = [
            'key' => 'create_offer',
            'value' => trans('messages.offerCreatedSuccessfully')
        ];
        if ($is_active == 'review') {
            return ApiController::respondWithSuccess($success);
        } else {
            return ApiController::respondWithSuccess(new OfferResource($offer));
        }
    }

    public function edit_offer(Request $request, $id)
    {
        $offer = Offer::find($id);
        if ($offer) {
            $rules = [
                'title' => 'sometimes',
                'price_type' => 'sometimes|in:0,1',
                'price' => 'sometimes',
                'price_after_discount' => 'sometimes_without:price_percent',
                'price_percent' => 'sometimes_without:price_after_discount',
                'end_date' => 'sometimes|date',
                'max_quantity' => 'sometimes',
                'code' => 'sometimes',
                'details' => 'sometimes',
                'external_link' => 'sometimes',
                'photos*' => 'sometimes|mimes:jpg,jpeg,png,gif,tif,bmp,bsd|max:5000',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

            $offer->update([
                'user_id' => $request->user()->id,
                'price_type' => $request->price_type == null ? $offer->price_type : $request->price_type,
                'price' => $request->price == null ? $offer->price : $request->price,
                'price_after_discount' => $request->price_after_discount == null ? $offer->price_after_discount : $request->price_after_discount,
                'price_percent' => $request->price_percent == null ? $offer->price_percent : $request->price_percent,
                'title' => $request->title == null ? $offer->title : $request->title,
                'end_date' => $request->end_date == null ? $offer->end_date : $request->end_date,
                'max_quantity' => $request->max_quantity == null ? $offer->max_quantity : $request->max_quantity,
                'code' => $request->code == null ? $offer->code : $request->code,
                'external_link' => $request->external_link == null ? $offer->external_link : $request->external_link,
                'details' => $request->details == null ? $offer->details : $request->details,
            ]);
            // create offer photos
            $name = $request->file('photos');
            $fileFinalName_ar = "";
            if ($name != "") {
                if ($files = $name) {
                    foreach ($files as $file) {
                        $images = new OfferPhoto();
                        $fileFinalName_ar = time() . rand(1111,
                                9999) . '.' . $file->getClientOriginalExtension();
                        $path = base_path() . "/public/uploads/offers";
                        $images->offer_id = $offer->id;
                        $images->photo = $fileFinalName_ar;
                        $images->save();
                        $file->move($path, $fileFinalName_ar);
                    }
                }
            }
//            return ApiController::respondWithSuccess($success);
            return ApiController::respondWithSuccess(new OfferResource($offer));
        } else {
            $errors = [
                'key' => 'edit_offer',
                'value' => trans('messages.offer_not_found')
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }

    public function delete_offer($id)
    {
        $offer = Offer::find($id);
        if ($offer) {
            $offer_photos = OfferPhoto::whereOfferId($id)->get();
            if ($offer_photos->count() > 0) {
                foreach ($offer_photos as $photo) {
                    if (file_exists(public_path('uploads/offers/' . $photo->photo))) {
                        unlink(public_path('uploads/offers/' . $photo->photo));
                    }
                    $photo->delete();
                }
            }
            $offer->delete();
            $success = [
                'key' => 'delete_offer',
                'value' => trans('messages.offerDeleted')
            ];
            return ApiController::respondWithSuccess($success);
        } else {
            $errors = [
                'key' => 'categories',
                'value' => trans('messages.offer_not_found')
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }

    public function delete_offer_photo($id)
    {
        $photo = OfferPhoto::find($id);
        if ($photo) {
            if (file_exists(public_path('uploads/offers/' . $photo->photo))) {
                unlink(public_path('uploads/offers/' . $photo->photo));
            }
            $photo->delete();
            $success = [
                'key' => 'delete_offer',
                'value' => trans('messages.Photo_successfully_deleted')
            ];
            return ApiController::respondWithSuccess($success);
        } else {
            $errors = [
                'key' => 'delete_offer_photo',
                'value' => trans('messages.photo_not_found')
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }
    public function my_discriminate_offers(Request $request)
    {
        $offers = Offer::whereUserId($request->user()->id)
            ->where('status', '0')
            ->where('discriminate' , '1')
            ->orderBy('created_at', 'desc')
            ->simplePaginate();
        if ($offers->count() > 0) {
            return new OfferCollection($offers);
//            return ApiController::respondWithSuccess(new OfferCollection($offers));
        } else {
            $errors = [
                'key' => 'my_discriminate_offers',
                'value' => trans('messages.NoOffers'),
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }

    public function my_offers(Request $request)
    {
        $offers = Offer::whereUserId($request->user()->id)
            ->where('status', '0')
            ->orderBy('created_at', 'desc')
            ->simplePaginate();
        if ($offers->count() > 0) {
            return new OfferCollection($offers);
//            return ApiController::respondWithSuccess(new OfferCollection($offers));
        } else {
            $errors = [
                'key' => 'my_offers',
                'value' => trans('messages.NoOffers'),
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
    public function offers_by_store_id($id)
    {
        $offers = Offer::whereUserId($id)
            ->where('status', '0')
            ->orderBy('created_at', 'desc')
            ->simplePaginate();
        if ($offers->count() > 0) {
            return new OfferCollection($offers);
//            return ApiController::respondWithSuccess(new OfferCollection($offers));
        } else {
            $errors = [
                'key' => 'offers_by_store_id',
                'value' => trans('messages.NoOffers'),
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }

    public function offers()
    {
        $offers = Offer::with('discriminate_place')
            ->where('status', '0')
            ->where('active', '1')
            ->simplePaginate();
//        $offers = Offer::with('discriminate_place')
//            ->where('discriminate_place_id', null)
//            ->where('status', '0')
//            ->where('active', '1')
//            ->orWhereHas('discriminate_place', function ($q) {
//                $q->where('discriminate_place', '!=', '0');
//                $q->where('discriminate_place', '!=', '1');
//            })
//            ->where('status', '0')
//            ->where('active', '1')
//            ->simplePaginate();
        if ($offers->count() > 0) {
            return new OfferCollection($offers);
//            return ApiController::respondWithSuccess(new OfferCollection($offers));
        } else {
            $errors = [
                'key' => 'offers',
                'value' => trans('messages.NoOffers'),
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }

    public function filter_search(Request $request)
    {
        $rules = [
            'search'        => 'sometimes',
            'store_type_id' => 'sometimes|exists:store_types,id',
            'city_id'       => 'sometimes|exists:cities,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        if ($request->search != null)
        {
            $offers = Offer::with('discriminate_place')
                ->whereHas('discriminate_place', function ($q) {
                    $q->where('discriminate_place', '!=', '0');
                    $q->where('discriminate_place', '!=', '1');
                })
                ->where('status', '0')
                ->where('title' , 'LIKE' , "%{$request->search}%")
                ->orWhere('discriminate_place_id', null)
                ->where('title' , 'LIKE' , "%{$request->search}%")
                ->where('status', '0')
                ->paginate();
        }elseif ($request->store_type_id != null && $request->city_id == null){
            $offers = Offer::with('discriminate_place' , 'user')
                ->whereHas('discriminate_place', function ($q) {
                    $q->where('discriminate_place', '!=', '0');
                    $q->where('discriminate_place', '!=', '1');
                })
                ->where('status', '0')
                ->whereHas('user' , function ($q) use ($request) {
                    $q->where('store_type_id' , $request->store_type_id);
                })
                ->orWhere('discriminate_place_id', null)
                ->whereHas('user' , function ($q) use ($request) {
                    $q->where('store_type_id' , $request->store_type_id);
                })
                ->where('status', '0')
                ->paginate();
        }elseif ($request->city_id != null && $request->store_type_id == null){
            $offers = Offer::with('discriminate_place' , 'user')
                ->whereHas('discriminate_place', function ($q) {
                    $q->where('discriminate_place', '!=', '0');
                    $q->where('discriminate_place', '!=', '1');
                })
                ->where('status', '0')
                ->whereHas('user' , function ($q) use ($request) {
                    $q->where('city_id' , $request->city_id);
                })
                ->orWhere('discriminate_place_id', null)
                ->whereHas('user' , function ($q) use ($request) {
                    $q->where('city_id' , $request->city_id);
                })
                ->where('status', '0')
                ->paginate();
        }elseif ($request->store_type_id != null && $request->city_id != null){
            $offers = Offer::with('discriminate_place' , 'user')
                ->whereHas('discriminate_place', function ($q) {
                    $q->where('discriminate_place', '!=', '0');
                    $q->where('discriminate_place', '!=', '1');
                })
                ->where('status', '0')
                ->whereHas('user' , function ($q) use ($request) {
                    $q->where('store_type_id' , $request->store_type_id);
                })
                ->whereHas('user' , function ($q) use ($request) {
                    $q->where('city_id' , $request->city_id);
                })
                ->orWhere('discriminate_place_id', null)
                ->whereHas('user' , function ($q) use ($request) {
                    $q->where('store_type_id' , $request->store_type_id);
                })
                ->whereHas('user' , function ($q) use ($request) {
                    $q->where('city_id' , $request->city_id);
                })
                ->where('status', '0')
                ->paginate();
        }else{
            $offers = Offer::with('discriminate_place')
                ->whereHas('discriminate_place', function ($q) {
                    $q->where('discriminate_place', '!=', '0');
                    $q->where('discriminate_place', '!=', '1');
                })
                ->where('status', '0')
                ->orWhere('discriminate_place_id', null)
                ->where('status', '0')
                ->paginate();
        }
        if ($offers->count() > 0) {
            return new OfferCollection($offers);
//            return ApiController::respondWithSuccess(new OfferCollection($offers));
        } else {
            $errors = [
                'key' => 'offers',
                'value' => trans('messages.NoOffers'),
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }

    public function discriminate_offers($id)
    {
        $offers = Offer::with('discriminate_place')
            ->whereHas('discriminate_place', function ($q) use ($id) {
                $q->where('discriminate_place', $id);
            })
            ->where('status', '0')
            ->where('discriminate', '1')
            ->orderBy('created_at', 'desc')
            ->paginate();
        if ($offers->count() > 0) {
            return ApiController::respondWithSuccess(new OfferCollection($offers));
        } else {
            $errors = [
                'key' => 'offers',
                'value' => trans('messages.NoOffers'),
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }

    public function finished_offers(Request $request)
    {
        $offers = Offer::whereUserId($request->user()->id)
            ->where('status', '1')
            ->orderBy('created_at', 'desc')
            ->paginate();
        if ($offers->count() > 0) {
            return ApiController::respondWithSuccess(new OfferCollection($offers));
        } else {
            $errors = [
                'key' => 'finished_offers',
                'value' => trans('messages.NoOffers'),
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }

    public function activate_finished_offer(Request $request, $id)
    {
        $offer = Offer::whereId($id)
            ->where('user_id', $request->user()->id)
            ->first();
        if ($offer) {
            $rules = [
                'end_date' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

            $offer->update([
                'status' => '0',
                'end_date' => $request->end_date,
            ]);
            $success = [
                'key' => 'activate_finished_offer',
                'value' => trans('messages.offerActivated')
            ];
            return ApiController::respondWithSuccess($success);
        } else {
            $errors = [
                'key' => 'activate_finished_offer',
                'value' => trans('messages.offer_not_found')
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }

    public function get_offers(Request $request, $id)
    {
        $order = Order::find($id);
        if ($order) {
            if ($order->user_id == $request->user()->id) {
                $offers = Offer::whereOrderId($order->id)
                    ->where('status', '0')
                    ->orderBy('created_at', 'desc')
                    ->get();
                if ($offers->count() > 0) {
                    return ApiController::respondWithSuccess(\App\Http\Resources\Offer::collection($offers));
                } else {
                    $errors = [
                        'key' => 'Offers',
                        'value' => trans('messages.NoOffers'),
                    ];
                    return ApiController::respondWithErrorArray(array($errors));
                }
            } else {
                $errors = [
                    'key' => 'offers',
                    'value' => trans('messages.orderNotBelongToYou')
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }
        } else {
            $errors = [
                'key' => 'offers',
                'value' => trans('messages.order_not_found')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }

    }

    /**
     *  discriminate_offer
     *  by bank transfer
     *  or by using myfatoourah
     */
    public function discriminate_offer(Request $request, $id)
    {
        $offer = Offer::whereUserId($request->user()->id)
            ->where('id', $id)
            ->first();
        if ($offer) {
            $rules = [
                'payment_method' => 'required|in:0,1',  // 0  -> bank transfer   , 1 -> myfatoourah
                'views_count' => 'required|numeric',
                'views_price' => 'required|numeric',
                'discriminate_place_id' => 'required|exists:offer_discriminate_places,id', //  0-> pop up , 1-> slider , 2 -> category up , 3 -> category down
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

            if ($request->payment_method == '0') {
                // bank transfer
                $rules = [
                    'transfer_photo' => 'required|mimes:jpg,jpeg,png,gif,tif,png,bmp,bsd|max:5000',
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails())
                    return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

                $offer->update([
                    'transfer_photo' => $request->file('transfer_photo') == null ? null : UploadImage($request->file('transfer_photo'), 'transfer', '/uploads/transfer_photos'),
                    'views_count' => $request->views_count,
                    'remaining_views' => $request->views_count,
                    'views_price' => $request->views_price,
                    'discriminate_place_id' => $request->discriminate_place_id,
                ]);
                $success = [
                    'key' => 'discriminate_offer',
                    'value' => trans('messages.success_transfer')
                ];
                return ApiController::respondWithSuccess($success);
            } elseif ($request->payment_method == '1') {
                $rules = [
                    'payment_type' => 'required|in:1,2,3'
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails())
                    return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

                // payment by myFatoourah
                $amount = $request->views_price;
                $user = $request->user();
                if ($request->payment_type == 1) {
                    $charge = 2;
                } // visa && master
                elseif ($request->payment_type == 2) {
                    $charge = 6;
                } // mada
                elseif ($request->payment_type == 3) {
                    $charge = 11;
                } // Apple payment
                $token = "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";
                $data = "{\"PaymentMethodId\":\"$charge\",\"CustomerName\": \"$user->name\",\"DisplayCurrencyIso\": \"SAR\",
                \"MobileCountryCode\":\"+966\",\"CustomerMobile\": \"$user->phone_number\",
                \"CustomerEmail\": \"email@mail.com\",\"InvoiceValue\": $amount,\"CallBackUrl\": \"https://myoffersstation.com/check-status\",
                \"ErrorUrl\": \"https://youtube.com\",\"Language\": \"ar\",\"CustomerReference\" :\"ref 1\",
                \"CustomerCivilId\":12345678,\"UserDefinedField\": \"Custom field\",\"ExpireDate\": \"\",
                \"CustomerAddress\" :{\"Block\":\"\",\"Street\":\"\",\"HouseBuildingNo\":\"\",\"Address\":\"\",\"AddressInstructions\":\"\"},
                \"InvoiceItems\": [{\"ItemName\": \"$user->name\",\"Quantity\": 1,\"UnitPrice\": $amount}]}";
                $fatooraRes = MyFatoorah($token, $data);
                $result = json_decode($fatooraRes);
                if ($result->IsSuccess === true) {
//            return redirect($result->Data->PaymentURL);
                    if ($result->IsSuccess === true) {
                        $offer->update([
                            'invoice_id' => $result->Data->InvoiceId,
                            'views_count' => $request->views_count,
                            'views_price' => $request->views_price,
                            'remaining_views' => $request->views_count,
                            'discriminate_place_id' => $request->discriminate_place_id,
                        ]);
                        $all = [];
                        array_push($all, [
                            'key' => 'discriminate_offer',
                            'payment_url' => $result->Data->PaymentURL,
                        ]);
                        return ApiController::respondWithSuccess($all);
                    }
                }
            }

        } else {
            $errors = [
                'key' => 'discriminate_offer',
                'value' => trans('messages.offer_not_found')
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }

    public function fatooraStatus()
    {
        $token = "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";
        $PaymentId = \Request::query('paymentId');
        $resData = MyFatoorahStatus($token, $PaymentId);
        $result = json_decode($resData);
        if ($result->IsSuccess === true && $result->Data->InvoiceStatus === "Paid") {
            $InvoiceId = $result->Data->InvoiceId;
            $offer = Offer::where('invoice_id', $InvoiceId)->first();
            $offer->update([
                'invoice_id' => null,
                'discriminate' => '1',
            ]);

            return redirect()->to('/fatoora/success');
        }
    }

    public function get_offer_discriminate_info()
    {
        $places = OfferDiscriminatePlaces::all();
        $arr = [];
        foreach ($places as $place) {
            array_push($arr, [
                'id' => $place->id,
                'views_count' => $place->views_count,
                'views_price' => $place->views_price,
                'discriminate_place' => $place->discriminate_place,
            ]);
        }
        return ApiController::respondWithSuccess($arr);
    }

    public function bank_info()
    {
        $arr = [
            'bank_name' => Setting::find(1)->bank_name,
            'account_number' => Setting::find(1)->account_number,
            'IBAN_number' => Setting::find(1)->IBAN_number,
        ];
        return ApiController::respondWithSuccess($arr);
    }

    public function user_view_offer(Request $request, $id)
    {
        $offer = Offer::find($id);
        if ($offer) {
            if ($offer->discriminate == '1') {
                $remaining_views = $offer->remaining_views - 1;
                $views = $offer->views + 1;
                $offer->update([
                    'remaining_views' => $remaining_views,
                    'views' => $views,
                ]);
            } else {
                $views = $offer->views + 1;
                $offer->update([
                    'views' => $views,
                ]);
            }
            return ApiController::respondWithSuccess([
                'key' => 'user_view_offer',
                'value' => 'success',
            ]);
        } else {
            $errors = [
                'key' => 'user_view_offer',
                'value' => trans('messages.offer_not_found'),
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }

    public function customer_use_offer(Request $request, $id)
    {
        $offer = Offer::find($id);
        if ($offer) {
            if ($offer->end_date < Carbon::now() || $offer->status == '1') {
                $errors = [
                    'key' => 'customer_use_offer',
                    'value' => trans('messages.offerDateTerminated'),
                ];
                return ApiController::respondWithErrorAuthArray(array($errors));
            }
            $user_offer_check = UserOffer::whereUserId($request->user()->id)
                ->where('offer_id', $offer->id)
                ->first();
            if ($user_offer_check == null) {
                /**
                 *  use this offer
                 *  create new user offer UserOffer App
                 */
                UserOffer::create([
                    'user_id' => $request->user()->id,
                    'offer_id' => $offer->id,
                ]);
                $success = [
                    'key' => 'customer_use_offer',
                    'value' => trans('messages.offerUserSuccessfully'),
                ];
                return ApiController::respondWithSuccess($success);
            } else {
                $errors = [
                    'key' => 'customer_use_offer',
                    'value' => trans('messages.offerUserBefore'),
                ];
                return ApiController::respondWithErrorAuthArray(array($errors));
            }
        } else {
            $errors = [
                'key' => 'customer_use_offer',
                'value' => trans('messages.offer_not_found'),
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }

    public function get_offer_by_id($id)
    {
        $offer = Offer::find($id);
        if ($offer) {
            return new OfferResource($offer);
        } else {
            $errors = [
                'key' => 'customer_use_offer',
                'value' => trans('messages.offer_not_found'),
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }

    public function customer_offers(Request $request)
    {
        $user_offers = UserOffer::whereUserId($request->user()->id)->simplePaginate();
        if ($user_offers->count() > 0) {
            return new OfferCollection($user_offers);
        } else {
            $errors = [
                'key' => 'customer_offers',
                'value' => trans('messages.no_offers_used')
            ];
            return ApiController::respondWithSuccess(array($errors));
        }
    }

    public function get_news()
    {
        $news = News::simplePaginate();
        if ($news->count() > 0) {
            return new NewsCollection($news);
        } else {
            $errors = [
                'key' => 'get_news',
                'value' => trans('messages.noNewsFound')
            ];
            return ApiController::respondWithSuccess(array($errors));
        }
    }
    /**
     *  Add Offer to Favorite
     * @add_offer_to_favorite
     */
    public function add_offer_to_favorite(Request $request , $id)
    {
        $offer = Offer::find($id);
        if ($offer)
        {
            $checkOffer = Favorite::whereUserId($request->user()->id)
                ->where('offer_id' , $offer->id)->first();
            if ($checkOffer == null)
            {
                // add offer to favorite
                Favorite::create([
                    'user_id'   => $request->user()->id,
                    'offer_id'  => $offer->id,
                ]);
                $success = [
                    'key'   => 'add_offer_to_favorite',
                    'value' => trans('messages.offerFavoriteSuccessfully')
                ];
                return ApiController::respondWithSuccess($success);
            }else{
                $errors = [
                    'key'   => 'add_offer_to_favorite',
                    'value' => trans('messages.offerFavoriteBefore'),
                ];
                return ApiController::respondWithErrorAuthArray(array($errors));
            }
        }else{
            $errors = [
                'key'   => 'add_offer_to_favorite',
                'value' => trans('messages.offer_not_found'),
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }
    public function remove_favorite_offer(Request $request , $id)
    {
        $offer = Offer::find($id);
        if ($offer)
        {
            $checkOffer = Favorite::whereUserId($request->user()->id)
                ->where('offer_id' , $offer->id)->first();
            if ($checkOffer != null)
            {
                $checkOffer->delete();
                $success = [
                    'key'   => 'remove_favorite_offer',
                    'value' => 'success'
                ];
                return ApiController::respondWithSuccess($success);
            }else{
                $errors = [
                    'key'   => 'remove_favorite_offer',
                    'value' => 'not found'
                ];
                return ApiController::respondWithErrorAuthArray(array($errors));
            }
        }else{
            $errors = [
                'key'   => 'add_offer_to_favorite',
                'value' => trans('messages.offer_not_found'),
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }
    public function my_favorite_offers(Request $request)
    {
        $favorite_offers = Favorite::whereUserId($request->user()->id)->get();
        if ($favorite_offers->count() > 0)
        {
            return ApiController::respondWithSuccess(new OfferCollection($favorite_offers));
        }else{
            $errors = [
                'key'   => 'add_offer_to_favorite',
                'value' => trans('messages.noFavorite'),
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }
    /**
     *  Report Offers
     * @user_make_report
     */
    public function user_make_report(Request  $request , $id)
    {
        $offer = Offer::find($id);
        if ($offer)
        {
            $checkReport = Report::whereUserId($request->user()->id)
                ->where('offer_id' , $offer->id)
                ->first();
            if ($checkReport == null)
            {
                $rules = [
                    'report' => 'sometimes',
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails())
                    return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

                // create new  offer report
                Report::create([
                    'user_id'  => $request->user()->id,
                    'offer_id' => $offer->id,
                    'report'   => $request->report,
                ]);
                $success = [
                    'key'   => 'user_make_report',
                    'value' => trans('messages.offerReportedSuccessfully')
                ];
                return ApiController::respondWithSuccess($success);
            }else{
                $errors = [
                    'key'   => 'user_make_report',
                    'value' => trans('messages.offerReportedBefore'),
                ];
                return ApiController::respondWithErrorAuthArray(array($errors));
            }
        }else{
            $errors = [
                'key'   => 'user_make_report',
                'value' => trans('messages.offer_not_found'),
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }
}
