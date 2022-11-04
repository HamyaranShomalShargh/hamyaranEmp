@extends('layouts.admin_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-table-list fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">عناوین پیش فرض</h5>
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
                    <input type="text" class="form-control text-center iranyekan" data-table="main_table" placeholder="جستجو با نام و نوع" v-on:input="filter_table">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
                    </div>
                </div>
            </div>
            <div id="table_responsive" class="table-responsive p-3">
                <table id="main_table" class="table table-striped static-table" data-filter="[1,2]">
                    <thead class="bg-dark white-color">
                    <tr class="iransans">
                        <th scope="col"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col"><span>نوع</span></th>
                        <th scope="col"><span>دسته بندی</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($default_table_attributes as $attribute)
                        <tr>
                            <td><span class="iranyekan">{{ $attribute->id }}</span></td>
                            <td><span class="iranyekan">{{ $attribute->name }}</span></td>
                            <td>
                                <span class="iranyekan">
                                    @if($attribute->type == "performance")
                                        {{ "کارکرد ماهیانه" }}
                                    @elseif($attribute->type == "invoice")
                                        {{ "وضعیت" }}
                                    @elseif($attribute->type == "invoice_cover")
                                        {{ "روکش وضعیت" }}
                                    @else
                                        {{ "نامشخص" }}
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span class="iranyekan">
                                    @if($attribute->category == "function")
                                        {{ "کارکرد" }}
                                    @elseif($attribute->category == "advantage")
                                        {{ "مزایا" }}
                                    @elseif($attribute->category == "deduction")
                                        {{ "کسورات" }}
                                    @else
                                        {{ $attribute->category }}
                                    @endif
                                </span>
                            </td>
                            <td><span class="iranyekan">{{ $attribute->user->name }}</span></td>
                            <td><span class="iranyekan">{{ verta($attribute->cretaed_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td><span class="iranyekan">{{ verta($attribute->updated_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td>
                                <div class="dropdown table-functions iranyekan">
                                    <button class="btn btn-outline-info dropdown-toggle iranyekan" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-cog fa-1-4x"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a role="button" href="{{ route("DefaultTableAttributes.edit",$attribute->id) }}" class="dropdown-item">
                                            <i class="fa fa-edit"></i>
                                            <span class="iranyekan">ویرایش</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form class="w-100" id="delete-form-{{ $attribute->id }}" action="{{ route("DefaultTableAttributes.destroy",$attribute->id) }}" method="POST" v-on:submit="submit_form">
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
                    <form id="main_submit_form" class="p-3" action="{{ route("DefaultTableAttributes.store") }}" method="POST" v-on:submit="submit_form">
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
                                <select class="form-control iranyekan @error('type') is-invalid @enderror" name="type">
                                    <option value="performance">کارکرد</option>
                                    <option value="invoice">وضعیت</option>
                                    <option value="invoice_cover">روکش وضعیت</option>
                                </select>
                                @error('type')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    دسته بندی
                                    <strong class="red-color">*</strong>
                                </label>
                                <select class="form-control iranyekan @error('category') is-invalid @enderror" name="category">
                                    <option value="ندارد">ندارد</option>
                                    <option value="function">کارکرد</option>
                                    <option value="advantage">مزایا</option>
                                    <option value="deduction">کسورات</option>
                                </select>
                                @error('category')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    نوع مقدار
                                    <strong class="red-color">*</strong>
                                </label>
                                <select class="form-control iranyekan @error('kind') is-invalid @enderror" name="kind">
                                    <option value="number">عددی</option>
                                    <option value="text">متنی</option>
                                </select>
                                @error('kind')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
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
