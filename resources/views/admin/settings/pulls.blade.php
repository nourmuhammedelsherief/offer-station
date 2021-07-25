@extends('admin.layouts.master')

@section('title')
    طلبات سحب  الرصيد  من المحفظه الألكترونيه
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
                <a href="{{url('/admin/pulls')}}">طلبات سحب  الرصيد  من المحفظه الألكترونيه</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>عرض طلبات سحب  الرصيد  من المحفظه الألكترونيه</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">عرض طلبات سحب  الرصيد  من المحفظه الألكترونيه
        <small>عرض جميع طلبات سحب  الرصيد  من المحفظه الألكترونيه</small>
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
                            <th> النوع </th>
                            <th> القيمه </th>
                            <th> العملة </th>
                            <th> العمليات </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i=0 ?>
                        @foreach($pulls as $pull)
                            <tr class="odd gradeX">
                                <td>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" value="1" />
                                        <span></span>
                                    </label>
                                </td>
                                <td><?php echo ++$i ?></td>
                                <td> {{$pull->user->name}} </td>
                                <td> {{$pull->user->phone_number}} </td>
                                <td>
                                    @if($pull->user->type == '1')
                                        عميل
                                    @elseif($pull->user->type == '2')
                                        سائق
                                    @endif
                                </td>
                                <td> {{$pull->amount}} </td>
                                <td> {{$pull->user->country->ar_currency}} </td>

                                <td>
                                    <a class="btn btn-success" href="{{route('PullDone' , $pull->id)}}"> تم التحويل </a>
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

                    window.location.href = "{{ url('/') }}" + "/admin/orders/delete/"+id;


                });

            });

        });
    </script>

@endsection
