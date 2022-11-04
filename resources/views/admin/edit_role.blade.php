@extends('layouts.admin_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-chair fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">عنوان شغلی</h5>
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
            <form id="update_form" class="p-3" action="{{ route("Roles.update",$role->id) }}" method="POST" enctype="multipart/form-data" v-on:submit="submit_form">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label class="col-form-label iranyekan" for="name">
                            نام عنوان شغلی
                            <strong class="red-color">*</strong>
                        </label>
                        <input type="text" class="form-control iranyekan text-center @error('name') is-invalid @enderror" id="name" name="name" value="{{$role->name}}">
                        @error('name')
                        <span class="invalid-feedback iranyekan" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    @forelse($menu_headers as $menu_header)
                        @if($menu_header->items->isNotEmpty())
                            <div class="form-group col-md-12 col-lg-3 col-xl-2  iranyekan mt-3">
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
                                                                <input class="mr-1" @if($role->menu_items->where("pivot.menu_item_id",$menu_item->id)->where("pivot.menu_action_id",$action->id)->first()) checked @endif type="checkbox" name="role_menu[]" value="{{$menu_item->id."#".$action->id."#".$menu_item->route.".".$action->action}}" v-on:click="menu_action_checkmark">
                                                                <span>{{$action->name}}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                                @if($menu_item->children)
                                                    <ul class="menu-list">
                                                        @foreach($menu_item->children as $child)
                                                            <li>
                                                <span class="d-flex flex-row align-items-center justify-content-start mt-1" style="font-weight: 600;font-size: 14px;color: #1a5d2b">
                                                    <i class="fa fa-bullseye mr-2"></i>
                                                    {{$child->name}}
                                                </span>
                                                                @if($child->actions)
                                                                    <ul class="menu-list sub-menu-list">
                                                                        @foreach($child->actions as $action)
                                                                            <li class="menu-item-selectable" v-on:click="menu_action_checkmark">
                                                                                <input class="mr-1" @if($role->menu_items->where("pivot.menu_item_id",$child->id)->where("pivot.menu_action_id",$action->id)->first()) checked @endif type="checkbox" name="role_menu[]" value="{{$child->id."#".$action->id."#".$child->route.".".$action->action}}" v-on:click="menu_action_checkmark">
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
                        @endif
                    @empty
                    @endforelse
                    <div class="form-group col-12 form-button-row text-center pt-4 pb-2">
                        <button type="submit" form="update_form" class="btn btn-success submit_button">
                            <i class="submit_button_icon fa fa-database fa-1-2x mr-1"></i>
                            <span class="iranyekan">ارسال و ویرایش</span>
                        </button>
                        <a role="button" href="{{ route("Roles.index") }}" class="btn btn-outline-secondary iranyekan" data-dismiss="modal">
                            <i class="fa fa-arrow-turn-right fa-1-2x mr-1"></i>
                            <span class="iranyekan">بازگشت به لیست</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
