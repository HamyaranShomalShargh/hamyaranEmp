<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>خطا</title>
    <script src="{{ asset('js/app.js?v='.time()) }}" defer></script>
    <link href="{{ asset('css/app.css?v='.time()) }}" rel="stylesheet">
    <style>
        body{
            margin-top: 150px;
            background-color: #C4CCD9;
        }
        .error-main{
            background-color: #fff;
            box-shadow: 0px 10px 10px -10px #5D6572;
        }
        .error-main h1{
            font-weight: bold;
            color: #444444;
            font-size: 150px;
            text-shadow: 2px 4px 5px #6E6E6E;
        }
        .error-main h6{
            color: #42494F;
            font-size: 20px;
        }
        .error-main p{
            color: #9897A0;
            font-size: 15px;
        }
    </style>
</head>
<body>

<div class="container rtl" id="app">
    <div class="row d-flex justify-content-center align-items-center">
        <div class="col-11 col-md-9 col-lg-6 col-xl-5 p-5 error-main">
            <div class="row">
                <div class="w-100 d-flex flex-column justify-content-center align-items-center">
                    <i class="fa fa-exclamation-triangle fa-4x yellow-color"></i>
                    <h6 class="iransans pt-4 text-center">دسترسی به این صفحه برای شما محدود شده است</h6>
                    <span class="iranyekan text-center">در صورت بروز هرگونه خطا و یا مشاهده مغایریت، با پشتیبانی تماس حاصل نمایید</span>
                    <a role="button" href="{{ route("idle") }}" class="btn btn-secondary iranyekan mt-3">بازگشت</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
