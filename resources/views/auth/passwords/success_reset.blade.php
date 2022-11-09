<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="{{ asset("/images/logo.ico?v=2.01") }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>بازنشانی گذرواژه</title>
    <script src="{{ asset('js/app.js?v='.time()) }}" defer></script>
    <link href="{{ asset('css/app.css?v='.time()) }}" rel="stylesheet">
</head>
<body style="background: #0d1117">
<div id="app">
    <div class="container">
        <loading v-show="show_loading" v-cloak></loading>
        <div class="row justify-content-center">
            <div class="col-12 text-center pt-5 pb-5">
                <img src="{{asset("/images/bw-logo.png?v=jj")}}" alt="logo"/>
                <h5 class="mid-white-color iransans text-center mt-4">مدیریت کارکرد ماهانه</h5>
            </div>
            <div class="login-box pt-3 pb-4 pr-3 pl-3 rtl d-flex align-items-center justify-content-center flex-column">
                <i class="fa fa-check-circle green-color fa-5x mb-4"></i>
                <h4 class="iransans green-color mb-5">گذرواژه با موفقیت تغییر یافت</h4>
                <a tabindex="1" role="button" class="btn btn-outline-secondary form-control iranyekan login-button" href="{{ route("login") }}">
                    <i id="login-button-icon" class="fa fa-redo fa-1-2x mr-2"></i>
                    <span id="login-button-text" class="font-size-lg">صفحه ورود</span>
                </a>
            </div>
        </div>
        <div class="row justify-content-center mt-2 iranyekan text-muted font-size-sm d-flex flex-column justify-content-center align-items-center">
        <span>
            <i class="fa fa-copyright"></i>
            کلیه حقوق متعلق به شرکت همیاران شمال شرق می باشد.
        </span>
            <span>نسخه 2.0.1</span>
        </div>
    </div>
</div>
</body>
</html>
