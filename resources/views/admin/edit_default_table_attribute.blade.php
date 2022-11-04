@extends('layouts.admin_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-table-list fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">عناوین پیش فرض</h5>
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
            <form id="main_submit_form" class="p-3" action="{{ route("DefaultTableAttributes.update",$default_table_attribute->id) }}" method="POST" enctype="multipart/form-data" v-on:submit="submit_form">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            نام
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control text-center iranyekan @error('name') is-invalid @enderror" type="text" name="name" value="{{ $default_table_attribute->name }}">
                        @error('name')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            نوع
                            <strong class="red-color">*</strong>
                        </label>
                        <select class="form-control iranyekan @error('type') is-invalid @enderror" name="type">
                            <option @if($default_table_attribute->type == "performance") selected @endif value="performance">کارکرد</option>
                            <option @if($default_table_attribute->type == "invoice") selected @endif value="invoice">وضعیت</option>
                            <option @if($default_table_attribute->type == "invoice_cover") selected @endif value="invoice_cover">روکش وضعیت</option>
                        </select>
                        @error('type')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            دسته بندی
                            <strong class="red-color">*</strong>
                        </label>
                        <select class="form-control iranyekan @error('category') is-invalid @enderror" name="category">
                            <option @if($default_table_attribute->category == "ندارد") selected @endif value="ندارد">ندارد</option>
                            <option @if($default_table_attribute->category == "function") selected @endif value="function">کارکرد</option>
                            <option @if($default_table_attribute->category == "advantage") selected @endif value="advantage">مزایا</option>
                            <option @if($default_table_attribute->category == "deduction") selected @endif value="deduction">کسورات</option>
                            <option value="advantage">مزایا</option>
                            <option value="deduction">کسورات</option>
                        </select>
                        @error('category')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            نوع مقدار
                            <strong class="red-color">*</strong>
                        </label>
                        <select class="form-control iranyekan @error('kind') is-invalid @enderror" name="kind">
                            <option @if($default_table_attribute->kind == "number") selected @endif value="number">عددی</option>
                            <option @if($default_table_attribute->kind == "text") selected @endif value="text">متنی</option>
                        </select>
                        @error('kind')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 form-button-row text-center pt-4 pb-2">
                        <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                            <i class="submit_button_icon fa fa-database fa-1-2x mr-1"></i>
                            <span class="iranyekan">ارسال و ویرایش</span>
                        </button>
                        <a role="button" href="{{ route("DefaultTableAttributes.index") }}" class="btn btn-outline-secondary iranyekan" data-dismiss="modal">
                            <i class="fa fa-arrow-turn-right fa-1-2x mr-1"></i>
                            <span class="iranyekan">بازگشت به لیست</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
