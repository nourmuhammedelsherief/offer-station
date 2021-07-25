<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Validator;
use App\User;
use App;
use Auth;

class ProfileController extends Controller
{
    //

    public function about_us()
    {
        $about = App\AboutUs::first();
        $all = [
            'title' => $about->title,
            'content' => $about->content,
        ];
        return ApiController::respondWithSuccess($all);
    }

    public function terms_and_conditions()
    {
        $terms = App\TermsCondition::first();
        $all = [
            'title' => $terms->title,
            'content' => $terms->content,
        ];
        return ApiController::respondWithSuccess($all);
    }


    /**
     * @get the werash range
     * @get_range
     */
    public function get_range()
    {
        $range = App\Setting::orderBy('created_at', 'desc')->first()->search_range;
        return ApiController::respondWithSuccess([
            'range' => intval($range)
        ]);
    }
    public function get_app_logo()
    {
        $logo = App\Setting::orderBy('created_at', 'desc')->first()->logo;
        return ApiController::respondWithSuccess([
            'logo' => asset('/uploads/logos/'.$logo)
        ]);
    }

}
