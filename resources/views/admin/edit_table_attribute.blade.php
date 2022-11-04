@extends('layouts.admin_dashboard')
@push('scripts')
    <script>
        let attributes_data = @json($items);
    </script>
@endpush
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-table-list fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">عناوین ورودی</h5>
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
            <form id="main_submit_form" class="p-3" action="{{ route("TableAttributes.update",$table_attribute->id) }}" method="POST" data-json="attributes_list" v-on:submit="submit_form">
                @csrf
                @method('PUT')
                <input type="hidden" id="attributes_list" name="attributes_list">
                <div class="form-row">
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            نام
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control text-center iranyekan @error('name') is-invalid @enderror" type="text" name="name" value="{{ $table_attribute->name }}">
                        @error('name')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            نوع
                            <strong class="red-color">*</strong>
                        </label>
                        <div>
                            <input @if($table_attribute->type == "performance") checked @endif type="radio" id="performance_type" name="type" v-model="table_attributes_type" value="performance">
                            <label for="performance_type" class="iranyekan mb-0">کارکرد</label>
                            <input @if($table_attribute->type == "invoice") checked @endif type="radio" id="invoice_type" name="type" class="ml-3" v-model="table_attributes_type" value="invoice">
                            <label for="invoice_type" class="iranyekan mb-0">وضعیت</label>
                        </div>
                    </div>
                    <div class="form-group col-12">
                        <label class="form-lbl iranyekan">
                            اضافه کردن عنوان
                            <strong class="red-color">*</strong>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button data-element="attribute_title" type="button" class="btn btn-outline-info mr-2" v-on:click="add_attribute_item">
                                    <i class="fa fa-plus-circle fa-1-2x mr-1"></i>
                                    <span class="iransans create-button">افزودن</span>
                                </button>
                            </div>
                            <input id="attribute_title" type="text" class="form-control text-center iranyekan">
                        </div>
                        @error('name')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan black_color">
                            عناوین اضافه شده
                        </label>
                        <ul class="list-group w-100 pl-0">
                            <li class="list-group-item" v-if="attributes_list.length === 0"><h6 class="iranyekan text-center m-0">انتخابی صورت نگرفته است</h6></li>
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" v-for="attribute in attributes_list">
                                <div>
                                    <span class="iranyekan text-center pb-2" style="font-weight: 900">@{{ attribute.name }}</span>
                                    <div class="d-flex flex-row align-items-center justify-content-start">
                                        <input checked type="radio" :id="`number-${attribute.slug}`" value="number" v-on:change="change_attributes_kind($event,attribute.slug)">
                                        <label :for="`number-${attribute.slug}`" class="iranyekan ml-1 mb-0">عددی</label>
                                        <div class="vertical-divider"></div>
                                        <input type="radio" :id="`text-${attribute.slug}`" value="text" v-on:change="change_attributes_kind($event,attribute.slug)">
                                        <label :for="`text-${attribute.slug}`" class="iranyekan ml-1 mb-0">متنی</label>
                                        <div v-if="table_attributes_type === 'invoice'" class="vertical-divider"></div>
                                        <input v-if="table_attributes_type === 'invoice'" type="radio" :id="`function-${attribute.slug}`" value="function" v-on:change="change_attributes_category($event,attribute.slug)">
                                        <label v-if="table_attributes_type === 'invoice'" :for="`function-${attribute.slug}`" class="iranyekan ml-1 mb-0">کارکرد</label>
                                        <div v-if="table_attributes_type === 'invoice'" class="vertical-divider"></div>
                                        <input v-if="table_attributes_type === 'invoice'" type="radio" :id="`advantage-${attribute.slug}`" value="advantage" v-on:change="change_attributes_category($event,attribute.slug)">
                                        <label v-if="table_attributes_type === 'invoice'" :for="`advantage-${attribute.slug}`" class="iranyekan ml-1 mb-0">مزایا</label>
                                        <div v-if="table_attributes_type === 'invoice'" class="vertical-divider"></div>
                                        <input v-if="table_attributes_type === 'invoice'" type="radio" :id="`deduction-${attribute.slug}`" value="deduction" v-on:change="change_attributes_category($event,attribute.slug)">
                                        <label v-if="table_attributes_type === 'invoice'" :for="`deduction-${attribute.slug}`" class="iranyekan ml-1 mb-0">کسورات</label>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-light mr-2" data-function="up" :data-slug="attribute.slug" v-on:click="modify_attribute">
                                        <i class="fa fa-arrow-up gray-color fa-1-2x"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-light mr-2" data-function="down" :data-slug="attribute.slug" v-on:click="modify_attribute">
                                        <i class="fa fa-arrow-down gray-color fa-1-2x"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-light" data-function="remove" :data-slug="attribute.slug" v-on:click="modify_attribute">
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
                        <a role="button" href="{{ route("TableAttributes.index") }}" class="btn btn-outline-secondary iranyekan" data-dismiss="modal">
                            <i class="fa fa-arrow-turn-right fa-1-2x mr-1"></i>
                            <span class="iranyekan">بازگشت به لیست</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
