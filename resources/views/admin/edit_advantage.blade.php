@extends('layouts.admin_dashboard')
@push('scripts')
    <script>
        let advantage_data = @json($attachments);
    </script>
@endpush
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-table-pivot fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">عناوین تغییرات مزایا</h5>
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
            <form id="main_submit_form" class="p-3" action="{{ route("Advantages.update",$advantage->id) }}" data-json="advantage_list" method="POST" v-on:submit="submit_form">
                @csrf
                @method('PUT')
                <input type="hidden" id="advantage_list" name="advantage_list">
                <div class="form-row">
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            نام
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control text-center iranyekan @error('name') is-invalid @enderror" type="text" name="name" value="{{ $advantage->name }}">
                        @error('name')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            گردش اتوماسیون
                            <strong class="red-color">*</strong>
                        </label>
                        <select class="form-control text-center iranyekan @error('automation_flow_id') is-invalid @enderror selectpicker" title="انتخاب کنید" size="20" data-live-search="true" name="automation_flow_id">
                            @forelse($automation_flows as $flows)
                                <option @if($flows->id == $advantage->automation_flow_id) selected @endif value="{{ $flows->id }}">{{ $flows->name }}</option>
                            @empty
                            @endforelse
                        </select>
                        @error('automation_flow_id')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            <input @if($advantage->attachments->period) checked @endif type="checkbox" name="period" value="period">
                            دوره زمانی شروع و پایان
                        </label>
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            اضافه کردن عنوان
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button data-element="advantage_title" type="button" class="btn btn-outline-info mr-2" v-on:click="add_advantage_item">
                                    <i class="fa fa-plus-circle fa-1-2x mr-1"></i>
                                    <span class="iransans create-button">افزودن</span>
                                </button>
                            </div>
                            <input id="advantage_title" type="text" class="form-control text-center iranyekan">
                        </div>
                    </div>
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan black_color">
                            عناوین اضافه شده
                        </label>
                        <ul class="list-group w-100 pl-0">
                            <li class="list-group-item" v-if="advantage_list.length === 0"><h6 class="iranyekan text-center m-0">انتخابی صورت نگرفته است</h6></li>
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" v-for="title in advantage_list">
                                <div>
                                    <span class="iranyekan text-center" style="font-weight: 900">@{{ title.name }}</span>
                                    <div class="d-flex flex-row align-items-center justify-content-start">
                                        <input :checked="title.kind === 'text'" type="radio" :id="`number-${title.slug}`" :name="`kind-${title.slug}`" value="text" v-on:change="change_advantage_kind($event,title.slug)">
                                        <label :for="`number-${title.slug}`" class="iranyekan ml-1 mb-0">متنی</label>
                                        <div class="vertical-divider"></div>
                                        <input :checked="title.kind === 'file'" type="radio" :id="`text-${title.slug}`" value="file" :name="`kind-${title.slug}`" v-on:change="change_advantage_kind($event,title.slug)">
                                        <label :for="`text-${title.slug}`" class="iranyekan ml-1 mb-0">فایل</label>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-light mr-2" data-function="up" :data-slug="title.slug" v-on:click="modify_advantage">
                                        <i class="fa fa-arrow-up gray-color fa-1-2x"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-light mr-2" data-function="down" :data-slug="title.slug" v-on:click="modify_advantage">
                                        <i class="fa fa-arrow-down gray-color fa-1-2x"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-light" data-function="remove" :data-slug="title.slug" v-on:click="modify_advantage">
                                        <i class="fa fa-times gray-color fa-1-2x"></i>
                                    </button>
                                </div>
                            </li>
                        </ul>
                        <small class="iranyekan red-color">اولویت نمایش در جدول به ترتیب لیست ورود می باشد</small>
                    </div>
                    <div class="form-group col-12 form-button-row text-center pt-4 pb-2">
                        <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                            <i class="submit_button_icon fa fa-database fa-1-2x mr-1"></i>
                            <span class="iranyekan">ارسال و ویرایش</span>
                        </button>
                        <a role="button" href="{{ route("Advantages.index") }}" class="btn btn-outline-secondary iranyekan" data-dismiss="modal">
                            <i class="fa fa-arrow-turn-right fa-1-2x mr-1"></i>
                            <span class="iranyekan">بازگشت به لیست</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
