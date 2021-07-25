@extends('admin.layouts.master')

@section('title')
    اعدادات بيانات الأرسال
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
                <a href="{{url('/admin/sms/setting')}}">اعدادات بيانات الأرسال</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>تعديل اعدادات بيانات الأرسال</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title"> اعدادات بيانات الأرسال
        <small>تعديل اعدادات بيانات الأرسال</small>
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
                                        <h2 class="text-center"> بيانات الرسايل sms </h2>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"> التوكن (Bearer Token) </label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control"
                                                       placeholder="التوكن (Bearer Token)" name="bearer_token"
                                                       value="{{$settings->bearer_token}}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"> اسم المرسل (Sender Name) </label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control"
                                                       placeholder="اسم  المرسل (Sender Name)" name="sender_name"
                                                       value="{{$settings->sender_name}}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"> ايميل المرسل </label>
                                            <div class="col-md-9">
                                                <input type="email" class="form-control" placeholder="ايميل المرسل"
                                                       name="sender_email" value="{{$settings->sender_email}}">
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

