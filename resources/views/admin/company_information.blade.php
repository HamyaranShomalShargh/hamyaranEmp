@extends('layouts.admin_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-building fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">اطلاعات شرکت</h5>
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
            <form id="main_submit_form" class="p-3" action="{{ route("CompanyInformation.update",$company_information->id) }}" method="POST" v-on:submit="submit_form">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            نام
                        </label>
                        <input class="form-control text-center iranyekan @error('name') is-invalid @enderror" type="text" name="name" value="{{ $company_information->name }}">
                        @error('name')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            نام مختصر
                        </label>
                        <input class="form-control text-center iranyekan @error('short_name') is-invalid @enderror" type="text" name="short_name" value="{{ $company_information->short_name }}">
                        @error('short_name')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            توضیحات
                        </label>
                        <input class="form-control text-center iranyekan @error('description') is-invalid @enderror" type="text" name="description" value="{{ $company_information->description }}">
                        @error('description')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            شماره ثبت
                        </label>
                        <input class="form-control text-center iranyekan @error('registration_number') is-invalid @enderror" type="text" name="registration_number" value="{{ $company_information->registration_number }}">
                        @error('registration_number')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            شناسه ملی
                        </label>
                        <input class="form-control text-center iranyekan @error('national_id') is-invalid @enderror" type="text" name="national_id" value="{{ $company_information->national_id }}">
                        @error('national_id')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            آدرس وبسایت
                        </label>
                        <input class="form-control text-center iranyekan @error('website') is-invalid @enderror" type="text" name="website" value="{{ $company_information->website }}">
                        @error('website')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            مدیرعامل
                        </label>
                        <select class="form-control text-center selectpicker iranyekan @error('ceo_user_id') is-invalid @enderror" data-live-search="true" name="ceo_user_id" title="انتخاب کنید" data-size="20">
                            @forelse($users as $user)
                                <option @if($user->id == $company_information->ceo_user_id) selected @endif value="{{ $user->id }}">{{ $user->name }}</option>
                            @empty
                            @endforelse
                        </select>
                        @error('ceo_user_id')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            عنوان مدیرعامل
                        </label>
                        <input class="form-control text-center iranyekan @error('ceo_title') is-invalid @enderror" type="text" name="ceo_title" value="{{ $company_information->ceo_title }}">
                        @error('ceo_title')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            معاونت
                        </label>
                        <select class="form-control text-center selectpicker iranyekan @error('substitute_user_id') is-invalid @enderror" data-live-search="true" name="substitute_user_id" title="انتخاب کنید" data-size="20">
                            @forelse($users as $user)
                                <option @if($user->id == $company_information->substitute_user_id) selected @endif value="{{ $user->id }}">{{ $user->name }}</option>
                            @empty
                            @endforelse
                        </select>
                        @error('substitute_user_id')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            عنوان معاونت
                        </label>
                        <input class="form-control text-center iranyekan @error('substitute_title') is-invalid @enderror" type="text" name="substitute_title" value="{{ $company_information->substitute_title }}">
                        @error('substitute_title')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            آدرس
                        </label>
                        <input class="form-control text-center iranyekan @error('address') is-invalid @enderror" type="text" name="address" value="{{ $company_information->address }}">
                        @error('address')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            تلفن
                        </label>
                        <input class="form-control text-center iranyekan @error('phone') is-invalid @enderror" type="text" name="phone" value="{{ $company_information->phone }}">
                        @error('phone')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            فکس
                        </label>
                        <input class="form-control text-center iranyekan @error('fax') is-invalid @enderror" type="text" name="fax" value="{{ $company_information->fax }}">
                        @error('fax')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            نسخه نرم افزار
                        </label>
                        <input class="form-control text-center iranyekan @error('app_version') is-invalid @enderror" type="text" name="app_version" value="{{ $company_information->app_version }}">
                        @error('app_version')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 form-button-row text-center pt-4 pb-2">
                        <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                            <i class="submit_button_icon fa fa-database fa-1-2x mr-1"></i>
                            <span class="iranyekan">ارسال و ویرایش</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
