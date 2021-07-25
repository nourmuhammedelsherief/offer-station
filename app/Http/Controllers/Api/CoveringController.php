<?php

namespace App\Http\Controllers\Api;

use App\Covering;
use App\Http\Resources\CoveringCollection;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class CoveringController extends Controller
{
    public function get_Covering_section_price()
    {
        $success = [
            'key' => 'get_Covering_section_price',
            'value' => Setting::find(1)->coverings_day_price,
        ];
        return ApiController::respondWithSuccess($success);
    }

    public function add_video_to_covering_section(Request $request)
    {
        $user = $request->user();
        $rules = [
            'payment_method' => 'required|in:0,1',   // 0  -> bank transfer   , 1 -> myfatoourah
            'price' => 'required|numeric',
            'days' => 'required|numeric',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $checkCovering = Covering::whereUserId($request->user()->id)
            ->where('status' , '1')
            ->first();
        if ($checkCovering)
        {
            $errors = [
                'key'     => 'add_video_to_covering_section',
                'value'   => trans('messages.uCoveringYourVideo')
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
        if ($request->payment_method == '0') {
            // bank transfer
            $rules = [
                'transfer_photo' => 'required|mimes:jpg,jpeg,png,gif,tif,png,bmp,bsd|max:5000',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

            // create new user covering
            Covering::create([
                'user_id' => $user->id,
                'video_link' => $user->video_link,
                'days' => $request->days,
                'price' => $request->price,
                'status' => '0',
                'end_date' => Carbon::now()->addDays($request->days),
                'transfer_photo' => $request->file('transfer_photo') == null ? null : UploadImage($request->file('transfer_photo'), 'transfer', '/uploads/transfer_photos'),
            ]);
            $success = [
                'key' => 'add_video_to_covering_section',
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
            $amount = $request->price;
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
                \"CustomerEmail\": \"email@mail.com\",\"InvoiceValue\": $amount,\"CallBackUrl\": \"http://127.0.0.1:8000/check-status-covering\",
                \"ErrorUrl\": \"https://youtube.com\",\"Language\": \"ar\",\"CustomerReference\" :\"ref 1\",
                \"CustomerCivilId\":12345678,\"UserDefinedField\": \"Custom field\",\"ExpireDate\": \"\",
                \"CustomerAddress\" :{\"Block\":\"\",\"Street\":\"\",\"HouseBuildingNo\":\"\",\"Address\":\"\",\"AddressInstructions\":\"\"},
                \"InvoiceItems\": [{\"ItemName\": \"$user->name\",\"Quantity\": 1,\"UnitPrice\": $amount}]}";
            $fatooraRes = MyFatoorah($token, $data);
            $result = json_decode($fatooraRes);
            if ($result->IsSuccess === true) {
//            return redirect($result->Data->PaymentURL);
                if ($result->IsSuccess === true) {
                    Covering::create([
                        'user_id' => $user->id,
                        'video_link' => $user->video_link,
                        'days' => $request->days,
                        'price' => $request->price,
                        'status' => '0',
                        'invoice_id' => $result->Data->InvoiceId,
                        'end_date' => Carbon::now()->addDays($request->end_date),
//                        'transfer_photo' => $request->file('transfer_photo') == null ? null : UploadImage($request->file('transfer_photo'), 'transfer', '/uploads/transfer_photos'),
                    ]);
                    $all = [];
                    array_push($all, [
                        'key' => 'add_video_to_covering_section',
                        'payment_url' => $result->Data->PaymentURL,
                    ]);
                    return ApiController::respondWithSuccess($all);
                }
            }
        }

    }
    public function fatooraStatusCovering()
    {
        $token = "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";
        $PaymentId = \Request::query('paymentId');
        $resData = MyFatoorahStatus($token, $PaymentId);
        $result = json_decode($resData);
        if ($result->IsSuccess === true && $result->Data->InvoiceStatus === "Paid") {
            $InvoiceId = $result->Data->InvoiceId;
            $covering = Covering::where('invoice_id', $InvoiceId)->first();
            $covering->update([
                'invoice_id' => null,
                'status' => '1',
            ]);

            return redirect()->to('/fatoora/success');
        }
    }
    public function covering_section()
    {
        $coverings = Covering::whereStatus('1')->simplePaginate();
        if($coverings->count() > 0)
        {
            return new CoveringCollection($coverings);
        }else{
            $errors = [
                'key'   => 'covering_section',
                'value' => trans('messages.on_coverings')
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }

}
