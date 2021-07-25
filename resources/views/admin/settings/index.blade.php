@extends('admin.layouts.master')

@section('title')
    اعدادات الموقع
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
                <a href="{{url('/admin/setting')}}">اعدادات الموقع</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>تعديل اعدادات الموقع</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title"> اعدادات الموقع
        <small>تعديل اعدادات الموقع</small>
    </h1>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="row">

        <div class="col-md-8">
            <!-- BEGIN TAB PORTLET-->
            @if(count($errors))
                <ul class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
            @endif
            <form action="{{url('admin/add/settings')}}" method="post" enctype="multipart/form-data">
                <input type='hidden' name='_token' value='{{Session::token()}}'>

                <!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->

                    <div class="row">
                        <!-- BEGIN SAMPLE FORM PORTLET-->
                        <div class="portlet light bordered table-responsive">
                            <div class="portlet-body form">
                                <div class="form-horizontal" role="form">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <label class="col-md-3 control-label"> نطاف البحث </label>
                                                <div class="col-md-7">
                                                    <input class="form-control" type="number"
                                                           value="{{$settings->search_range}}" name="search_range"
                                                           placeholder="نطاق البحث">
                                                </div>
                                                <div class="col-md-2">
                                                    كيلومتر
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <label class="col-md-3 control-label"> رقم التواصل </label>
                                                <div class="col-md-9">
                                                    <input class="form-control" type="text"
                                                           value="{{$settings->contact_number}}" name="contact_number"
                                                           placeholder="رقم التواصل">
                                                </div>
                                            </div>
                                        </div>

                                        <h2 class="text-center"> التحكم في نوع التسجيل للعميل والمتاجر </h2>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"> نوع التسجيل في التطبيق
                                                للعميل </label>
                                            <div class="col-md-9">
                                                <select name="user_verify" required class="form-control">
                                                    <option selected disabled> أختر نوع التسجيل</option>
                                                    <option
                                                        value="email" {{$settings->user_verify == 'email' ? 'selected' : ''}}>
                                                        بريد الكتروني
                                                    </option>
                                                    <option
                                                        value="mobile" {{$settings->user_verify == 'mobile' ? 'selected' : ''}}>
                                                        رقم الجوال
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"> نوع التسجيل في التطبيق
                                                للمتجر </label>
                                            <div class="col-md-9">
                                                <select name="store_verify" required class="form-control">
                                                    <option selected disabled> أختر نوع التسجيل</option>
                                                    <option
                                                        value="email" {{$settings->store_verify == 'email' ? 'selected' : ''}}>
                                                        بريد الكتروني
                                                    </option>
                                                    <option
                                                        value="mobile" {{$settings->store_verify == 'mobile' ? 'selected' : ''}}>
                                                        رقم الجوال
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <label class="col-md-3 control-label"> سعر اليوم الواحد بقسم
                                                    التغطيات </label>
                                                <div class="col-md-7">
                                                    <input class="form-control" type="number"
                                                           value="{{$settings->coverings_day_price}}"
                                                           name="coverings_day_price"
                                                           placeholder="قم بتحديد سعر اليوم الواحد في قسم التغطيات">
                                                </div>
                                                <div class="col-md-2">
                                                    ريال سعودي
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label class="control-label col-md-3"> شعار الموقع </label>
                                            <div class="col-md-9">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-preview thumbnail"
                                                         data-trigger="fileinput"
                                                         style="width: 200px; height: 150px;">
                                                        @if($settings->logo !==null)
                                                            <img
                                                                src='{{ asset("uploads/logos/$settings->logo") }}'>
                                                        @endif
                                                    </div>
                                                    <div>
                                                            <span class="btn red btn-outline btn-file">
                                                                <span class="fileinput-new"> اختر الصورة </span>
                                                                <span class="fileinput-exists"> تغيير </span>
                                                                <input type="file" name="logo"> </span>
                                                        <a href="javascript:;" class="btn red fileinput-exists"
                                                           data-dismiss="fileinput"> إزالة </a>


                                                    </div>
                                                </div>
                                                @if ($errors->has('offer_photo'))
                                                    <span class="help-block">
                                                               <strong
                                                                   style="color: red;">{{ $errors->first('offer_photo') }}</strong>
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
                </div>
                <!-- END CONTENT -->


                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn green">حفظ</button>
                        </div>
                    </div>
                </div>
            </form>
            <!-- END TAB PORTLET-->

        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ URL::asset('admin/js/select2.full.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/components-select2.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>
@endsection

