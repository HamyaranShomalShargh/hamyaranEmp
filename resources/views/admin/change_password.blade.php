@extends('layouts.admin_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-key fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">تغییر گذرواژه</h5>
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
            <form id="update_form" autocomplete="off" class="p-3" action="{{ route("user.password.reset.change") }}" method="POST" v-on:submit="submit_form">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            گذرواژه فعلی
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control iranyekan text-center @error('old_password') is-invalid @enderror" autocomplete="off" type="password" name="old_password">
                        @error('old_password')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            گذرواژه جدید
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control iranyekan text-center @error('password') is-invalid @enderror" autocomplete="off" type="password" name="password">
                        @error('password')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            تکرار گذرواژه جدید
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control iranyekan text-center @error('password_confirmation') is-invalid @enderror" type="password" autocomplete="off" name="password_confirmation">
                        @error('password_confirmation')
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
