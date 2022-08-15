<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ورود به سیستم</title>
    <script src="{{ asset('js/app.js?v='.time()) }}" defer></script>
    <link href="{{ asset('css/app.css?v='.time()) }}" rel="stylesheet">
</head>
<body style="background: #0d1117">
<div id="app">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                <img class="pt-5 pb-5" src="{{asset("/images/bw-logo.png?v=jj")}}" alt="logo"/>
            </div>
            <div class="col-12 text-center">
                <h5 class="mid-white-color pb-2 iranyekan">ورود به سیستم</h5>
            </div>
            <div class="login-box pt-3 pb-4 pr-3 pl-3 rtl">
                <form id="login_form" method="POST" action="{{ route('login') }}" v-on:submit="login">
                    @csrf
                    <label class="mid-white-color iranyekan form-lbl">
                        <i class="fa fa-user"></i>
                        نام کاربری
                    </label>
                    <input type="text" class="form-control login-input-text @error('username') is-invalid @enderror" name="username">
                    @error('username')
                    <span class="invalid-feedback iranyekan" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    <label class="mid-white-color iranyekan form-lbl mt-3">
                        <i class="fa fa-key"></i>
                        <span>کلمه عبور</span>
                        <a href="#">(فراموشی کلمه عبور)</a>
                    </label>
                    <input type="password" class="form-control login-input-text @error('password') is-invalid @enderror" name="password">
                    @error('password')
                    <span class="invalid-feedback iranyekan" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    <label class="mid-white-color iranyekan form-lbl mt-3">
                        <i class="fa fa-shield-blank"></i>
                        <span>کد امنیتی</span>
                        <a href="#" v-on:click="recaptcha"><i class="fa fa-refresh"></i></a>
                    </label>
                    <span class="captcha-image d-block mb-2 text-center">{!! Captcha::img() !!}</span>
                    <input type="text" class="form-control login-input-text iranyekan font-size-sm @error('captcha') is-invalid @enderror" placeholder="کد امنیتی" name="captcha">
                    @error('captcha')
                    <span class="invalid-feedback iranyekan" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    <input class="@error('login_failed') is-invalid @enderror" type="hidden">
                    @error('login_failed')
                    <span class="invalid-feedback iranyekan text-center" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    <button type="submit" class="btn btn-outline-success form-control mt-4 iranyekan login-button">
                        <i id="login-button-icon" class="fa fa-sign-in"></i>
                        <span id="login-button-text">ورود به سیستم</span>
                    </button>
                </form>
            </div>
        </div>
        <div class="row justify-content-center mt-3 iranyekan">
            <div class="login-box no-background pt-3 pb-3 pr-3 pl-3 rtl text-center d-flex justify-content-center align-items-center">
                <span class="mid-white-color mr-2">ثبت نام نکرده اید؟ </span>
                <a href="{{ route("step_one") }}">ثبت نام در سامانه</a>
            </div>
        </div>
        <div class="row justify-content-center mt-5 pt-3 iranyekan text-muted font-size-sm d-flex flex-column justify-content-center align-items-center">
        <span>
            <i class="fa fa-copyright"></i>
            کلیه حقوق متعلق به شرکت همیاران شمال شرق می باشد.
        </span>
            <span>نسخه 3.0.0</span>
        </div>
    </div>
</div>
</body>
</html>
