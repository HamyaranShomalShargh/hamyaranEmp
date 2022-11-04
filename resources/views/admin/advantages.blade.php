@extends('layouts.admin_dashboard')
@if(old("advantage_list"))
    @push('scripts')
        <script>
            let advantage_data = JSON.parse(@json(old("advantage_list")));
        </script>
    @endpush
@endif
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-table-pivot fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">عناوین تغییرات مزایا</h5>
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
                    <input type="text" class="form-control text-center iranyekan" data-table="main_table" placeholder="جستجو با نام عنوان" v-on:input="filter_table">
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
                        <th scope="col"><span>وضعیت</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($advantages as $advantage)
                        <tr>
                            <td><span class="iranyekan">{{ $advantage->id }}</span></td>
                            <td><span class="iranyekan">{{ $advantage->name }}</span></td>
                            <td>
                                <span class="iranyekan">
                                    @if($advantage->inactive == 1)
                                        <i class="fa fa-times-circle red-color fa-1-6x"></i>
                                    @elseif($advantage->inactive == 0)
                                        <i class="fa fa-check-circle green-color fa-1-6x"></i>
                                    @endif
                                </span>
                            </td>
                            <td><span class="iranyekan">{{ $advantage->user->name }}</span></td>
                            <td><span class="iranyekan">{{ verta($advantage->cretaed_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td><span class="iranyekan">{{ verta($advantage->updated_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td>
                                <div class="dropdown table-functions">
                                    <button class="btn btn-outline-info dropdown-toggle iranyekan" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-cog fa-1-4x"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <form class="w-100" id="activation-form-{{ $advantage->id }}" action="{{ route("Advantages.activation",$advantage->id) }}" method="POST" v-on:submit="submit_form">
                                            @csrf
                                            <button type="submit" form="activation-form-{{ $advantage->id }}" class="dropdown-item">
                                                @if($advantage->inactive == 0)
                                                    <i class="fa fa-lock"></i>
                                                    <span class="iranyekan">غیر فعال سازی</span>
                                                @elseif($advantage->inactive == 1)
                                                    <i class="fa fa-lock-open"></i>
                                                    <span class="iranyekan">فعال سازی</span>
                                                @endif
                                            </button>
                                        </form>
                                        <div class="dropdown-divider"></div>
                                        <a role="button" href="{{ route("Advantages.edit",$advantage->id) }}" class="dropdown-item">
                                            <i class="fa fa-edit"></i>
                                            <span class="iranyekan">ویرایش</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form class="w-100" id="delete-form-{{ $advantage->id }}" action="{{ route("Advantages.activation",$advantage->id) }}" method="POST" v-on:submit="submit_form">
                                            @csrf
                                            @method("Delete")
                                            <button type="submit" form="delete-form-{{ $advantage->id }}" class="dropdown-item">
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
                    <form id="main_submit_form" class="p-3" action="{{ route("Advantages.store") }}" data-json="advantage_list" method="POST" v-on:submit="submit_form">
                        @csrf
                        <input type="hidden" id="advantage_list" name="advantage_list">
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
                                    گردش اتوماسیون
                                    <strong class="red-color">*</strong>
                                </label>
                                <select class="form-control text-center iranyekan @error('automation_flow_id') is-invalid @enderror selectpicker" data-container="body" title="انتخاب کنید" size="20" data-live-search="true" name="automation_flow_id">
                                    @forelse($automation_flows as $flows)
                                        <option @if(old("automation_flow_id") && $flows->id == old("automation_flow_id")) selected @endif value="{{ $flows->id }}">{{ $flows->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('automation_flow_id')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    <input type="checkbox" name="period" value="period">
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
                                                <input checked type="radio" :id="`number-${title.slug}`" :name="`kind-${title.slug}`" value="text" v-on:change="change_advantage_kind($event,title.slug)">
                                                <label :for="`number-${title.slug}`" class="iranyekan ml-1 mb-0">متنی</label>
                                                <div class="vertical-divider"></div>
                                                <input type="radio" :id="`text-${title.slug}`" value="file" :name="`kind-${title.slug}`" v-on:change="change_advantage_kind($event,title.slug)">
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
