@extends('layouts.admin_dashboard')
@section('content')
    <div class="page w-100 pt-3">
        @if(auth()->user()->email == null)
            <div class="alert alert-danger iransans mb-0 h-100 border border-danger pt-3 pb-3 mb-3" role="alert">
                <h6 class="pt-2 font-weight-bold">
                    <i class="fa fa-envelope mr-1 fa-1-4x black-color" style="color: #980000"></i>
                    <a href="{{ route("account.information") }}" role="button" style="color: #980000">
                        آدرس پست الکترونیکی شما در حساب کاربری ثبت نشده است. لطفا نسبت به ثبت و تایید آن اقدام فرمایید.
                    </a>
                </h6>
            </div>
        @endif
        @if(auth()->user()->mobile == null)
            <div class="alert alert-danger iransans mb-0 h-100 border border-danger pt-3 pb-3 mb-3" role="alert">
                <h6 class="pt-2 font-weight-bold">
                    <i class="fa fa-mobile mr-1 fa-1-4x black-color" style="color: #980000"></i>
                    <a href="{{ route("account.information") }}" role="button" style="color: #980000">
                        شماره تلفن همراه شما در حساب کاربری ثبت نشده است. لطفا نسبت به ثبت و تایید آن اقدام فرمایید.
                    </a>
                </h6>
            </div>
        @endif
        @if(auth()->user()->email != null && auth()->user()->email_verified_at == null)
            <div class="alert alert-danger iransans mb-0 h-100 border border-danger pt-3 pb-3 mb-3" role="alert">
                <h6 class="pt-2 font-weight-bold">
                    <i class="fa fa-envelope-open mr-1 fa-1-4x black-color" style="color: #980000"></i>
                    <a href="{{ route("account.verification") }}" role="button" style="color: #980000">
                        آدرس پست الکترونیکی شما در حساب کاربری تایید نشده است. لطفا نسبت به تایید آن اقدام فرمایید.
                    </a>
                </h6>
            </div>
        @endif
        @if(auth()->user()->mobile != null && auth()->user()->mobile_verified_at == null)
            <div class="alert alert-danger iransans mb-0 h-100 border border-danger pt-3 pb-3 mb-3" role="alert">
                <h6 class="pt-2 font-weight-bold">
                    <i class="fa fa-mobile-signal mr-1 fa-1-4x black-color" style="color: #980000"></i>
                    <a href="{{ route("account.verification") }}" role="button" style="color: #980000">
                        شماره تلفن همراه شما در حساب کاربری تایید نشده است. لطفا نسبت به تایید آن اقدام فرمایید.
                    </a>
                </h6>
            </div>
        @endif
    </div>
@endsection
