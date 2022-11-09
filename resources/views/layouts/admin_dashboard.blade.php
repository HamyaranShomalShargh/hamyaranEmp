<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="{{ asset("/images/logo.ico?v=2.01") }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>داشبورد</title>
    <script src="{{ asset('js/app.js?v=').$company_information->app_version }}" defer></script>
    @stack('styles')
    <link href="{{ asset('css/app.css?v=').$company_information->app_version }}" rel="stylesheet">
    <script>
        window.Laravel = {!! json_encode([
        'user' => auth()->check() ? auth()->user()->id : null,
    ]) !!};
    </script>
    @stack('scripts')
</head>
<body class="dashboard-container">
<div id="app">
    <loading v-show="show_loading" v-cloak></loading>
    <header class="rtl header position-fixed w-100">
        @if(auth()->user()->is_admin)
            <ul class="nav nav-tabs bg-menu pr-2 pl-2" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link menu-header-item" id="menu-tab" data-toggle="tab" href="#menu" role="tab" aria-controls="menu" aria-selected="true">
                        <span class="menu-header-text iransans">منو</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-header-item" id="contract-tab" data-toggle="tab" href="#contract" role="tab" aria-controls="contract" aria-selected="true">
                        <span class="menu-header-text iransans">قرارداد</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link user-header-item" id="user-tab" data-toggle="tab" href="#user" role="tab" aria-controls="user" aria-selected="true">
                        <span class="menu-header-text iransans">کاربران</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link user-header-item" id="performance-tab" data-toggle="tab" href="#performance" role="tab" aria-controls="performance" aria-selected="true">
                        <span class="menu-header-text iransans">اتوماسیون</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link user-header-item" id="settings-tab" data-toggle="tab" href="#settings" role="tab" aria-controls="settings" aria-selected="true">
                        <span class="menu-header-text iransans">تنظیمات</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="account-tab" data-toggle="tab" href="#account" role="tab" aria-controls="account" aria-selected="true">
                        <span class="menu-header-text iransans">حساب کاربری</span>
                    </a>
                </li>
            </ul>
            <div class="tab-content position-absolute w-100" id="menu-contents" style="box-shadow: 0 5px 5px -1px #e7e7e7">
                <div class="tab-pane fade pb-2 pt-2" id="menu" role="tabpanel" aria-labelledby="menu-tab">
                    <ul class="ribbon-container">
                        <li class="position-relative ribbon-li">
                            <a role="button" href="{{route("MenuHeaders.index")}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-around">
                                <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/menu.png")}}" alt="menu">
                                <span class="iranyekan ribbon-item-text">سرفصل منو</span>
                            </a>
                        </li>
                        <li class="position-relative ribbon-li">
                            <a role="button" href="{{route("MenuItems.index")}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-around">
                                <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/list.png")}}" alt="menu">
                                <span class="iranyekan ribbon-item-text">عناوین منو</span>
                            </a>
                        </li>
                        <li class="position-relative ribbon-li">
                            <a role="button" href="{{route("MenuActions.index")}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-around">
                                <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/action.png")}}" alt="menu">
                                <span class="iranyekan ribbon-item-text">عملیات منو</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-pane fade pb-2 pt-2" id="contract" role="tabpanel" aria-labelledby="contract-tab">
                    <ul class="ribbon-container">
                        <li class="position-relative ribbon-li">
                            <a role="button" href="{{route("AdminContractHeader.index")}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-around">
                                <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/conhead.png")}}" alt="menu">
                                <span class="iranyekan ribbon-item-text">قرارداد</span>
                            </a>
                        </li>
                        <li class="position-relative ribbon-li">
                            <a role="button" href="{{route("AdminContractSubset.index")}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-around">
                                <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/subcon.png")}}" alt="menu">
                                <span class="iranyekan ribbon-item-text">زیرمجموعه قرارداد</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-pane pb-2 pt-2 fade" id="user" role="tabpanel" aria-labelledby="user-tab">
                    <ul class="ribbon-container">
                        <li class="position-relative ribbon-li">
                            <a role="button" href="{{route("Users.index")}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-around">
                                <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/emp.png")}}" alt="menu">
                                <span class="iranyekan ribbon-item-text">کاربران</span>
                            </a>
                        </li>
                        <li class="position-relative ribbon-li">
                            <a role="button" href="{{route("Roles.index")}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-around">
                                <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/role.png")}}" alt="menu">
                                <span class="iranyekan ribbon-item-text">عنوان شغلی</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-pane fade pb-2 pt-2" id="performance" role="tabpanel" aria-labelledby="performance-tab">
                    <ul class="ribbon-container">
                        <li class="position-relative ribbon-li">
                            <a role="button" href="{{route("AutomationFlow.index")}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-around">
                                <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/data-flow.png")}}" alt="menu">
                                <span class="iranyekan ribbon-item-text">جریان گردش اتوماسیون</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-pane pb-2 pt-2 fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                    <ul class="ribbon-container">
                        <li class="position-relative ribbon-li">
                            <a role="button" href="{{route("DefaultTableAttributes.index")}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-around">
                                <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/chart1.png")}}" alt="menu">
                                <span class="iranyekan ribbon-item-text">عناوین پیش فرض</span>
                            </a>
                        </li>
                        <li class="position-relative ribbon-li">
                            <a role="button" href="{{route("TableAttributes.index")}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-around">
                                <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/chart2.png")}}" alt="menu">
                                <span class="iranyekan ribbon-item-text">عناوین ورودی</span>
                            </a>
                        </li>
                        <li class="position-relative ribbon-li">
                            <a role="button" href="{{route("InvoiceCoverTitles.index")}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-around">
                                <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/chart2.png")}}" alt="menu">
                                <span class="iranyekan ribbon-item-text">عناوین روکش وضعیت</span>
                            </a>
                        </li>
                        <li class="position-relative ribbon-li">
                            <a role="button" href="{{route("Advantages.index")}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-around">
                                <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/chart2.png")}}" alt="menu">
                                <span class="iranyekan ribbon-item-text">عناوین تغییرات مزایا</span>
                            </a>
                        </li>
                        <li class="position-relative ribbon-li">
                            <a role="button" href="{{route("CompanyInformation.index")}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-around">
                                <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/company.png")}}" alt="menu">
                                <span class="iranyekan ribbon-item-text">اطلاعات سازمان</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-pane fade pb-2 pt-2" id="account" role="tabpanel" aria-labelledby="account-tab">
                    <ul class="ribbon-container">
                        <li class="position-relative ribbon-li">
                            <a role="button" href="{{route("account.information")}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-between">
                                <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/user_inf.png")}}" alt="menu">
                                <span class="iranyekan ribbon-item-text">حساب کاربری</span>
                            </a>
                        </li>
                        <li class="position-relative ribbon-li">
                            <a role="button" href="{{route("account.verification")}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-between">
                                <img src="{{asset("/images/static_menu_icons/verification.png")}}" alt="menu">
                                <span class="iranyekan ribbon-item-text">تاییدیه اطلاعات</span>
                            </a>
                        </li>
                        <li class="position-relative ribbon-li">
                            <a role="button" href="{{route("user.password.reset")}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-between">
                                <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/password.png")}}" alt="menu">
                                <span class="iranyekan ribbon-item-text">تغییر گذرواژه</span>
                            </a>
                        </li>
                        <li class="position-relative ribbon-li">
                            <form id="logout_form" class="p-0 m-0 w-20 d-inline" action="{{ route("logout") }}" method="post" v-on:submit="logout">
                                @csrf
                                <div class="form-row p-0 m-0">
                                    <div class="form-group col-12 p-0 m-0">
                                        <button type="submit" form="logout_form" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-between">
                                            <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/exit.png")}}" alt="menu">
                                            <span class="iranyekan ribbon-item-text">خروج</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        @endif
    </header>
    <div class="main rtl container-fluid pr-3 pl-3" v-on:click="hide_ribbon">
        <img src="{{ asset("/images/idle-bg.png?v=888") }}" class=" idle-bg position-absolute m-auto">
        @if($errors->any())
            <div class="w-100 mt-2 error-box">
                <div class="alert alert-danger iransans mb-0 h-100 border border-danger pt-3 pb-3" role="alert">
                    <h6 class="font-weight-bold">
                        <i class="fa fa-exclamation-circle fa-1-4x red-color"></i>
                        در هنگام اجرای عملیات، خطا(های) زیر رخ داده است:
                    </h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li class="iransans">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        @if(session()->has("result"))
            <div class="information-box position-absolute mt-2">
                <div class="alert alert-{{ session("result") }} iransans mb-0 h-100 border border-{{ session("result") }} pt-3 pb-3" role="alert">
                    <h6 class="pt-2 font-weight-bold">
                        @switch(session("result"))
                            @case("success")
                                <i class="fa fa-check-circle fa-1-4x green-color"></i>
                                @break
                            @case("warning")
                                <i class="fa fa-exclamation-triangle fa-1-4x red-color"></i>
                                @break
                        @endswitch
                        @switch(session("message"))
                            @case("unknown")
                                <span>نتیجه عملیات نامشخص می باشد</span>
                                @break
                            @case("saved")
                                <span>عملیات ذخیره سازی با موفقیت انجام شد</span>
                                @break
                            @case("incomplete_import")
                                <span>عملیات ذخیره سازی با موفقیت انجام شد اما ثبت اطلاعات فایل اکسل به طور کامل انجام نشد</span>
                                <a href="#" role="button" data-toggle="modal" data-target="#import_errors">
                                    مشاهده بیشتر
                                </a>
                                @break
                            @case("updated")
                                <span>عملیات ویرایش با موفقیت انجام شد</span>
                                @break
                            @case("deleted")
                                <span>عملیات حذف با موفقیت انجام شد</span>
                                @break
                            @case("relation_exists")
                                <span>به دلیل وجود رابطه با اطلاعات دیگر،امکان حذف آن وجود ندارد</span>
                                @break
                            @case("inactive")
                                <span>عملیات غیرفعال سازی با موفقیت انجام شد</span>
                                @break
                            @case("active")
                                <span>عملیات فعال سازی با موفقیت انجام شد</span>
                                @break
                            @case("confirmed")
                                <span>عملیات تایید نهایی با موفقیت انجام شد</span>
                                @break
                            @case("sent")
                                <span>عملیات تایید و ارسال با موفقیت انجام شد</span>
                                @break
                            @case("referred")
                                <span>عملیات عدم تایید و ارجاع با موفقیت انجام شد</span>
                                @break
                        @endswitch
                    </h6>
                </div>
            </div>
        @endif
        @yield('content')
    </div>
    <footer class="d-flex bottom w-100 p-1 bg-menu">
        <span class="text-center iranyekan font-weight-bold font-size-sm pt-2 pr-2 pl-2 pb-1 ml-2 d-flex align-items-center justify-content-center" style="border: 1px inset #d5d5d5">
            <i class="fa fa-buildings fa-1-2x ml-1"></i>
            {{ $company_information->name }}
        </span>
        <span class="text-center iranyekan font-weight-bold font-size-sm  pt-2 pr-2 pl-2 pb-1 ml-2 d-flex align-items-center justify-content-center" style="border: 1px inset #d5d5d5">
            <i class="fa fa-user fa-1-2x ml-1"></i>
            {{ $auth_user->name }}
        </span>
        <span class="text-center iranyekan font-weight-bold font-size-sm pt-2 pr-2 pl-2 pb-1 ml-2 d-flex align-items-center justify-content-center" style="border: 1px inset #d5d5d5">
            <i class="fa fa-user-cog fa-1-2x ml-1"></i>
            {{ "مدیر سیستم" }}
        </span>
        <span class="text-center iranyekan font-weight-bold font-size-sm pt-2 pr-2 pl-2 pb-1 ml-2 d-flex align-items-center justify-content-center" style="border: 1px inset #d5d5d5">
            <i class="fa fa-shield fa-1-2x ml-1"></i>
            {{ "پروفایل : " }}
            @if($auth_user->email_verified_at != null && $auth_user->mobile_verified_at != null)
                <span class="text-center iranyekan font-weight-bold font-size-sm green-color">تایید شده</span>
            @elseif($auth_user->email_verified_at != null && $auth_user->mobile_verified_at == null)
                <span class="text-center iranyekan font-weight-bold font-size-sm green-color">ایمیل</span>
            @elseif($auth_user->email_verified_at == null && $auth_user->mobile_verified_at != null)
                <span class="text-center iranyekan font-weight-bold font-size-sm green-color">تلفن همراه</span>
            @else
                <span class="text-center iranyekan font-weight-bold font-size-sm red-color">تایید نشده</span>
            @endif
        </span>
        <span class="text-center iranyekan font-weight-bold font-size-sm pt-2 pr-2 pl-2 pb-1 ml-2 d-flex align-items-center justify-content-center" style="border: 1px inset #d5d5d5">
            <i class="fa fa-box-circle-check fa-1-2x ml-1"></i>
            {{ "نسخه ".$company_information->app_version }}
        </span>
    </footer>
    @yield('modals')
</div>
</body>
</html>
