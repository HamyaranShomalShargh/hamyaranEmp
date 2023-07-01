@extends('layouts.admin_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-list fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">ایجاد کارکرد جدید</h5>
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
            <form action="{{ route("MakePerformance.get_information") }}" method="post" class="p-3" v-on:submit="submit_form">
                @csrf
                <div class="form-row">
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            انتخاب قرارداد
                        </label>
                        <select class="form-control text-center iranyekan @error('contract_id') is-invalid @enderror selectpicker rtl" name="id" data-container="body" size="5" data-live-search="true">
                            @forelse($contracts as $contract)
                                <option value="{{ $contract->id }}">{{ $contract->name }}</option>
                            @empty
                            @endforelse
                        </select>
                        @error('contract_id')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            انتخاب سال
                        </label>
                        <select class="form-control text-center iranyekan @error('contract_id') is-invalid @enderror selectpicker rtl" name="year" data-container="body" size="5" data-live-search="true" name="contract_id">
                            <option value="{{ verta()->subYear()->format("Y") }}">{{ verta()->subYear()->format("Y") }}</option>
                            <option selected value="{{ verta()->format("Y") }}">{{ verta()->format("Y") }}</option>
                        </select>
                        @error('contract_id')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            انتخاب ماه
                        </label>
                        <select class="form-control text-center iranyekan @error('contract_id') is-invalid @enderror selectpicker rtl" name="month" data-container="body" size="5" data-live-search="true" name="contract_id">
                            @forelse($month_names as $key => $name)
                                <option value="{{ $key + 1}}">{{ $name }}</option>
                            @empty
                            @endforelse
                        </select>
                        @error('contract_id')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 form-button-row text-center pt-4 pb-2">
                        <button type="submit" class="btn btn-success submit_button">
                            <i class="submit_button_icon fa fa-search fa-1-2x mr-1"></i>
                            <span class="iranyekan">بررسی و ادامه</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
