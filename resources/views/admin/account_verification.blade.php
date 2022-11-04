@extends('layouts.admin_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-user-check fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">تایید اطلاعات حساب کاربری</h5>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-light">
                        <i class="fa fa-circle-question fa-1-4x green-color"></i>
                    </button>
                    <a role="button" href="{{ route("admin_idle") }}" class="btn btn-sm btn-outline-light">
                        <i class="fa fa-times fa-1-4x gray-color"></i>
                    </a>
                </div>
            </div>
            <form id="update_form" class="p-3" action="{{ route("account.verification.send") }}" method="POST" v-on:submit="show_loading = true">
                @csrf
                <div class="form-row">
                    @if($user->mobile_verified_at == null && $user->mobile != null)
                        <div class="form-group col-12">
                            <label class="form-lbl iranyekan" for="mobile">
                                <input checked type="checkbox" id="mobile" name="mobile" value="mobile">
                                ارسال لینک تاییدیه به تلفن همراه
                            </label>
                        </div>
                    @endif
                    @if($user->email_verified_at == null && $user->email != null)
                        <div class="form-group col-12">
                            <label class="form-lbl iranyekan" for="email">
                                <input checked type="checkbox" id="email" name="email" value="email">
                                ارسال لینک تاییدیه به پست الکترونیکی
                            </label>
                        </div>
                    @endif
                        @if($user->email_verified_at != null && $user->mobile_verified_at != null)
                            <div class="form-group col-12 text-center">
                                <i class="fa fa-check-circle fa-4x green-color mb-3"></i>
                                <h5 class="iranyekan text-center">اطلاعات حساب شما تایید شده است</h5>
                            </div>
                        @else
                            <div class="form-group col-12 form-button-row text-center pt-4 pb-2">
                                <button type="submit" form="update_form" class="btn btn-success submit_button">
                                    <i class="submit_button_icon fa fa-link fa-1-2x mr-1"></i>
                                    <span class="iranyekan">ارسال لینک تایید اطلاعات</span>
                                </button>
                            </div>
                        @endif
                </div>
            </form>
        </div>
    </div>
@endsection
