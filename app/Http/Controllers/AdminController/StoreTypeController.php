<?php

namespace App\Http\Controllers\AdminController;

use App\StoreType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = StoreType::orderBy('id' , 'desc')->get();
        return view('admin.types.index' , compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request , [
            'ar_name'  => 'required',
            'en_name'  => 'required',
            'photo'     => 'sometimes|mimes:jpg,jpeg,png,gif,tif,bmp,bsd|max:5000'
        ]);
        StoreType::create([
            'ar_name'   => $request->ar_name,
            'en_name'   => $request->en_name,
            'photo'     => $request->file('photo') == null ? null : UploadImage($request->file('photo'), 'photo', '/uploads/categories'),
        ]);
        flash('تم أضافه نوع المتجر بنجاح')->success();
        return  redirect()->route('StoreType');
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
    public function edit($id)
    {
        $store_type = StoreType::findOrFail($id);
        return view('admin.types.edit' , compact('store_type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $store_type = StoreType::findOrFail($id);
        $this->validate($request , [
            'ar_name'  => 'required',
            'en_name'  => 'required',
            'photo'     => 'sometimes|mimes:jpg,jpeg,png,gif,tif,bmp,bsd|max:5000'
        ]);
        $store_type->update([
            'ar_name'   => $request->ar_name,
            'en_name'   => $request->en_name,
            'photo'     => $request->file('photo') == null ? $store_type->photo : UploadImageEdit($request->file('photo'), 'photo', '/uploads/categories' , $store_type->photo),
        ]);
        flash('تم تعديل نوع المتجر بنجاح')->success();
        return  redirect()->route('StoreType');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $store_type = StoreType::findOrFail($id);
        if ($store_type->users->count() > 0)
        {
            flash('لا يمكنك مسح هذا النوع من  المتاجر لانه مستخدم')->error();
            return  redirect()->route('StoreType');
        }
        if ($id == '4')
        {
            flash('لا يمكنك مسح هذا القسم')->error();
            return  redirect()->route('StoreType');
        }
        if (file_exists(public_path('uploads/categories/' . $store_type->photo))) {
            unlink(public_path('uploads/categories/' . $store_type->photo));
        }
        $store_type->delete();
        flash('تم مسح نوع المتجر بنجاح')->success();
        return  redirect()->route('StoreType');
    }
}
