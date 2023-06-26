<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="{{ asset("/images/logo.ico?v=2.01") }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>داشبورد</title>
    <link href="{{ asset('css/app.css?v=').$company_information->app_version }}" rel="stylesheet">
    @stack('styles')
    <script src="{{ asset('/js/app.js?v=').$company_information->app_version.time() }}" defer></script>
{{--    <script src="{{ asset('/js/enable_push.js?v='.time()) }}" defer></script>--}}
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
        <ul class="nav nav-tabs bg-menu pr-2 pl-2 toolbar-menu" role="tablist">
            @forelse($menu_headers as $menu_header)
                @if($role->menu_items->whereIn("id",$menu_header->items->pluck("id"))->isNotEmpty())
                    <li class="nav-item">
                        <a class="nav-link" id="{{$menu_header->slug}}-tab" data-toggle="tab" href="#{{$menu_header->slug}}" role="tab" aria-controls="{{$menu_header->slug}}" aria-selected="true">
                            <span class="menu-header-text iransans">{{ $menu_header->name }}</span>
                        </a>
                    </li>
                @endif
            @empty
            @endforelse
            <li class="nav-item">
                <a class="nav-link" id="account-tab" data-toggle="tab" href="#account" role="tab" aria-controls="account" aria-selected="true">
                    <span class="menu-header-text iransans">حساب کاربری</span>
                </a>
            </li>
        </ul>
        <div class="tab-content position-absolute w-100" id="menu-contents">
            @forelse($menu_headers as $menu_header)
                @if($role->menu_items->whereIn("id",$menu_header->items->pluck("id"))->isNotEmpty())
                    <div class="tab-pane fade pb-2 pt-2" id="{{$menu_header->slug}}" role="tabpanel" aria-labelledby="{{$menu_header->slug}}-tab">
                        <ul class="ribbon-container">
                            @foreach($menu_header->items as $item)
                                @if($item->children->isNotEmpty())
                                    @if($role->menu_items->whereIn("id",$item->children->pluck("id"))->isNotEmpty())
                                        <li class="position-relative ribbon-li">
                                            <a class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-around dropdown-toggle" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <img class="ribbon-item-icon" src="{{asset("/storage/menu_item_icons/{$item->id}/{$item->icon}")}}" alt="menu">
                                                <span class="iranyekan ribbon-item-text">{{$item->name}}</span>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                                @foreach($item->children as $child)
                                                    @if($role->menu_items->where("id",$child->id)->isNotEmpty() && Route::has($role->menu_items->where("pivot.menu_item_id",$child->id)->where("pivot.menu_action_id",$child->actions->where("action",$child->main_route)->first()->id)->first()->pivot->route))
                                                        <a class="dropdown-item iranyekan" href="{{route($role->menu_items->where("pivot.menu_item_id",$child->id)->where("pivot.menu_action_id",$child->actions->where("action",$child->main_route)->first()->id)->first()->pivot->route)}}">{{$child->name}}</a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </li>
                                    @endif
                                @else
                                    @if($role->menu_items->where("id",$item->id)->isNotEmpty() && $item->parent_id == null && Route::has($role->menu_items->where("pivot.menu_item_id",$item->id)->where("pivot.menu_action_id",$item->actions->where("action",$item->main_route)->first()->id)->first()->pivot->route))
                                        <li class="position-relative ribbon-li">
                                            <a role="button" href="{{route($role->menu_items->where("pivot.menu_item_id",$item->id)->where("pivot.menu_action_id",$item->actions->where("action",$item->main_route)->first()->id)->first()->pivot->route)}}" class="ribbon-item btn btn-light d-flex flex-column align-items-center justify-content-around">
                                                <img class="ribbon-item-icon" src="{{asset("/storage/menu_item_icons/{$item->id}/{$item->icon}")}}" alt="menu">
                                                <span class="iranyekan ribbon-item-text">{{$item->name}}</span>
                                            </a>
                                        </li>
                                    @endif
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
            @empty
            @endforelse
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
                            <img class="ribbon-item-icon" src="{{asset("/images/static_menu_icons/verification.png")}}" alt="menu">
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
    </header>
    <div class="main rtl container-fluid pr-3 pl-3 position-relative" v-on:click="hide_ribbon">
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
            <div class="position-absolute information-box mt-2">
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
            {{ $auth_user->role->name }}
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
    @if(session("import_errors"))
        <div class="modal fade" id="import_errors" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title iransans" id="exampleModalLongTitle">مشکلات بارگذاری فایل اکسل</h6>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered text-center w-100 iransans">
                            <thead class="thead-dark">
                            <tr>
                                <th>ردیف فایل</th>
                                <th>مقدار</th>
                                <th>پیام خطا</th>
                            </tr>
                            @forelse(session("import_errors") as $import_error)
                                <tr>
                                    <td>{{ $import_error["row"] }}</td>
                                    <td>{{ $import_error["value"] }}</td>
                                    <td>{{ $import_error["message"] }}</td>
                                </tr>
                            @empty
                            @endforelse
                            </thead>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @yield('modals')
</div>
</body>
</html>
