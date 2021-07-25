<?php

namespace App\Http\Controllers\AdminController;

use App\Complain;
use App\Electronic_wallet;
use App\OfferDiscriminatePlaces;
use App\Report;
use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Redirect;
use Image;
use Auth;
use App\Permission;

class SettingController extends Controller
{
    //
    public function index()
    {
        $settings = settings();
        return view('admin.settings.index', compact('settings'));
    }

    public function index_bank()
    {
        $settings = settings();
        return view('admin.settings.index_bank', compact('settings'));
    }
    public function index_sms()
    {
        $settings = settings();
        return view('admin.settings.sms', compact('settings'));
    }
    public function index_offers()
    {
        $settings = settings();
        return view('admin.settings.index_offers', compact('settings'));
    }

    public function store(Request $request)
    {
        $setting = Setting::where('id', 1)->first();
        if ($request->offer_photo != null) {
            $photo = $request->offer_photo == null ? $setting->offer_photo : UploadImageEdit($request->file('offer_photo'), 'offer_photo', '/uploads/offers/', $setting->offer_photo);
            $setting->offer_photo = $photo;
            $setting->save();
        }
        if ($request->logo != null) {
            $photo = $request->logo == null ? $setting->logo : UploadImageEdit($request->file('logo'), 'logo', '/uploads/logos/', $setting->logo);
            $setting->logo = $photo;
            $setting->save();
        }
        $setting->update($request->except(['offer_photo' , 'logo']));
        return Redirect::back()->with('success', 'تم حفظ البيانات بنجاح');
    }

    public function drivers_commission(Request $request)
    {
        $this->validate($request, [
            'drivers_commission' => 'required',
        ]);
        // update drivers_commission value
        $setting = Setting::find(1);
        $setting->update([
            'drivers_commission' => $request->drivers_commission,
        ]);
        flash('تم تعديل  نسبة  اعموله  للسائقين')->success();
        return \redirect()->back();
    }

    public function pulls()
    {
        $pulls = Electronic_wallet::where('pull_request', '1')->get();
        return view('admin.settings.pulls', compact('pulls'));
    }

    public function PullDone($id)
    {
        $wallet = Electronic_wallet::findOrFail($id);
        $wallet->update([
            'pull_request' => '0',
            'cash' => 0.0,
        ]);
        flash('تمت العمليه بنجاح')->success();
        return \redirect()->back();
    }

    public function discriminate_places()
    {
        $places = OfferDiscriminatePlaces::all();
        return view('admin.settings.discriminate_places', compact('places'));
    }

    public function editDiscriminate($id)
    {
        $place = OfferDiscriminatePlaces::findOrFail($id);
        return view('admin.settings.edit_discriminate_places', compact('place'));
    }

    public function updateDiscriminate_place(Request $request, $id)
    {
        $place = OfferDiscriminatePlaces::findOrFail($id);
        $this->validate($request, [
            'views_count' => 'required',
            'views_price' => 'required',
        ]);
        $place->update($request->all());
        flash('تم التعديل بنجاح')->success();
        return \redirect()->route('discriminate_places');
    }
    public function complaints()
    {
        $complaints = Complain::orderBy('id' , 'desc')->get();
        return view('admin.settings.complaints' , compact('complaints'));
    }
    public function reports()
    {
        $reports = Report::orderBy('id' , 'desc')->get();
        return view('admin.settings.reports' , compact('reports'));
    }
    public function deleteComplaint($id)
    {
        $complaint = Complain::findOrFail($id);
        $complaint->delete();
        flash('تم مسح الشكوي بنجاح')->success();
        return \redirect()->back();
    }
    public function deleteReport($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();
        flash('تم مسح الأبلاغ بنجاح')->success();
        return \redirect()->back();
    }

}
