@extends('admin.layouts.master')

@section('title')
    تحديد خصائص الأعلانات المميزة
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
                <a href="{{route('discriminate_places')}}">تحديد خصائص الأعلانات المميزة </a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>عرض تحديد خصائص الأعلانات المميزة</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">عرض تحديد خصائص الأعلانات المميزة
        <small>اضافة جميع تحديد خصائص الأعلانات المميزة  </small>
    </h1>
@endsection

@section('content')

    <div class="row">

        <div class="col-md-8">
            <!-- BEGIN TAB PORTLET-->
            <form method="post" action="{{route('updateDiscriminate_place' , $place->id)}}" enctype="multipart/form-data" >
                <input type = 'hidden' name = '_token' value = '{{Session::token()}}'>
                <div class="portlet light bordered table-responsive">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-anchor font-green-sharp"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">تعديل مدينة  </span>
                        </div>

                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <!-- BEGIN SAMPLE FORM PORTLET-->
                            <div class="portlet light bordered table-responsive">
                                <div class="portlet-body form">
                                    <div class="form-horizontal" role="form">
                                        <div class="form-body">
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> عدد المشاهدات </label>
                                                <div class="col-md-9">
                                                    <input type="number" name="views_count" class="form-control" placeholder="عدد المشاهدات" value="{{$place->views_count}}" required>
                                                    @if ($errors->has('views_count'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('views_count') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> السعر </label>
                                                <div class="col-md-7">
                                                    <input type="number" name="views_price" class="form-control" placeholder="السعر" value="{{$place->views_price}}" required>
                                                    @if ($errors->has('views_price'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('views_price') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                                <div class="col-md-2">
                                                    SR
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
