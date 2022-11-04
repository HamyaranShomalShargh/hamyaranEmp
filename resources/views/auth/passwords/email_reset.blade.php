<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>بازنشانی گذرواژه</title>
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
                <form id="login_form" autocomplete="off" method="POST" action="{{ route('password.update.email') }}" v-on:submit="show_loading = true">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="token" value="{{ $token }}">
                    <label class="mid-white-color iranyekan form-lbl">
                        <i class="fa fa-envelope fa-1-2x mr-1"></i>
                        آدرس ایمیل
                    </label>
                    <span class="form-control login-input-text iranyekan">{{ $email }}</span>
                    <label class="mid-white-color iranyekan form-lbl mt-3">
                        <i class="fa fa-key fa-1-2x mr-1"></i>
                        گذرواژه جدید
                    </label>
                    <input type="password" tabindex="1" class="form-control login-input-text iranyekan @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
                    <label class="mid-white-color iranyekan form-lbl mt-3">
                        <i class="fa fa-repeat fa-1-2x mr-1"></i>
                        تکرار گذرواژه جدید
                    </label>
                    <input type="password" tabindex="2" class="form-control login-input-text iranyekan @error('password_confirmation') is-invalid @enderror" name="password_confirmation" autocomplete="new-password">
                    <div class="login-errors">
                        @error('general_error')
                        <span class="invalid-feedback d-block iranyekan font-size-lg text-center p-0 m-0" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        @error('mobile')
                        <span class="invalid-feedback iranyekan d-block font-size-lg text-center p-0 m-0" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        @error('token')
                        <span class="invalid-feedback iranyekan d-block font-size-lg text-center p-0 m-0" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        @error('password')
                        <span class="invalid-feedback iranyekan d-block font-size-lg text-center p-0 m-0" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        @error('password_confirmation')
                        <span class="invalid-feedback iranyekan d-block font-size-lg text-center p-0 m-0" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <button tabindex="4" type="submit" class="btn btn-outline-primary form-control iranyekan login-button">
                        <i id="login-button-icon" class="fa fa-key-skeleton fa-1-2x mr-2"></i>
                        <span id="login-button-text" class="font-size-lg">تغییر گذرواژه</span>
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
