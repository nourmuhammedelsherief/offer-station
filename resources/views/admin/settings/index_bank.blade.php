@extends('admin.layouts.master')

@section('title')
    اعدادات بيانات البنك
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
                <a href="{{url('/admin/bank/setting')}}">اعدادات بيانات البنك</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>تعديل اعدادات بيانات البنك</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title"> اعدادات بيانات البنك
        <small>تعديل اعدادات بيانات البنك</small>
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

                                        <h2 class="text-center"> بيانات البنك </h2>
                                        <div class="form-group">
                                            <div class="row">
                                                <label class="col-md-3 control-label"> اسم البنك </label>
                                                <div class="col-md-9">
                                                    <input class="form-control" type="text"
                                                           value="{{$settings->bank_name}}" name="bank_name"
                                                           placeholder="اكتب اسم البنك الذي سيقوم  المتاجر بالتحويل اليه" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <label class="col-md-3 control-label"> رقم الحساب البنكي </label>
                                                <div class="col-md-9">
                                                    <input class="form-control" type="text"
                                                           value="{{$settings->account_number}}" name="account_number"
                                                           placeholder="اكتب رقم الحساب البنكي" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <label class="col-md-3 control-label"> رقم الأبيان </label>
                                                <div class="col-md-9">
                                                    <input class="form-control" type="text"
                                                           value="{{$settings->IBAN_number}}" name="IBAN_number"
                                                           placeholder="اكتب رقم الأبيان" required>
                                                </div>
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

