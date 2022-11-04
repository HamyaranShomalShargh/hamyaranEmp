@extends('layouts.admin_dashboard')
@if(old("attributes_list"))
    @push('scripts')
        <script>
            let attributes_data = JSON.parse(@json(old("attributes_list")));
        </script>
    @endpush
@endif
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-table-list fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">عناوین ورودی</h5>
                    <span>(ایجاد، جستجو و ویرایش)</span>
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
            <div class="page-header">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-info mr-2" data-toggle="modal" data-target="#new_menu_header_modal">
                            <i class="fa fa-plus-circle fa-1-2x mr-1"></i>
                            <span class="iransans create-button">عنوان جدید</span>
                        </button>
                    </div>
                    <input type="text" class="form-control text-center iranyekan" data-table="main_table" placeholder="جستجو با نام" v-on:input="filter_table">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
                    </div>
                </div>
            </div>
            <div id="table_responsive" class="table-responsive p-3">
                <table id="main_table" class="table table-striped static-table" data-filter="[1]">
                    <thead class="bg-dark white-color">
                    <tr class="iransans">
                        <th scope="col"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col"><span>عناوین</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($table_attributes as $attribute)
                        <tr>
                            <td><span class="iranyekan">{{ $attribute->id }}</span></td>
                            <td><span class="iranyekan">{{ $attribute->name }}</span></td>
                            <td><span class="iranyekan">{{ count($attribute->items) }}</span></td>
                            <td><span class="iranyekan">{{ $attribute->user->name }}</span></td>
                            <td><span class="iranyekan">{{ verta($attribute->cretaed_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td><span class="iranyekan">{{ verta($attribute->updated_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td>
                                <div class="dropdown table-functions iranyekan">
                                    <button class="btn btn-outline-info dropdown-toggle iranyekan" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-cog fa-1-4x"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a role="button" href="{{ route("TableAttributes.edit",$attribute->id) }}" class="dropdown-item">
                                            <i class="fa fa-edit"></i>
                                            <span class="iranyekan">ویرایش</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form class="w-100" id="delete-form-{{ $attribute->id }}" action="{{ route("TableAttributes.destroy",$attribute->id) }}" method="POST" v-on:submit="submit_form">
                                            @csrf
                                            @method("Delete")
                                            <button type="submit" form="delete-form-{{ $attribute->id }}" class="dropdown-item">
                                                <i class="fa fa-trash"></i>
                                                <span class="iranyekan">حذف</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="new_menu_header_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد عنوان جدید</h6>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("TableAttributes.store") }}" data-json="attributes_list" method="POST" v-on:submit="submit_form">
                        @csrf
                        <input type="hidden" id="attributes_list" name="attributes_list">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    نام
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center iranyekan @error('name') is-invalid @enderror" type="text" name="name" value="{{ old("name") }}">
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
                                    <input checked type="radio" id="performance_type" name="type" v-model="table_attributes_type" value="performance">
                                    <label for="performance_type" class="iranyekan mb-0">کارکرد</label>
                                    <input type="radio" id="invoice_type" name="type" class="ml-3" v-model="table_attributes_type" value="invoice">
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
                                            <span class="iranyekan text-center" style="font-weight: 900">@{{ attribute.name }}</span>
                                            <div class="d-flex flex-row align-items-center justify-content-start">
                                                <input checked type="radio" :id="`number-${attribute.slug}`" value="number" :name="`kind-${attribute.slug}`" v-on:change="change_attributes_kind($event,attribute.slug)">
                                                <label :for="`number-${attribute.slug}`" class="iranyekan ml-1 mb-0">عددی</label>
                                                <div class="vertical-divider"></div>
                                                <input type="radio" :id="`text-${attribute.slug}`" value="text" :name="`kind-${attribute.slug}`" v-on:change="change_attributes_kind($event,attribute.slug)">
                                                <label :for="`text-${attribute.slug}`" class="iranyekan ml-1 mb-0">متنی</label>
                                                <div v-if="table_attributes_type === 'invoice'" class="vertical-divider"></div>
                                                <input v-if="table_attributes_type === 'invoice'" type="radio" :id="`function-${attribute.slug}`" :name="`type-${attribute.slug}`" value="function" v-on:change="change_attributes_category($event,attribute.slug)">
                                                <label v-if="table_attributes_type === 'invoice'" :for="`function-${attribute.slug}`" class="iranyekan ml-1 mb-0">کارکرد</label>
                                                <div v-if="table_attributes_type === 'invoice'" class="vertical-divider"></div>
                                                <input v-if="table_attributes_type === 'invoice'" type="radio" :id="`advantage-${attribute.slug}`" :name="`type-${attribute.slug}`" value="advantage" v-on:change="change_attributes_category($event,attribute.slug)">
                                                <label v-if="table_attributes_type === 'invoice'" :for="`advantage-${attribute.slug}`" class="iranyekan ml-1 mb-0">مزایا</label>
                                                <div v-if="table_attributes_type === 'invoice'" class="vertical-divider"></div>
                                                <input v-if="table_attributes_type === 'invoice'" type="radio" :id="`deduction-${attribute.slug}`" :name="`type-${attribute.slug}`" value="deduction" v-on:change="change_attributes_category($event,attribute.slug)">
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
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                        <i class="submit_button_icon fa fa-database fa-1-2x mr-1"></i>
                        <span class="iranyekan">ارسال و ذخیره</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iranyekan" data-dismiss="modal">
                        <i class="fa fa-times fa-1-2x mr-1"></i>
                        <span class="iranyekan">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
