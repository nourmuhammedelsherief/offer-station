@extends('admin.layouts.master')

@section('title')
    اشعارات لفئه معينة
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ URL::asset('admin/css/bootstrap-fileinput.css') }}">
@endsection


@section('page_header')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{url('/admin/home')}}">لوحة التحكم</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{url('/admin/category_notifications')}}">اشعارات لفئه معينة</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>عرض اشعارات لفئه معينة</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">عرض اشعارات لفئه معينة
        <small>اضافة جميع اشعارات لفئه معينة</small>
    </h1>
    @include('flash::message')
@endsection

@section('content')

    <div class="row">

        <div class="col-md-8">
            <!-- BEGIN TAB PORTLET-->
            <form method="post" action="{{route('storeCategoryNotification')}}" enctype="multipart/form-data" >
                <input type = 'hidden' name = '_token' value = '{{Session::token()}}'>
                <div class="portlet light bordered table-responsive">
                    <div class="portlet-body">
                        <div class="row">
                            <!-- BEGIN SAMPLE FORM PORTLET-->
                            <div class="portlet light bordered table-responsive">
                                <div class="portlet-body form">
                                    <div class="form-horizontal" role="form">
                                        <div class="form-body">
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">الفئه</label>
                                                <div class="col-md-9">
                                                    <select name="category" class="form-control" required>
                                                        <option disabled selected> اختر الفئة </option>
                                                        <option value="1"> عملاء </option>
                                                        <option value="2"> متاجر </option>
                                                    </select>
                                                    @if ($errors->has('category'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('category') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">العنوان بالعربية</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="ar_title" class="form-control" placeholder="أكتب عنوان الاشعار بالعربية" value="{{old('ar_title')}}">
                                                    @if ($errors->has('ar_title'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('ar_title') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">العنوان بالأنجليزية</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="en_title" class="form-control" placeholder="أكتب عنوان الاشعار بالأنجليزية" value="{{old('en_title')}}">
                                                    @if ($errors->has('en_title'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('en_title') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">محتوي  الاشعار بالعربية</label>
                                                <div class="col-md-9">
                                                    <textarea  name="ar_message" class="form-control" placeholder="أكتب  محتوي  الاشعار بالعربية"></textarea>
                                                    @if ($errors->has('ar_message'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('ar_message') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">محتوي  الاشعار بالأنجليزية</label>
                                                <div class="col-md-9">
                                                    <textarea  name="en_message" class="form-control" placeholder="أكتب  محتوي  الاشعار بالأنجليزية"></textarea>
                                                    @if ($errors->has('en_message'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('en_message') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>







                                    </div>
                                </div>
                            </div>
                            <!-- END SAMPLE FORM PORTLET-->


                        </div>


                        <!-- END CONTENT BODY -->

                        <!-- END CONTENT -->


                    </div>
                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn green" value="حفظ" onclick="this.disabled=true;this.value='تم الارسال, انتظر...';this.form.submit();">حفظ</button>
                        </div>
                    </div>
                </div>
            </form>
            <!-- END TAB PORTLET-->
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>

@endsection
