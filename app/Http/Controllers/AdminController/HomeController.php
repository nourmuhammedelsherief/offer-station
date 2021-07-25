<?php

namespace App\Http\Controllers\AdminController;

use App\City;
use App\Complain;
use App\Covering;
use App\Food;
use App\FoodRequest;
use App\Http\Controllers\Controller;
use App\Offer;
use App\Report;
use App\StoreType;
use App\User;
use App\UserDevice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admins = DB::table('admins')->count();
        $stores = DB::table('users')->where('type', '2')->count();
        $users = DB::table('users')->where('type', '1')->count();
        $news = DB::table('news')->count();
        $carTypes = StoreType::count();
        $cities = City::count();
        $complaints = Complain::count();
        $reports = Report::count();
        $active_offers = Offer::whereActive('1')->count();
        $unActiveOffers = Offer::whereActive('0')->count();
        $terminatedOffers = Offer::whereStatus('1')->count();
        $coverings = Covering::where('transfer_photo', '!=', null)
            ->where('status', '0')
            ->count();
        return view('admin.home', compact('coverings','complaints','reports', 'terminatedOffers', 'unActiveOffers', 'active_offers', 'cities', 'carTypes', 'users', 'news', 'stores', 'admins'));
    }

    public function public_notifications()
    {
        return view('admin.notifications.public_notifications');
    }
    public function category_notifications()
    {
        return view('admin.notifications.category_notifications');
    }
    public function store_public_notifications(Request $request)
    {
        $this->validate($request, [
            "ar_title" => "required",
            "en_title" => "required",
            "ar_message" => "required",
            "en_message" => "required",
        ]);
        // Create New Notification

        $users = User::where('active', '1')->get();
        foreach ($users as $user) {
            $ar_title = $request->ar_title;
            $en_title = $request->en_title;
            $ar_message = $request->ar_message;
            $en_message = $request->en_message;
            $devicesTokens = UserDevice::where('user_id', $user->id)
                ->get()
                ->pluck('device_token')
                ->toArray();
            if ($devicesTokens) {
                sendMultiNotification($ar_title, $ar_message, $devicesTokens);
            }
            saveNotification($user->id, $ar_title, $en_title, $ar_message, $en_message, '0', null);
        }
        flash('تم ارسال الاشعار لجميع مستخدمي التطبيق')->success();
        return redirect()->route('public_notifications');

    }
    public function user_notifications()
    {
        return view('admin.notifications.user_notification');
    }
    public function store_user_notifications(Request $request)
    {
        $this->validate($request, [
            'user_id*' => 'required',
            "ar_title" => "required",
            "en_title" => "required",
            "ar_message" => "required",
            "en_message" => "required",
        ]);
        foreach ($request->user_id as $one) {
            $user = User::find($one);
            $ar_title = $request->ar_title;
            $en_title = $request->en_title;
            $ar_message = $request->ar_message;
            $en_message = $request->en_message;
            $devicesTokens = UserDevice::where('user_id', $user->id)
                ->get()
                ->pluck('device_token')
                ->toArray();
            if ($devicesTokens) {
                sendMultiNotification($ar_title, $ar_message, $devicesTokens);
            }
            saveNotification($user->id, $ar_title, $en_title, $ar_message, $en_message, '0', null);
        }
        flash('تم ارسال الاشعار للمستخدمين بنجاح')->success();
        return redirect()->route('user_notifications');
    }
    public function store_category_notifications(Request $request)
    {
        $this->validate($request, [
            'category' => 'required|in:1,2',
            "ar_title" => "required",
            "en_title" => "required",
            "ar_message" => "required",
            "en_message" => "required",
        ]);
        // Create New Notification

        $users = User::whereType($request->category)
            ->where('active', '1')
            ->get();
        foreach ($users as $user) {
            $ar_title = $request->ar_title;
            $en_title = $request->en_title;
            $ar_message = $request->ar_message;
            $en_message = $request->en_message;
            $devicesTokens = UserDevice::where('user_id', $user->id)
                ->get()
                ->pluck('device_token')
                ->toArray();
            if ($devicesTokens) {
                sendMultiNotification($ar_title, $ar_message, $devicesTokens);
            }
            saveNotification($user->id, $ar_title, $en_title, $ar_message, $en_message, '0', null);
        }
        flash('تم ارسال الاشعار بنجاح')->success();
        return redirect()->route('category_notifications');
    }
}
