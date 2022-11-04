@extends('layouts.staff_dashboard')
@push('scripts')
    <script>
        let new_performance_data = @json($new_performance_notifications);
        let new_invoice_data = @json($new_invoice_notifications);
        let new_advantage_data = @json($new_advantage_notifications);
    </script>
@endpush
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
        <div class="mt-2" v-cloak>
            <div class="alert alert-info iransans mb-0 h-100 border border-info pt-3 pb-3 mb-3" role="alert" v-for="notification in new_performance_notifications">
                <h6 class="pt-2 font-weight-bold">
                    <i class="fa fa-mailbox mr-2 fa-1-4x black-color" style="color: #005cbf"></i>
                    <a :href="notification.action" role="button">
                        @{{ notification.message }}
                    </a>
                </h6>
            </div>
        </div>
        <div class="mt-2" v-cloak>
            <div class="alert alert-success iransans mb-0 h-100 border border-success pt-3 pb-3 mb-3" role="alert" v-for="notification in new_invoice_notifications">
                <h6 class="pt-2 font-weight-bold">
                    <i class="fa fa-mailbox mr-2 fa-1-4x" style="color: #1a5d2b"></i>
                    <a :href="notification.action" role="button" style="color: #1a5d2b">
                        @{{ notification.message }}
                    </a>
                </h6>
            </div>
        </div>
        <div class="mt-2" v-cloak>
            <div class="alert alert-dark iransans mb-0 h-100 border border-dark pt-3 pb-3 mb-3" role="alert" v-for="notification in new_advantage_notifications">
                <h6 class="pt-2 font-weight-bold">
                    <i class="fa fa-mailbox mr-2 fa-1-4x" style="color: #000000"></i>
                    <a :href="notification.action" role="button" style="color: #000000">
                        @{{ notification.message }}
                    </a>
                </h6>
            </div>
        </div>
    </div>
@endsection
