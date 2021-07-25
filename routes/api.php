<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/**
 *  Developed By Nour Muhammad El sheriff
 *  01119399781
 *  nourmuhammed20121994@gmail.com
 */
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('v1')->group(function () {
    Route::group(['middleware' =>  ['cors' , 'localization']], function () {

        Route::post( '/register_mobile','Api\AuthController@registerMobile');
        Route::post( '/phone_verification','Api\AuthController@register_phone_post');
        Route::post( '/resend_code','Api\AuthController@resend_code');
        Route::post( '/register','Api\AuthController@register');
        Route::post( '/login','Api\AuthController@login');
        Route::post( '/forget_password','Api\AuthController@forgetPassword');
        Route::post( '/confirm_reset_code','Api\AuthController@confirmResetCode');
        Route::post( '/reset_password','Api\AuthController@resetPassword');

        Route::get( '/terms_and_conditions','Api\ProfileController@terms_and_conditions');
        Route::get( '/about_us','Api\ProfileController@about_us');
        Route::get( '/get_range','Api\ProfileController@get_range');
        Route::get( '/get_app_logo','Api\ProfileController@get_app_logo');
        Route::post('/disconnect-room', 'Api\MessageController@disconnect_room');
        Route::get('/settings', 'Api\DetailsController@settings');
        Route::get('/get_store_banners/{id}', 'Api\AuthController@get_store_banners');

        Route::get( '/get_store_by_id/{id}','Api\AuthController@get_store_by_id');

        Route::get( '/cities','Api\AuthController@cities');
        // start store routes
        Route::get( '/store_types','Api\AuthController@store_types');
        Route::post( '/get_stores','Api\StoreController@get_stores');

        // start offers routes
//        Route::get( '/categories','Api\OfferController@categories');
        Route::get('/offers', 'Api\OfferController@offers');
        Route::post('/filter_search', 'Api\OfferController@filter_search');
        Route::get('/get_offer_by_id/{id}', 'Api\OfferController@get_offer_by_id');
        Route::get('/discriminate_offers/{id}', 'Api\OfferController@discriminate_offers');
        Route::get('/get_offer_discriminate_info', 'Api\OfferController@get_offer_discriminate_info');
        Route::get('/bank_info', 'Api\OfferController@bank_info');

        Route::get( '/get_Covering_section_price','Api\CoveringController@get_Covering_section_price');
        Route::get( '/covering_section','Api\CoveringController@covering_section');
        Route::get('/get_news', 'Api\OfferController@get_news');
        Route::get('/offers_by_store_id/{id}', 'Api\OfferController@offers_by_store_id');


    });

    Route::group(['middleware' => ['auth:api', 'cors' , 'localization']], function () {

        /*notification*/
        Route::get('/list_notifications', 'Api\ApiController@listNotifications');
        Route::post('/delete_Notifications/{id}', 'Api\ApiController@delete_Notifications');

        Route::get('/read_all_notification', 'Api\ApiController@read_all_notification');
        Route::get('/read_notification/{id}', 'Api\ApiController@read_notification');

        /*notification*/

        /**
         *  Start User Routes
         */
        //====================user app ====================
        Route::post( '/change_password','Api\AuthController@changePassword');
        Route::post( '/change_phone_number_or_email','Api\AuthController@change_phone_number');
        Route::post( '/check_code_change_phone_number_or_email','Api\AuthController@check_code_changeNumber');
        Route::post( '/edit_account','Api\AuthController@user_edit_account');
        Route::post( '/store_add_banner_photo','Api\AuthController@store_add_banner_photo');
        Route::get( '/store_delete_banner_photo/{id}','Api\AuthController@store_delete_banner_photo');

        //===============logout========================
        Route::post('/logout','Api\AuthController@logout');
        /**
         *  End User Routes
         */

        /**
         *  Start Offers Routes
         */
        Route::post('/create_offer', 'Api\OfferController@create_offer');
        Route::post('/edit_offer/{id}', 'Api\OfferController@edit_offer');
        Route::get('/delete_offer/{id}', 'Api\OfferController@delete_offer');
        Route::get('/delete_offer_photo/{id}', 'Api\OfferController@delete_offer_photo');
        Route::get('/my_offers', 'Api\OfferController@my_offers');
        Route::get('/my_finished_offers', 'Api\OfferController@finished_offers');
        Route::post('/activate_finished_offer/{id}', 'Api\OfferController@activate_finished_offer');
        Route::post('/discriminate_offer/{id}', 'Api\OfferController@discriminate_offer');
        Route::get('/user_view_offer/{id}', 'Api\OfferController@user_view_offer');
        Route::get('/customer_use_offer/{id}', 'Api\OfferController@customer_use_offer');
        Route::get('/customer_offers', 'Api\OfferController@customer_offers');
        Route::get('/add_offer_to_favorite/{id}', 'Api\OfferController@add_offer_to_favorite');
        Route::get('/my_favorite_offers', 'Api\OfferController@my_favorite_offers');
        Route::get('/remove_favorite_offer/{id}', 'Api\OfferController@remove_favorite_offer');
        Route::post('/user_make_report/{id}', 'Api\OfferController@user_make_report');
        Route::get('/my_discriminate_offers', 'Api\OfferController@my_discriminate_offers');


        Route::post('/user_make_complaint', 'Api\UserController@user_make_complaint');

        /**
         *  End Offers Routes
         */



        // Covering Routes
        Route::post( '/add_video_to_covering_section','Api\CoveringController@add_video_to_covering_section');

        // News

//    ===========refreshToken ====================

        Route::post('/refresh-device-token','Api\DetailsController@refreshDeviceToken');
        Route::post('/refreshToken','Api\DetailsController@refreshToken');
        //===============logout========================
        Route::post('/logout','Api\AuthController@logout');

    });
});
