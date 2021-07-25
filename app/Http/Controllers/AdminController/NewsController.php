<?php

namespace App\Http\Controllers\AdminController;

use App\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $news = News::orderBy('id' , 'desc')->get();
        return view('admin.news.index' , compact('news'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.news.create');
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
            'ar_title'   => 'required',
            'en_title'   => 'required',
            'ar_details' => 'required',
            'en_details' => 'required',
            'photo'   => 'required|mimes:jpg,jpeg,png,gif,tif,bsd,bmp|max:5000',
        ]);
        News::create([
            'ar_title'    => $request->ar_title,
            'en_title'    => $request->en_title,
            'ar_details'  => $request->ar_details,
            'en_details'  => $request->en_details,
            'photo'       => $request->file('photo') == null ? null : UploadImage($request->file('photo'), 'photo', '/uploads/news'),
        ]);
        flash('تم أضافه الخبر بنجاح')->success();
        return redirect()->route('News');
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
        $news = News::findOrFail($id);
        return view('admin.news.edit' , compact('news'));
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
        $news = News::findOrFail($id);
        $this->validate($request , [
            'ar_title'   => 'required',
            'en_title'   => 'required',
            'ar_details' => 'required',
            'en_details' => 'required',
            'photo'   => 'sometimes|mimes:jpg,jpeg,png,gif,tif,bsd,bmp|max:5000',
        ]);
        $news->update([
            'ar_title'    => $request->ar_title,
            'en_title'    => $request->en_title,
            'ar_details'  => $request->ar_details,
            'en_details'  => $request->en_details,
            'photo'    => $request->file('photo') == null ? $news->photo : UploadImageEdit($request->file('photo'), 'photo', '/uploads/news',$news->photo),
        ]);
        flash('تم تعديل الخبر بنجاح')->success();
        return redirect()->route('News');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $news = News::findOrFail($id);
        if (file_exists(public_path('uploads/news/' . $news->photo))) {
            unlink(public_path('uploads/news/' . $news->photo));
        }
        $news->delete();
        flash('تم حذف الخبر بنجاح')->success();
        return redirect()->route('News');
    }
}
