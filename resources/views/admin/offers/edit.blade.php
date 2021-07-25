@extends('admin.layouts.master')

@section('title')
    العروض
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
                <a href="{{route('Offer' , '0')}}">العروض </a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>عرض العروض</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">عرض العروض
        <small>تعديل  جميع العروض  </small>
    </h1>
@endsection

@section('content')

    <div class="row">

        <div class="col-md-10">
            <!-- BEGIN TAB PORTLET-->
            <form method="post" action="{{route('updateOffer' , $offer->id)}}" enctype="multipart/form-data" >
                <input type = 'hidden' name = '_token' value = '{{Session::token()}}'>
                <div class="portlet-body">
                    <div class="row">
                        <!-- BEGIN SAMPLE FORM PORTLET-->
                        <div class="portlet light bordered table-responsive">
                            <div class="portlet-body form">
                                <div class="form-horizontal" role="form">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"> اختر المستخدم </label>
                                            <div class="col-md-9">
                                                <select onchange="yesnoCheck(this);" name="user_id" class="form-control" required>
                                                    <option disabled selected> اختر مستخدم </option>
                                                    @foreach($users as $user)
                                                        <option value="{{$user->id}}" {{$offer->user_id == $user->id ? 'selected' : ''}}> {{$user->name}} </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('price_type'))
                                                    <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('price_type') }}</strong>
                                            </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"> عنوان  العرض </label>
                                            <div class="col-md-9">
                                                <input type="text" name="title" class="form-control" placeholder="عنوان  العرض" value="{{$offer->title}}" required>
                                                @if ($errors->has('title'))
                                                    <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('title') }}</strong>
                                            </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"> طريقة  عرض السعر </label>
                                            <div class="col-md-9">
                                                <select onchange="yesnoCheck(this);" name="price_type" class="form-control" required>
                                                    <option disabled selected> اختر طريقة  عرض  السعر </option>
                                                    <option value="0" {{$offer->price_type == '0' ? 'selected' : ''}}> السعر قبل  وبعد الخصم </option>
                                                    <option value="1" {{$offer->price_type == '1' ? 'selected' : ''}}> النسبة  المئويه للخصم </option>
                                                </select>
                                                @if ($errors->has('price_type'))
                                                    <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('price_type') }}</strong>
                                            </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div id="beforeAfter" style="{{$offer->price_after_discount != null ? 'display: block;' : 'display: none;'}}">
                                            <div  class="form-group">
                                                <label class="col-md-3 control-label"> السعر قبل  الخصم </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="price" class="form-control" placeholder="السعر قبل  الخصم" value="{{$offer->price}}" >
                                                    @if ($errors->has('price'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('price') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div  class="form-group">
                                                <label class="col-md-3 control-label"> السعر بعد الخصم </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="price_after_discount" class="form-control" placeholder="السعر بعد  الخصم" value="{{$offer->price_after_discount}}" required>
                                                    @if ($errors->has('price_after_discount'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('price_after_discount') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                        <div id="percent" style="{{$offer->price_percent != null ? 'display: block;' : 'display: none;'}}">
                                            <div  class="form-group">
                                                <label class="col-md-3 control-label"> سعر  العرض </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="price" class="form-control" placeholder="سعر  العرض" value="{{$offer->price}}" >
                                                    @if ($errors->has('price'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('price') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div  class="form-group">
                                                <label class="col-md-3 control-label"> النسبة المئوية للخصم </label>
                                                <div class="col-md-7">
                                                    <input type="text" name="price_percent" class="form-control" placeholder="النسبة المئوية للخصم" value="{{$offer->price_percent}}" required>
                                                    @if ($errors->has('price_percent'))
                                                        <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('price_percent') }}</strong>
                                            </span>
                                                    @endif
                                                </div>
                                                <div class="col-md-2">
                                                    %
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"> تاريخ  أنتهاء  العرض </label>
                                            <div class="col-md-9">
                                                <input type="date" name="end_date" class="form-control" placeholder="تاريخ  أنتهاء  العرض" value="{{$offer->end_date->format('Y-m-d')}}" required>
                                                @if ($errors->has('end_date'))
                                                    <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('end_date') }}</strong>
                                            </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"> الكمية القصوي  للعرض </label>
                                            <div class="col-md-9">
                                                <input type="number" name="max_quantity" class="form-control" placeholder="الكمية القصوي  للعرض" value="{{$offer->max_quantity}}" required>
                                                @if ($errors->has('max_quantity'))
                                                    <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('max_quantity') }}</strong>
                                            </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"> الرابط الخارجي  للعرض </label>
                                            <div class="col-md-9">
                                                <input type="url" name="external_link" class="form-control" placeholder="الرابط الخارجي  للعرض" value="{{$offer->external_link}}" required>
                                                @if ($errors->has('external_link'))
                                                    <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('external_link') }}</strong>
                                            </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"> كود الخصم </label>
                                            <div class="col-md-9">
                                                <input type="text" name="code" class="form-control" placeholder="كود الخصم" value="{{$offer->code}}" required>
                                                @if ($errors->has('code'))
                                                    <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('code') }}</strong>
                                            </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"> تفاصيل  العرض </label>
                                            <div class="col-md-9">
                                                <textarea name="details" class="form-control" placeholder="تفاصيل  العرض"> {{$offer->details}} </textarea>
                                                @if ($errors->has('details'))
                                                    <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('details') }}</strong>
                                            </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"> تفعيل  العرض </label>
                                            <div class="col-md-9">
                                                <input type="radio" name="active"  value="1"  {{$offer->active == '1' ? 'checked' : ''}}> نعم
                                                <input type="radio" name="active"  value="0" {{$offer->active == '0' ? 'checked' : ''}} > لا
                                                @if ($errors->has('active'))
                                                    <span class="help-block">
                                               <strong style="color: red;">{{ $errors->first('active') }}</strong>
                                            </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <h4 style="text-align: right">   صور  العرض  </h4>
                                            <div class="row">
                                                @foreach($photos as $image)
                                                    <div class="col-sm-3 img_{{ $image->id }}">
                                                        <p><img src="{{ URL::to('uploads/offers/'.$image->photo) }}" class="img-fluid" height="150" width="150" id="file_name"></p>
                                                        <a  id="{{ $image->id }}"  style="color: white;text-decoration: none;" class="delete_image hideDiv btn btn-danger">
                                                            <i class="glyphicon glyphicon-trash "></i> مسح</a>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <input name="photos[]" type="file" class="form-control" multiple>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-offset-3 col-md-9">
                                                    <button type="submit" class="btn green" value="حفظ" onclick="this.disabled=true;this.value='تم الارسال, انتظر...';this.form.submit();">حفظ</button>
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

                    <!-- END CONTENT -->


                </div>

            </form>
            <!-- END TAB PORTLET-->





        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>
    <script>
        function yesnoCheck(that) {
            if (that.value == "0") {
                document.getElementById("beforeAfter").style.display = "block";
                document.getElementById("percent").style.display = "none";
            } else if (that.value == "1") {
                document.getElementById("percent").style.display = "block";
                document.getElementById("beforeAfter").style.display = "none";
            } else {
                document.getElementById("percent").style.display = "none";
                document.getElementById("beforeAfter").style.display = "none";
            }
        }
    </script>
    <script>
        $(".delete_image").click(function(){
            var id = $(this).attr('id');
            var url = '{{ route("imageOfferRemove", ":id") }}';

            url = url.replace(':id', id);

            //alert(image_id );
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result) {
                    if (!result.message)
                    {
                        $(".img_"+id).fadeOut('1000');
                    }

                }
            });
        });
    </script>

@endsection
