@extends('layouts.admin_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-list fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">سرفصل منو</h5>
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
                            <span class="iransans create-button">سرفصل جدید</span>
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
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($menu_headers as $menu_header)
                        <tr>
                            <td><span class="iranyekan">{{ $menu_header->id }}</span></td>
                            <td><span class="iranyekan">{{ $menu_header->name }}</span></td>
                            <td>
                                <span class="iranyekan">
                                     @if($menu_header->inactive == 1)
                                        <i class="fa fa-times-circle red-color fa-1-6x"></i>
                                    @elseif($menu_header->inactive == 0)
                                        <i class="fa fa-check-circle green-color fa-1-6x"></i>
                                    @endif
                                </span>
                            </td>
                            <td><span class="iranyekan">{{ $menu_header->user->name }}</span></td>
                            <td><span class="iranyekan">{{ verta($menu_header->cretaed_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td><span class="iranyekan">{{ verta($menu_header->updated_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td>
                                <div class="dropdown table-functions iranyekan">
                                    <button class="btn btn-outline-info dropdown-toggle iranyekan" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-cog fa-1-4x"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <form class="w-100" id="activation-form-{{ $menu_header->id }}" action="{{ route("MenuHeaders.activation",$menu_header->id) }}" method="POST" v-on:submit="submit_form">
                                            @csrf
                                            <button type="submit" form="activation-form-{{ $menu_header->id }}" class="dropdown-item">
                                                @if($menu_header->inactive == 0)
                                                    <i class="fa fa-lock"></i>
                                                    <span>غیر فعال سازی</span>
                                                @elseif($menu_header->inactive == 1)
                                                    <i class="fa fa-lock-open"></i>
                                                    <span>فعال سازی</span>
                                                @endif
                                            </button>
                                        </form>
                                        <div class="dropdown-divider"></div>
                                        <a role="button" href="{{ route("MenuHeaders.edit",$menu_header->id) }}" class="dropdown-item">
                                            <i class="fa fa-edit"></i>
                                            <span class="iranyekan">ویرایش</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form class="w-100" id="delete-form-{{ $menu_header->id }}" action="{{ route("MenuHeaders.destroy",$menu_header->id) }}" method="POST" v-on:submit="submit_form">
                                            @csrf
                                            @method("Delete")
                                            <button type="submit" form="delete-form-{{ $menu_header->id }}" class="dropdown-item">
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
                    <h6 class="modal-title iransans">ایجاد سرفصل جدید</h6>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("MenuHeaders.store") }}" method="POST" enctype="multipart/form-data" v-on:submit="submit_form">
                        @csrf
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
                                <label class="form-lbl iranyekan">نام مختصر</label>
                                <input class="form-control text-center" type="text" name="short_name" value="{{ old("short_name") }}">
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    مشخصه
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center iranyekan @error('slug') is-invalid @enderror" type="text" name="slug" value="{{ old("slug") }}">
                                @error('slug')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">اولویت نمایش</label>
                                <input class="form-control text-center iranyekan @error('priority') is-invalid @enderror" type="number" min="0" max="1000" name="priority" value="{{old("priority") ? old("priority") : 0}}">
                                @error('priority')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">آیکون</label>
                                <s-file-browser :accept="['png']" :size="325000"></s-file-browser>
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
