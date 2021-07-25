<?php

namespace App\Http\Controllers\AdminController;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::orderBy('id' , 'desc')->get();
        return view('admin.categories.index' , compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.categories.create');
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
            'ar_name'   => 'required',
            'en_name'   => 'required',
            'photo'     => 'sometimes|mimes:jpg,jpeg,png,gif,tif,bmp,bsd|max:5000'
        ]);
        // create new category
        Category::create([
            'ar_name'   => $request->ar_name,
            'en_name'   => $request->en_name,
            'photo'     => $request->file('photo') == null ? null : UploadImage($request->file('photo'), 'photo', '/uploads/categories'),
        ]);
        flash('تم أضافة  القسم بنجاح')->success();
        return redirect()->route('Category');
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
        $category = Category::findOrFail($id);
        return view('admin.categories.edit' , compact('category'));

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
        $category = Category::findOrFail($id);
        $this->validate($request , [
            'ar_name'   => 'required',
            'en_name'   => 'required',
            'photo'     => 'sometimes|mimes:jpg,jpeg,png,gif,tif,bmp,bsd|max:5000'
        ]);
        $category->update([
            'ar_name'   => $request->ar_name,
            'en_name'   => $request->en_name,
            'photo'     => $request->file('photo') == null ? $category->photo : UploadImageEdit($request->file('photo'), 'photo', '/uploads/categories' , $category->photo),
        ]);
        flash('تم تعديل  القسم بنجاح')->success();
        return redirect()->route('Category');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        if ($category->offers->count() > 0)
        {
            flash('لا يمكنك مسح هذا القسم لأنه مستخدم')->error();
            return redirect()->route('Category');
        }
        if (file_exists(public_path('uploads/categories/' . $category->logo))) {
            unlink(public_path('uploads/categories/' . $category->logo));
        }
        $category->delete();
        flash('تم حذف  القسم بنجاح')->success();
        return redirect()->route('Category');
    }
}
