<?php

namespace App\Http\Controllers\Api;

use App\Department;
use App\DriverOrder;
use App\ElectronicPocket;
use App\Food;
use App\FoodRequest;
use App\History;
use App\Offer;
use App\Order;
use App\OrderMeal;
use App\Setting;
use App\Truck;
use App\User;
use App\UserDevice;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use function Matrix\trace;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        $user = $request->user();
//        $user_last_order = Order::whereUserId($user->id)->orderBy('id' , 'desc')->first();
//        if ($user_last_order != null)
//        {
//            if ($user_last_order->status == '0' || $user_last_order->status == '1')
//            {
//                $errors = [
//                    'key'   => 'create_order',
//                    'value' => trans('messages.uCanNotCreateOrder')
//                ];
//                return ApiController::respondWithErrorArray(array($errors));
//            }
//        }
        $rules = [
            'truck_type_id'  => 'required|exists:truck_types,id',
            'latitude_from'  => 'required',
            'longitude_from' => 'required',
            'latitude_to'    => 'required',
            'longitude_to'   => 'required',
            'type'           =>'required',    // text
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));
        // check  if the stores trucks  has the same truck type  that  user search
        $check = Truck::where('truck_type_id' , $request->truck_type_id)->get();
        if ($check->count() <= 0)
        {
            $errors = [
                'key'   => 'create_order',
                'value' => trans('messages.noTruckYouSearch')
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
        $user = User::find($request->user()->id);
        // create a new order
        $order = Order::create([
            'user_id'         => $request->user()->id,
            'truck_type_id'   => $request->truck_type_id,
            'status'          => '0',                   // new order
            'latitude_from'   => $request->latitude_from,
            'longitude_from'  => $request->longitude_from,
            'latitude_to'     => $request->latitude_to,
            'longitude_to'    => $request->longitude_to,
            'type'            => $request->type,
        ]);
        // send Notification To Drivers
        $range = Setting::find(1)->search_range;
        $lat = $order->latitude_from;
        $lon = $order->longitude_from;
        $drivers = User::with('driver_orders' , 'trucks')
            ->whereHas('driver_orders' , function ($q) {
                $q->where('status' , '!=' , '2'); // driver do not have an active orders
            })->whereHas('trucks' , function ($q) use ($request){
                $q->where('truck_type_id' ,  $request->truck_type_id); // driver that have the same truck that user search
            })
            ->selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude) ) ) ) AS distance', [$lat, $lon, $lat])
            ->having('distance', '<=',  $range)
            ->where('type' , 2)
            ->where('active' , 1)
            ->where('country_id' , $request->user()->country_id)
            ->where('availability' , '1')
            ->orderBy('distance')
            ->get();
        if ($drivers->count() > 0)
        {
            foreach ($drivers as $driver)
            {
                // save order to stores
                DriverOrder::create([
                    'driver_id'  => $driver->id,
                    'order_id'   => $order->id,
                    'status'     => '0',
                ]);
                $ar_title = 'طلب جديد';
                $en_title = 'New Order';
                $ur_title = 'نیا حکم';
                $ar_message = 'تفحص  الطلبات الجديدة';
                $en_message = 'Check New Orders';
                $ur_message = 'نئے احکامات چیک کریں';
                $devicesTokens =  UserDevice::where('user_id',$driver->id)
                    ->get()
                    ->pluck('device_token')
                    ->toArray();
                if ($devicesTokens) {
                    sendMultiNotification(trans('messages.new_order'), trans('messages.orderNew') ,$devicesTokens);
                }
                saveNotification($driver->user_id, $ar_title,$en_title,$ur_title, $ar_message ,$en_message,$ur_message,'1' , $order->id);
            }
        }
