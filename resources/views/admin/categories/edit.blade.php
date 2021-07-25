@extends('admin.layouts.master')

@section('title')
    الأقسام
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
                <a href="{{route('Category')}}">الأقسام </a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>عرض الأقسام</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">عرض الأقسام
        <small>اضافة جميع الأقسام  </small>
    </h1>
@endsection

@section('content')

    <div class="row">

        <div class="col-md-8">
            <!-- BEGIN TAB PORTLET-->
            <form method="post" action="{{route('updateCategory' , $category->id)}}" enctype="multipart/form-data" >
                <input type = 'hidden' name = '_token' value = '{{Session::token()}}'>
                <div class="portlet light bordered table-responsive">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-anchor font-green-sharp"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">تعديل قسم  </span>
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
                                                <label class="col-md-3 control-label"> أسم القسم بالعربي</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="ar_name" class="form-control" placeholder="أسم القسم بالعربي" value="{{$category->ar_name}}" required>
                                                    @if ($errors->has('ar_name'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('ar_name') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> أسم القسم بالأنجليزي </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="en_name" class="form-control" placeholder="أسم القسم بالأنجليزي " value="{{$category->en_name}}" required>
                                                    @if ($errors->has('en_name'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('en_name') }}</strong>
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
