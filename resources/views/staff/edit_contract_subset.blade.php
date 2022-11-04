@extends('layouts.staff_dashboard')
@push('scripts')
    <script>
        let table_data = @json($contract_subset->employees);
    </script>
@endpush
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-diagram-subtask fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">زیرمجموعه قرارداد</h5>
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
            <form id="main_submit_form" class="p-3" action="{{ route("ContractSubset.update",$contract_subset->id) }}" method="POST" enctype="multipart/form-data" v-on:submit="submit_form">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            نام
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control text-center iranyekan @error('name') is-invalid @enderror" type="text" name="name" value="{{ $contract_subset->name }}">
                        @error('name')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            قرارداد
                        </label>
                        <select class="form-control text-center iranyekan @error('contract_id') is-invalid @enderror selectpicker" size="20" data-live-search="true" name="contract_id">
                            @forelse($contract_headers as $header)
                                <option @if($header->id == $contract_subset->contract->id) selected @endif value="{{ $header->id }}">{{ $header->name }}</option>
                            @empty
                            @endforelse
                        </select>
                        @error('contract_id')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            وابستگی
                        </label>
                        <select class="form-control text-center iranyekan @error('parent_id') is-invalid @enderror selectpicker" size="20" data-live-search="true" name="parent_id">
                            <option selected value="">ندارد</option>
                            @forelse($contract_subsets as $subset)
                                <option @if($contract_subset->parent && $subset->id == $contract_subset->parent->id) selected @endif value="{{ $subset->id }}">{{ $subset->name }}</option>
                            @empty
                            @endforelse
                        </select>
                        @error('parent_id')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            عنوان محل خدمت
                        </label>
                        <input class="form-control text-center iranyekan @error('workplace') is-invalid @enderror" type="text" name="workplace" value="{{ $contract_subset->workplace }}">
                        @error('workplace')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">عناوین کارکرد ماهیانه</label>
                        <select class="form-control iranyekan text-center selectpicker" data-live-search="true" id="performance_attributes_id" name="performance_attributes_id" title="انتخاب کنید" data-size="20">
                            @forelse($table_attributes as $attribute)
                                <option @if($contract_subset->performance_attributes_id == $attribute->id) selected @endif value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">عناوین وضعیت ماهیانه</label>
                        <select class="form-control iranyekan text-center selectpicker" data-live-search="true" id="invoice_attributes_id" name="invoice_attributes_id" title="انتخاب کنید" data-size="20">
                            @forelse($table_attributes as $attribute)
                                <option @if($contract_subset->invoice_attributes_id == $attribute->id) selected @endif value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">عناوین روکش وضعیت</label>
                        <select class="form-control iranyekan text-center selectpicker" data-live-search="true" id="invoice_cover_id" name="invoice_cover_id" title="انتخاب کنید" data-size="20">
                            @forelse($invoice_cover_titles as $title)
                                <option @if($title->id == $contract_subset->invoice_cover_id) selected @endif value="{{ $title->id }}">{{ $title->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan black_color" for="short_name">گردش کارکرد</label>
                        <select class="form-control iranyekan text-center selectpicker" data-live-search="true" id="performance_flow_id" name="performance_flow_id" title="انتخاب کنید" data-size="20">
                            @forelse($automation_flows as $p_flow)
                                <option @if($contract_subset->performance_flow_id == $p_flow->id) selected @endif value="{{ $p_flow->id }}">{{ $p_flow->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan black_color" for="short_name">گردش وضعیت</label>
                        <select class="form-control iranyekan text-center selectpicker" data-live-search="true" id="invoice_flow_id" name="invoice_flow_id" title="انتخاب کنید" data-size="20">
                            @forelse($automation_flows as $i_flow)
                                <option @if($contract_subset->invoice_flow_id == $i_flow->id) selected @endif value="{{ $i_flow->id }}">{{ $i_flow->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group col-12 col-lg-4">
                        <label class="form-lbl iranyekan">
                            روز مجاز ثبت کارکرد
                        </label>
                        <input class="form-control text-center iranyekan @error('registration_start_day') is-invalid @enderror" type="number" min="1" name="registration_start_day" value="{{ $contract_subset->registration_start_day }}">
                        @error('registration_start_day')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4">
                        <label class="form-lbl iranyekan">
                            روز پایان ثبت کارکرد
                        </label>
                        <input class="form-control text-center iranyekan @error('registration_final_day') is-invalid @enderror" type="number" min="2" name="registration_final_day" value="{{ $contract_subset->registration_final_day }}">
                        @error('registration_final_day')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 col-lg-4">
                        <label class="form-lbl iranyekan">
                            مجموع اضافه کار مجاز ماهانه
                        </label>
                        <input class="form-control text-center iranyekan @error('overtime_registration_limit') is-invalid @enderror" type="number" min="0" name="overtime_registration_limit" value="{{ $contract_subset->overtime_registration_limit }}">
                        @error('overtime_registration_limit')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">مستندات زیرمجموعه قرارداد</label>
                        <m-file-browser @if($contract_subset->files) :already="true" @endif :accept="['png','jpg','bmp','tiff','pdf','xlsx','txt']" :size="325000"></m-file-browser>
                    </div>
                    <div class="form-group col-12 form-button-row text-center pt-4 pb-2">
                        <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                            <i class="submit_button_icon fa fa-database fa-1-2x mr-2"></i>
                            <span class="iranyekan">ارسال و ویرایش</span>
                        </button>
                        <a role="button" href="{{ route("ContractSubset.index") }}" class="btn btn-outline-secondary iranyekan" data-dismiss="modal">
                            <i class="fa fa-arrow-turn-right fa-1-2x mr-2"></i>
                            <span class="iranyekan">بازگشت به لیست</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
