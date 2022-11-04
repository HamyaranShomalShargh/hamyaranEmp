@extends('layouts.admin_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-list fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">سرفصل منو</h5>
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
            <form id="main_submit_form" class="p-3" action="{{ route("AdminContractHeader.update",$contract_header->id) }}" method="POST" enctype="multipart/form-data" v-on:submit="submit_form">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            نام
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control text-center iranyekan @error('name') is-invalid @enderror" type="text" name="name" value="{{ $contract_header->name }}">
                        @error('name')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            شماره قراداد
                        </label>
                        <input class="form-control text-center iranyekan @error('number') is-invalid @enderror" type="text" name="number" value="{{ $contract_header->number }}">
                        @error('number')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            شروع قرارداد
                            <strong class="red-color">*</strong>
                            <small class="text-muted">(فرمت صحیح : 1401/01/01)</small>
                        </label>
                        <input class="form-control text-center iranyekan @error('start_date') is-invalid @enderror persian_datepicker_range_from date_masked" data-mask="0000/00/00" type="text" name="start_date" value="{{ str_replace("-","/",$contract_header->start_date) }}">
                        @error('start_date')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            پایان قرارداد
                            <strong class="red-color">*</strong>
                            <small class="text-muted">(فرمت صحیح : 1401/01/01)</small>
                        </label>
                        <input class="form-control text-center iranyekan @error('end_date') is-invalid @enderror persian_datepicker_range_to date_masked" data-mask="0000/00/00" type="text" name="end_date" value="{{ str_replace("-","/",$contract_header->end_date) }}">
                        @error('end_date')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">مستندات قرارداد</label>
                        <m-file-browser @if($contract_header->files) :already="true" @endif :accept="['png','jpg','bmp','tiff','pdf','xlsx','txt']" :size="325000"></m-file-browser>
                    </div>
                    <div class="form-group col-12 form-button-row text-center pt-4 pb-2">
                        <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                            <i class="submit_button_icon fa fa-database fa-1-2x mr-1"></i>
                            <span class="iranyekan">ارسال و ویرایش</span>
                        </button>
                        <a role="button" href="{{ route("AdminContractHeader.index") }}" class="btn btn-outline-secondary iranyekan" data-dismiss="modal">
                            <i class="fa fa-arrow-turn-right fa-1-2x mr-1"></i>
                            <span class="iranyekan">بازگشت به لیست</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
