@extends('admin.layouts.master')

@section('title')
    الأخبار
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
                <a href="{{route('News')}}">الأخبار </a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>عرض الأخبار</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">عرض الأخبار
        <small>تعديل جميع الأخبار  </small>
    </h1>
@endsection

@section('content')

    <div class="row">

        <div class="col-md-8">
            <!-- BEGIN TAB PORTLET-->
            <form method="post" action="{{route('updateNews' , $news->id)}}" enctype="multipart/form-data" >
                <input type = 'hidden' name = '_token' value = '{{Session::token()}}'>
                <div class="portlet light bordered table-responsive">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-anchor font-green-sharp"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">تعديل خبر </span>
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
                                                <label class="col-md-3 control-label"> العنوان بالعربي </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="ar_title" class="form-control" placeholder="اكتب عنوان  الخبر باللغة العربية" value="{{$news->ar_title}}" required>
                                                    @if ($errors->has('ar_title'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('ar_title') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> العنوان بالأنجليزية </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="en_title" class="form-control" placeholder="اكتب عنوان  الخبر باللغة الأنجليزية" value="{{$news->en_title}}" required>
                                                    @if ($errors->has('en_title'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('en_title') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> التفاصيل بالعربي</label>
                                                <div class="col-md-9">
                                                    <textarea class="form-control" rows="5" name="ar_details" placeholder="أكتب تفاصيل  الخبر باللغه العربية" > {{$news->ar_details}} </textarea>
                                                    @if ($errors->has('ar_details'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('ar_details') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label"> التفاصيل بالأنجليزي</label>
                                                <div class="col-md-9">
                                                    <textarea class="form-control" rows="5" name="en_details" placeholder="أكتب تفاصيل  الخبر باللغه الأنجليزية" > {{$news->en_details}} </textarea>
                                                    @if ($errors->has('en_details'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('en_details') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="form-group ">
                                                <label class="control-label col-md-3"> صورة الخبر </label>
                                                <div class="col-md-9">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                            @if($news->photo != null)
                                                                <img src="{{asset('/uploads/news/'.$news->photo)}}">
                                                            @endif    
                                                        </div>
                                                        <div>
                                                            <span class="btn red btn-outline btn-file">
                                                                <span class="fileinput-new"> اختر الصورة </span>
                                                                <span class="fileinput-exists"> تغيير </span>
                                                                <input type="file" name="photo"> </span>
                                                            <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> إزالة </a>



                                                        </div>
                                                    </div>
                                                    @if ($errors->has('photo'))
                                                        <span class="help-block">
                                                               <strong style="color: red;">{{ $errors->first('photo') }}</strong>
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
