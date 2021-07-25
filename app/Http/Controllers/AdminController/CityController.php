<?php

namespace App\Http\Controllers\AdminController;

use App\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cities = City::orderBy('id' , 'desc')->get();
        return view('admin.cities.index' , compact('cities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.cities.create');
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
        ]);
        City::create($request->all());
        flash('تم أنشاء المدينه  بنجاح')->success();
        return redirect()->route('City');
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
        $city = City::findOrFail($id);
        return view('admin.cities.edit', compact('city'));
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
        $city = City::findOrFail($id);
        $this->validate($request , [
            'ar_name'  => 'required',
            'en_name'  => 'required',
        ]);
        $city->update($request->all());
        flash('تم تعديل المدينه  بنجاح')->success();
        return redirect()->route('City');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        if ($city->users->count() > 0)
        {
            flash('لا يمكنك مسح المدينة لأنها مستخدمة')->error();
            return redirect()->route('City');
        }
        $city->delete();
        flash('تم حذف المدينه  بنجاح')->success();
        return redirect()->route('City');
    }
}
