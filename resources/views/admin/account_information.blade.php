@extends('layouts.admin_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-user-edit fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">حساب کاربری</h5>
                    <span>(ویرایش)</span>
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
            <form id="update_form" class="p-3" action="{{ route("account.information.update") }}" method="POST" v-on:submit="submit_form">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            نام
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control iranyekan text-center @error('name') is-invalid @enderror" type="text" name="name" value="{{ $user->name }}">
                        @error('name')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            نام کاربری
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control iranyekan text-center @error('username') is-invalid @enderror" type="text" name="username" value="{{ $user->username }}">
                        @error('username')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            موبایل
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control iranyekan text-center @error('mobile') is-invalid @enderror number_masked" type="text" data-mask="00000000000" name="mobile" value="{{ $user->mobile ?: old("mobile")}}">
                        @error('mobile')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            پست الکترونیکی
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control iranyekan text-center @error('email') is-invalid @enderror" type="text" name="email" value="{{ $user->email ?: old("email") }}">
                        @error('email')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 form-button-row text-center pt-4 pb-2">
                        <button type="submit" form="update_form" class="btn btn-success submit_button">
                            <i class="submit_button_icon fa fa-database fa-1-2x mr-1"></i>
                            <span class="iranyekan">ارسال و ویرایش</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
