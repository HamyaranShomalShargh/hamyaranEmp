@extends('layouts.admin_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-diagram-successor fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">عناوین منو</h5>
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
            <form id="main_submit_form" class="p-3" action="{{ route("MenuItems.update",$menu_item->id) }}" method="POST" enctype="multipart/form-data" v-on:submit="submit_form">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan black_color" for="name">
                            نام
                            <strong class="red-color">*</strong>
                        </label>
                        <input type="text" class="form-control iranyekan text-center @error('name') is-invalid @enderror" id="name" name="name" value="{{$menu_item->name}}">
                        @error('name')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan black_color" for="short_name">
                            نام مختصر
                            <strong class="red-color">*</strong>
                        </label>
                        <input type="text" class="form-control iranyekan text-center @error('short_name') is-invalid @enderror" id="short_name" name="short_name" value="{{$menu_item->name}}">
                        @error('short_name')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan black_color" for="menu_header_id">
                            سرفصل منو
                            <strong class="red-color">*</strong>
                        </label>
                        <select class="form-control iranyekan text-center selectpicker @error('menu_header_id') is-invalid @enderror" data-live-search="true" id="menu_header_id" name="menu_header_id" title="انتخاب کنید" data-size="20">
                            @forelse($menu_headers as $menu_header)
                                <option @if($menu_header->id == $menu_item->menu_header_id) selected @endif value="{{$menu_header->id}}">{{$menu_header->name}}</option>
                            @empty
                            @endforelse
                        </select>
                        @error('menu_header_id')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan black_color" for="parent_id">وابستگی</label>
                        <select class="form-control iranyekan text-center selectpicker @error('parent_id') is-invalid @enderror" data-live-search="true" id="parent_id" name="parent_id" title="انتخاب کنید" data-size="20">
                            <option value="">هیچکدام</option>
                            @forelse($menu_items as $item)
                                <option @if($item->id == $menu_item->parent_id) selected @endif value="{{$item->id}}">{{$item->name}}</option>
                            @empty
                            @endforelse
                        </select>
                        @error('parent_id')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan black_color" for="menu_action_id">عملیات وابسته</label>
                        <select class="form-control iranyekan text-center selectpicker @error('menu_action_id') is-invalid @enderror" v-on:change="main_route_change" multiple data-live-search="true" id="menu_action_id" name="menu_action_id[]" title="انتخاب کنید" data-size="20">
                            @forelse($menu_actions as $menu_action)
                                <option @if(in_array($menu_action->id,array_column($menu_item->actions->toArray(),"id"))) selected @endif value="{{$menu_action->id}}">{{$menu_action->name}}</option>
                            @empty
                            @endforelse
                        </select>
                        @error('menu_action_id')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan black_color" for="main">عملیات اصلی</label>
                        <select class="form-control iranyekan text-center selectpicker @error('main') is-invalid @enderror" data-live-search="true" id="main" name="main" title="انتخاب کنید" data-size="20">
                            @forelse($menu_item->actions as $menu_action)
                                <option @if($menu_action->action == $menu_item->main_route) selected @endif value="{{$menu_action->id}}">{{$menu_action->name}}</option>
                            @empty
                            @endforelse
                        </select>
                        @error('main')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan black_color" for="route">
                            مسیر
                            <strong class="red-color">*</strong>
                        </label>
                        <input type="text" class="form-control text-center @error('route') is-invalid @enderror ltr" id="route" name="route" value="{{$menu_item->route}}">
                        @error('route')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan black_color" for="priority">اولویت نمایش</label>
                        <input min="0" type="number" class="form-control text-center iranyekan @error('priority') is-invalid @enderror" id="priority" name="priority" value="{{$menu_item->priority}}">
                        @error('priority')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan black_color" for="upload_file">آیکون</label>
                        <s-file-browser :accept="['png']" :size="325000"></s-file-browser>
                        @error('upload_file')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12 form-button-row text-center pt-4 pb-2">
                        <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                            <i class="submit_button_icon fa fa-database fa-1-2x mr-1"></i>
                            <span class="iranyekan">ارسال و ویرایش</span>
                        </button>
                        <a role="button" href="{{ route("MenuItems.index") }}" class="btn btn-outline-secondary iranyekan">
                            <i class="fa fa-arrow-turn-right fa-1-2x mr-1"></i>
                            <span class="iranyekan">بازگشت به لیست</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
