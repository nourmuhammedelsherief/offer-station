<?php

namespace App\Http\Controllers\AdminController;

use App\City;

use App\Country;
use App\FoodCategory;
use App\Http\Controllers\Controller;
use App\StoreBanner;
use App\StoreType;
use App\User;
use App\UserDevice;
use App\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;
use App\UserDepartment;
use Auth;
use Image;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type)
    {
        if ($type == '1')
        {
            $users =User::whereType('1')
                ->orderBy('id','desc')
                ->get();
            return view('admin.users.index',compact('users'));
        }elseif($type == '2'){
            $users =User::whereType('2')
                ->orderBy('id','desc')
                ->get();
            return view('admin.users.stores.index',compact('users'));
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
            if ($id == '1'){
                return view('admin.users.create');
            }elseif ($id == '2'){
                $store_types = StoreType::all();
                return view('admin.users.stores.create' , compact('store_types'));
            }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$type)
    {
        // the beneficiary type  is 2  the restaurant  type  is  2
        if($type == 1){
            // create user
            $this->validate($request, [
                'email'                 => 'required|email|unique:users',
                'phone_number'          => 'required|unique:users',
                'name'                  => 'required|max:255',
                'photo'                 => 'nullable|mimes:jpeg,bmp,png,jpg|max:5000',
                'password'              => 'required|string|min:6',
                'password_confirmation' => 'required|same:password',
                'active'                => 'required',
            ]);

            $user= User::create([
                'phone_number'    => $request->phone_number,
                'email'           => $request->email,
                'name'            => $request->name,
                'active'          => $request->active,
                'password'        => Hash::make($request->password),
                'photo'           => $request->file('photo') == null ? null : UploadImage($request->file('photo'), 'photo', '/uploads/users'),
                'type'            => 1,
                'api_token'       => $request->token,
            ]);
            flash('تم أنشاء المستخدم بنجاح')->success();
//            return redirect('admin/users/1');
            return redirect('admin/users/1');

        }
        elseif ($type == 2){
            // create store
            $this->validate($request, [
                 'store_type_id'         => 'required|exists:store_types,id',
                 'phone_number'          => 'required|unique:users',
                 'email'                 => 'required|unique:users',
                 'name'                  => 'required|max:255',
                 'en_name'               => 'required|max:255',
                 'password'              => 'required|string|min:6',
                 'password_confirmation' => 'required|same:password',
                 'photo'                 => 'nullable|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                 'commercial_register'   => 'sometimes|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                 'license'               => 'sometimes|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                 'work_times'            => 'sometimes',
                 'contact_number'        => 'sometimes',
                 'store_url'             => 'sometimes',
                 'video_link'            => 'sometimes',
                 'logo'                  => 'required|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                 'banner_photos*'        => 'sometimes|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                 'latitude'              => 'sometimes',
                 'longitude'             => 'sometimes',
                 'active'                => 'required',
             ]);
            $user = User::create([
                'store_type_id'       => $request->store_type_id,
                'email'               => $request->email,
                'phone_number'        => $request->phone_number,
                'name'                => $request->name,
                'en_name'             => $request->en_name,
                'latitude'            => $request->latitude,
                'longitude'           => $request->longitude,
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
                'active'              => $request->active,
            ]);
            // store banners
            $name = $request->file('banner_photos');
            $fileFinalName_ar = "";
            if ($name != "") {
                if($files = $name) {
                    foreach ($files as $file) {
                        $images = new StoreBanner();
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
            flash('تم أنشاء المتجر  بنجاح')->success();
            return redirect('admin/users/2');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,$type)
    {
        if ($type == 2){
                $user = User::findOrfail($id);
                $store_types = StoreType::all();
                $photos = StoreBanner::whereUserId($id)->get();
                return view('admin.users.stores.edit' ,compact('photos','store_types','user'));
            }elseif ($type == 1){
                $user = User::findOrfail($id);
                return view('admin.users.edit_user' ,compact('user'));
            }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id,$type)
    {
        $user = User::find($id);
        if($type == 2){
            $this->validate($request, [
                'store_type_id'         => 'required|exists:store_types,id',
                'phone_number'          => 'required|unique:users,phone_number,'.$id,
                'email'                 => 'required|unique:users,email,'.$id,
                'name'                  => 'required|max:255',
                'en_name'               => 'required|max:255',
                'photo'                 => 'nullable|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                'commercial_register'   => 'sometimes|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                'license'               => 'sometimes|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                'work_times'            => 'sometimes',
                'contact_number'        => 'sometimes',
                'store_url'             => 'sometimes',
                'video_link'            => 'sometimes',
                'logo'                  => 'nullable|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                'banner_photos*'        => 'sometimes|mimes:jpg,png,gif,tif,psd,jpeg,bmp|max:5000',
                'latitude'              => 'sometimes',
                'longitude'             => 'sometimes',
            ]);
            $user->update([
                'store_type_id'       => $request->store_type_id == null ? $user->store_type_id : $request->store_type_id,
                'phone_number'        => $request->phone_number == null ? $user->phone_number : $request->phone_number,
                'email'               => $request->email == null ? $user->email : $request->email,
                'name'                => $request->name == null ? $user->name : $request->name,
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
            ]);
            // store banners
            $name = $request->file('banner_photos');
            $fileFinalName_ar = "";
            if ($name != "") {
                if($files = $name) {
                    foreach ($files as $file) {
                        $images = new StoreBanner();
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
            flash('تم تعديل بيانات المتجر')->success();
            return redirect('admin/users/2');
        }
        elseif ($type == 1){
            $this->validate($request, [
                'phone_number'     => 'required|unique:users,phone_number,'.$id,
                'email'            => 'required|unique:users,email,'.$id,
                'name'             => 'required|max:255',
                'photo'            => 'nullable|mimes:jpeg,bmp,png,jpg|max:5000',
            ]);

            $user->update([
                'phone_number'    => $request->phone_number,
                'email'           => $request->email,
                'name'            => $request->name,
                'type'            => '1',
                'photo'           => $request->file('photo') == null ? $user->photo : UploadImage($request->file('photo'), 'photo', '/uploads/users'),
            ]);
            flash('تم تعديل بيانات  المستخدم بنجاح')->success();
            return redirect('admin/users/1');
        }

    }
    public function update_pass(Request $request, $id)
    {
        //
        $this->validate($request, [
            'password' => 'required|string|min:6|confirmed',

        ]);
        $users = User::findOrfail($id);
        $users->password = Hash::make($request->password);

        $users->save();

        return redirect()->back()->with('information', 'تم تعديل كلمة المرور المستخدم');
    }
    public function update_privacy(Request $request, $id)
    {
        $this->validate($request, [
            'active' => 'required',
        ]);
        $users = User::findOrfail($id);
        $users->active =$request->active;
        $users->save();

        return redirect()->back()->with('information', 'تم تعديل اعدادات المستخدم');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user->type == '1')
        {
            if ($user->photo != null)
            {
                if (file_exists(public_path('uploads/users/' . $user->photo))) {
                    unlink(public_path('uploads/users/' . $user->photo));
                }
            }
            $user->delete();
            flash('تم الحذف بنجاح')->success();
            return redirect()->back();
        }else{
            if ($user->photo != null)
            {
                if (file_exists(public_path('uploads/users/' . $user->photo))) {
                    unlink(public_path('uploads/users/' . $user->photo));
                }
            }
            if ($user->logo != null)
            {
                if (file_exists(public_path('uploads/logos/' . $user->logo))) {
                    unlink(public_path('uploads/logos/' . $user->logo));
                }
            }
            if ($user->commercial_register != null)
            {
                if (file_exists(public_path('uploads/commercial_registers/' . $user->commercial_register))) {
                    unlink(public_path('uploads/commercial_registers/' . $user->commercial_register));
                }
            }
            if ($user->license != null)
            {
                if (file_exists(public_path('uploads/licenses/' . $user->license))) {
                    unlink(public_path('uploads/licenses/' . $user->license));
                }
            }
            $user_banners = StoreBanner::whereUserId($id)->get();
            if ($user_banners->count() > 0)
            {
                foreach ($user_banners as $banner)
                {
                    if (file_exists(public_path('uploads/store_banners/' . $banner->photo))) {
                        unlink(public_path('uploads/store_banners/' . $banner->photo));
                    }
                    $banner->delete();
                }
            }
            $user->delete();
            flash('تم الحذف بنجاح')->success();
            return back();
        }
    }
    public function remove_store_photo($id)
    {
        $deleted = StoreBanner::where('id', $id)->first();
        if (file_exists(public_path('uploads/store_banners/' . $deleted->photo))) {
            unlink(public_path('uploads/store_banners/' . $deleted->photo));
        }
        $deleted->delete();
        if ($deleted) {
            $v = '{"message":"done"}';
            return response()->json($v);
        }
    }
    public function privacy_user(Request $request , $id)
    {
        if ($request->ajax()) {
            $user = User::findOrFail($id);
            if ($user->active == '1') {
                $user->active = '0';
                $user->save();
            } else {
                $user->active = '1';
                $user->save();
            }
            return 'true';
        }
    }
}
