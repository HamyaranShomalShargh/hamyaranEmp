@extends('layouts.staff_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-user-edit fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">پرسنل</h5>
                    <span>(ویرایش)</span>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-light">
                        <i class="fa fa-circle-question fa-1-4x green-color"></i>
                    </button>
                    <a role="button" href="{{ route("staff_idle") }}" class="btn btn-sm btn-outline-light">
                        <i class="fa fa-times fa-1-4x gray-color"></i>
                    </a>
                </div>
            </div>
            <form id="update_form" class="p-3" action="{{ route("Employees.update",$employee->id) }}" method="POST" v-on:submit="submit_form">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            قرارداد
                            <strong class="red-color">*</strong>
                        </label>
                        <select class="form-control text-center selectpicker iranyekan mb-4 @error('contract_subset_id') is-invalid @enderror" data-size="10" data-live-search="true" name="contract_subset_id">
                            @forelse($contracts as $contract)
                                <optgroup label="{{ $contract["name"] }}">
                                    @forelse($contract["data"] as $subset)
                                        <option @if($employee->contract_subset_id == $subset["id"]) selected @endif value="{{ $subset["id"] }}">
                                            @if($subset["child_name"])
                                                {{ $subset["contract_subset"]." - ".$subset["child_name"]."({$subset['workplace']})" }}
                                            @else
                                                {{ $subset["contract_subset"]."({$subset['workplace']})" }}
                                            @endif
                                        </option>
                                    @empty
                                    @endforelse
                                </optgroup>
                            @empty
                            @endforelse
                        </select>
                        @error('contract_subset_id')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">
                            نام
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control iranyekan text-center @error('first_name') is-invalid @enderror" type="text" name="first_name" value="{{ $employee->first_name }}">
                        @error('first_name')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">
                            نام خانوادگی
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control iranyekan text-center @error('last_name') is-invalid @enderror" type="text" name="last_name" value="{{ $employee->last_name }}">
                        @error('last_name')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">جنسیت</label>
                        <select class="form-control iranyekan" name="gender">
                            <option @if($employee->gender == 'm') selected @endif value="m">مرد</option>
                            <option @if($employee->gender == 'f') selected @endif  value="f">زن</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">
                            کد ملی
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control iranyekan text-center @error('national_code') is-invalid @enderror number_masked" data-mask="0000000000" type="text" name="national_code" value="{{ $employee->national_code }}">
                        @error('national_code')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">شماره شناسنامه</label>
                        <input class="form-control iranyekan text-center @error('id_number') is-invalid @enderror number_masked" data-mask="0000000000" type="text" name="id_number" value="{{ $employee->id_number }}">
                        @error('id_number')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">تاریخ تولد</label>
                        <input class="form-control iranyekan text-center @error('birth_date') is-invalid @enderror date_masked" data-mask="0000/00/00" type="text" name="birth_date" value="{{ $employee->birth_date }}">
                        @error('birth_date')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">محل تولد</label>
                        <input class="form-control iranyekan text-center @error('birth_city') is-invalid @enderror" type="text" name="birth_city" value="{{ $employee->birth_city }}">
                        @error('birth_city')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">تحصیلات</label>
                        <select class="form-control iranyekan" name="education">
                            <option @if($employee->education == "دیپلم و زیردیپلم") selected @endif value="دیپلم و زیردیپلم">دیپلم و زیردیپلم</option>
                            <option @if($employee->education == "فوق دیپلم") selected @endif value="فوق دیپلم">فوق دیپلم</option>
                            <option @if($employee->education == "لیسانس") selected @endif value="لیسانس">لیسانس</option>
                            <option @if($employee->education == "فوق لیسانس") selected @endif value="فوق لیسانس">فوق لیسانس</option>
                            <option @if($employee->education == "دکتری") selected @endif value="دکتری">دکتری</option>
                            <option @if($employee->education == "فوق دکتری") selected @endif value="فوق دکتری">فوق دکتری</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">وضعیت خدمت سربازی</label>
                        <select class="form-control iranyekan" name="military_status">
                            <option value="h">کارت پایان خدمت</option>
                            <option value="e">معاف</option>
                            <option value="n">انجام نشده</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">وضعیت تاهل</label>
                        <select class="form-control iranyekan" name="marital_status">
                            <option @if($employee->marital_status == "m") selected @endif value="m">متاهل</option>
                            <option @if($employee->marital_status == "s") selected @endif value="s">مجرد</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">تعداد فرزند مشمول</label>
                        <input class="form-control iranyekan text-center @error('children_number') is-invalid @enderror" type="number" min="0" name="children_number" value="{{ $employee->children_number }}">
                        @error('children_number')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">شماره بیمه</label>
                        <input class="form-control iranyekan text-center @error('insurance_number') is-invalid @enderror number_masked" data-mask="000000000000000000000" type="text" name="insurance_number" value="{{ $employee->insurance_number }}">
                        @error('insurance_number')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">سابقه بیمه(روز)</label>
                        <input class="form-control iranyekan text-center @error('insurance_days') is-invalid @enderror" type="number" min="0" name="insurance_days" value="{{ $employee->insurance_days }}">
                        @error('insurance_days')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">حقوق پایه</label>
                        <input class="form-control iranyekan text-center @error('basic_salary') is-invalid @enderror thousand_separator" type="text" name="basic_salary" value="{{ $employee->basic_salary }}">
                        @error('basic_salary')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">دستمزد روزانه</label>
                        <input class="form-control iranyekan text-center @error('daily_wage') is-invalid @enderror thousand_separator" type="text" name="daily_wage" value="{{ $employee->daily_wage }}">
                        @error('daily_wage')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">بن ماهیانه</label>
                        <input class="form-control iranyekan text-center @error('worker_credit') is-invalid @enderror thousand_separator" type="text" name="worker_credit" value="{{ $employee->worker_credit }}">
                        @error('worker_credit')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">کمک هزینه مسکن</label>
                        <input class="form-control iranyekan text-center @error('housing_credit') is-invalid @enderror thousand_separator" type="text" name="housing_credit" value="{{ $employee->housing_credit }}">
                        @error('housing_credit')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">حق اولاد</label>
                        <input class="form-control iranyekan text-center @error('child_credit') is-invalid @enderror thousand_separator" type="text" name="child_credit" value="{{ $employee->child_credit }}">
                        @error('child_credit')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">گروه شغلی</label>
                        <input class="form-control iranyekan text-center @error('job_group') is-invalid @enderror" type="number" min="1" name="job_group" value="{{ $employee->job_group }}">
                        @error('job_group')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">نام بانک</label>
                        <input class="form-control iranyekan text-center @error('bank_name') is-invalid @enderror" type="text" name="bank_name" value="{{ $employee->bank_name }}">
                        @error('bank_name')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">شماره حساب بانکی</label>
                        <input class="form-control iranyekan text-center @error('bank_account') is-invalid @enderror number_masked" type="text" data-mask="0000000000000000000000000" name="bank_account" value="{{ $employee->bank_account }}">
                        @error('bank_account')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">شماره کارت بانکی</label>
                        <input class="form-control iranyekan text-center @error('credit_card') is-invalid @enderror number_masked" type="text" data-mask="0000-0000-0000-0000" name="credit_card" value="{{ $employee->credit_card }}">
                        @error('credit_card')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">شماره شبا(بدون IR)</label>
                        <input class="form-control iranyekan text-center @error('sheba_number') is-invalid @enderror number_masked" type="text" data-mask="IR00-0000-0000-0000-0000-0000-00" name="sheba_number" value="{{ $employee->sheba_number }}">
                        @error('sheba_number')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">تلفن</label>
                        <input class="form-control iranyekan text-center @error('phone') is-invalid @enderror number_masked" type="text" data-mask="000-00000000" name="phone" value="{{ $employee->phone }}">
                        @error('phone')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">سقف اضافه کار مجاز</label>
                        <input class="form-control iranyekan text-center @error('extra_work_limit') is-invalid @enderror number_masked" type="text" data-mask="000" name="extra_work_limit" value="{{ $employee->extra_work_limit }}">
                        @error('extra_work_limit')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">موبایل</label>
                        <input class="form-control iranyekan text-center @error('mobile') is-invalid @enderror number_masked" type="text" data-mask="00000000000" name="mobile" value="{{ $employee->mobile }}">
                        @error('mobile')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4 col-xl-3">
                        <label class="form-lbl iranyekan">آدرس</label>
                        <input class="form-control iranyekan text-center @error('address') is-invalid @enderror" type="text" name="address" value="{{ $employee->address }}">
                        @error('address')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 form-button-row text-center pt-4 pb-2">
                        <button type="submit" form="update_form" class="btn btn-success submit_button">
                            <i class="submit_button_icon fa fa-database fa-1-2x mr-1"></i>
                            <span class="iranyekan">ارسال و ویرایش</span>
                        </button>
                        <a role="button" href="{{ route("Employees.index") }}" class="btn btn-outline-secondary iranyekan" data-dismiss="modal">
                            <i class="fa fa-arrow-turn-right fa-1-2x mr-1"></i>
                            <span class="iranyekan">بازگشت به لیست</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
