@extends('admin.layouts.master')

@section('title')
    لوحة التحكم
@endsection

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="/admin/home"> لوحة التحكم</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>الإحصائيات</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">  الإحصائيات
        <small>عرض الإحصائيات</small>
    </h1>

    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-blue" href="{{ url('/admin/admins') }}">
                <div class="visual">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$admins}}</span>
                    </div>
                    <div class="desc"> عدد المديرين  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-red" href="{{ url('/admin/users/1') }}">
                <div class="visual">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$users}}</span>
                    </div>
                    <div class="desc"> عدد المستخدمين  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-yellow" href="{{ url('/admin/users/2') }}">
                <div class="visual">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$stores}}</span>
                    </div>
                    <div class="desc"> عدد المتاجر  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 gray-background" href="{{ url('/admin/cities') }}">
                <div class="visual">
                    <i class="fa fa-building-o"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$cities}}</span>
                    </div>
                    <div class="desc"> عدد المدن  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-orange" href="{{ url('/admin/store_types') }}">
                <div class="visual">
                    <i class="fa fa-list-alt"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$carTypes}}</span>
                    </div>
                    <div class="desc"> عدد الأقسام  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-brown" href="{{ url('/admin/offers/1') }}">
                <div class="visual">
                    <i class="fa fa-ban"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$active_offers}}</span>
                    </div>
                    <div class="desc"> عدد العروض النشطة  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-pink" href="{{ url('/admin/offers/0') }}">
                <div class="visual">
                    <i class="fa fa-toggle-on"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$unActiveOffers}}</span>
                    </div>
                    <div class="desc"> العروض الغير النشطة  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-purple" href="{{ url('/admin/terminated/offers') }}">
                <div class="visual">
                    <i class="fa fa-toggle-down"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$terminatedOffers}}</span>
                    </div>
                    <div class="desc"> العروض المنتهية  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-purple-plum" href="{{ url('/admin/news') }}">
                <div class="visual">
                    <i class="fa fa-newspaper-o"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$news}}</span>
                    </div>
                    <div class="desc">  الأخبار  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 gray-background" href="{{ url('/admin/complaints') }}">
                <div class="visual">
                    <i class="fa fa-gavel"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$complaints}}</span>
                    </div>
                    <div class="desc">  الشكاوي  </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 bg-red" href="{{ url('/admin/reports') }}">
                <div class="visual">
                    <i class="fa fa-bar-chart"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span>{{$reports}}</span>
                    </div>
                    <div class="desc">  الأبلاغات  </div>
                </div>
            </a>
        </div>

    </div>
@endsection