//                return $this->prepare_order($request , $order->id);
        return $order
            ? ApiController::respondWithSuccess(new \App\Http\Resources\Order($order))
            : ApiController::respondWithServerErrorArray();
    }
    public function finish_order(Request $request , $order_id)
    {
        $order = Order::find($order_id);
        if ($order != null)
        {
            if ($order->user_id == $request->user()->id && $order->status == '1')
            {
                // user pay Order
                $rules = [
//            'order_id'           => 'required|exists:orders,id',
                    'payment_method'     => 'required|in:0,1,2',  // 0 -> cash , 1 -> online , 3 -> electronic wallet
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails())
                    return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

                if ($request->payment_method == '0')
                {
                    // cash payment
                    $restaurant  = $order->restaurant;
                    // calculate commission
                    $commission = ($restaurant->restaurant_commission * $order->price) / 100;
                    $restaurant_wallet = ElectronicPocket::whereUserId($restaurant->id)->first();
                    if ($restaurant_wallet != null)
                    {
                        $restaurant_wallet->update([
                            'cash'  => $restaurant_wallet->cash - $commission,
                        ]);
                    }
                    else{
                        ElectronicPocket::create([
                            'user_id'  => $restaurant->id,
                            'cash'     => - $commission,
                        ]);
                    }
                    History::create([
                        'user_id'  => $restaurant->id,
                        'ar_title' => 'تم خصم عموله التطبيق',
                        'en_title' => 'App Reduce the commission from u',
                        'price'    => $commission,
                    ]);

                    // check  if the user are belongs to association or not
                    $user = $request->user();
                    if ($user->association  == '1')
                    {
                        if ($order->place != null)
                        {
                            if ($order->place->association == '1')
                            {
                                /**
                                 *  Free Delivery
                                 *  Calculate the delivery commission and add the rest to driver wallet
                                 */
                                $drivers_commission = Setting::find(1)->drivers_commission;
                                $delivery_price = $order->delivery_price;
                                $driver_commission = ($drivers_commission * $delivery_price) /100 ;
                                $driver_price = $delivery_price - $driver_commission;
                                // add the delivery price to driver wallet
                                $driver_wallet = ElectronicPocket::whereUserId($order->driver->id)->first();
                                if ($driver_wallet)
                                {
                                    $driver_wallet->update([
                                        'cash'   => $driver_wallet->cash + $driver_price,
                                    ]);
                                }else{
                                    ElectronicPocket::create([
                                        'user_id'  => $order->driver->id,
                                        'cash'     => $driver_price,
                                    ]);
                                }
                                History::create([
                                    'user_id'  => $order->driver->id,
                                    'ar_title' => 'تم أضافه قيمه توصيل  الطلب الي محفظتك الألكترونيه  من  قبل أطعام',
                                    'en_title' => 'Ettam Organization are added to u the delivery price to your electronic wallet',
                                    'price'    => $driver_price,
                                ]);
                                History::create([
                                    'user_id'  => $order->driver->id,
                                    'ar_title' => 'تم خصم قيمه عموله  التطبيق  من  محفظتك الألكترونيه',
                                    'en_title' => 'Ettam Organization are take order delivery price from your electronic wallet',
                                    'price'    => $driver_commission,
                                ]);
                            }
                        }
                    }
                    else{
                        /**
                         *  take the delivery  price commission from driver
                        */
                        $drivers_commission = Setting::find(1)->drivers_commission;
                        $delivery_price = $order->delivery_price;
                        $driver_commission = ($drivers_commission * $delivery_price) /100 ;
                        // add the delivery price to driver wallet
                        $driver_wallet = ElectronicPocket::whereUserId($order->driver->id)->first();
                        if ($driver_wallet)
                        {
                            $driver_wallet->update([
                                'cash'   => $driver_wallet->cash - $driver_commission,
                            ]);
                        }else{
                            ElectronicPocket::create([
                                'user_id'  => $order->driver->id,
                                'cash'     => - $driver_commission,
                            ]);
                        }
                        History::create([
                            'user_id'  => $order->driver->id,
                            'ar_title' => 'تم خصم قيمه عموله  التطبيق  من  محفظتك الألكترونيه',
                            'en_title' => 'Ettam Organization are take order delivery price from your electronic wallet',
                            'price'    => $driver_commission,
                        ]);
                    }
                    $order->update([
                        'status'         => '0',    // new order
                        'payment_type'   => '0',    // cash payment
                        'payment_status' => '1',    // paid
                        'commission_status' => '1', // paid
                        'commission_value'  => $commission
                    ]);
                }
                elseif ($request->payment_method == '1')
                {
                    // online payment
                    /**
                     *  check if the user will pay the order price and delivery  price
                     *  if the user are in associations
                     *  if user place are fixed to  association to free delivery
                     *  the user will be pay the order price  only
                     *  else  if the user  will be pay  the order price and delivery  price
                    */
                    // check  if the user are belongs to association or not
                    $user = $request->user();
                    if ($user->association  == '1')
                    {
                        if ($order->place != null && $order->place->association == '1')
                        {
                            $cash = $order->price;
                        }else{
                            $cash = $order->price + $order->delivery_price;
                            History::create([
                                'user_id'  => $user->id,
                                'ar_title' => $order->driver->name .'لقد قمت بدفع قيمه  التوصيل الي  السائق ',
                                'en_title' => 'u pay the order delivery price to  driver ' . $order->driver->name,
                                'price'    => $order->delvery_price,
                            ]);
                        }
                    }else{
                        $cash = $order->price + $order->delivery_price;
                        History::create([
                            'user_id'  => $user->id,
                            'ar_title' => $order->driver->name .'لقد قمت بدفع قيمه  التوصيل الي  السائق ',
                            'en_title' => 'u pay the order delivery price to  driver ' . $order->driver->name,
                            'price'    => $order->delvery_price,
                        ]);
                    }
                    $user  = $request->user();
                    $token = "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";
                    $data = "{\"PaymentMethodId\":\"2\",\"CustomerName\": \"$user->name\",\"DisplayCurrencyIso\": \"SAR\",
                        \"MobileCountryCode\":\"+966\",\"CustomerMobile\": \"$user->phone_number\",
                        \"CustomerEmail\": \"email@mail.com\",\"InvoiceValue\": $cash,\"CallBackUrl\": \"http://127.0.0.1:8000/check-status-payOrder\",
                        \"ErrorUrl\": \"https://youtube.com\",\"Language\": \"ar\",\"CustomerReference\" :\"ref 1\",
                        \"CustomerCivilId\":12345678,\"UserDefinedField\": \"Custom field\",\"ExpireDate\": \"\",
                        \"CustomerAddress\" :{\"Block\":\"\",\"Street\":\"\",\"HouseBuildingNo\":\"\",\"Address\":\"\",\"AddressInstructions\":\"\"},
                        \"InvoiceItems\": [{\"ItemName\": \"$user->name\",\"Quantity\": 1,\"UnitPrice\": $cash}]}";
                    $fatooraRes = MyFatoorah($token, $data);
                    $result = json_decode($fatooraRes);
                    if ($result->IsSuccess === true) {
                        $user->update([
                            'invoice_id' => $result->Data->InvoiceId
                        ]);
                        $order->update([
                            'invoice_id' => $result->Data->InvoiceId
                        ]);
                        $all = [];
                        array_push($all, [
                            'key' => 'pay_order',
                            'payment_url' => $result->Data->PaymentURL,
                        ]);
                        return ApiController::respondWithSuccess($all);
                    }
                }
                elseif ($request->payment_method == '2')
                {
                    // electronic wallet payment
                    $user = $request->user();

                    if ($order->place != null)
                    {
                        if ($user->association  == '1' && $order->place->association == '1')
                        {
                            $price = $order->price;
                            $wallet = ElectronicPocket::whereUserId($user->id)->first();
                            // check if  user has electronic wallet or not
                            if ($wallet)
                            {
                                if ($price > $wallet->cash)
                                {
                                    $errors = [
                                        'key'   => 'finish_order',
                                        'value' => trans('messages.sorryYouNotHaveEnoughMoney')
                                    ];
                                    return ApiController::respondWithErrorArray(array($errors));
                                }
                                else{
                                    // delete the order amount and delivery price from user electronic wallet
                                    $wallet->update([
                                        'cash' => ($wallet->cash - $price)
                                    ]);
                                    // add the order price to restaurant wallet after delete commission
                                    $restaurant  = $order->restaurant;
                                    // calculate commission
                                    $commission = ($restaurant->restaurant_commission * $order->price) / 100;
                                    $order_price = $order->price - $commission;
                                    $restaurant_wallet = ElectronicPocket::whereUserId($restaurant->id)->first();
                                    if ($restaurant_wallet != null)
                                    {
                                        $restaurant_wallet->update([
                                            'cash'  => $restaurant_wallet->cash + $order_price,
                                        ]);
                                    }
                                    else{
                                        ElectronicPocket::create([
                                            'user_id'  => $restaurant->id,
                                            'cash'     => $order_price,
                                        ]);
                                    }
                                    History::create([
                                        'user_id'  => $restaurant->id,
                                        'ar_title' => 'تم دفع قيمه الطلب بنجاح من قبل العميل وخصم العمولة',
                                        'en_title' => 'You have charged a balance in your e-wallet and reduce commission',
                                        'price'    => $order_price,
                                    ]);
                                    // history for user
                                    History::create([
                                        'user_id'  => $request->user()->id,
                                        'ar_title' => $restaurant->name .'لقد قمت بدفع قيمه  طلب الي  المطعم ',
                                        'en_title' => 'u pay the order value to  restaurant ' . $restaurant->name,
                                        'price'    => $order->price,
                                    ]);
                                    // add the order delivery price to driver after delete commission
                                    $drivers_commission = Setting::find(1)->drivers_commission;
                                    $delivery_price = $order->delivery_price;
                                    $driver_commission = ($drivers_commission * $delivery_price) /100;
                                    $driver_price = $delivery_price - $driver_commission;
                                    // add the delivery price to driver wallet
                                    $driver_wallet = ElectronicPocket::whereUserId($order->driver->id)->first();
                                    if ($driver_wallet)
                                    {
                                        $driver_wallet->update([
                                            'cash'   => $driver_wallet->cash + $driver_price,
                                        ]);
                                    }else{
                                        ElectronicPocket::create([
                                            'user_id'  => $order->driver->id,
                                            'cash'     => $driver_price,
                                        ]);
                                    }
                                    History::create([
                                        'user_id'  => $order->driver->id,
                                        'ar_title' => 'تم أضافه قيمه توصيل  الطلب الي محفظتك الألكترونيه  من  قبل العميل',
                                        'en_title' => 'Ettam Organization are added to u the delivery price to your electronic wallet',
                                        'price'    => $driver_price,
                                    ]);
                                    History::create([
                                        'user_id'  => $order->driver->id,
                                        'ar_title' => 'تم خصم قيمه عموله  التطبيق  من  محفظتك الألكترونيه',
                                        'en_title' => 'Ettam Organization are take order delivery price from your electronic wallet',
                                        'price'    => $driver_commission,
                                    ]);
//                                    $driver_wallet->update([
//                                        'cash'   => $driver_wallet->cash - $driver_commission,
//                                    ]);

                                    $order->update([
                                        'status'         => '0',  // new order
                                        'payment_type'   => '2',  // electronic Wallet payment
                                        'payment_status' => '1',  // paid
                                        'commission_status' => '1', // paid
                                        'commission_value'  => $commission
                                    ]);
                                }
                            }else{
                                $errors = [
                                    'key'   => 'finish_order',
                                    'value' => trans('messages.noWallet')
                                ];
                                return ApiController::respondWithErrorArray(array($errors));
                            }
                        }
                        else{
                            $price = $order->price + $order->delivery_price;
                            // check electronic wallet mount
                            $wallet = ElectronicPocket::whereUserId($user->id)->first();
                            // check if  user has electronic wallet or not
                            if ($wallet)
                            {
                                if ($price > $wallet->cash)
                                {
                                    $errors = [
                                        'key'   => 'pay_order',
                                        'value' => trans('messages.sorryYouNotHaveEnoughMoney')
                                    ];
                                    return ApiController::respondWithErrorArray(array($errors));
                                }
                                else{
                                    // delete the order amount and delivery price from user electronic wallet
                                    $wallet->update([
                                        'cash' => ($wallet->cash - $price)
                                    ]);
                                    // add the order price to restaurant wallet after delete commission
                                    $restaurant  = $order->restaurant;
                                    // calculate commission
                                    $commission = ($restaurant->restaurant_commission * $order->price) / 100;
                                    $order_price = $order->price - $commission;
                                    $restaurant_wallet = ElectronicPocket::whereUserId($restaurant->id)->first();
                                    if ($restaurant_wallet != null)
                                    {
                                        $restaurant_wallet->update([
                                            'cash'  => $restaurant_wallet->cash + $order_price,
                                        ]);
                                    }
                                    else{
                                        ElectronicPocket::create([
                                            'user_id'  => $restaurant->id,
                                            'cash'     => $order_price,
                                        ]);
                                    }
                                    History::create([
                                        'user_id'  => $restaurant->id,
                                        'ar_title' => 'تم دفع قيمه الطلب بنجاح من قبل العميل وخصم العمولة',
                                        'en_title' => 'You have charged a balance in your e-wallet and reduce commission',
                                        'price'    => $order_price,
                                    ]);
                                    // history for user
                                    History::create([
                                        'user_id'  => $request->user()->id,
                                        'ar_title' => $restaurant->name .'لقد قمت بدفع قيمه  طلب الي  المطعم ',
                                        'en_title' => 'u pay the order value to  restaurant ' . $restaurant->name,
                                        'price'    => $order->price,
                                    ]);
                                    History::create([
                                        'user_id'  => $request->user()->id,
                                        'ar_title' => $order->driver->name .'لقد قمت بدفع قيمه  التوصيل الي  السواق ',
                                        'en_title' => 'u pay the order value to  restaurant ' . $order->driver->name,
                                        'price'    => $order->delivery_price,
                                    ]);
                                    // add the order delivery price to driver after delete commission

                                    $drivers_commission = Setting::find(1)->drivers_commission;
                                    $delivery_price = $order->delivery_price;
                                    $driver_commission = ($drivers_commission * $delivery_price) /100 ;
                                    $driver_price = $delivery_price - $driver_commission;
                                    // add the delivery price to driver wallet
                                    $driver_wallet = ElectronicPocket::whereUserId($order->driver->id)->first();
                                    if ($driver_wallet)
                                    {
                                        $driver_wallet->update([
                                            'cash'   => $driver_wallet->cash + $driver_price,
                                        ]);
                                    }else{
                                        ElectronicPocket::create([
                                            'user_id'  => $order->driver->id,
                                            'cash'     => $driver_price,
                                        ]);
                                    }
                                    History::create([
                                        'user_id'  => $order->driver->id,
                                        'ar_title' => 'تم أضافه قيمه توصيل  الطلب الي محفظتك الألكترونيه  من  قبل العميل',
                                        'en_title' => 'Ettam Organization are added to u the delivery price to your electronic wallet',
                                        'price'    => $driver_price,
                                    ]);
                                    History::create([
                                        'user_id'  => $order->driver->id,
                                        'ar_title' => 'تم خصم قيمه عموله  التطبيق  من  محفظتك الألكترونيه',
                                        'en_title' => 'Ettam Organization are take order delivery price from your electronic wallet',
                                        'price'    => $driver_commission,
                                    ]);
                                    $order->update([
                                        'status'         => '0',  // new order
                                        'payment_type'   => '2',  // electronic Wallet payment
                                        'payment_status' => '1',  // paid
                                        'commission_status' => '1', // paid
                                        'commission_value'  => $commission
                                    ]);
                                }
                            }else{
                                $errors = [
                                    'key'   => 'finish_order',
                                    'value' => trans('messages.noWallet')
                                ];
                                return ApiController::respondWithErrorArray(array($errors));
                            }
                        }
                    }else{
                        $price = $order->price + $order->delivery_price;
                        // check electronic wallet mount
                        $wallet = ElectronicPocket::whereUserId($user->id)->first();
                        // check if  user has electronic wallet or not
                        if ($wallet)
                        {
                            if ($price > $wallet->cash)
                            {
                                $errors = [
                                    'key'   => 'pay_order',
                                    'value' => trans('messages.sorryYouNotHaveEnoughMoney')
                                ];
                                return ApiController::respondWithErrorArray(array($errors));
                            }
                            else{
                                // delete the order amount and delivery price from user electronic wallet
                                $wallet->update([
                                    'cash' => ($wallet->cash - $price)
                                ]);
                                // add the order price to restaurant wallet after delete commission
                                $restaurant  = $order->restaurant;
                                // calculate commission
                                $commission = ($restaurant->restaurant_commission * $order->price) / 100;
                                $order_price = $order->price - $commission;
                                $restaurant_wallet = ElectronicPocket::whereUserId($restaurant->id)->first();
                                if ($restaurant_wallet != null)
                                {
                                    $restaurant_wallet->update([
                                        'cash'  => $restaurant_wallet->cash + $order_price,
                                    ]);
                                }
                                else{
                                    ElectronicPocket::create([
                                        'user_id'  => $restaurant->id,
                                        'cash'     => $order_price,
                                    ]);
                                }
                                History::create([
                                    'user_id'  => $restaurant->id,
                                    'ar_title' => 'تم دفع قيمه الطلب بنجاح من قبل العميل وخصم العمولة',
                                    'en_title' => 'You have charged a balance in your e-wallet and reduce commission',
                                    'price'    => $order_price,
                                ]);
                                // history for user
                                History::create([
                                    'user_id'  => $request->user()->id,
                                    'ar_title' => $restaurant->name .'لقد قمت بدفع قيمه  طلب الي  المطعم ',
                                    'en_title' => 'u pay the order value to  restaurant ' . $restaurant->name,
                                    'price'    => $order->price,
                                ]);
                                History::create([
                                    'user_id'  => $request->user()->id,
                                    'ar_title' => $order->driver->name .'لقد قمت بدفع قيمه  التوصيل الي  السواق ',
                                    'en_title' => 'u pay the order value to  restaurant ' . $order->driver->name,
                                    'price'    => $order->delivery_price,
                                ]);
                                // add the order delivery price to driver after delete commission

                                $drivers_commission = Setting::find(1)->drivers_commission;
                                $delivery_price = $order->delivery_price;
                                $driver_commission = ($drivers_commission * $delivery_price) /100 ;
                                $driver_price = $delivery_price - $driver_commission;
                                // add the delivery price to driver wallet
                                $driver_wallet = ElectronicPocket::whereUserId($order->driver->id)->first();
                                if ($driver_wallet)
                                {
                                    $driver_wallet->update([
                                        'cash'   => $driver_wallet->cash + $driver_price,
                                    ]);
                                }else{
                                    ElectronicPocket::create([
                                        'user_id'  => $order->driver->id,
                                        'cash'     => $driver_price,
                                    ]);
                                }
                                History::create([
                                    'user_id'  => $order->driver->id,
                                    'ar_title' => 'تم أضافه قيمه توصيل  الطلب الي محفظتك الألكترونيه  من  قبل العميل',
                                    'en_title' => 'Ettam Organization are added to u the delivery price to your electronic wallet',
                                    'price'    => $driver_price,
                                ]);
                                History::create([
                                    'user_id'  => $order->driver->id,
                                    'ar_title' => 'تم خصم قيمه عموله  التطبيق  من  محفظتك الألكترونيه',
                                    'en_title' => 'Ettam Organization are take order delivery price from your electronic wallet',
                                    'price'    => $driver_commission,
                                ]);
                                $order->update([
                                    'status'         => '0',  // new order
                                    'payment_type'   => '2',  // electronic Wallet payment
                                    'payment_status' => '1',  // paid
                                    'commission_status' => '1', // paid
                                    'commission_value'  => $commission
                                ]);
                            }
                        }else{
                            $errors = [
                                'key'   => 'finish_order',
                                'value' => trans('messages.noWallet')
                            ];
                            return ApiController::respondWithErrorArray(array($errors));
                        }
                    }
                }
                else{
                    $errors = [
                        'key'   => 'pay_order',
                        'value' => trans('messages.errorPaymentMethod')
                    ];
                    return ApiController::respondWithErrorArray(array($errors));
                }
//                // calculate the driver commission
//                $driver = $order->driver;
//                $driverCommission = Setting::find(1)->drivers_commission;
//                $commission = $order->delivery_price * $driverCommission / 100;
//                // take the commission value from driver
//                $driver_wallet = ElectronicPocket::whereUserId($driver->id)->first();
//                if ($driver_wallet != null)
//                {
//                    $driver_wallet->update([
//                        'cash'  => $driver_wallet->cash - $commission,
//                    ]);
//                }
//                else{
//                    ElectronicPocket::create([
//                        'user_id'  => $driver->id,
//                        'cash'     => - $commission,
//                    ]);
//                }
//                History::create([
//                    'user_id'  => $driver->id,
//                    'ar_title' => 'تم خصم عموله التطبيق',
//                    'en_title' => 'App Reduce the commission from u',
//                    'price'    => $commission,
//                ]);

                // payment methods to order
                $order->update([
                    'status' => '2',     // order completed
                ]);
                // update driver order
                $driverOrder = DriverOrder::whereOrderId($order->id)
                    ->where('driver_id' , $order->driver_id)
                    ->first();
                $driverOrder->update([
                    'status' => '3' // completed
                ]);
                // update offer status
                $offer = Offer::whereOrderId($order->id)
                    ->where('driver_id' , $order->driver_id)
                    ->first();
                $offer->update([
                    'status'  => '2'  // completed
                ]);
                // send notification to driver
                $devicesTokens =  UserDevice::where('user_id',$order->driver_id)
                    ->get()
                    ->pluck('device_token')
                    ->toArray();
                if ($devicesTokens) {
                    sendMultiNotification(trans('messages.orders'), trans('messages.orderCompleted') ,$devicesTokens);
                }
                saveNotification($order->driver_id, trans('messages.orders'), '1', trans('messages.orderCompleted'), null , $order->id);
                $success = [
                    'data'=>'success',
                    'value'=> trans('messages.order_completed_successfully')
                ];
                return $order
                    ? ApiController::respondWithSuccess($success)
                    : ApiController::respondWithServerErrorArray();

            }
            else{
                $errors = [
                    'key'=>'finish_order',
                    'value'=> trans('messages.orderNotBelongToYou')
                ];
                return ApiController::respondWithErrorClient(array($errors));
            }
        }else{
            $errors = [
                'key'=>'finish_order',
                'value'=> trans('messages.order_not_found')
            ];
            return ApiController::respondWithErrorClient(array($errors));
        }
    }
    /**
     *  new orders for stores
     * @user_orders
    */
    public function user_orders(Request  $request , $status)
    {
        $orders = Order::where('user_id' , $request->user()->id)
            ->where('status' , $status)
            ->orderBy('created_at' , 'desc')
            ->get();
        if ($orders->count() > 0)
        {
            return ApiController::respondWithSuccess(\App\Http\Resources\Order::collection($orders));
        }else{
            if ($status == '0')
            {
                $errors = [
                    'key'    => 'user_orders',
                    'value'  => trans('messages.noNewOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }elseif ($status == '1')
            {
                $errors = [
                    'key'    => 'user_orders',
                    'value'  => trans('messages.noActiveOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }elseif ($status == '2')
            {
                $errors = [
                    'key'    => 'user_orders',
                    'value'  => trans('messages.noFinishedOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }elseif ($status == '3')
            {
                $errors = [
                    'key'    => 'user_orders',
                    'value'  => trans('messages.noCanceledOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }else{
                $errors = [
                    'key'    => 'user_orders',
                    'value'  => 'Wrong Url status is 0 , 1 , 2 , 3',
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }

        }
    }
    public function driver_orders(Request  $request , $status)
    {
        $orders = DriverOrder::with('order')
            ->where('driver_id' , $request->user()->id)
            ->where('status' , $status)
            ->orderBy('created_at' , 'desc')
            ->get();
        if ($orders->count() > 0)
        {
            return ApiController::respondWithSuccess(\App\Http\Resources\Order::collection($orders));
        }else{
            if ($status == '0')
            {
                $errors = [
                    'key'    => 'driver_orders',
                    'value'  => trans('messages.noNewOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }elseif ($status == '1')
            {
                $errors = [
                    'key'    => 'driver_orders',
                    'value'  => trans('messages.noHoldOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }elseif ($status == '2') {
                $errors = [
                    'key'    => 'driver_orders',
                    'value'  => trans('messages.noActiveOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }elseif ($status == '3') {
                $errors = [
                    'key'    => 'driver_orders',
                    'value'  => trans('messages.noFinishedOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }elseif ($status == '4') {
                $errors = [
                    'key'    => 'driver_orders',
                    'value'  => trans('messages.noCanceledOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }
//            return ApiController::respondWithErrorArray(array($errors));
        }
    }
    /**
     * @daily_complete_order_statistics
    */
    public function daily_complete_order_statistics(Request $request)
    {
        $user =User::find($request->user()->id);
        $complete_orders_association = FoodRequest::with('foods' , 'users')
            ->whereHas('foods', function ($params) use ($request) {
                $params->where('user_id', $request->user()->id);
                $params->whereDate('end_time' , '=', Carbon::now()->subDays(1)->format('Y-m-d'));
                $params->where('sold', '!=' , '0');
            })->whereHas('users', function ($params) use ($request) {
                $params->where('association', '1');
            })->where('status' , '1')
            ->get();
        $complete_orders_unassociation = FoodRequest::with('foods' , 'users')
            ->whereHas('foods', function ($params) use ($request) {
                $params->where('user_id', $request->user()->id);
                $params->whereDate('end_time' , '=', Carbon::now()->subDays(1)->format('Y-m-d'));
                $params->where('sold', '!=' , '0');
            })->whereHas('users', function ($params) use ($request) {
                $params->where('association', '0');
            })->where('status' , '1')
            ->get();
        return $user
            ? ApiController::respondWithSuccess([
                'association'     => $complete_orders_association->count(),
                'no_association'  => $complete_orders_unassociation->count(),
            ])
            : ApiController::respondWithServerErrorArray();
    }
    /**
     * @daily_canceled_order_statistics
    */
    public function daily_canceled_order_statistics(Request $request)
    {
        $user =User::find($request->user()->id);
        $canceled_orders_association = FoodRequest::with('foods' , 'users')
            ->whereHas('foods', function ($params) use ($request) {
                $params->where('user_id', $request->user()->id);
                $params->whereDate('end_time' , '=', Carbon::now()->subDays(1)->format('Y-m-d'));
            })->whereHas('users', function ($params) use ($request) {
                $params->where('association', '1');
            })->where('status' , '2')
            ->get();
        $canceled_orders_unassociation = FoodRequest::with('foods' , 'users')
            ->whereHas('foods', function ($params) use ($request) {
                $params->where('user_id', $request->user()->id);
                $params->whereDate('end_time' , '=', Carbon::now()->subDays(1)->format('Y-m-d'));
            })->whereHas('users', function ($params) use ($request) {
                $params->where('association', '0');
            })->where('status' , '2')
            ->get();
        return $user
            ? ApiController::respondWithSuccess([
                'association'     => $canceled_orders_association->count(),
                'no_association'  => $canceled_orders_unassociation->count(),
            ])
            : ApiController::respondWithServerErrorArray();
    }
    /**
     * @weekly_complete_order_statistics
    */
    public function weekly_complete_order_statistics(Request $request)
    {
        $user =User::find($request->user()->id);
        $complete_orders_association = FoodRequest::with('foods' , 'users')
            ->whereHas('foods', function ($params) use ($request) {
                $params->where('user_id', $request->user()->id);
                $params->whereDate('end_time' , '>=', Carbon::now()->subDays(7)->format('Y-m-d'));
                $params->where('sold', '!=' , '0');
            })->whereHas('users', function ($params) use ($request) {
                $params->where('association', '1');
            })->where('status' , '1')
            ->get();
        $complete_orders_unassociation = FoodRequest::with('foods' , 'users')
            ->whereHas('foods', function ($params) use ($request) {
                $params->where('user_id', $request->user()->id);
                $params->whereDate('end_time' , '>=', Carbon::now()->subDays(7)->format('Y-m-d'));
                $params->where('sold', '!=' , '0');
            })->whereHas('users', function ($params) use ($request) {
                $params->where('association', '0');
            })->where('status' , '1')
            ->get();
        return $user
            ? ApiController::respondWithSuccess([
                'association'     => $complete_orders_association->count(),
                'no_association'  => $complete_orders_unassociation->count(),
            ])
            : ApiController::respondWithServerErrorArray();
    }
    /**
     * @weekly_canceled_order_statistics
    */
    public function weekly_canceled_order_statistics(Request $request)
    {
        $user =User::find($request->user()->id);
        $canceled_orders_association = FoodRequest::with('foods' , 'users')
            ->whereHas('foods', function ($params) use ($request) {
                $params->where('user_id', $request->user()->id);
                $params->whereDate('end_time' , '>=', Carbon::now()->subDays(1)->format('Y-m-d'));
            })->whereHas('users', function ($params) use ($request) {
                $params->where('association', '1');
            })->where('status' , '2')
            ->get();
        $canceled_orders_unassociation = FoodRequest::with('foods' , 'users')
            ->whereHas('foods', function ($params) use ($request) {
                $params->where('user_id', $request->user()->id);
                $params->whereDate('end_time' , '>=', Carbon::now()->subDays(1)->format('Y-m-d'));
            })->whereHas('users', function ($params) use ($request) {
                $params->where('association', '0');
            })->where('status' , '2')
            ->get();
        return $user
            ? ApiController::respondWithSuccess([
                'association'     => $canceled_orders_association->count(),
                'no_association'  => $canceled_orders_unassociation->count(),
            ])
            : ApiController::respondWithServerErrorArray();
    }
    /**
     * @monthly_complete_order_statistics
    */
    public function monthly_complete_order_statistics(Request $request)
    {
        $user =User::find($request->user()->id);
        $complete_orders_association = FoodRequest::with('foods' , 'users')
            ->whereHas('foods', function ($params) use ($request) {
                $params->where('user_id', $request->user()->id);
                $params->whereDate('end_time' , '>=', Carbon::now()->subDays(30)->format('Y-m-d'));
                $params->where('sold', '!=' , '0');
            })->whereHas('users', function ($params) use ($request) {
                $params->where('association', '1');
            })->where('status' , '1')
            ->get();
        $complete_orders_unassociation = FoodRequest::with('foods' , 'users')
            ->whereHas('foods', function ($params) use ($request) {
                $params->where('user_id', $request->user()->id);
                $params->whereDate('end_time' , '>=', Carbon::now()->subDays(30)->format('Y-m-d'));
                $params->where('sold', '!=' , '0');
            })->whereHas('users', function ($params) use ($request) {
                $params->where('association', '0');
            })->where('status' , '1')
            ->get();
        return $user
            ? ApiController::respondWithSuccess([
                'association'     => $complete_orders_association->count(),
                'no_association'  => $complete_orders_unassociation->count(),
            ])
            : ApiController::respondWithServerErrorArray();
    }
    /**
     * @monthly_canceled_order_statistics
    */
    public function monthly_canceled_order_statistics(Request $request)
    {
        $user =User::find($request->user()->id);
        $canceled_orders_association = FoodRequest::with('foods' , 'users')
            ->whereHas('foods', function ($params) use ($request) {
                $params->where('user_id', $request->user()->id);
                $params->whereDate('end_time' , '>=', Carbon::now()->subDays(30)->format('Y-m-d'));
            })->whereHas('users', function ($params) use ($request) {
                $params->where('association', '1');
            })->where('status' , '2')
            ->get();
        $canceled_orders_unassociation = FoodRequest::with('foods' , 'users')
            ->whereHas('foods', function ($params) use ($request) {
                $params->where('user_id', $request->user()->id);
                $params->whereDate('end_time' , '>=', Carbon::now()->subDays(30)->format('Y-m-d'));
            })->whereHas('users', function ($params) use ($request) {
                $params->where('association', '0');
            })->where('status' , '2')
            ->get();
        return $user
            ? ApiController::respondWithSuccess([
                'association'     => $canceled_orders_association->count(),
                'no_association'  => $canceled_orders_unassociation->count(),
            ])
            : ApiController::respondWithServerErrorArray();
    }
    /**
     * @yearly_complete_order_statistics
    */
    public function yearly_complete_order_statistics(Request $request)
    {
        $user =User::find($request->user()->id);
        $complete_orders_association = FoodRequest::with('foods' , 'users')
            ->whereHas('foods', function ($params) use ($request) {
                $params->where('user_id', $request->user()->id);
                $params->whereDate('end_time' , '>=', Carbon::now()->subDays(365)->format('Y-m-d'));
                $params->where('sold', '!=' , '0');
            })->whereHas('users', function ($params) use ($request) {
                $params->where('association', '1');
            })->where('status' , '1')
            ->get();
        $complete_orders_unassociation = FoodRequest::with('foods' , 'users')
            ->whereHas('foods', function ($params) use ($request) {
                $params->where('user_id', $request->user()->id);
                $params->whereDate('end_time' , '>=', Carbon::now()->subDays(365)->format('Y-m-d'));
                $params->where('sold', '!=' , '0');
            })->whereHas('users', function ($params) use ($request) {
                $params->where('association', '0');
            })->where('status' , '1')
            ->get();
        return $user
            ? ApiController::respondWithSuccess([
                'association'     => $complete_orders_association->count(),
                'no_association'  => $complete_orders_unassociation->count(),
            ])
            : ApiController::respondWithServerErrorArray();
    }
    /**
     * @yearly_canceled_order_statistics
    */
    public function yearly_canceled_order_statistics(Request $request)
    {
        $user =User::find($request->user()->id);
        $canceled_orders_association = FoodRequest::with('foods' , 'users')
            ->whereHas('foods', function ($params) use ($request) {
                $params->where('user_id', $request->user()->id);
                $params->whereDate('end_time' , '>=', Carbon::now()->subDays(365)->format('Y-m-d'));
            })->whereHas('users', function ($params) use ($request) {
                $params->where('association', '1');
            })->where('status' , '2')
            ->get();
        $canceled_orders_unassociation = FoodRequest::with('foods' , 'users')
            ->whereHas('foods', function ($params) use ($request) {
                $params->where('user_id', $request->user()->id);
                $params->whereDate('end_time' , '>=', Carbon::now()->subDays(365)->format('Y-m-d'));
            })->whereHas('users', function ($params) use ($request) {
                $params->where('association', '0');
            })->where('status' , '2')
            ->get();
        return $user
            ? ApiController::respondWithSuccess([
                'association'     => $canceled_orders_association->count(),
                'no_association'  => $canceled_orders_unassociation->count(),
            ])
            : ApiController::respondWithServerErrorArray();
    }
    /**
     *  the User Cancel Order
     *  @param int $order_id
     * @user_cancel_order
     */
    public function user_cancel_order(Request $request , $order_id)
    {
        $order = Order::find($order_id);
        if($order)
        {
            $cancel_time = Setting::find(1)->order_cancel_time;

            if ($order->created_at->addMinutes($cancel_time) > Carbon::now())
            {
                $restaurant  = $order->meal->user;
                // cancel  order without any commission
                $order->update([
                    'status'   =>  '3'         // the order is canceled
                ]);
                // cancel stores orders
                $driver_orders = DriverOrder::whereOrderId($order->id)->get();
                if ($driver_orders->count() > 0)
                {
                    foreach ($driver_orders as $driver_order) {
                        $driver_order->update([
                            'status'  => '4'
                        ]);
                    }
                }
                $order->meal->update([
                    'available'   => $order->meal->available + $order->mael_count,         // the order is canceled
                    'booked_up'   => $order->meal->booked_up - $order->mael_count,         // the order is canceled
                ]);

                $devicesTokens =  UserDevice::where('user_id',$order->meal->user_id)
                    ->get()
                    ->pluck('device_token')
                    ->toArray();
                if ($devicesTokens) {
                    sendMultiNotification("ألغاء الطلب", "تم الغاء طلب من وجبتك" ,$devicesTokens);
                }
                saveNotification($order->meal->user_id, "ألغاء الطلب" , '1', "تم الغاء طلب من وجبتك", $order->meal->id , $order->id);
                $success = [
                    'key'   => 'user_cancel_order',
                    'value' => trans('messages.order_canceled_successfully')
                ];
                return ApiController::respondWithSuccess($success);
            }else
            {
                // cancel  order with commission
                $restaurant  = $order->meal->user;
                // calculate commission
                $commission = ($restaurant->restaurant_commission * $order->price) / 100;
                $user_wallet = ElectronicPocket::whereUserId($request->user()->id)->first();
                if ($user_wallet)
                {
                    $user_wallet->update([
                        'cash'   => $user_wallet->cash - $commission,
                    ]);
                }else{
                    ElectronicPocket::create([
                        'user_id'   => $request->user()->id,
                        'cash'      => - $commission,
                    ]);
                }
                $order->update([
                    'status'   =>  '3'         // the order is canceled
                ]);
                // cancel stores orders
                $driver_orders = DriverOrder::whereOrderId($order->id)->get();
                if ($driver_orders->count() > 0)
                {
                    foreach ($driver_orders as $driver_order) {
                        $driver_order->update([
                            'status'  => '4'
                        ]);
                    }
                }
                $order->meal->update([
                    'available'   => $order->meal->available + $order->mael_count,         // the order is canceled
                    'booked_up'   => $order->meal->booked_up - $order->mael_count,         // the order is canceled
                ]);
                // record the operation to history
                History::create([
                    'user_id'   => $request->user()->id,
                    'ar_title'  => ' لقد قمت بالغاء  الطلب من المطعم  '. $restaurant->name . ' وخصم العموله  من محفظتك ',
                    'en_title'  => 'U Cancel the order from restaurant ' . $restaurant->name . ' And the reduce Commission',
                    'price'     => $commission,
                ]);
                $devicesTokens =  UserDevice::where('user_id',$order->meal->user_id)
                    ->get()
                    ->pluck('device_token')
                    ->toArray();
                if ($devicesTokens) {
                    sendMultiNotification("ألغاء الطلب", "تم الغاء طلب من وجبتك" ,$devicesTokens);
                }
                saveNotification($order->meal->user_id, "ألغاء الطلب" , '1', "تم الغاء طلب من وجبتك", $order->meal->id , $order->id);
                $success = [
                    'key'   => 'user_cancel_order',
                    'value' => trans('messages.order_canceled_successfully')
                ];
                return ApiController::respondWithSuccess($success);
            }
        }else{
            $errors = [
                'key'     =>'user_cancel_order',
                'value'   => trans('messages.order_not_found')
            ];
            return ApiController::respondWithErrorObject(array($errors));
        }
    }
}
