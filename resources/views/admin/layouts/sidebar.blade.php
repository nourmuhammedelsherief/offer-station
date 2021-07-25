<div class="page-sidebar-wrapper">
    <!-- BEGIN SIDEBAR -->
    <div class="page-sidebar navbar-collapse collapse">

        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true"
            data-slide-speed="200" style="padding-top: 20px">
            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
            <li class="sidebar-toggler-wrapper hide">
                <div class="sidebar-toggler">
                    <span></span>
                </div>
            </li>

            <li class="nav-item start active open">
                <a href="/admin/home" class="nav-link nav-toggle">
                    <i class="icon-home"></i>
                    <span class="title">الرئيسية</span>
                    <span class="selected"></span>

                </a>
            </li>
            <li class="heading">
                <h3 class="uppercase">القائمة الجانبية</h3>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'admins') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-users" style="color: aqua;"></i>
                    <span class="title">المشرفين</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{ url('/admin/admins') }}" class="nav-link ">
                            <span class="title">عرض المشرفين</span>
                            <?php $admins = \App\Admin::count(); ?>
                            <span class="badge badge-success">{{$admins}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/admins/create') }}" class="nav-link ">
                            <span class="title">اضافة مشرف</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ strpos(URL::current(), 'admin/users/0') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-users" style="color: aqua;"></i>
                    <span class="title">المستخدمين</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ strpos(URL::current(), 'admin/users/1') !== false ? 'active' : '' }}">
                        <a href="{{ url('/admin/users/1') }}" class="nav-link ">
                            <i class="fa fa-users" style="color: aqua;"></i>
                            <span class="title"> المستخدمين </span>
                            <?php $users = \App\User::where('type', '1')->get()->count(); ?>
                            <span class="badge badge-success">{{$users}}</span>
                        </a>
                    </li>
                    <li class="nav-item {{ strpos(URL::current(), 'admin/users/2') !== false ? 'active' : '' }}">
                        <a href="{{ url('/admin/users/2') }}" class="nav-link ">
                            <i class="fa fa-shopping-cart" style="color: aqua;"></i>
                            <span class="title"> المتاجر </span>
                            <?php $stores = \App\User::where('type', '2')->get()->count(); ?>
                            <span class="badge badge-success">{{$stores}}</span>
                        </a>
                    </li>
                </ul>
            </li>


            <li class="nav-item {{ strpos(URL::current(), 'admin/store_types') !== false ? 'active' : '' }}">
                <a href="{{url('/admin/store_types')}}" class="nav-link ">
                    <i class="fa fa-list-alt" style="color: aqua;"></i>
                    <span class="title"> الأقسام </span>
                    <span class="pull-right-container"></span>
                    <?php $carTypes = \App\StoreType::count(); ?>
                    <span class="badge badge-success">{{$carTypes}}</span>
                </a>
            </li>
            <li class="nav-item {{ strpos(URL::current(), 'admin/cities') !== false ? 'active' : '' }}">
                <a href="{{url('/admin/cities')}}" class="nav-link ">
                    <i class="fa fa-building-o" style="color: aqua;"></i>
                    <span class="title"> المدن </span>
                    <span class="pull-right-container"></span>
                    <?php $cities = \App\City::count(); ?>
                    <span class="badge badge-success">{{$cities}}</span>
                </a>
            </li>
            {{--            <li class="nav-item {{ strpos(URL::current(), 'admin/categories') !== false ? 'active' : '' }}">--}}
            {{--                <a href="{{url('/admin/categories')}}" class="nav-link ">--}}
            {{--                    <i class="icon-layers"></i>--}}
            {{--                    <span class="title"> الأقسام </span>--}}
            {{--                    <span class="pull-right-container">--}}
            {{--            </span>--}}

            {{--                </a>--}}
            {{--            </li>--}}
            <li class="nav-item {{ strpos(URL::current(), 'admin/users/0') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-bell" style="color: aqua;"></i>
                    <span class="title">الأشعارات</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ strpos(URL::current(), 'admin/public_notifications') !== false ? 'active' : '' }}">
                        <a href="{{url('/admin/public_notifications')}}" class="nav-link ">
                            <i class="fa fa-bell-o" style="color: aqua;"></i>
                            <span class="title">ألاشعارات العامه</span>
                            <span class="pull-right-container"></span>

                        </a>
                    </li>

                    <li class="nav-item {{ strpos(URL::current(), 'admin/category_notifications') !== false ? 'active' : '' }}">
                        <a href="{{url('/admin/category_notifications')}}" class="nav-link ">
                            <i class="fa fa-bell-o" style="color: aqua;"></i>
                            <span class="title">ألاشعارات لفئة معينة</span>
                            <span class="pull-right-container"></span>

                        </a>
                    </li>
                    <li class="nav-item {{ strpos(URL::current(), 'admin/user_notifications') !== false ? 'active' : '' }}">
                        <a href="{{url('/admin/user_notifications')}}" class="nav-link ">
                            <i class="fa fa-bell-o" style="color: aqua;"></i>
                            <span class="title"> اشعارات لأشخاص محددين </span>
                            <span class="pull-right-container"></span>

                        </a>
                    </li>
                </ul>
            </li>


            <li class="nav-item {{ strpos(URL::current(), 'admin/offers/0') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-gift" style="color: aqua;"></i>
                    <span class="title">العروض</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ strpos(URL::current(), 'admin/offers/1') !== false ? 'active' : '' }}">
                        <a href="{{ url('/admin/offers/1') }}" class="nav-link ">
                            <i class="fa fa-ban" style="color: aqua;"></i>
                            <span class="title"> النشطة </span>
                            <?php $active_offers = \App\Offer::whereActive('1')->count(); ?>
                            <span class="badge badge-success">{{$active_offers}}</span>
                        </a>
                    </li>
                    <li class="nav-item {{ strpos(URL::current(), 'admin/offers/0') !== false ? 'active' : '' }}">
                        <a href="{{ url('/admin/offers/0') }}" class="nav-link ">
                            <i class="fa fa-toggle-on" style="color: aqua;"></i>
                            <span class="title"> الغير نشطة </span>
                            <?php $unActiveOffers = \App\Offer::whereActive('0')->count(); ?>
                            <span class="badge badge-success">{{$unActiveOffers}}</span>
                        </a>
                    </li>
                    <li class="nav-item {{ strpos(URL::current(), 'admin/terminated/offers') !== false ? 'active' : '' }}">
                        <a href="{{ url('/admin/terminated/offers') }}" class="nav-link ">
                            <i class="fa fa-toggle-down" style="color: aqua;"></i>
                            <span class="title"> المنتهية </span>
                            <?php $terminatedOffers = \App\Offer::whereStatus('1')->count(); ?>
                            <span class="badge badge-success">{{$terminatedOffers}}</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li class="nav-item {{ strpos(URL::current(), 'admin/news') !== false ? 'active' : '' }}">
                <a href="{{url('/admin/news')}}" class="nav-link ">
                    <i class="fa fa-newspaper-o" style="color: aqua;"></i>
                    <span class="title"> الأخبار </span>
                    <span class="pull-right-container"></span>
                    <?php $news = \App\News::count(); ?>
                    <span class="badge badge-success">{{$news}}</span>
                </a>
            </li>
            <li class="nav-item {{ strpos(URL::current(), 'admin/discriminate_places') !== false ? 'active' : '' }}">
                <a href="{{url('/admin/discriminate_places')}}" class="nav-link ">
                    <i class="fa fa-money" style="color: aqua;"></i>
                    <span class="title"> اسعار الأعلانات المميزة </span>
                    <span class="pull-right-container"></span>
                    <?php $ad_prices = \App\OfferDiscriminatePlaces::count(); ?>
                    <span class="badge badge-success">{{$ad_prices}}</span>
                </a>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'admin/offer_transfer/0') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-exchange" style="color: aqua;"></i>
                    <span class="title">التحويلات  البنكية</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ strpos(URL::current(), 'admin/offer_transfer') !== false ? 'active' : '' }}">
                        <a href="{{url('/admin/offer_transfer')}}" class="nav-link ">
                            <i class="fa fa-gift" style="color: aqua;"></i>
                            <span class="title"> تمميز الأعلانات </span>
                            <span class="pull-right-container"></span>
                            <?php $transfer_photos = \App\Offer::where('transfer_photo', '!=', null)
                                ->where('discriminate', '0')
                                ->where('status', '0')
                                ->get()->count();
                            ?>
                            <span class="badge badge-success">{{$transfer_photos}}</span>
                        </a>
                    </li>
                    <li class="nav-item {{ strpos(URL::current(), 'admin/coverings') !== false ? 'active' : '' }}">
                        <a href="{{url('/admin/coverings')}}" class="nav-link ">
                            <i class="fa fa-shield" style="color: aqua;"></i>
                            <span class="title"> التغطيات </span>
                            <span class="pull-right-container"></span>
                            <?php $coverings = \App\Covering::where('transfer_photo', '!=', null)
                                ->where('status', '0')
                                ->get()->count();
                            ?>
                            <span class="badge badge-success">{{$coverings}}</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li class="nav-item {{ strpos(URL::current(), 'admin/complaints') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-file" style="color: aqua;"></i>
                    <span class="title"> الشكاوي والأبلاغات </span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ strpos(URL::current(), 'admin/complaints') !== false ? 'active' : '' }}">
                        <a href="{{url('/admin/complaints')}}" class="nav-link ">
                            <i class="fa fa-gavel" style="color: aqua;"></i>
                            <span class="title"> الشكاوي </span>
                            <span class="pull-right-container"></span>
                            <?php $complaints = \App\Complain::count();
                            ?>
                            <span class="badge badge-success">{{$complaints}}</span>
                        </a>
                    </li>
                    <li class="nav-item {{ strpos(URL::current(), 'admin/reports') !== false ? 'active' : '' }}">
                        <a href="{{url('/admin/reports')}}" class="nav-link ">
                            <i class="fa fa-bar-chart" style="color: aqua;"></i>
                            <span class="title"> الأبلاغات </span>
                            <span class="pull-right-container"></span>
                            <?php $reports = \App\Report::count();
                            ?>
                            <span class="badge badge-success">{{$reports}}</span>
                        </a>
                    </li>

                </ul>
            </li>





            <li class="nav-item {{ strpos(URL::current(), 'admin/setting') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cogs" style="color: aqua;"></i>
                    <span class="title">الأعدادات</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ strpos(URL::current(), 'admin/setting') !== false ? 'active' : '' }}">
                        <a href="{{url('/admin/setting')}}" class="nav-link ">
                            <i class="fa fa-cog" style="color: aqua;"></i>
                            <span class="title"> الأعدادات  العامة </span>
                        </a>
                    </li>
                    <li class="nav-item {{ strpos(URL::current(), 'admin/offers/setting') !== false ? 'active' : '' }}">
                        <a href="{{url('/admin/offers/setting')}}" class="nav-link ">
                            <i class="fa fa-gift" style="color: aqua;"></i>
                            <span class="title"> اعدادات العروض </span>
                        </a>
                    </li>
                    <li class="nav-item {{ strpos(URL::current(), 'admin/bank/setting') !== false ? 'active' : '' }}">
                        <a href="{{url('/admin/bank/setting')}}" class="nav-link ">
                            <i class="fa fa-bank" style="color: aqua;"></i>
                            <span class="title"> اعدادات البنك </span>
                        </a>
                    </li>
                    <li class="nav-item {{ strpos(URL::current(), 'admin/sms/setting') !== false ? 'active' : '' }}">
                        <a href="{{url('/admin/sms/setting')}}" class="nav-link ">
                            <i class="fa fa-paper-plane" style="color: aqua;"></i>
                            <span class="title"> بيانات  الأرسال </span>
                        </a>
                    </li>


                </ul>
            </li>
            <li class="nav-item {{ strpos(URL::current(), 'admin/pages') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cog" style="color: aqua;"></i>
                    <span class="title">الصفحات</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item  ">
                        <a href="{{url('/admin/pages/about')}}" class="nav-link ">
                            <span class="title">من نحن</span>
                        </a>
                    </li>
                    <li class="nav-item  ">
                        <a href="{{url('/admin/pages/terms')}}" class="nav-link ">
                            <span class="title">الشروط والاحكام</span>
                        </a>
                    </li>


                </ul>
            </li>


        </ul>
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
