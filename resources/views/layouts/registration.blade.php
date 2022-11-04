<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ثبت نام</title>
    <script src="{{ asset('js/app.js?v='.time()) }}" defer></script>
    <link href="{{ asset('css/app.css?v='.time()) }}" rel="stylesheet">
</head>
<body style="background: #0d1117">
<div id="app">
    <loading v-show="show_loading" v-cloak></loading>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                <img class="pt-5 pb-5" src="{{asset("/images/bw-logo.png?v=jj")}}" alt="logo"/>
            </div>
            <div class="col-12 text-center">
                @yield('title')
            </div>
            <div class="login-box pt-4 pb-4 pr-4 pl-4 rtl">
                @yield('content')
            </div>
        </div>
    </div>
</div>
</body>
</html>
