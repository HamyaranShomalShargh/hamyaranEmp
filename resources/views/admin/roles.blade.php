@extends('layouts.admin_dashboard')
@section('content')
    <div class="page w-100 pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-chair fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">عنوان شغلی کاربران</h5>
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
                        <button class="btn btn-outline-info mr-2" data-toggle="modal" data-target="#new_user_modal">
                            <i class="fa fa-plus-circle fa-1-2x mr-1"></i>
                            <span class="iransans create-button">عنوان شغلی جدید</span>
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
                        <th scope="col"><span>وضعیت</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span><span>تاریخ ثبت</span></span></th>
                        <th scope="col"><span><span>تاریخ ویرایش</span></span></th>
                        <th scope="col"><span><span>عملیات</span></span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td><span class="iranyekan">{{$role->id}}</span></td>
                            <td><span class="iranyekan">{{$role->name}}</span></td>
                            @if($role->inactive == 0)
                                <td><span class="iranyekan"><i class="fa fa-check-circle green-color fa-1-4x"></i></span></td>
                            @elseif($role->inactive == 1)
                                <td><span class="iranyekan"><i class="fa fa-times-circle red-color fa-1-4x"></i></span></td>
                            @endif
                            <td><span class="iranyekan">{{$role->user->name}}</span></td>
                            <td><span class="iranyekan">{{verta($role->created_at)->format("H:i:s Y/m/d")}}</span></td>
                            <td><span class="iranyekan">{{verta($role->updated_at)->format("H:i:s Y/m/d")}}</span></td>
                            <td>
                                <div class="dropdown table-functions iranyekan">
                                    <button class="btn btn-outline-info dropdown-toggle iranyekan" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-cog fa-1-4x"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <form class="w-100" id="activation-form-{{ $role->id }}" action="{{ route("Roles.activation",$role->id) }}" method="POST" v-on:submit="submit_form">
                                            @csrf
                                            <button type="submit" form="activation-form-{{ $role->id }}" class="dropdown-item">
                                                @if($role->inactive == 0)
                                                    <i class="fa fa-lock"></i>
                                                    <span>غیر فعال سازی</span>
                                                @elseif($role->inactive == 1)
                                                    <i class="fa fa-lock-open"></i>
                                                    <span>فعال سازی</span>
                                                @endif
                                            </button>
                                        </form>
                                        <div class="dropdown-divider"></div>
                                        <a role="button" href="{{ route("Roles.edit",$role->id) }}" class="dropdown-item">
                                            <i class="fa fa-edit"></i>
                                            <span class="iranyekan">ویرایش</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form class="w-100" id="delete-form-{{ $role->id }}" action="{{ route("Roles.destroy",$role->id) }}" method="POST" v-on:submit="submit_form">
                                            @csrf
                                            @method("Delete")
                                            <button type="submit" form="delete-form-{{ $role->id }}" class="dropdown-item">
                                                <i class="fa fa-trash"></i>
                                                <span class="iranyekan">حذف</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10"><span class="iransans">اطلاعاتی وجود ندارد</span></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="new_user_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-2xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">ایجاد عنوان شغلی جدید</h5>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" action="{{route("Roles.store")}}" method="post" data-type="create" v-on:submit="submit_form">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label class="col-form-label iranyekan black_color" for="name">
                                    نام عنوان شغلی
                                    <strong class="red-color">*</strong>
                                </label>
                                <input type="text" class="form-control iranyekan text-center @error('name') is-invalid @enderror" id="name" name="name" value="{{old("name")}}">
                                @error('name')
                                <span class="invalid-feedback iranyekan" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            @forelse($menu_headers as $menu_header)
                                @if($menu_header->items->isNotEmpty())
                                    <div class="form-group col-md-12 col-lg-3 col-xl-2 iranyekan">
                                        <ul class="menu-list main-menu-list border">
                                            <li class="bg-dark white-color role-menu-header-text-container">
                                                <h5 class="role-menu-header-text iransans">
                                                    {{$menu_header->name}}
                                                    <span>
                                                <i class="far fa-check-square fa-1-4x all-checkboxes ml-3" v-on:click="select_all_checkboxes"></i>
                                                <i class="far fa-square fa-1-4x all-checkboxes" v-on:click="deselect_all_checkboxes"></i>
                                            </span>
                                                </h5>
                                            </li>
                                            @forelse($menu_header->items as $menu_item)
                                                @if($menu_item->parent_id == null)
                                                    <li class="lev1-menu-list">
                                <span class="d-flex flex-row align-items-center justify-content-start mt-1" style="font-weight: 600;font-size: 14px;color: #1a5d2b">
                                    <i class="fa fa-bullseye mr-2"></i>
                                    {{$menu_item->name}}
                                </span>
                                                        @if($menu_item->actions)
                                                            <ul class="menu-list sub-menu-list">
                                                                @foreach($menu_item->actions as $action)
                                                                    <li class="menu-item-selectable" v-on:click="menu_action_checkmark">
                                                                        <input class="mr-1" type="checkbox" name="role_menu[]" value="{{$menu_item->id."#".$action->id."#".$menu_item->route.".".$action->action}}" v-on:click="menu_action_checkmark">
                                                                        <span>{{$action->name}}</span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                        @if($menu_item->children)
                                                            <ul class="menu-list">
                                                                @foreach($menu_item->children as $child)
                                                                    <li>
                                                <span class="d-flex flex-row align-items-center justify-content-start" style="font-weight: 500;font-size: 13px;color: #2a733c">
                                                    <i class="fa fa-bullseye mr-2"></i>
                                                    {{$child->name}}
                                                </span>
                                                                        @if($child->actions)
                                                                            <ul class="menu-list sub-menu-list">
                                                                                @foreach($child->actions as $action)
                                                                                    <li class="menu-item-selectable" v-on:click="menu_action_checkmark">
                                                                                        <input class="mr-1" type="checkbox" name="role_menu[]" value="{{$child->id."#".$action->id."#".$child->route.".".$action->action}}" v-on:click="menu_action_checkmark">
                                                                                        <span>{{$action->name}}</span>
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </li>
                                                @endif
                                            @empty
                                            @endforelse
                                        </ul>
                                    </div>
                                    @error('name')
                                    <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                                    @enderror
                                @endif
                            @empty
                            @endforelse
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="main_submit_form" class="btn btn-success">
                        <i class="fa fa-database fa-1-2x mr-1"></i>
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
