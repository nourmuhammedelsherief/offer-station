<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
//    \Illuminate\Support\Facades\Artisan::call('check::commission');
    return view('welcome');
});
Route::get('/check-status/{id?}/{id1?}', 'Api\OfferController@fatooraStatus');
Route::get('/check-status-covering/{id?}/{id1?}', 'Api\CoveringController@fatooraStatusCovering');

Route::get('/', function(){
    return redirect()->to('/admin/login');
});

Route::get('/fatoora/success', function(){
    return view('fatoora');
})->name('fatoora-success');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
/*admin panel routes*/

Route::get('/admin/home', ['middleware'=> 'auth:admin', 'uses'=>'AdminController\HomeController@index'])->name('admin.home');

Route::prefix('admin')->group(function () {

    Route::get('login', 'AdminController\Admin\LoginController@showLoginForm')->name('admin.login');
    Route::post('login', 'AdminController\Admin\LoginController@login')->name('admin.login.submit');
    Route::get('password/reset', 'AdminController\Admin\ForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
    Route::post('password/email', 'AdminController\Admin\ForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
    Route::get('password/reset/{token}', 'AdminController\Admin\ResetPasswordController@showResetForm')->name('admin.password.reset');
    Route::post('password/reset', 'AdminController\Admin\ResetPasswordController@reset')->name('admin.password.update');
    Route::post('logout', 'AdminController\Admin\LoginController@logout')->name('admin.logout');


    Route::group(['middleware'=> ['web','auth:admin']],function(){
        // public notifications
        Route::get('public_notifications' , 'AdminController\HomeController@public_notifications')->name('public_notifications');
        Route::post('store_public_notifications' , 'AdminController\HomeController@store_public_notifications')->name('storePublicNotification');

        Route::get('category_notifications' , 'AdminController\HomeController@category_notifications')->name('category_notifications');
        Route::post('storeCategoryNotification' , 'AdminController\HomeController@store_category_notifications')->name('storeCategoryNotification');

        Route::get('user_notifications' , 'AdminController\HomeController@user_notifications')->name('user_notifications');
        Route::post('storeUserNotification' , 'AdminController\HomeController@store_user_notifications')->name('storeUserNotification');


        Route::get('setting','AdminController\SettingController@index')->name('settings');
        Route::get('bank/setting','AdminController\SettingController@index_bank')->name('settingsBank');
        Route::get('sms/setting','AdminController\SettingController@index_sms')->name('settingsSms');
        Route::get('offers/setting','AdminController\SettingController@index_offers')->name('settingsOffer');
        Route::post('add/settings','AdminController\SettingController@store');

        Route::get('pages/about','AdminController\PageController@about');
        Route::post('add/pages/about','AdminController\PageController@store_about');

        Route::get('pages/terms','AdminController\PageController@terms');
        Route::post('add/pages/terms','AdminController\PageController@store_terms');

        Route::get('users/{type}','AdminController\UserController@index');
        Route::get('add/user/{type}','AdminController\UserController@create');
        Route::post('add/user/{type}','AdminController\UserController@store');
        Route::get('edit/user/{id}/{type}','AdminController\UserController@edit');
        Route::get('edit/userAccount/{id}/{type}','AdminController\UserController@edit_account');
        Route::post('update/userAccount/{id}/{type}','AdminController\UserController@update_account');
        Route::post('update/user/{id}/{type}','AdminController\UserController@update');
        Route::post('update/pass/{id}','AdminController\UserController@update_pass');
        Route::post('update/privacy/{id}','AdminController\UserController@update_privacy');
        Route::get('update/active_user/{id}', 'AdminController\UserController@privacy_user')->name('privacy_user');
        Route::get('delete/{id}/user','AdminController\UserController@destroy');
        Route::get('remove_store_photo/{id}', 'AdminController\UserController@remove_store_photo')->name('imageStoreRemove');
        // =============================== start Store Type ==============================
        Route::get('store_types','AdminController\StoreTypeController@index')->name('StoreType');
        Route::get('store_types/create','AdminController\StoreTypeController@create')->name('createStoreType');
        Route::post('store_types/store','AdminController\StoreTypeController@store')->name('storeStoreType');
        Route::get('store_types/{id}/edit','AdminController\StoreTypeController@edit')->name('editStoreType');
        Route::post('store_types/update/{id}','AdminController\StoreTypeController@update')->name('updateStoreType');
        Route::get('store_types/delete/{id}','AdminController\StoreTypeController@destroy')->name('deleteStoreType');
        // =============================== end Store Type ============================TruckType

        // =============================== start City ==============================
        Route::get('cities','AdminController\CityController@index')->name('City');
        Route::get('cities/create','AdminController\CityController@create')->name('createCity');
        Route::post('cities/store','AdminController\CityController@store')->name('storeCity');
        Route::get('cities/{id}/edit','AdminController\CityController@edit')->name('editCity');
        Route::post('cities/update/{id}','AdminController\CityController@update')->name('updateCity');
        Route::get('cities/delete/{id}','AdminController\CityController@destroy')->name('deleteCity');
        // =============================== end City ============================

        // =============================== start News ==============================
        Route::get('news','AdminController\NewsController@index')->name('News');
        Route::get('news/create','AdminController\NewsController@create')->name('createNews');
        Route::post('news/store','AdminController\NewsController@store')->name('storeNews');
        Route::get('news/{id}/edit','AdminController\NewsController@edit')->name('editNews');
        Route::post('news/update/{id}','AdminController\NewsController@update')->name('updateNews');
        Route::get('news/delete/{id}','AdminController\NewsController@destroy')->name('deleteNews');
        // =============================== end News ============================

        // offers routes

        // =============================== start offers ==============================
        Route::get('offers/{active?}','AdminController\OfferController@index')->name('Offer');
        Route::get('terminated/offers','AdminController\OfferController@terminated')->name('terminated');
        Route::get('offer/create','AdminController\OfferController@create')->name('createOffer');
        Route::post('offer/store','AdminController\OfferController@store')->name('storeOffer');
        Route::get('offer/{id}/edit','AdminController\OfferController@edit')->name('editOffer');
        Route::post('offer/update/{id}','AdminController\OfferController@update')->name('updateOffer');
        Route::get('offer/delete/{id}','AdminController\OfferController@destroy')->name('deleteOffer');
        Route::get('remove_offer_photo/{id}', 'AdminController\OfferController@remove_offer_photo')->name('imageOfferRemove');
        Route::get('update/active_offer/{id}', 'AdminController\OfferController@is_active')->name('privacy_offer');
        // =============================== end offers ============================


        Route::get('offer_transfer','AdminController\OfferController@offer_transfer')->name('offer_transfer');
        Route::get('transferDone/{id}','AdminController\OfferController@transferDone')->name('transferDone');
        Route::get('transferNotDone/{id}','AdminController\OfferController@transferNotDone')->name('transferNotDone');
        Route::get('discriminate_places','AdminController\SettingController@discriminate_places')->name('discriminate_places');
        Route::get('editDiscriminate/{id}','AdminController\SettingController@editDiscriminate')->name('editDiscriminate');
        Route::post('updateDiscriminate_place/{id}','AdminController\SettingController@updateDiscriminate_place')->name('updateDiscriminate_place');
        Route::get('complaints','AdminController\SettingController@complaints')->name('complaints');
        Route::get('complaints/delete/{id}','AdminController\SettingController@deleteComplaint')->name('deleteComplaint');
        Route::get('reports','AdminController\SettingController@reports')->name('reports');
        Route::get('reports/delete/{id}','AdminController\SettingController@deleteReport')->name('deleteReports');


        // coverings
        Route::get('coverings','AdminController\OfferController@coverings')->name('coverings');
        Route::get('coveringsDone/{id}','AdminController\OfferController@coveringsDone')->name('coveringsDone');
        Route::get('coveringNotDone/{id}','AdminController\OfferController@coveringNotDone')->name('coveringNotDone');

        // =============================== start categories ==============================
        Route::get('categories','AdminController\CategoryController@index')->name('Category');
        Route::get('categories/create','AdminController\CategoryController@create')->name('createCategory');
        Route::post('categories/store','AdminController\CategoryController@store')->name('storeCategory');
        Route::get('categories/{id}/edit','AdminController\CategoryController@edit')->name('editCategory');
        Route::post('categories/update/{id}','AdminController\CategoryController@update')->name('updateCategory');
        Route::get('categories/delete/{id}','AdminController\CategoryController@destroy')->name('deleteCategory');
        // =============================== end categories ============================


        // =============================== start Ranges ==============================
        Route::get('range','AdminController\RangeController@index')->name('Range');
        Route::get('range/create','AdminController\RangeController@create')->name('createRange');
        Route::post('range/store','AdminController\RangeController@store')->name('storeRange');
        Route::get('range/{id}/edit','AdminController\RangeController@edit')->name('editRange');
        Route::post('range/update/{id}','AdminController\RangeController@update')->name('updateRange');
        Route::get('range/delete/{id}','AdminController\RangeController@destroy')->name('deleteRange');
        // =============================== end Ranges ============================

        // Admins Route
        Route::resource('admins', 'AdminController\AdminController');
        Route::get('/profile','AdminController\AdminController@my_profile');
        Route::post('/profileEdit','AdminController\AdminController@my_profile_edit');
        Route::get('/profileChangePass','AdminController\AdminController@change_pass');
        Route::post('/profileChangePass','AdminController\AdminController@change_pass_update');
        Route::get('/admin_delete/{id}','AdminController\AdminController@admin_delete');

    });



});
Route::get('/Privacy-Policy' , function ()
{
   return view('admin.privacyAndPolicy');
});
