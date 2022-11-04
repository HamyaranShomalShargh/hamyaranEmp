<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ورود به سیستم</title>
    <script src="{{ asset('js/app.js?v='.time()) }}" defer></script>
    <link href="{{ asset('css/app.css?v='.time()) }}" rel="stylesheet">
    <script>
        window.Laravel = {!! json_encode([
        'user' => auth()->check() ? auth()->user()->id : null,
    ]) !!};
    </script>
</head>
<body style="background: #0d1117">
<div id="app">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center pt-5 pb-5">
                <img src="{{asset("/images/bw-logo.png?v=jj")}}" alt="logo"/>
                <h5 class="mid-white-color iransans text-center mt-4">مدیریت کارکرد ماهانه</h5>
            </div>
            <div class="col-12 text-center">
                <h5 class="text-muted pb-2 iranyekan">ورود به سیستم</h5>
            </div>
            <div class="login-box pt-3 pb-4 pr-3 pl-3 rtl">
                <form id="login_form" method="POST" action="{{ route('login') }}" v-on:submit="login">
                    @csrf
                    <label class="mid-white-color iranyekan form-lbl">
                        <i class="fa fa-user"></i>
                        نام کاربری
                    </label>
                    <input type="text" autofocus tabindex="1" class="form-control login-input-text @error('username') is-invalid @enderror" name="username">
                    @error('username')
                    <span class="invalid-feedback iranyekan text-center" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    <label class="mid-white-color iranyekan form-lbl mt-3">
                        <i class="fa fa-key"></i>
                        کلمه عبور
                        <a tabindex="-1" href="{{ route("password.reset") }}">(فراموشی کلمه عبور)</a>
                    </label>
                    <input type="password" tabindex="2" class="form-control login-input-text @error('password') is-invalid @enderror" name="password">
                    @error('password')
                    <span class="invalid-feedback iranyekan text-center" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    <label class="mid-white-color iranyekan form-lbl mt-3">
                        <i class="fa fa-shield-blank"></i>
                        کد امنیتی
                        <a tabindex="-1" href="#" v-on:click="recaptcha"><i class="fa fa-refresh fa-1-2x"></i></a>
                    </label>
                    <span class="captcha-image d-block mb-2 text-center">{!! Captcha::img() !!}</span>
                    <input type="text" tabindex="3" class="form-control login-input-text captcha-input iranyekan font-size-xl number_masked @error('captcha') is-invalid @enderror" data-mask="000000" name="captcha">
                    <div class="login-errors">
                        @error('captcha')
                        <span class="invalid-feedback d-block iranyekan font-size-lg text-center p-0 m-0" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        @error('login_failed')
                        <span class="invalid-feedback iranyekan d-block font-size-lg text-center p-0 m-0" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <button tabindex="4" type="submit" class="btn btn-outline-success form-control iranyekan login-button">
                        <i id="login-button-icon" class="fa fa-sign-in fa-1-2x mr-2"></i>
                        <span id="login-button-text" class="font-size-lg">ورود به سیستم</span>
                    </button>
                </form>
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
