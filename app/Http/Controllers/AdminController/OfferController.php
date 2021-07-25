<?php

namespace App\Http\Controllers\AdminController;

use App\Covering;
use App\Offer;
use App\OfferPhoto;
use App\Setting;
use App\User;
use App\UserDevice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($active)
    {
        $offers = Offer::whereActive($active)
            ->where('status', '0')
            ->orderBy('id', 'desc')
            ->get();
        return view('admin.offers.index', compact('offers', 'active'));
    }

    public function terminated()
    {
        $offers = Offer::whereStatus('1')->orderBy('id', 'desc')->get();
        return view('admin.offers.terminated', compact('offers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::whereType('2')->get();
        return view('admin.offers.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'price_type' => 'required|in:0,1',
            'title' => 'required',
            'price' => 'required_with:price_after_discount',
            'price_per' => 'required_with:price_percent',
            'price_after_discount' => 'required_without:price_percent',
            'price_percent' => 'required_without:price_after_discount',
            'end_date' => 'required|date',
            'max_quantity' => 'required|numeric',
            'active' => 'required',
            'code' => 'required',
            'external_link' => 'sometimes',
            'details' => 'sometimes',
            'photos*' => 'sometimes|mimes:jpg,jpeg,png,gif,tif,bmp,bsd|max:5000',
        ]);
        // create new offer
        $offer_time = Setting::find(1)->offer_time;
        $end_date = Carbon::now()->addDays($offer_time);
        $offer = Offer::create([
            'user_id' => $request->user_id,
            'price_type' => $request->price_type,
            'price' => $request->price_type == '0' ? $request->price : $request->price_per,
            'price_after_discount' => $request->price_after_discount == null ? null : $request->price_after_discount,
            'price_percent' => $request->price_percent == null ? null : $request->price_percent,
            'title' => $request->title,
            'end_date' => $request->end_date,
            'offer_time' => $end_date,
            'external_link' => $request->external_link == null ? null : $request->external_link,
            'max_quantity' => $request->max_quantity,
            'code' => $request->code,
            'details' => $request->details,
            'active' => $request->active,   // active
            'status' => '0',   // active
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
        flash()->success('تم أنشاء  العرض بنجاح');
        return redirect()->route('Offer', $request->active);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $offer = Offer::findOrFail($id);
        $users = User::whereType('2')->get();
        $photos = OfferPhoto::whereOfferId($id)->get();
        return view('admin.offers.edit', compact('users', 'offer', 'photos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $offer = Offer::findOrFail($id);
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'title' => 'required',
            'price' => 'required',
            'price_after_discount' => 'required_without:price_percent',
            'price_percent' => 'required_without:price_after_discount',
            'end_date' => 'required|date',
            'max_quantity' => 'required|numeric',
            'active' => 'required',
            'code' => 'required',
            'external_link' => 'sometimes',
            'details' => 'sometimes',
            'photos*' => 'sometimes|mimes:jpg,jpeg,png,gif,tif,bmp,bsd|max:5000',
        ]);
        $offer_time = Setting::find(1)->offer_time;
        $end_date = Carbon::now()->addDays($offer_time);
        if ($request->active == '1') {
            $offer->update([
                'offer_time' => $end_date,
                'active' => $request->active,   // active
            ]);
        } else {
            $offer->update([
                'active' => $request->active,   // un active
            ]);
        }
        if ($request->price_type == '0') {
            $offer->update([
                'price_after_discount' => $request->price_after_discount,
                'price_percent' => null,
            ]);
        } elseif ($request->price_type == '1') {
            $offer->update([
                'price_after_discount' => null,
                'price_percent' => $request->price_percent,
            ]);
        }
        $offer->update([
            'user_id' => $request->user_id,
            'price_type' => $request->price_type,
            'price' => $request->price == null ? $offer->price : $request->price,
            'title' => $request->title,
            'end_date' => $request->end_date,
//            'offer_time'           => $end_date,
            'external_link' => $request->external_link == null ? null : $request->external_link,
            'max_quantity' => $request->max_quantity,
            'code' => $request->code,
            'details' => $request->details,
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
        flash('تم  تعديل  العرض بنجاح')->success();
        return redirect()->route('Offer', $request->active);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);
        $photos = OfferPhoto::whereOfferId($offer->id)->get();
        if ($photos->count() > 0) {
            foreach ($photos as $photo) {
                if (file_exists(public_path('uploads/offers/' . $photo->photo))) {
                    unlink(public_path('uploads/offers/' . $photo->photo));
                }
            }
        }
        $offer->delete();
        flash('تم  حذف  العرض بنجاح')->success();
        return redirect()->back();
    }

    public function remove_offer_photo($id)
    {
        $deleted = OfferPhoto::where('id', $id)->first();
        if (file_exists(public_path('uploads/offers/' . $deleted->photo))) {
            unlink(public_path('uploads/offers/' . $deleted->photo));
        }
        $deleted->delete();
        if ($deleted) {
            $v = '{"message":"done"}';
            return response()->json($v);
        }
    }

    public function offer_transfer()
    {
        $offers = Offer::where('transfer_photo', '!=', null)
            ->where('discriminate', '0')
            ->where('status', '0')
            ->get();
        return view('admin.offers.offer_transfer', compact('offers'));
    }

    public function transferDone($id)
    {
        $offer = Offer::findOrFail($id);
        $discriminate_time = Setting::find(1)->discriminate_time;
        $end = Carbon::now()->addDays($discriminate_time);
        if (file_exists(public_path('uploads/transfer_photos/' . $offer->transfer_photo))) {
            unlink(public_path('uploads/transfer_photos/' . $offer->transfer_photo));
        }
        $offer->update([
            'transfer_photo' => null,
            'discriminate' => '1',
            'end_discriminate' => $end,
        ]);

        $ar_title = 'العروض';
        $en_title = 'Offers';
        $ar_message = 'تم أضافة العرض الخاص بك الي  الأعلانات  المميزة';
        $en_message = 'Your offer has been added to featured ads';
        $devicesTokens = UserDevice::where('user_id', $offer->user_id)
            ->get()
            ->pluck('device_token')
            ->toArray();
        if ($devicesTokens) {
            sendMultiNotification($ar_title, $ar_message, $devicesTokens);
        }
        saveNotification($offer->user_id, $ar_title, $en_title, $ar_message, $en_message, '3', $offer->id);

        flash('تم تمميز الأعلان بنجاح')->success();
        return redirect()->back();
    }

    public function coveringsDone($id)
    {
        $covering = Covering::findOrFail($id);
        $covering->update([
            'transfer_photo' => null,
            'status'         => '1',
            'end_date'       => Carbon::now()->addDays($covering->days),
        ]);
        // send Notification to store
        $ar_title = 'التغطيات';
        $en_title = 'Coverings';
        $ar_message = 'تم تفعيل الفيديو الخاص بك في قسم التغطيات بنجاح';
        $en_message = 'Your video has been successfully activated in the coverage section';
        $devicesTokens = UserDevice::where('user_id', $covering->user_id)
            ->get()
            ->pluck('device_token')
            ->toArray();
        if ($devicesTokens) {
            sendMultiNotification($ar_title, $ar_message, $devicesTokens);
        }
        saveNotification($covering->user_id, $ar_title, $en_title, $ar_message, $en_message, '3', null);

        flash('تم اضافة لينك  الفيديو الي قسم التغطيات بنجاح')->success();
        return redirect()->back();
    }

    public function transferNotDone($id)
    {
        $offer = Offer::findOrFail($id);
        if (file_exists(public_path('uploads/transfer_photos/' . $offer->transfer_photo))) {
            unlink(public_path('uploads/transfer_photos/' . $offer->transfer_photo));
        }
        $offer->update([
            'transfer_photo' => null,
            'discriminate' => '0',
            'end_discriminate' => null,
        ]);
        $ar_title = 'العروض';
        $en_title = 'Offers';
        $ar_message = 'تم الغاء أضافة العرض الخاص بك الي  الأعلانات  المميزة';
        $en_message = 'Your offer has been cancelled';
        $devicesTokens = UserDevice::where('user_id', $offer->user_id)
            ->get()
            ->pluck('device_token')
            ->toArray();
        if ($devicesTokens) {
            sendMultiNotification($ar_title, $ar_message, $devicesTokens);
        }
        saveNotification($offer->user_id, $ar_title, $en_title, $ar_message, $en_message, '3', $offer->id);

        flash('تم الغاء تمميز الأعلان بنجاح')->success();
        return redirect()->back();
    }

    public function coveringNotDone($id)
    {
        $covering = Covering::findOrFail($id);
        $covering->delete();
        // send Notification to store
        $ar_title = 'التغطيات';
        $en_title = 'Coverings';
        $ar_message = 'تم الغاء اضافة لينك الفيديو الخاص بك الي قسم التغطيات بنجاح';
        $en_message = 'Your video link has been successfully added to the coverage section';
        $devicesTokens = UserDevice::where('user_id', $covering->user_id)
            ->get()
            ->pluck('device_token')
            ->toArray();
        if ($devicesTokens) {
            sendMultiNotification($ar_title, $ar_message, $devicesTokens);
        }
        saveNotification($covering->user_id, $ar_title, $en_title, $ar_message, $en_message, '3', null);

        flash('تم الغاء اضافة لينك الفيديو الي قسم التغطيات')->success();
        return redirect()->back();
    }

    public function is_active(Request $request, $id)
    {
        if ($request->ajax()) {
            $offer = Offer::findOrfail($id);
            $offer_time = Setting::find(1)->offer_time;
            $end_date = Carbon::now()->addDays($offer_time);

            if ($offer->active == '1') {
                $offer->active = '0';
                $offer->offer_time = $end_date;
                $offer->save();
            } else {
                $offer->active = '1';
                $offer->offer_time = $end_date;
                $offer->save();
            }
            return 'true';
        }
    }

    public function coverings()
    {
        $coverings = Covering::whereStatus('0')
            ->where('transfer_photo', '!=', null)
            ->get();
        return view('admin.offers.coverings', compact('coverings'));
    }


}
