<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>فراموشی گذرواژه</title>
    <script src="{{ asset('js/app.js?v='.time()) }}" defer></script>
    <link href="{{ asset('css/app.css?v='.time()) }}" rel="stylesheet">
</head>
<body style="background: #0d1117">
<div id="app">
    <loading v-show="show_loading" v-cloak></loading>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center pt-5 pb-5">
                <img src="{{asset("/images/bw-logo.png?v=jj")}}" alt="logo"/>
                <h5 class="mid-white-color iransans text-center mt-4">مدیریت کارکرد ماهانه</h5>
            </div>
            <div class="col-12 text-center">
                <h5 class="text-muted pb-2 iranyekan">بازنشانی گذرواژه</h5>
            </div>
            <div class="login-box pt-3 pb-4 pr-3 pl-3 rtl">
                <form id="login_form" autocomplete="off" method="POST" action="{{ route('password.reset.send.link') }}" v-on:submit="show_loading = true">
                    @csrf
                    <label class="mid-white-color iranyekan form-lbl" for="mobile_check">
                        <input @if(!old("email")) checked @endif type="radio" id="mobile_check" name="type" value="mobile" v-on:change="reset_password_platform">
                        پیامک
                    </label>
                    <input type="text" @if(old("email")) disabled @endif autofocus id="mobile" tabindex="1" autocomplete="off" class="form-control login-input-text iranyekan @error('mobile') is-invalid @enderror" name="mobile" value="{{ old("mobile") }}" placeholder="شماره موبایل">
                    @error('mobile')
                    <span class="invalid-feedback iranyekan text-center" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    <label class="mid-white-color iranyekan form-lbl mt-3" for="email_check">
                        <input @if(old("email")) checked @endif type="radio" name="type" id="email_check" value="email" v-on:change="reset_password_platform">
                        ایمیل
                    </label>
                    <input type="text" @if(!old("email")) disabled @endif id="email" tabindex="2" autocomplete="off" class="form-control login-input-text iranyekan @error('email') is-invalid @enderror" name="email" value="{{ old("email") }}" placeholder="آدرس ایمیل">
                    @error('email')
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
                        @if(session()->has("success_m"))
                            <span class="m-auto w-100 green-color d-block iranyekan font-size-lg text-center p-0 m-0" role="alert">
                                <strong>{{ "آدرس بازنشانی گذرواژه به موبایل شما ارسال شد" }}</strong>
                            </span>
                        @endif
                        @if(session()->has("success_e"))
                            <span class="m-auto w-100 green-color d-block iranyekan font-size-lg text-center p-0 m-0" role="alert">
                                <strong>{{ "آدرس بازنشانی گذرواژه به ایمیل شما ارسال شد" }}</strong>
                            </span>
                        @endif
                        @error('captcha')
                        <span class="m-auto w-100 invalid-feedback d-block iranyekan font-size-lg text-center p-0 m-0" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        @error('type')
                        <span class="m-auto w-100 invalid-feedback iranyekan d-block font-size-lg text-center p-0 m-0" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        @error('general_error')
                        <span class="m-auto w-100 invalid-feedback iranyekan d-block font-size-lg text-center p-0 m-0" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <button tabindex="4" type="submit" class="btn btn-outline-primary form-control iranyekan login-button">
                        <i id="login-button-icon" class="fa fa-link fa-1-2x mr-2"></i>
                        <span id="login-button-text" class="font-size-lg">دریافت لینک بازنشانی</span>
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
