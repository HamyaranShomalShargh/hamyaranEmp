@extends('layouts.staff_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-square-list fa-1-6x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">
                        {{ "فرم تغییرات مزایا - " . $automation->advantage->name }}
                    </h5>
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
            <div class="page-header">
                <div class="input-group">
                    <div class="input-group-append">
                        <span class="input-group-text iranyekan" id="basic-addon1">مشخصات پرسنل</span>
                    </div>
                    <span type="text" class="form-control iranyekan">{{ "{$automation->employee->first_name} {$automation->employee->last_name} - {$automation->employee->national_code} - {$automation->contract}" }}</span>
                </div>
            </div>
            <form class="p-3" id="main_submit_form" action="{{ route("AdvantageForms.update",$automation->id) }}" method="post" enctype="multipart/form-data" v-on:submit="submit_form">
                @csrf
                @method("put")
                <div class="form-row">
                    <div class="form-group col-12">
                        <input id="add_type" @if($automation->type == "add") checked @endif type="radio" value="add" name="type">
                        <label for="add_type" class="iranyekan">اضافه کردن مزایا</label>
                        <input id="remove_type" @if($automation->type == "remove") checked @endif type="radio" class="ml-2" value="remove" name="type">
                        <label for="remove_type" class="iranyekan">حذف کردن مزایا</label>
                    </div>
                    @forelse(json_decode($automation->advantage->attachments->texts,true) as $text)
                        <div class="form-group col-12">
                            <label class="form-lbl iranyekan">{{ $text }}</label>
                            @if($automation->texts && in_array($text,array_column(json_decode($automation->texts,true),"name")))
                                <input type="text" class="form-control iranyekan text-center" name="{{ $text }}" value="{{ json_decode($automation->texts,true)[array_search($text,array_column(json_decode($automation->texts,true),"name"))]["value"] }}">
                            @else
                                <input type="text" class="form-control iranyekan text-center" name="{{ $text }}">
                            @endif
                        </div>
                    @empty
                    @endforelse
                    @forelse(json_decode($automation->advantage->attachments->files,true) as $file)
                        <div class="form-group col-12">
                            <label class="form-lbl iranyekan">
                                {{ $file }}
                                @if($automation->files && in_array($file,array_column(json_decode($automation->files,true),"name")))
                                    <a href="{{ route("AdvantageForms.files",[$automation->id,"$file"]) }}" class="iransans green-color">(دانلود فایل(های) آپلود شده)</a>
                                @endif
                            </label>
                            <div>
                                <input type="file" hidden class="form-control iranyekan text-center" v-on:change="custom_file_check" multiple id="{{ "formFile-{$loop->iteration}" }}" name="{{ $file }}[]" accept=".png,.jpg,.bmp,.tiff,.pdf,.xlsx,.txt,.doc,.docx">
                                <input type="text" class="form-control iranyekan text-center file_selector_box" v-on:click="pop_up_custom_file" id="{{ "formFileBox-{$loop->iteration}" }}" readonly value="فایلی انتخاب نشده است">
                                <small class="iranyekan red-color">در صورت انتخاب فایل و یا فایل های جدید، فایل های قبلی حذف و فایل های جدید جایگزین خواهند شد</small>
                            </div>
                        </div>
                    @empty
                    @endforelse
                    @if($automation->advantage->attachments->period)
                        <div class="form-group col-12">
                            <label class="form-lbl iranyekan">ماه شروع</label>
                            <select class="form-control text-center iranyekan" name="start_month">
                                <option selected>ندارد</option>
                                @forelse($months as $month)
                                    <option @if(old("start_month") && $month == old("start_month")) selected @endif value="{{ $month }}">{{ $month }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group col-12">
                            <label class="form-lbl iranyekan">ماه پایان</label>
                            <select class="form-control text-center iranyekan" name="end_month">
                                <option selected>ندارد</option>
                                @forelse($months as $month)
                                    <option @if(old("end_month") && $month == old("end_month")) selected @endif value="{{ $month }}">{{ $month }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    @endif
                    <div class="form-group col-12 form-button-row text-center pt-4 pb-2">
                        <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                            <i class="submit_button_icon fa fa-database fa-1-2x mr-1"></i>
                            <span class="iranyekan">ارسال و ویرایش</span>
                        </button>
                        <a role="button" href="{{ route("AdvantageForms.index") }}" class="btn btn-outline-secondary iranyekan" data-dismiss="modal">
                            <i class="fa fa-arrow-turn-right fa-1-2x mr-1"></i>
                            <span class="iranyekan">بازگشت به لیست</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
