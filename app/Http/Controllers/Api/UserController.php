<?php

namespace App\Http\Controllers\Api;

use App\Electronic_wallet;
use App\Setting;
use App\UserDevice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Psy\Command\HistoryCommand;
use Validator;
use App\User;
use App;
use Auth;
use App\History;

class UserController extends Controller
{
    public function charge_electronic_wallet(Request $request)
    {
        $rules = [
            'amount'     => 'required',
            'payment_type' => 'required|in:1,2,3'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));
        // create or update your electronic
        $data = [];
        $amount = $request->amount;
        $user  = $request->user();
        if($request->payment_type == 1)       {$charge = 2 ;}
        elseif ($request->payment_type == 2)  {$charge = 6 ;}
        elseif($request->payment_type == 3)   {$charge = 11 ;}
        $token = "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";
        $data = "{\"PaymentMethodId\":\"$charge\",\"CustomerName\": \"$user->name\",\"DisplayCurrencyIso\": \"SAR\",
            \"MobileCountryCode\":\"+966\",\"CustomerMobile\": \"$user->phone_number\",
                \"CustomerEmail\": \"email@mail.com\",\"InvoiceValue\": $amount,\"CallBackUrl\": \"http://127.0.0.1:8000/check-status\",
                \"ErrorUrl\": \"https://youtube.com\",\"Language\": \"ar\",\"CustomerReference\" :\"ref 1\",
                \"CustomerCivilId\":12345678,\"UserDefinedField\": \"Custom field\",\"ExpireDate\": \"\",
                \"CustomerAddress\" :{\"Block\":\"\",\"Street\":\"\",\"HouseBuildingNo\":\"\",\"Address\":\"\",\"AddressInstructions\":\"\"},
                \"InvoiceItems\": [{\"ItemName\": \"$user->name\",\"Quantity\": 1,\"UnitPrice\": $amount}]}";
        $fatooraRes = MyFatoorah($token, $data);
        $result = json_decode($fatooraRes);
        if ($result->IsSuccess === true) {
//            return redirect($result->Data->PaymentURL);
            $user = User::find($request->user()->id);
            if ($result->IsSuccess === true) {
                $user->update([
                    'invoice_id' => $result->Data->InvoiceId
                ]);
                $all = [];
//                   $pocket->update([
//                       'amount'   => $amount,
//                   ]);
                array_push($all , [
                    'key'  => 'charge_electronic_wallet',
                    'payment_url' => $result->Data->PaymentURL,
                ]);
                return ApiController::respondWithSuccess($all);
            }
        }
    }
    public  function fatooraStatus(){
        $token = "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";
        $PaymentId = \Request::query('paymentId');
        $resData = MyFatoorahStatus($token, $PaymentId);
        $result = json_decode($resData);
        if($result->IsSuccess === true && $result->Data->InvoiceStatus === "Paid"){
            $InvoiceId = $result->Data->InvoiceId;
            $user = App\User::where('invoice_id',$InvoiceId)->first();
            $user->update([
                'active' => 1
            ]);
            $check = Electronic_wallet::whereUser_id($user->id)->first();
            if ($check)
            {
                $check->update([
                    'amount'   => $check->amount + $result->Data->InvoiceValue,
                ]);
            }else{
                $pocket = Electronic_wallet::create([
                    'user_id'  => $user->id,
                    'amount'     => $result->Data->InvoiceValue,
                ]);
            }
            History::create([
                'user_id'  => $user->id,
                'ar_title' => 'لقد قمت بشحن رصيد في محفظتك الألكترونية',
                'en_title' => 'You have charged a balance in your e-wallet',
                'ur_title' => 'آپ نے اپنے ای والٹ میں بیلنس وصول کیا ہے',
                'price'    => $result->Data->InvoiceValue,
            ]);

            return redirect()->to('/fatoora/success');
        }
    }
    public  function statusDonation(){
        $token = "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";
        $PaymentId = \Request::query('paymentId');
        $resData = MyFatoorahStatus($token, $PaymentId);
        $result = json_decode($resData);
        if($result->IsSuccess === true && $result->Data->InvoiceStatus === "Paid"){
            $InvoiceId = $result->Data->InvoiceId;
            $user = App\User::where('invoice_id',$InvoiceId)->first();
            return redirect()->to('/fatoora/success');
        }
    }
    public  function fatooraStatusPayOrder(){
        $token = "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";
        $PaymentId = \Request::query('paymentId');
        $resData = MyFatoorahStatus($token, $PaymentId);
        $result = json_decode($resData);
        if($result->IsSuccess === true && $result->Data->InvoiceStatus === "Paid"){
            $InvoiceId = $result->Data->InvoiceId;
            $user = App\User::where('invoice_id',$InvoiceId)->first();
            $order = App\Order::where('invoice_id',$InvoiceId)->first();
            // add the order amount to restaurant wallet
            $restaurant  = $order->restaurant;
            // calculate commission
            $commission = ($restaurant->restaurant_commission * $order->price) / 100;
            $order_price = $order->price - $commission;
            $restaurant_wallet = ElectronicPocket::whereUserId($restaurant->id)->first();
            if ($restaurant_wallet != null)
            {
                $restaurant_wallet->update([
                    'amount'  => $restaurant_wallet->amount + $order_price,
                ]);
            }
            else{
                ElectronicPocket::create([
                    'user_id'  => $restaurant->id,
                    'amount'     => $order_price,
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
                'user_id'  => $user->id,
                'ar_title' => $restaurant->name .'لقد قمت بدفع قيمه  طلب الي  المطعم ',
                'en_title' => 'u pay the order value to  restaurant ' . $restaurant->name,
                'price'    => $order->price,
            ]);

            $order->update([
                'status'         => '0',  // new order
                'payment_type'   => '1',  // Online payment
                'payment_status' => '1',  // paid
                'commission_status' => '1', // paid
                'commission_value'  => $commission
            ]);
            $driver = $order->driver;
            $driverCommission = Setting::find(1)->drivers_commission;
            $commission = $order->delivery_price * $driverCommission / 100;
            $value = $order->delivery_price - $commission;
            // take the commission value from driver
            $driver_wallet = ElectronicPocket::whereUserId($driver->id)->first();
            if ($driver_wallet != null)
            {
                $driver_wallet->update([
                    'amount'  => $driver_wallet->amount + $value,
                ]);
            }
            else{
                ElectronicPocket::create([
                    'user_id'  => $driver->id,
                    'amount'     => $value,
                ]);
            }
            History::create([
                'user_id'  => $driver->id,
                'ar_title' => 'تم أضافه مبلغ  التوصيل الي محفظتك الألكترونيه وخصم العمولة',
                'en_title' => 'Order Delivery Price And Reduce the commission from u',
                'price'    => $value,
            ]);


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
                'key'     => 'pay_order',
                'value'   => trans('messages.successPayment')
            ];
//            return ApiController::respondWithSuccess($success);

            return redirect()->to('/fatoora/success');
        }
    }
    public function get_commission(Request $request)
    {
        $commission = App\ElectronicPocket::whereUserId($request->user()->id)->first()->amount;
        $commissionLimit = Setting::find(1)->commission_limit;
        if ($commission <= $commissionLimit)
        {
            $success = [
                'key'    => 'get_commission',
                'value'  => -$commission,
            ];
            return ApiController::respondWithSuccess($success);
        }else{
            $success = [
                'key'    => 'get_commission',
                'value'  => trans('messages.UNotHaveCommission'),
            ];
            return ApiController::respondWithSuccess($success);
        }
    }
    public function pay_commission(Request $request)
    {
        $commission = App\ElectronicPocket::whereUserId($request->user()->id)->first()->amount;
        $commissionLimit = Setting::find(1)->commission_limit;
        if ($commission <= $commissionLimit)
        {
            $rules = [
                'commission'     => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails())
                return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

            if ($request->commission > -$commission)
            {
                $amount = $commission >=  0 ? $commission + $request->commission : $request->commission;
                $user  = $request->user();
                $token = "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";
                $data = "{\"PaymentMethodId\":\"2\",\"CustomerName\": \"$user->name\",\"DisplayCurrencyIso\": \"SAR\",
                \"MobileCountryCode\":\"+966\",\"CustomerMobile\": \"$user->phone_number\",
                \"CustomerEmail\": \"email@mail.com\",\"InvoiceValue\": $amount,\"CallBackUrl\": \"http://127.0.0.1:8000/check-status\",
                \"ErrorUrl\": \"https://youtube.com\",\"Language\": \"ar\",\"CustomerReference\" :\"ref 1\",
                \"CustomerCivilId\":12345678,\"UserDefinedField\": \"Custom field\",\"ExpireDate\": \"\",
                \"CustomerAddress\" :{\"Block\":\"\",\"Street\":\"\",\"HouseBuildingNo\":\"\",\"Address\":\"\",\"AddressInstructions\":\"\"},
                \"InvoiceItems\": [{\"ItemName\": \"$user->name\",\"Quantity\": 1,\"UnitPrice\": $amount}]}";
                $fatooraRes = MyFatoorah($token, $data);
                $result = json_decode($fatooraRes);
                if ($result->IsSuccess === true) {
//            return redirect($result->Data->PaymentURL);
                    $user = User::find($request->user()->id);
                    if ($result->IsSuccess === true) {
                        $user->update([
                            'invoice_id' => $result->Data->InvoiceId
                        ]);
                        $all = [];
//                   $pocket->update([
//                       'amount'   => $amount,
//                   ]);
                        array_push($all , [
                            'key'  => 'pay_commission',
                            'payment_url' => $result->Data->PaymentURL,
                        ]);
                        return ApiController::respondWithSuccess($all);
                    }
                }else{
                    $errors = [
                        'key'    => 'pay_commission',
                        'value'  => 'Something Went Wrong'
                    ];
                    return ApiController::respondWithErrorArray(array($errors));
                }

            }else{
                $errors = [
                    'key'  => 'pay_commission',
                    'value'   => trans('messages.lessCommission'),
                    'commission' => trans('messages.commissionIs') . -$commission,
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }
        }else{
            $success = [
                'key'    => 'get_commission',
                'value'  => trans('messages.UNotHaveCommission'),
            ];
            return ApiController::respondWithSuccess($success);
        }
    }
    public function history(Request $request)
    {
        $histories = History::whereUserId($request->user()->id)->get();
        if ($histories->count() > 0)
        {
            $arr = [];
            $lang = $request->header('Content-Language');
            foreach ($histories as $history)
            {
                array_push($arr , [
                    'id'       => $history->id,
                    'user_id'  => $history->user_id,
                    'user'     => $history->user->id,
                    'title'    => $request->header('Content-Language') == 'en' ? $history->en_title : ($request->header('Content-Language') == 'ur' ? $history->ur_title :$history->ar_title),
                    'amount'   => $history->price,
                    'currency'   => $lang == 'en' ? $history->user->country->en_currency : ($lang == 'ur' ? $history->user->country->ur_currency : $history->user->country->ar_currency),
                    'created_at' => $history->created_at->diffForHumans(),
                ]);
            }
            return ApiController::respondWithSuccess($arr);
        }else{
            $errors = [
                'key'   => 'history',
                'value' => trans('messages.NoHistory')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
    public function check_my_balance(Request $request){
        $user = $request->user();
        $lang = $request->header('Content-Language');
        $wallet = Electronic_wallet::whereUserId($user->id)->first();
        $arr = [];
        if ($wallet)
        {
            array_push($arr , [
                'amount' => $wallet->amount,
                'currency'   => $lang == 'en' ? $wallet->user->country->en_currency : ($lang == 'ur' ? $wallet->user->country->ur_currency : $wallet->user->country->ar_currency),
            ]);
            return ApiController::respondWithSuccess($arr);
        }else{
            array_push($arr , [
                'amount' => 0.0,
                'currency'   => $lang == 'en' ? $wallet->user->country->en_currency : ($lang == 'ur' ? $wallet->user->country->ur_currency : $wallet->user->country->ar_currency),
            ]);
            return ApiController::respondWithSuccess($arr);
        }
    }
    public function create_place(Request $request)
    {
        $rules = [
            'name' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'description' => 'nullable',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));
        // create new Place
//        $check = App\UserPlace::with('user')
//            ->whereHas('user' , function ($q) use ($request){
//                $q->where('association' , '1');
//            })
//            ->where('user_id',$request->user()->id)
//            ->first();
        $place = App\UserPlace::create([
            'name' => $request->name,
            'user_id' => $request->user()->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description' => $request->description == null ? null :$request->description,
        ]);
        return ApiController::respondWithSuccess(new App\Http\Resources\Place($place));
    }
    public function edit_place(Request $request , $id)
    {
        $place = App\UserPlace::find($id);
        if ($place != null && $request->user()->id == $place->user_id)
        {
            $rules = [
                'name' => 'sometimes',
                'latitude' => 'sometimes',
                'longitude' => 'sometimes',
                'description' => 'sometimes',
            ];
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails())
                return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));
            // Edit A Place
            $place->update([
                'name' => $request->name == null ? $place->name : $request->name,
                'user_id' => $request->user()->id,
                'latitude' => $request->latitude == null ? $place->latitude : $request->latitude,
                'longitude' => $request->longitude == null ? $place->longitude : $request->longitude,
                'description' => $request->description == null ? $place->description :$request->description,
            ]);
            return ApiController::respondWithSuccess(new App\Http\Resources\Place($place));
        }else{
            $errors = [
                'key'     => 'edit_place',
                'value'   => trans('messages.no_place')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
    public function delete_place(Request $request , $id)
    {
        $place = App\UserPlace::find($id);
        if ($place != null && $request->user()->id == $place->user_id)
        {
            // Delete A Place
            $place->delete();
            $success = [
                'key'   => 'delete_place',
                'value' => trans('messages.placeDeletedSuccessfully')
            ];
            return ApiController::respondWithSuccess($success);
        }else{
            $errors = [
                'key'     => 'delete_place',
                'value'   => trans('messages.no_place')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
    public function user_places(Request  $request)
    {
        $places = App\UserPlace::whereUserId($request->user()->id)->get();
        if ($places->count() > 0)
        {
            return ApiController::respondWithSuccess(App\Http\Resources\Place::collection($places));
        }else{
            $errros = [
                'key'    => 'user_places',
                'value'  => trans('messages.no_places')
            ];
            return ApiController::respondWithErrorArray(array($errros));
        }
    }
    public function pull_balance(Request  $request)
    {
        $user = $request->user();
        $electronic_wallet = Electronic_wallet::whereUserId($user->id)->first();
        if ($electronic_wallet)
        {
            if ($electronic_wallet->amount > 0)
            {
                $electronic_wallet->update([
                    'pull_request' => '1',
                ]);
                $success = [
                    'key'   => 'pull_my_balance',
                    'value' => trans('messages.pull_balance_requested')
                ];
                return ApiController::respondWithSuccess($success);
            }else{
                $errors = [
                    'key'   => 'pull_my_balance',
                    'value' => trans('messages.UNotHaveBalance')
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }
        }else{
            $errors = [
                'key'   => 'pull_my_balance',
                'value' => trans('messages.noWallet')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }

    public function user_make_complaint(Request $request)
    {
        $rules = [
            'complain'  => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));
        // create new complaint
        App\Complain::create([
            'user_id'  => $request->user()->id,
            'complain' => $request->complain,
        ]);
        $success = [
            'key'   => 'user_make_complaint',
            'value' => trans('messages.complain_created_successfully')
        ];
        return ApiController::respondWithSuccess($success);
    }
}
