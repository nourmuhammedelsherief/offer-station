<?php

namespace App\Http\Controllers\Api;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use function Matrix\trace;
use Validator;
use App;
use Auth;
use App\User;
use App\City;
use App\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmCode;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    public function cities()
    {
        $cities = City::all();
        if ($cities->count()  > 0)
        {
            return ApiController::respondWithSuccess(App\Http\Resources\CityResource::collection($cities));
        }else{
            $errors = [
                'key'    => 'cities',
                'value'  => trans('messages.no_cities')
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }
    public function store_types()
    {
        $store_types = App\StoreType::all();
        if ($store_types->count() > 0)
        {
            return ApiController::respondWithSuccess(App\Http\Resources\StoreTypeResource::collection($store_types));
        }else{
            $errors = [
                'key'   => 'store_types',
                'value' => trans('messages.no_store_types'),
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }
    public function registerMobile(Request $request)
    {
        $rules = [
            'type' => 'required|in:0,1',   // 0 -> user   ,  1 -> store
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        /**
         * User Registration
         * @get the Registration Type From Settings
         * Setting::find(1)->user_verify
         */
        $user_verify = Setting::find(1)->user_verify;
        $store_verify = Setting::find(1)->store_verify;
        if (($request->type == '0' && $user_verify =='email') || ($request->type == '1' && $store_verify == 'email'))
        {
            $rules = [
                'email'   => 'required|unique:users',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails())
                return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

            $user = User::wherePhone_number($request->phone_number)->first();
            if ($user == null)
            {
                $code = mt_rand(1000, 9999);
                $data = [
                    'code'          => $code,
                ];
                Mail::to($request->email)->send(new App\Mail\Register($data));
                App\PhoneVerification::create([
                    'code'=>$code,
                    'phone_number'=>$request->email
                ]);
                return  ApiController::respondWithSuccess([]);
            }else{
                $errors = [
                    'key'    => 'register_mobile',
                    'value'  => trans('messages.uRegisteredBefore')
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }
        }elseif (($request->type == '0' && $user_verify =='mobile') || ($request->type == '1' && $store_verify == 'mobile'))
        {
            // phone_number registration
            $rules = [
                'phone_number'   => 'required|unique:users',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

            $code = mt_rand(1000, 9999);
            $check = substr($request->input('phone_number'), 0, 2) === "05";
            if ($check == true)
            {
                $phone = '966'.ltrim($request->phone_number , '0');
            }else{
                $phone = $request->phone_number;
            }
            $user = User::wherePhone_number($request->phone_number)->first();
            if ($user == null)
            {
                $body = trans('messages.confirmation_code').$code;
                taqnyatSms($body , $phone);
            }else{
                $errors = [
                    'key'    => 'user_register_mobile',
                    'value'  => trans('messages.uRegisteredBefore')
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }
            $created = App\PhoneVerification::create([
                'code'=>$code,
                'phone_number'=>$request->phone_number
            ]);
            return  ApiController::respondWithSuccess([]);
        }
    }
    public function register_phone_post(Request $request)
    {
        $rules = [
            'type' => 'required|in:0,1',   // 0 -> user   ,  1 -> store
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        /**
         * User Registration
         * @get the Registration Type From Settings
         * Setting::find(1)->user_verify
         */
        $user_verify = Setting::find(1)->user_verify;
        $store_verify = Setting::find(1)->store_verify;
        if (($request->type == '0' && $user_verify =='email') || ($request->type == '1' && $store_verify == 'email'))
        {
            $rules = [
                'code' => 'required',
                'email' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $user= App\PhoneVerification::where('phone_number',$request->email)->orderBy('id','desc')->first();

            if ($user){

                if($user->code == $request->code){
                    $successLogin = ['key'=>'message',
                        'value'=> trans('messages.activation_code_success')
                    ];
                    return ApiController::respondWithSuccess($successLogin);
                }else{
                    $errorsLogin = ['key'=>'message',
                        'value'=> trans('messages.error_code')
                    ];
                    return ApiController::respondWithErrorClient(array($errorsLogin));
                }

            }else{

                $errorsLogin = ['key'=>'message',
                    'value'=> trans('messages.error_code')
                ];
                return ApiController::respondWithErrorClient(array($errorsLogin));
            }
        }elseif (($request->type == '0' && $user_verify =='mobile') || ($request->type == '1' && $store_verify == 'mobile'))
        {
            $rules = [
                'code' => 'required',
                'phone_number' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $user= App\PhoneVerification::where('phone_number',$request->phone_number)->orderBy('id','desc')->first();
            if ($user){

                if($user->code == $request->code){
                    $successLogin = ['key'=>'message',
                        'value'=> trans('messages.activation_code_success')
                    ];
                    return ApiController::respondWithSuccess($successLogin);
                }else{
                    $errorsLogin = ['key'=>'message',
                        'value'=> trans('messages.error_code')
                    ];
                    return ApiController::respondWithErrorClient(array($errorsLogin));
                }

            }else{

                $errorsLogin = ['key'=>'message',
                    'value'=> trans('messages.error_code')
                ];
                return ApiController::respondWithErrorClient(array($errorsLogin));
            }
        }
    }
    public function resend_code(Request $request){
        $rules = [
            'type' => 'required|in:0,1',   // 0 -> user   ,  1 -> store
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        /**
         * User Registration
         * @get the Registration Type From Settings
         * Setting::find(1)->user_verify
         */
        $user_verify = Setting::find(1)->user_verify;
        $store_verify = Setting::find(1)->store_verify;
        if (($request->type == '0' && $user_verify =='email') || ($request->type == '1' && $store_verify == 'email'))
        {
            $rules = [
                'email' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $code = mt_rand(1000, 9999);
            $data = [
                'code'          => $code,
            ];
            Mail::to($request->email)->send(new App\Mail\Register($data));
            App\PhoneVerification::create([
                'code'=>$code,
                'phone_number'=>$request->email
            ]);
            return  ApiController::respondWithSuccess(trans('messages.success_send_code'));

        }elseif (($request->type == '0' && $user_verify =='mobile') || ($request->type == '1' && $store_verify == 'mobile'))
        {
            $rules = [
                'phone_number' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $check = substr($request->input('phone_number'), 0, 2) === "05";
            if ($check == true)
            {
                $phone = '966'.ltrim($request->phone_number , '0');
            }else{
                $phone = $request->phone_number;
            }
            $code = mt_rand(1000, 9999);
            $body = trans('messages.confirmation_code').$code;
            taqnyatSms($body , $phone);
            $created = App\PhoneVerification::create([
                'code'=>$code,
                'phone_number'=>$request->phone_number
            ]);
            return $created
                ? ApiController::respondWithSuccess( trans('messages.success_send_code'))
                : ApiController::respondWithServerErrorObject();
        }
    }
    public function register(Request $request)
    {
        $rules = [
            'type' => 'required|in:0,1',   // 0 -> user   ,  1 -> store
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        if ($request->type == '0')
        {
            // user registration
            $rules = [
                'phone_number'          => 'required|unique:users',
                'email'                 => 'required|unique:users',
                'name'                  => 'required|max:255',
                'password'              => 'required|string|min:6',
                'password_confirmation' => 'required|same:password',
                'photo'                 => 'required|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                'device_token'          => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

            $user = User::create([
                'email'              => $request->email,
                'phone_number'       => $request->phone_number,
                'name'               => $request->name,
                'password'           => Hash::make($request->password),
                'photo'              => $request->file('photo') == null ? null : UploadImage($request->file('photo'), 'photo', '/uploads/users'),
                'type'               => '1',
                'active'             => '1',
            ]);

            $user->update(['api_token' => generateApiToken($user->id, 10)]);

            App\PhoneVerification::where('phone_number',$request->email)->orderBy('id','desc')->delete();

            //save_device_token....
            $created = ApiController::createUserDeviceToken($user->id, $request->device_token, $request->device_type);

            return $user
                ? ApiController::respondWithSuccess(new App\Http\Resources\User($user))
                : ApiController::respondWithServerErrorArray();

        }
        else{
            $rules = [
                'store_type_id'         => 'required|exists:store_types,id',   // 4 -> electric store
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails())
                return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

            $store_type = App\StoreType::find($request->store_type_id);
            if ($store_type == 'متجر الكتروني' || $store_type == 'Website' || $request->store_type_id == '4')
            {
                $rules = [
                    'phone_number'          => 'required|unique:users',
                    'email'                 => 'required|unique:users',
//                    'city_id'               => 'required|exists:cities,id',
                    'ar_name'               => 'required|max:255',
                    'en_name'               => 'required|max:255',
                    'password'              => 'required|string|min:6',
                    'password_confirmation' => 'required|same:password',
                    'photo'                 => 'nullable|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                    'work_times'            => 'sometimes',
                    'contact_number'        => 'required',
                    'store_url'             => 'required',
                    'video_link'            => 'required',
                    'logo'                  => 'required|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                    'banner_photos*'        => 'sometimes|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                    'device_token'          => 'required',
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails())
                    return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

            }else{
                $rules = [
                    'phone_number'          => 'required|unique:users',
                    'email'                 => 'required|unique:users',
                    'city_id'               => 'required|exists:cities,id',
                    'ar_name'               => 'required|max:255',
                    'en_name'               => 'required|max:255',
                    'password'              => 'required|string|min:6',
                    'password_confirmation' => 'required|same:password',
                    'photo'                 => 'nullable|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                    'commercial_register'   => 'required|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                    'license'               => 'required|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                    'work_times'            => 'required',
                    'contact_number'        => 'required',
                    'logo'                  => 'required|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                    'banner_photos*'        => 'sometimes|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                    'latitude'              => 'required',
                    'longitude'             => 'required',
                    'device_token'          => 'required',
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails())
                    return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

            }

            // Store Registration

            $user = User::create([
                'store_type_id'       => $request->store_type_id,
                'city_id'             => $request->city_id == null ? null : $request->city_id,
                'email'               => $request->email,
                'phone_number'        => $request->phone_number,
                'name'                => $request->ar_name,
                'en_name'             => $request->en_name,
                'latitude'            => $request->latitude == null ? null : $request->latitude,
                'longitude'           => $request->longitude == null ? null : $request->longitude,
                'password'            => Hash::make($request->password),
                'photo'               => $request->file('photo') == null ? null : UploadImage($request->file('photo'), 'photo', '/uploads/users'),
                'logo'                => $request->file('logo') == null ? null : UploadImage($request->file('logo'), 'logo', '/uploads/logos'),
                'commercial_register' => $request->file('commercial_register') == null ? null : UploadImage($request->file('commercial_register'), 'commercial_register', '/uploads/commercial_registers'),
                'license'             => $request->file('license') == null ? null : UploadImage($request->file('license'), 'logo', '/uploads/licenses'),
                'work_times'          => $request->work_times == null ? null : $request->work_times,
                'video_link'          => $request->video_link == null ? null : $request->video_link,
                'contact_number'      => $request->contact_number == null ? null : $request->contact_number,
                'store_url'           => $request->store_url == null ? null : $request->store_url,
                'type'                => '2',
                'active'              => '0',
            ]);
            // store banners
            $name = $request->file('banner_photos');
            $fileFinalName_ar = "";
            if ($name != "") {
                if($files = $name) {
                    foreach ($files as $file) {
                        $images = new App\StoreBanner();
                        $fileFinalName_ar = time() . rand(1111,
                                9999) . '.' . $file->getClientOriginalExtension();
                        $path = base_path() . "/public/uploads/store_banners";
                        $images->user_id = $user->id;
                        $images->photo = $fileFinalName_ar;
                        $images->save();
                        $file->move($path, $fileFinalName_ar);
                    }
                }
            }

            $user->update(['api_token' => generateApiToken($user->id, 10)]);

            App\PhoneVerification::where('phone_number',$request->email)->orderBy('id','desc')->delete();

            //save_device_token....
            $created = ApiController::createUserDeviceToken($user->id, $request->device_token, $request->device_type);

            return $user
                ? ApiController::respondWithSuccess(new App\Http\Resources\User($user))
                : ApiController::respondWithServerErrorArray();

        }
    }
    public function get_store_banners($id)
    {
        $banners = App\StoreBanner::whereUserId($id)->get();
        if ($banners->count() > 0)
        {
            return ApiController::respondWithSuccess(App\Http\Resources\StoreBannerResource::collection($banners));
        }else{
            $errors = [
                'key'   => 'get_store_banners',
                'value' => 'not found'
            ];
            return ApiController::respondWithErrorClient(array($errors));
        }
    }
    public function get_store_by_id(Request $request , $id)
    {
        $user = User::find($id);
        if ($user)
        {
            return ApiController::respondWithSuccess(new App\Http\Resources\User($user));
        }else{
            $errors = [
                'value'   => 'not found'
            ];
            return ApiController::respondWithErrorClient(array($errors));
        }
    }
    public function login(Request $request) {

        $rules = [
            'type' => 'required|in:0,1',   // 0 -> user   ,  1 -> store
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        /**
         * User Registration
         * @get the Registration Type From Settings
         * Setting::find(1)->user_verify
         */
        $user_verify = Setting::find(1)->user_verify;
        $store_verify = Setting::find(1)->store_verify;
        if (($request->type == '0' && $user_verify =='email') || ($request->type == '1' && $store_verify == 'email'))
        {
            $rules = [
                'email'         => 'required',
                'password'      => 'required',
                'device_token'  => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

            $type = $request->type == '0' ? '1' : '2';
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'type' => $type]))
            {

                if (Auth::user()->active == 0){
                    $errors = ['key'=>'message',
                        'value'=> trans('messages.Sorry_your_membership_was_stopped_by_Management')
                    ];
                    return ApiController::respondWithErrorArray(array($errors));
                }

                //save_device_token....
                $created = ApiController::createUserDeviceToken(Auth::user()->id, $request->device_token, $request->device_type);

                $all = User::where('email', $request->email)->first();
                $all->update(['api_token' => generateApiToken($all->id, 10)]);
                $user =  User::where('email', $request->email)->first();

                return $created
                    ? ApiController::respondWithSuccess(new App\Http\Resources\User($user))
                    : ApiController::respondWithServerErrorArray();
            }
            else{
                $user = User::wherePhone_number($request->phone_number)->first();
                if ($user == null)
                {
                    $errors = [
                        'key'=>'message',
                        'value'=>trans('messages.Wrong_phone'),
                    ];
                    return ApiController::respondWithErrorNOTFoundArray(array($errors));
                }else{
                    $errors = [
                        'key'=>'message',
                        'value'=>trans('messages.error_password'),
                    ];
                    return ApiController::respondWithErrorNOTFoundArray(array($errors));
                }
            }
        }elseif(($request->type == '0' && $user_verify =='mobile') || ($request->type == '1' && $store_verify == 'mobile'))
        {
            $rules = [
                'phone_number'  => 'required',
                'password'      => 'required',
                'device_token'  => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

            $type = $request->type == '0' ? '1' : '2';
            if (Auth::attempt(['phone_number' => $request->phone_number, 'password' => $request->password, 'type'=>$type]))
            {

                if (Auth::user()->active == 0){
                    $errors = ['key'=>'message',
                        'value'=> trans('messages.Sorry_your_membership_was_stopped_by_Management')
                    ];
                    return ApiController::respondWithErrorArray(array($errors));
                }

                //save_device_token....
                $created = ApiController::createUserDeviceToken(Auth::user()->id, $request->device_token, $request->device_type);

                $all = User::where('phone_number', $request->phone_number)->first();
                $all->update(['api_token' => generateApiToken($all->id, 10)]);
                $user =  User::where('phone_number', $request->phone_number)->first();

                return $created
                    ? ApiController::respondWithSuccess(new App\Http\Resources\User($user))
                    : ApiController::respondWithServerErrorArray();
            }
            else{
                $user = User::wherePhone_number($request->phone_number)->first();
                if ($user == null)
                {
                    $errors = [
                        'key'=>'message',
                        'value'=>trans('messages.Wrong_phone'),
                    ];
                    return ApiController::respondWithErrorNOTFoundArray(array($errors));
                }else{
                    $errors = [
                        'key'=>'message',
                        'value'=>trans('messages.error_password'),
                    ];
                    return ApiController::respondWithErrorNOTFoundArray(array($errors));
                }
            }

        }
    }
    public function forgetPassword(Request $request) {
        $rules = [
            'type' => 'required|in:0,1',   // 0 -> user   ,  1 -> store
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        /**
         * User Registration
         * @get the Registration Type From Settings
         * Setting::find(1)->user_verify
         */
        $user_verify = Setting::find(1)->user_verify;
        $store_verify = Setting::find(1)->store_verify;
        if (($request->type == '0' && $user_verify =='email') || ($request->type == '1' && $store_verify == 'email'))
        {
            $rules = [
                'email' => 'required|email',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $type = $request->type == '0' ? '1' : '2';
            $user = User::where('email',$request->email)
                ->where('type' , $type)
                ->first();
            if ($user != null)
            {
                $code = mt_rand(1000, 9999);
                $data = [
                    'code'          => $code,
                ];
                Mail::to($request->email)->send(new App\Mail\Register($data));
                $updated=  App\User::where('email',$request->email)
                    ->where('type' , $type)
                    ->update([
                        'verification_code'=>$code,
                    ]);
                $success = ['key'=>'message',
                    'value'=> trans('messages.success_send_code')
                ];
                return $updated
                    ? ApiController::respondWithSuccess($success)
                    : ApiController::respondWithServerErrorObject();
            }else{
                $errorsLogin = ['key'=>'message',
                    'value'=> trans('messages.wrong_email')
                ];
                return ApiController::respondWithErrorClient(array($errorsLogin));
            }
        }elseif (($request->type == '0' && $user_verify =='mobile') || ($request->type == '1' && $store_verify == 'mobile'))
        {
            $rules = [
                'phone_number' => 'required|numeric',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $type = $request->type == '0' ? '1' : '2';
            $user = User::where('phone_number',$request->phone_number)
                ->where('type' , $type)
                ->first();
            if ($user != null)
            {
                $code = mt_rand(1000, 9999);
                $check = substr($request->input('phone_number'), 0, 2) === "05";
                if ($check == true)
                {
                    $phone = '966'.ltrim($request->phone_number , '0');
                }else{
                    $phone = $request->phone_number;
                }
                $body = trans('messages.confirmation_code').$code;
                taqnyatSms($body , $phone);
                $updated=  App\User::where('phone_number',$request->phone_number)
                    ->where('type' , $type)
                    ->update([
                        'verification_code'=>$code,
                    ]);
                $success = ['key'=>'message',
                    'value'=> trans('messages.success_send_code')
                ];

                return $updated
                    ? ApiController::respondWithSuccess($success)
                    : ApiController::respondWithServerErrorObject();
            }else{
                $errorsLogin = ['key'=>'message',
                    'value'=> trans('messages.Wrong_phone')
                ];
                return ApiController::respondWithErrorClient(array($errorsLogin));
            }
        }
    }
    public function confirmResetCode(Request $request){

        $rules = [
            'type' => 'required|in:0,1',   // 0 -> user   ,  1 -> store
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        /**
         * User Registration
         * @get the Registration Type From Settings
         * Setting::find(1)->user_verify
         */
        $user_verify = Setting::find(1)->user_verify;
        $store_verify = Setting::find(1)->store_verify;
        if (($request->type == '0' && $user_verify =='email') || ($request->type == '1' && $store_verify == 'email'))
        {
            $rules = [
                'email' => 'required|email',
                'code' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
            $type = $request->type == '0' ? '1' : '2';

            $user= User::where('email',$request->email)
                ->where('verification_code',$request->code)
                ->where('type' , $type)
                ->first();
            if ($user){
                $updated=  App\User::where('email',$request->email)
                    ->where('verification_code',$request->code)
                    ->where('type' , $type)
                    ->update([
                        'verification_code'=>null
                    ]);
                $success = ['key'=>'message',
                    'value'=> trans('messages.code_success')
                ];
                return $updated
                    ? ApiController::respondWithSuccess($success)
                    : ApiController::respondWithServerErrorObject();
            }else{

                $errorsLogin = ['key'=>'message',
                    'value'=> trans('messages.error_code')
                ];
                return ApiController::respondWithErrorClient(array($errorsLogin));
            }
        }elseif (($request->type == '0' && $user_verify =='mobile') || ($request->type == '1' && $store_verify == 'mobile'))
        {
            $rules = [
                'phone_number' => 'required',
                'code' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $type = $request->type == '0' ? '1' : '2';
            $user= App\User::where('phone_number',$request->phone_number)
                ->where('verification_code',$request->code)
                ->where('type' , $type)
                ->first();
            if ($user){
                $updated=  App\User::where('phone_number',$request->phone_number)
                    ->where('verification_code',$request->code)
                    ->where('type' , $type)
                    ->update([
                        'verification_code'=>null
                    ]);
                $success = ['key'=>'message',
                    'value'=> trans('messages.code_success')
                ];
                return $updated
                    ? ApiController::respondWithSuccess($success)
                    : ApiController::respondWithServerErrorObject();
            }else{

                $errorsLogin = ['key'=>'message',
                    'value'=> trans('messages.error_code')
                ];
                return ApiController::respondWithErrorClient(array($errorsLogin));
            }
        }
    }
    public function resetPassword(Request $request) {

        $rules = [
            'type' => 'required|in:0,1',   // 0 -> user   ,  1 -> store
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        /**
         * User Registration
         * @get the Registration Type From Settings
         * Setting::find(1)->user_verify
         */
        $user_verify = Setting::find(1)->user_verify;
        $store_verify = Setting::find(1)->store_verify;
        if (($request->type == '0' && $user_verify =='email') || ($request->type == '1' && $store_verify == 'email'))
        {
            $rules = [
                'email'                 => 'required|email',
                'password'              => 'required',
                'password_confirmation' => 'required|same:password',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $user = User::where('email',$request->email)->first();
            if($user)
                $updated = $user->update(['password' => Hash::make($request->password)]);
            else{
                $errorsLogin = ['key'=>'message',
                    'value'=> trans('messages.wrong_email')
                ];
                return ApiController::respondWithErrorClient(array($errorsLogin));
            }
            return $updated
                ? ApiController::respondWithSuccess(trans('messages.Password_reset_successfully'))
                : ApiController::respondWithServerErrorObject();
        }elseif(($request->type == '0' && $user_verify =='mobile') || ($request->type == '1' && $store_verify == 'mobile')) {
            $rules = [
                'phone_number'          => 'required|numeric',
                'password'              => 'required',
                'password_confirmation' => 'required|same:password',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $user = User::where('phone_number',$request->phone_number)->first();
            if($user)
                $updated = $user->update(['password' => Hash::make($request->password)]);
            else{
                $errorsLogin = ['key'=>'message',
                    'value'=> trans('messages.Wrong_phone')
                ];
                return ApiController::respondWithErrorClient(array($errorsLogin));
            }
            return $updated
                ? ApiController::respondWithSuccess(trans('messages.Password_reset_successfully'))
                : ApiController::respondWithServerErrorObject();
        }
    }
    public function changePassword(Request $request) {

        $rules = [
            'current_password'      => 'required',
            'new_password'          => 'required',
            'password_confirmation' => 'required|same:new_password',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $error_old_password = ['key'=>'message',
            'value'=> trans('messages.error_old_password')
        ];
        if (!(Hash::check($request->current_password, $request->user()->password)))
                return ApiController::respondWithErrorNOTFoundObject(array($error_old_password));
//        if( strcmp($request->current_password, $request->new_password) == 0 )
//            return response()->json(['status' => 'error', 'code' => 404, 'message' => 'New password cant be the same as the old one.']);

        //update-password-finally ^^
        $updated = $request->user()->update(['password' => Hash::make($request->new_password)]);

        $success_password = ['key'=>'message',
            'value'=> trans('messages.Password_reset_successfully')
        ];

        return $updated
            ? ApiController::respondWithSuccess($success_password)
            : ApiController::respondWithServerErrorObject();
    }
    public function change_phone_number(Request $request)
    {
        $user = $request->user();
        /**
         * User Registration
         * @get the Registration Type From Settings
         * Setting::find(1)->user_verify
         */
        $user_verify = Setting::find(1)->user_verify;
        $store_verify = Setting::find(1)->store_verify;
        if (($user->type == '1' && $user_verify =='email') || ($user->type == '2' && $store_verify == 'email'))
        {
            $rules = [
                'email' => 'required|email|unique:users,email,'.$request->user()->id,
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $code = mt_rand(1000, 9999);
            $data = [
                'code'          => $code,
            ];
            Mail::to($request->email)->send(new App\Mail\Register($data));
            $updated=  User::where('id',Auth::user()->id)->update([
                'verification_code'=>$code,
            ]);
            $success = ['key'=>'message',
                'value'=> trans('messages.success_send_code')
            ];
            return $updated
                ? ApiController::respondWithSuccess($success)
                : ApiController::respondWithServerErrorObject();
        }elseif (($user->type == '1' && $user_verify =='mobile') || ($user->type == '2' && $store_verify == 'mobile'))
        {
            $rules = [
                'phone_number' => 'required|numeric|unique:users,phone_number,'.$request->user()->id,
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $code = mt_rand(1000, 9999);
            $check = substr($request->input('phone_number'), 0, 2) === "05";
            if ($check == true)
            {
                $phone = '966'.ltrim($request->phone_number , '0');
            }else{
                $phone = $request->phone_number;
            }
            $body = trans('messages.confirmation_code').$code;
            taqnyatSms($body , $phone);
            $updated=  User::where('id',Auth::user()->id)->update([
                'verification_code'=>$code,
            ]);
            $success = ['key'=>'message',
                'value'=> trans('messages.success_send_code')
            ];
            return $updated
                ? ApiController::respondWithSuccess($success)
                : ApiController::respondWithServerErrorObject();

        }
    }
    public function check_code_changeNumber(Request $request)
    {
        $user = $request->user();
        /**
         * User Registration
         * @get the Registration Type From Settings
         * Setting::find(1)->user_verify
         */
        $user_verify = Setting::find(1)->user_verify;
        $store_verify = Setting::find(1)->store_verify;
        if (($user->type == '1' && $user_verify =='email') || ($user->type == '2' && $store_verify == 'email'))
        {
            $rules = [
                'code' => 'required',
                'email' => 'required|email|unique:users,email,'.$request->user()->id,
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $user= User::where('id',Auth::user()->id)->where('verification_code', $request->code)->first();
            if ($user){
                $updated=  $user->update([
                    'verification_code'=>null,
                    'email'=>$request->email,
                ]);

                $success = ['key'=>'message',
                    'value'=> trans('messages.email_changed_successfully')
                ];
                return $updated
                    ? ApiController::respondWithSuccess($success)
                    : ApiController::respondWithServerErrorObject();
            }else{

                $errorsLogin = ['key'=>'message',
                    'value'=> trans('messages.error_code')
                ];
                return ApiController::respondWithErrorClient(array($errorsLogin));
            }
        }elseif (($user->type == '1' && $user_verify =='mobile') || ($user->type == '2' && $store_verify == 'mobile')) {
            $rules = [
                'code' => 'required',
                'phone_number' => 'required|numeric|unique:users,phone_number,'.$request->user()->id,
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $user= User::where('id',Auth::user()->id)->where('verification_code', $request->code)->first();
            if ($user){
                $updated=  $user->update([
                    'verification_code'=>null,
                    'phone_number'=>$request->phone_number,
                ]);

                $success = ['key'=>'message',
                    'value'=> trans('messages.phone_changed_successfully')
                ];
                return $updated
                    ? ApiController::respondWithSuccess($success)
                    : ApiController::respondWithServerErrorObject();
            }else{

                $errorsLogin = ['key'=>'message',
                    'value'=> trans('messages.error_code')
                ];
                return ApiController::respondWithErrorClient(array($errorsLogin));
            }
        }
    }
    public function user_edit_account(Request $request)
    {
        $user = $request->user();
        /**
         * User Registration
         * @get the Registration Type From Settings
         * Setting::find(1)->user_verify
         */
        $user_verify = Setting::find(1)->user_verify;
        $store_verify = Setting::find(1)->store_verify;
        if ($user->type == '1')
        {
            if ($user_verify == 'email')
            {
                $rules = [
                    'phone_number'    => 'nullable|numeric|unique:users,phone_number,'.$request->user()->id,
                    'name'            => 'nullable|max:255',
                    'photo'           => 'nullable|mimes:jpeg,bmp,png,jpg|max:5000',
                ];
            }else{
                $rules = [
                    'email'           => 'nullable|email|unique:users,email,'.$request->user()->id,
                    'name'            => 'nullable|max:255',
                    'photo'           => 'nullable|mimes:jpeg,bmp,png,jpg|max:5000',
                ];
            }

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $user= User::where('id',$request->user()->id)->first();

            $user->update([
                'phone_number'    =>  $request->phone_number == null ? $user->phone_number : $request->phone_number,
                'email'           =>  $request->email == null ? $user->email : $request->email,
                'name'            =>  $request->name == null ? $user->name : $request->name,
                'photo'           =>  $request->photo == null ? $user->photo : UploadImageEdit($request->file('photo'), 'photo', '/uploads/users',$request->user()->photo),
            ]);
            return ApiController::respondWithSuccess(new \App\Http\Resources\User($user));
        }
        else{
            $rules = [
                'store_type_id'         => 'sometimes|exists:store_types,id',
                'email'                 => 'sometimes|email|unique:users,email,'.$request->user()->id,
                'phone_number'          => 'sometimes|unique:users,phone_number,'.$request->user()->id,
                'city_id'               => 'sometimes',
                'ar_name'               => 'sometimes|max:255',
                'en_name'               => 'sometimes|max:255',
                'photo'                 => 'nullable|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                'commercial_register'   => 'sometimes|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                'license'               => 'sometimes|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                'work_times'            => 'sometimes',
                'contact_number'        => 'sometimes',
                'store_url'             => 'sometimes',
                'video_link'            => 'sometimes',
                'latitude'              => 'sometimes',
                'longitude'             => 'sometimes',
                'logo'                  => 'sometimes|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
//                'banner_photos*'        => 'sometimes|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
                return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

            $user= User::where('id',$request->user()->id)->first();
            $user->update([
                'store_type_id'       => $request->store_type_id == null ? $user->store_type_id : $request->store_type_id,
                'city_id'             => $request->city_id == null ? null : $request->city_id,
                'email'               => $request->email == null ? $user->email : $request->email,
                'phone_number'        => $request->phone_number == null ? $user->phone_number : $request->phone_number,
                'name'                => $request->ar_name == null ? $user->name : $request->ar_name,
                'en_name'             => $request->en_name == null ? $user->en_name : $request->en_name,
                'latitude'            => $request->latitude == null ? $user->latitude : $request->latitude,
                'longitude'           => $request->longitude == null ? $user->longitude : $request->longitude,
                'photo'               => $request->photo == null ? $user->photo : UploadImageEdit($request->file('photo'), 'photo', '/uploads/users',$request->user()->photo),
                'logo'                => $request->file('logo') == null ? $user->logo : UploadImageEdit($request->file('logo'), 'logo', '/uploads/logos' , $user->logo),
                'commercial_register' => $request->file('commercial_register') == null ? $user->commercial_register : UploadImageEdit($request->file('commercial_register'), 'commercial_register', '/uploads/commercial_registers' , $user->commercial_register),
                'license'             => $request->file('license') == null ? $user->license : UploadImageEdit($request->file('license'), 'logo', '/uploads/licenses' , $user->license),
                'work_times'          => $request->work_times == null ? $user->work_times : $request->work_times,
                'video_link'          => $request->video_link == null ? null : $request->video_link,
                'contact_number'      => $request->contact_number == null ? $user->video_link : $request->contact_number,
                'store_url'           => $request->store_url == null ? $user->store_url : $request->store_url,
                'type'                => '2',
//                'active'              => '0',
            ]);
            return $user
                ? ApiController::respondWithSuccess(new App\Http\Resources\User($user))
                : ApiController::respondWithServerErrorArray();

        }
    }
    public function logout(Request $request)
    {

        $rules = [
            'device_token'     => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $exists = App\UserDevice::where('id',$request->user()->id)->where('device_token',$request->device_token)->get();

        if (count($exists) !== 0){
            foreach ($exists  as $new){
                $new->delete();
            }

        }
        $users=  User::where('id',$request->user()->id)->first()->update(
            [
                'api_token'=>null
            ]
        );
        return $users
            ? ApiController::respondWithSuccess([])
            : ApiController::respondWithServerErrorArray();
    }
    public function store_add_banner_photo(Request  $request)
    {
        $user = $request->user();
        $rules = [
            'photo'           => 'required|mimes:jpeg,bmp,png,jpg|max:5000',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        // create new user banner photo
        $banner = App\StoreBanner::create([
            'user_id'   => $user->id,
            'photo'     => UploadImage($request->file('photo'), 'photo', '/uploads/store_banners'),
        ]);
        return ApiController::respondWithSuccess(new App\Http\Resources\StoreBannerResource($banner));
    }
    public function store_delete_banner_photo($id)
    {
        $banner = App\StoreBanner::find($id);
        if ($banner)
        {
            if (file_exists(public_path('uploads/store_banners/' . $banner->photo))) {
                unlink(public_path('uploads/store_banners/' . $banner->photo));
            }
            $banner->delete();
            $success = [
                'key'   => 'store_delete_banner_photo',
                'value' => trans('messages.Photo_successfully_deleted'),
            ];
            return ApiController::respondWithSuccess($success);
        }else{
            $errors = [
                'key'   => 'store_delete_banner_photo',
                'value' => trans('messages.photo_not_found'),
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }
}
