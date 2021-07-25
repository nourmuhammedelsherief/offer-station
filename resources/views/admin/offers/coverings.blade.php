@extends('admin.layouts.master')

@section('title')
    تمييز تفعيل لينك المتاجر عن طريق التحويل البنكي
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.bootstrap-rtl.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
@endsection

@section('page_header')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{url('/admin/home')}}">لوحة التحكم</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{url('/admin/coverings')}}">تمييز تفعيل لينك المتاجر عن طريق التحويل البنكي</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>عرض تمييز تفعيل لينك المتاجر عن طريق التحويل البنكي</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">عرض تمييز تفعيل لينك المتاجر عن طريق التحويل البنكي
        <small>عرض جميع تمييز تفعيل لينك المتاجر عن طريق التحويل البنكي</small>
    </h1>
@endsection

@section('content')
    @if (session('msg'))
        <div class="alert alert-danger">
            {{ session('msg') }}
        </div>
    @endif
    @include('flash::message')
    <div class="row">
        <div class="col-lg-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light bordered table-responsive">
                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-lg-6">
                                {{--                                <div class="btn-group">--}}
                                {{--                                    <a class="btn sbold green" href="/admin/add/user/1"> إضافة جديد--}}
                                {{--                                        <i class="fa fa-plus"></i>--}}
                                {{--                                    </a>--}}
                                {{--                                </div>--}}
                            </div>

                        </div>
                    </div>
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="sample_1">
                        <thead>
                        <tr>
                            <th>
                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                    <input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes" />
                                    <span></span>
                                </label>
                            </th>
                            <th></th>
                            <th> المستخدم </th>
                            <th> رقم الهاتف </th>
                            <th> اللينك </th>
                            <th> السعر </th>
                            <th> صوره التحويل </th>
                            <th> العمليات </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i=0 ?>
                        @foreach($coverings as $covering)
                            <tr class="odd gradeX">
                                <td>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" value="1" />
                                        <span></span>
                                    </label>
                                </td>
                                <td><?php echo ++$i ?></td>
                                <td> {{$covering->user->name}} </td>
                                <td> {{$covering->user->phone_number}} </td>
                                <td> {{$covering->video_link}} </td>
                                <td> {{$covering->price}} </td>
                                <td>
                                    @if($covering->transfer_photo != null)
                                        <a type="button"  data-toggle="modal"
                                           data-target="#exampleModalScrollable{{$covering->id}}">
                                            <img class="imageresource" src="{{asset('/uploads/transfer_photos/'.$covering->transfer_photo)}}" height="50" width="50">
                                        </a>
                                        <div class="modal fade" id="exampleModalScrollable{{$covering->id}}" tabindex="-1"
                                             role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalScrollableTitle"> صورة التحويل البنكي للعميل لأضافه الفيديو في قسم التغطيات </h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <img src="{{asset('/uploads/transfer_photos/'.$covering->transfer_photo)}}"
                                                             width="500px"/>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Close
                                                        </button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    @endif
                                </td>

                                <td>
                                    <a class="btn btn-success" href="{{route('coveringsDone' , $covering->id)}}"> أضافة </a>
                                    {{--                                    <a class="btn btn-danger" href="{{route('chargeNotDone' , $covering->id)}}"> الغاء </a>--}}
                                    <a class="delete_user btn btn-danger" data="{{ $covering->id }}" data_name="" >
                                        <i class="fa fa-key"></i> الغاء
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>


@endsection

@section('scripts')
    <script src="{{ URL::asset('admin/js/datatable.js') }}"></script>
    <script src="{{ URL::asset('admin/js/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/datatables.bootstrap.js') }}"></script>
    <script src="{{ URL::asset('admin/js/table-datatables-managed.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/ui-sweetalert.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            var CSRF_TOKEN = $('meta[name="X-CSRF-TOKEN"]').attr('content');

            $('body').on('click', '.delete_user', function() {
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
                }, function() {

                    window.location.href = "{{ url('/') }}" + "/admin/coveringNotDone/"+id;


                });

            });

        });
    </script>
    <script>
        $(".pop").on("click", function() {
            $('#imagepreview').attr('src', $('.imageresource').attr('src')); // here asign the image to the modal when the user click the enlarge link
            $('#imagemodal').modal('show'); // imagemodal is the id attribute assigned to the bootstrap modal, then i use the show function
        });
    </script>
@endsection
