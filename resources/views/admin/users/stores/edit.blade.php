@extends('admin.layouts.master')

@section('title')
    اضافة المتاجر
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ URL::asset('admin/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/bootstrap-fileinput.css') }}">
    <style>
        #map {
            height: 500px;
            width: 1000px;
        }
    </style>
@endsection

@section('page_header')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{url('/admin/home')}}">لوحة التحكم</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{url('/admin/users/2')}}">المتاجر</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>اضافة المتاجر</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title"> المتاجر
        <small>اضافة متجر    </small>
    </h1>
@endsection

@section('content')
    @if (session('information'))
        <div class="alert alert-success">
            {{ session('information') }}
        </div>
    @endif
    @if (session('pass'))
        <div class="alert alert-success">
            {{ session('pass') }}
        </div>
    @endif
    @if (session('privacy'))
        <div class="alert alert-success">
            {{ session('privacy') }}
        </div>
    @endif



    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">

            <!-- BEGIN PROFILE CONTENT -->
            <div class="profile-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light ">
                            <div class="portlet-title tabbable-line">
                                <div class="caption caption-md">
                                    <i class="icon-globe theme-font hide"></i>
                                    <span class="caption-subject font-blue-madison bold uppercase">حساب الملف الشخصي</span>
                                </div>
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#tab_1_1" data-toggle="tab"> بيانات  المتجر </a>
                                    </li>
                                    <li>
                                        <a href="#tab_1_3" data-toggle="tab">تغيير كلمة المرور</a>
                                    </li>
                                    <li>
                                        <a href="#tab_1_4" data-toggle="tab">اعدادات الخصوصية</a>
                                    </li>
                                </ul>
                            </div>
                                <div class="portlet-body">

                                    <div class="tab-content">
                                        <!-- PERSONAL INFO TAB -->
                                        <div class="tab-pane active" id="tab_1_1">
                                            <form role="form" action="/admin/update/user/{{$user->id}}/2" method="post" enctype="multipart/form-data">
                                                <input type = 'hidden' name = '_token' value = '{{Session::token()}}'>

                                                <div class="form-group">
                                                <label class="control-label"> انواع المتاجر </label>
                                                <select name="store_type_id" class="form-control" required>
                                                    <option disabled selected> أختر نوع المتجر </option>
                                                    @foreach($store_types  as $store_type)
                                                        <option value="{{$store_type->id}}" {{$user->store_type_id == $store_type->id ? 'selected' : ''}}> {{$store_type->ar_name}} </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('store_type_id'))
                                                    <span class="help-block">
                                                       <strong style="color: red;">{{ $errors->first('store_type_id') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label">اسم  المتجر بالعربي</label>
                                                <input type="text" name="name" placeholder="الاسم" class="form-control" value="{{$user->name}}" />
                                                @if ($errors->has('name'))
                                                    <span class="help-block">
                                                       <strong style="color: red;">{{ $errors->first('name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">اسم  المتجر بالأنجليزي</label>
                                                <input type="text" name="en_name" placeholder="اسم  المتجر بالأنجليزي" class="form-control" value="{{$user->en_name}}" />
                                                @if ($errors->has('en_name'))
                                                    <span class="help-block">
                                                       <strong style="color: red;">{{ $errors->first('en_name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">رقم الهاتف</label>

                                                <input type="text" name="phone_number" placeholder="رقم الهاتف" class="form-control" value="{{$user->phone_number}}" />
                                                @if ($errors->has('phone_number'))
                                                    <span class="help-block">
                                                       <strong style="color: red;">{{ $errors->first('phone_number') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">البريد الألكتروني</label>
                                                <input type="email" name="email" placeholder="البريد الألكتروني" class="form-control" value="{{$user->email}}" />
                                                @if ($errors->has('email'))
                                                    <span class="help-block">
                                                       <strong style="color: red;">{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">اوقات  العمل</label>
                                                <input type="text" name="work_times" placeholder="اوقات  العمل" class="form-control" value="{{$user->work_times}}" />
                                                @if ($errors->has('work_times'))
                                                    <span class="help-block">
                                                       <strong style="color: red;">{{ $errors->first('work_times') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">رقم  التواصل</label>
                                                <input type="text" name="contact_number" placeholder="رقم  التواصل" class="form-control" value="{{$user->contact_number}}" />
                                                @if ($errors->has('contact_number'))
                                                    <span class="help-block">
                                                       <strong style="color: red;">{{ $errors->first('contact_number') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">رابط المتجر الألكتروني</label>
                                                <input type="text" name="store_url" placeholder="رابط المتجر الألكتروني" class="form-control" value="{{$user->store_url}}" />
                                                @if ($errors->has('store_url'))
                                                    <span class="help-block">
                                                       <strong style="color: red;">{{ $errors->first('store_url') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">رابط الفيديو التعريفي</label>
                                                <input type="text" name="video_link" placeholder="رابط الفيديو التعريفي" class="form-control" value="{{$user->video_link}}" />
                                                @if ($errors->has('video_link'))
                                                    <span class="help-block">
                                                       <strong style="color: red;">{{ $errors->first('video_link') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-body">
                                                <div class="form-group ">
                                                    <label class="control-label col-md-3"> صورة المتجر</label>
                                                    <div class="col-md-9">
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                                @if($user->photo != null)
                                                                    <img src="{{asset('/uploads/users/'.$user->photo)}}">
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
                                            <div class="form-body">
                                                <div class="form-group ">
                                                    <label class="control-label col-md-3"> لوجو المتجر </label>
                                                    <div class="col-md-9">
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                                @if($user->logo != null)
                                                                    <img src="{{asset('/uploads/logos/'.$user->logo)}}">
                                                                @endif
                                                            </div>
                                                            <div>
                                                            <span class="btn red btn-outline btn-file">
                                                                <span class="fileinput-new"> اختر الصورة </span>
                                                                <span class="fileinput-exists"> تغيير </span>
                                                                <input type="file" name="logo"> </span>
                                                                <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> إزالة </a>



                                                            </div>
                                                        </div>
                                                        @if ($errors->has('logo'))
                                                            <span class="help-block">
                                                               <strong style="color: red;">{{ $errors->first('logo') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="form-body">
                                                <div class="form-group ">
                                                    <label class="control-label col-md-3"> صوره السجل التجاري </label>
                                                    <div class="col-md-9">
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                                @if($user->commercial_register != null)
                                                                    <img src="{{asset('/uploads/commercial_registers/'.$user->commercial_register)}}">
                                                                @endif
                                                            </div>
                                                            <div>
                                                            <span class="btn red btn-outline btn-file">
                                                                <span class="fileinput-new"> اختر الصورة </span>
                                                                <span class="fileinput-exists"> تغيير </span>
                                                                <input type="file" name="commercial_register"> </span>
                                                                <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> إزالة </a>
                                                            </div>
                                                        </div>
                                                        @if ($errors->has('commercial_register'))
                                                            <span class="help-block">
                                                               <strong style="color: red;">{{ $errors->first('commercial_register') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="form-body">
                                                <div class="form-group ">
                                                    <label class="control-label col-md-3"> رخصة المتجر </label>
                                                    <div class="col-md-9">
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                                @if($user->license != null)
                                                                    <img src="{{asset('/uploads/licenses/'.$user->license)}}">
                                                                @endif
                                                            </div>
                                                            <div>
                                                            <span class="btn red btn-outline btn-file">
                                                                <span class="fileinput-new"> اختر الصورة </span>
                                                                <span class="fileinput-exists"> تغيير </span>
                                                                <input type="file" name="license"> </span>
                                                                <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> إزالة </a>
                                                            </div>
                                                        </div>
                                                        @if ($errors->has('license'))
                                                            <span class="help-block">
                                                               <strong style="color: red;">{{ $errors->first('license') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <h4 style="text-align: right">   صور  البانر  </h4>
                                                    <div class="row">
                                                        @foreach($photos as $image)
                                                            <div class="col-sm-3 img_{{ $image->id }}">
                                                                <p><img src="{{ URL::to('uploads/store_banners/'.$image->photo) }}" class="img-fluid" height="150" width="150" id="file_name"></p>
                                                                <a  id="{{ $image->id }}"  style="color: white;text-decoration: none;" class="delete_image hideDiv btn btn-danger">
                                                                    <i class="glyphicon glyphicon-trash "></i> مسح</a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <input name="banner_photos[]" type="file" class="form-control" multiple>
                                                </div>
                                            </div>
                                            <div class="body-site">
                                                <div class="d-flex">
                                                    <div class="col-m-9">
                                                        <div class="content sections">
                                                            <h4 style="text-align: right">  حدد موقع المتجر علي  الخريطة  </h4>
                                                            <input type="text" id="lat" name="latitude" value="{{$user->latitude}}" readonly="yes" required />
                                                            <input type="text" id="lng" name="longitude" value="{{$user->longitude}}" readonly="yes" required />
                                                            <a class="btn btn-info" onclick="getLocation()" > حدد موقعك الان </a>
                                                            <div id="map"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="margiv-top-10">
                                                <div class="form-actions">
                                                    <button type="submit" class="btn green" value="حفظ" onclick="this.disabled=true;this.value='تم الارسال, انتظر...';this.form.submit();">حفظ</button>

                                                </div>
                                            </div>
                                            </form>
                                            <br>
                                        </div>
                                        <!-- END PERSONAL INFO TAB -->
                                        <!-- CHANGE PASSWORD TAB -->
                                        <div class="tab-pane" id="tab_1_3">
                                            <form action="/admin/update/pass/{{$user->id}}" method="post">
                                                <input type = 'hidden' name = '_token' value = '{{Session::token()}}'>

                                                <div class="form-group">
                                                    <label class="control-label">كلمة المرور الجديدة</label>
                                                    <input type="password" name="password" class="form-control" />
                                                    @if ($errors->has('password'))
                                                        <span class="help-block">
                                                       <strong style="color: red;">{{ $errors->first('password') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">إعادة كلمة المرور</label>
                                                    <input type="password" name="password_confirmation" class="form-control" />
                                                    @if ($errors->has('password_confirmation'))
                                                        <span class="help-block">
                                                       <strong style="color: red;">{{ $errors->first('password_confirmation') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div class="margin-top-10">
                                                    <div class="form-actions">
                                                        <button type="submit" class="btn green">حفظ</button>

                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- END CHANGE PASSWORD TAB -->
                                        <!-- PRIVACY SETTINGS TAB -->

                                        <div class="tab-pane" id="tab_1_4">
                                            <form action="/admin/update/privacy/{{$user->id}}" method="post">
                                                <input type = 'hidden' name = '_token' value = '{{Session::token()}}'>
                                                <table class="table table-light table-hover">

                                                    <tr>
                                                        <td> تفعيل المستخدم</td>
                                                        <td>
                                                            <div class="mt-radio-inline">
                                                                <label class="mt-radio">
                                                                    <input type="radio" name="active" value="1" {{ $user->active == "1" ? 'checked' : '' }}/> نعم
                                                                    <span></span>
                                                                </label>
                                                                <label class="mt-radio">
                                                                    <input type="radio" name="active" value="0" {{$user->active == "0" ? 'checked' : '' }}/> لا
                                                                    <span></span>
                                                                </label>
                                                                @if ($errors->has('active'))
                                                                    <span class="help-block">
                                                                       <strong style="color: red;">{{ $errors->first('active') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>


                                                </table>
                                                <div class="margin-top-10">
                                                    <div class="form-actions">
                                                        <button type="submit" class="btn green">حفظ</button>

                                                    </div>
                                                </div>
                                            </form>

                                        </div>
                                        <!-- END PRIVACY SETTINGS TAB -->
                                    </div>

                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PROFILE CONTENT -->
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ URL::asset('admin/js/select2.full.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/components-select2.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>
    <script>
        $(".delete_image").click(function(){
            var id = $(this).attr('id');
            var url = '{{ route("imageStoreRemove", ":id") }}';

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

    <script>
        function getLocation()
        {
            if (navigator.geolocation)
            {
                navigator.geolocation.getCurrentPosition(showPosition);
            }
            else{x.innerHTML="Geolocation is not supported by this browser.";}
        }

        function showPosition(position)
        {
            lat= position.coords.latitude;
            lon= position.coords.longitude;

            document.getElementById('lat').value = lat; //latitude
            document.getElementById('lng').value = lon; //longitude
            latlon=new google.maps.LatLng(lat, lon)
            mapholder=document.getElementById('mapholder')
            //mapholder.style.height='250px';
            //mapholder.style.width='100%';

            var myOptions={
                center:latlon,zoom:14,
                mapTypeId:google.maps.MapTypeId.ROADMAP,
                mapTypeControl:false,
                navigationControlOptions:{style:google.maps.NavigationControlStyle.SMALL}
            };
            var map = new google.maps.Map(document.getElementById("map"),myOptions);
            var marker=new google.maps.Marker({position:latlon,map:map,title:"You are here!"});
        }

    </script>
    <script type="text/javascript">
        var map;

        function initMap() {


            var latitude = {{$user->latitude}}; // YOUR LATITUDE VALUE
            var longitude = {{$user->longitude}};  // YOUR LONGITUDE VALUE

            console.log(latitude);
            console.log(longitude);
            var myLatLng = {lat: latitude, lng: longitude};

            map = new google.maps.Map(document.getElementById('map'), {
                center: myLatLng,
                zoom: 10,
                gestureHandling: 'true',
                zoomControl: false// disable the default map zoom on double click
            });




            var marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                //title: 'Hello World'

                // setting latitude & longitude as title of the marker
                // title is shown when you hover over the marker
                title: latitude + ', ' + longitude
            });


            //Listen for any clicks on the map.
            google.maps.event.addListener(map, 'click', function(event) {
                //Get the location that the user clicked.
                var clickedLocation = event.latLng;
                //If the marker hasn't been added.
                if(marker === false){
                    //Create the marker.
                    marker = new google.maps.Marker({
                        position: clickedLocation,
                        map: map,
                        draggable: true //make it draggable
                    });
                    //Listen for drag events!
                    google.maps.event.addListener(marker, 'dragend', function(event){
                        markerLocation();
                    });
                } else{
                    //Marker has already been added, so just change its location.
                    marker.setPosition(clickedLocation);
                }
                //Get the marker's location.
                markerLocation();
            });



            function markerLocation(){
                //Get location.
                var currentLocation = marker.getPosition();
                //Add lat and lng values to a field that we can save.
                document.getElementById('lat').value = currentLocation.lat(); //latitude
                document.getElementById('lng').value = currentLocation.lng(); //longitude
            }
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFUMq5htfgLMNYvN4cuHvfGmhe8AwBeKU&callback=initMap" async defer></script>
@endsection
