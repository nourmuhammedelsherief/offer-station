@extends('admin.layouts.master')

@section('title')
    الاشعارات لمستخدم معين
@endsection

@section('styles')

    <link rel="stylesheet" href="{{ URL::asset('admin/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.bootstrap-rtl.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">


@endsection

@section('page_header')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="/admin/home">لوحة التحكم</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="/admin/notifications/user">الاشعارات لمستخدم معين</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>ارسال الاشعارات لمستخدم معين</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">ارسال الاشعارات لمستخدم معين
        <small>ارسال الاشعارات لمستخدم معين</small>
    </h1>
@endsection

@section('content')
    @include('flash::message')
        <div class="row">
            <div class="co-md-8">
                <form action="{{route('storeUserNotification')}}" method="POST">@csrf
                    <input type='hidden' name='_token' value='{{Session::token()}}'>
                    <div class="portlet light bordered table-responsive">
                        <div class="portlet-body">
                            <div class="row">
                                <!-- BEGIN SAMPLE FORM PORTLET-->
                                <div class="portlet light bordered table-responsive">
                                    <div class="portlet-body form">
                                        <div class="form-horizontal" role="form">
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">اختر مستخدم</label>
                                                    <div class="col-md-9">
                                                        {!! Form::select('user_id[]',
                                                            [
                                                                'العملاء'        => App\User::where('type','1')->pluck('name','id'),
                                                                'المتاجر'      => App\User::where('type','2')->pluck('name','id'),
                                                            ],
                                                            null,
                                                            ['class'=>'form-control select2','multiple']) !!}
                                                                @if ($errors->has('user_id'))
                                                            <span class="help-block">
                                                            <strong style="color: red;">{{ $errors->first('user_id') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">العنوان بالعربية</label>
                                                    <div class="col-md-9">
                                                        <input type="text" name="ar_title" class="form-control"
                                                               placeholder="أكتب عنوان الاشعار بالعربية"
                                                               value="{{old('ar_title')}}">
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
                                                        <input type="text" name="en_title" class="form-control"
                                                               placeholder="أكتب عنوان الاشعار بالأنجليزية"
                                                               value="{{old('en_title')}}">
                                                        @if ($errors->has('en_title'))
                                                            <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('en_title') }}</strong>
                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">محتوي الاشعار بالعربية</label>
                                                    <div class="col-md-9">
                                                        <textarea name="ar_message" class="form-control"
                                                                  placeholder="أكتب  محتوي  الاشعار بالعربية"></textarea>
                                                        @if ($errors->has('ar_message'))
                                                            <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('ar_message') }}</strong>
                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">محتوي الاشعار
                                                        بالأنجليزية</label>
                                                    <div class="col-md-9">
                                                        <textarea name="en_message" class="form-control"
                                                                  placeholder="أكتب  محتوي  الاشعار بالأنجليزية"></textarea>
                                                        @if ($errors->has('en_message'))
                                                            <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('en_message') }}</strong>
                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <button class="btn btn-success" type="submit">ارسال</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>



@endsection

@section('scripts')
    <script src="{{ URL::asset('admin/js/select2.full.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/components-select2.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/datatable.js') }}"></script>
    <script src="{{ URL::asset('admin/js/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/datatables.bootstrap.js') }}"></script>
    <script src="{{ URL::asset('admin/js/table-datatables-managed.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/ui-sweetalert.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            var CSRF_TOKEN = $('meta[name="X-CSRF-TOKEN"]').attr('content');
            $('body').on('click', '.delete_attribute', function () {
                var id = $(this).attr('data');
                var swal_text = 'حذف ' + $(this).attr('data_name') + '؟';
                var swal_title = 'هل أنت متأكد من الحذف ؟';
                swal({
                    title: swal_title,
                    text: swal_text,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-warning",
                    confirmButtonText: "تأكيد",
                    cancelButtonText: "إغلاق",
                    closeOnConfirm: false
                }, function () {
                    window.location.href = "{{ url('/') }}" + "/admin/orders/" + id + "/delete";
                });
            });
        });
    </script>

@endsection
