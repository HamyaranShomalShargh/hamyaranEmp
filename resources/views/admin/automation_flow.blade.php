@extends('layouts.admin_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-arrows-spin fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">مدیریت گردش های اتوماسیون</h5>
                    <span>(ایجاد ، مشاهده و ویرایش)</span>
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
                        <button class="btn btn-outline-info mr-2" data-toggle="modal" data-target="#new_automation_flow_modal">
                            <i class="fa fa-plus-circle fa-1-2x mr-1"></i>
                            <span class="iransans create-button">گردش جدید</span>
                        </button>
                    </div>
                    <input type="text" class="form-control text-center iranyekan" data-table="flow_table" placeholder="جستجو با نام گردش" v-on:input="filter_table">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
                    </div>
                </div>
            </div>
            <div id="table_responsive" class="table-responsive p-3">
                <table id="flow_table" class="table table-striped static-table" data-filter="[1]">
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
                    @forelse($automation_flows as $automation_flow)
                        <tr>
                            <td><span class="iranyekan">{{ $automation_flow->id }}</span></td>
                            <td><span class="iranyekan">{{ $automation_flow->name }}</span></td>
                            <td>
                                @if($automation_flow->inactive == 1)
                                    <i class="fa fa-times-circle red-color fa-1-6x"></i>
                                @elseif($automation_flow->inactive == 0)
                                    <i class="fa fa-check-circle green-color fa-1-6x"></i>
                                @endif
                            </td>
                            <td><span class="iranyekan">{{ $automation_flow->user->name }}</span></td>
                            <td><span class="iranyekan">{{ verta($automation_flow->created_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td><span class="iranyekan">{{ verta($automation_flow->updated_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td>
                                <div class="dropdown table-functions iranyekan">
                                    <button class="btn btn-outline-info dropdown-toggle iranyekan" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-cog fa-1-4x"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <form class="w-100" id="activation-form-{{ $automation_flow->id }}" action="{{ route("AutomationFlow.activation",$automation_flow->id) }}" method="POST" v-on:submit="submit_form">
                                            @csrf
                                            <button type="submit" form="activation-form-{{ $automation_flow->id }}" class="dropdown-item">
                                                @if($automation_flow->inactive == 0)
                                                    <i class="fa fa-lock mr-1"></i>
                                                    <span>غیر فعال سازی</span>
                                                @elseif($automation_flow->inactive == 1)
                                                    <i class="fa fa-lock-open mr-1"></i>
                                                    <span>فعال سازی</span>
                                                @endif
                                            </button>
                                        </form>
                                        <div class="dropdown-divider"></div>
                                        <a role="button" href="{{ route("AutomationFlow.edit",$automation_flow->id) }}" class="dropdown-item">
                                            <i class="fa fa-edit"></i>
                                            <span class="iranyekan">ویرایش</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form class="w-100" id="delete-form-{{ $automation_flow->id }}" action="{{ route("AutomationFlow.destroy",$automation_flow->id) }}" method="POST" v-on:submit="submit_form">
                                            @csrf
                                            @method("Delete")
                                            <button type="submit" form="delete-form-{{ $automation_flow->id }}" class="dropdown-item">
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
    <div class="modal fade rtl" id="new_automation_flow_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد گردش جدید</h6>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" action="{{route("AutomationFlow.store")}}" method="post" data-type="create" v-on:submit="submit_form" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label class="col-form-label iranyekan black_color" for="name">
                                    نام
                                    <strong class="red-color">*</strong>
                                </label>
                                <input type="text" class="form-control iranyekan text-center @error('name') is-invalid @enderror" id="name" name="name" value="{{old("name")}}">
                                @error('name')
                                <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="col-form-label iranyekan black_color" for="short_name">
                                    اضافه کردن عناوین شغلی
                                    <strong class="red-color">*</strong>
                                </label>
                                <select class="form-control iranyekan text-center selectpicker @error('flow_roles') is-invalid @enderror" data-live-search="true" id="roles" title="انتخاب کنید" data-size="20" v-on:change="add_role_item">
                                    @forelse($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('flow_roles')
                                <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="col-form-label iranyekan black_color">
                                    گردش(اولویت به ترتیب ثبت)
                                </label>
                                <select hidden id="flow_roles" name="flow_roles[]" multiple>
                                    <option selected v-for="role in roles_list" :value="role.id">@{{ role.name }}</option>
                                </select>
                                <ul class="list-group w-100 pl-0">
                                    <li v-if="roles_list.length === 0"><h6 class="iranyekan text-center">انتخابی صورت نگرفته است</h6></li>
                                    <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" v-for="role in roles_list">
                                        <div>
                                            <input type="radio" name="main_role" :value="role.id" :id="role.slug">
                                            <label :for="role.slug" class="iranyekan mr-1">تایید کننده نهایی</label>
                                            <span>-</span>
                                            <span class="iranyekan text-center ml-1" style="font-weight: 700">@{{ role.name }}</span>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-light mr-2" data-function="up" :data-slug="role.slug" v-on:click="modify_role">
                                                <i class="fa fa-arrow-up gray-color fa-1-2x"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-light mr-2" data-function="down" :data-slug="role.slug" v-on:click="modify_role">
                                                <i class="fa fa-arrow-down gray-color fa-1-2x"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-light" data-function="remove" :data-slug="role.slug" v-on:click="modify_role">
                                                <i class="fa fa-times gray-color fa-1-2x"></i>
                                            </button>
                                        </div>
                                    </li>
                                </ul>
                                <small v-if="roles_list.length !== 0" class="iranyekan red-color">در صورت مشخص نکردن تایید کننده نهایی، عنوان شغلی انتهای لیست به عنوان تایید کننده نهایی در نظر گرفته می شود </small>
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
