@extends('layouts.staff_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-diagram-subtask fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">زیرمجموعه قرارداد</h5>
                    <span>(ایجاد، جستجو و ویرایش)</span>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-light">
                        <i class="fa fa-circle-question fa-1-4x green-color"></i>
                    </button>
                    <a role="button" href="{{ route("staff_idle") }}" class="btn btn-sm btn-outline-light">
                        <i class="fa fa-times fa-1-4x gray-color"></i>
                    </a>
                </div>
            </div>
            <div class="page-header">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-info mr-2" data-toggle="modal" data-target="#new_contract_modal">
                            <i class="fa fa-plus-circle fa-1-2x mr-1"></i>
                            <span class="iransans create-button">زیرمجموعه جدید</span>
                        </button>
                    </div>
                    <input type="text" class="form-control text-center iranyekan" placeholder="جستجو با نام زیرمجموعه و محل خدمت" data-table="search_table" v-on:input="filter_table">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
                    </div>
                </div>
            </div>
            <div class="table-responsive p-3">
                <table id="search_table" class="table table-striped static-table" data-filter="[1,5]">
                    <thead class="bg-dark white-color">
                    <tr class="iransans">
                        <th scope="col"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col"><span>وضعیت</span></th>
                        <th scope="col"><span>قرارداد</span></th>
                        <th scope="col"><span>وابستگی</span></th>
                        <th scope="col"><span>مستندات</span></th>
                        <th scope="col"><span>پرسنل</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($contract_subsets as $contract_subset)
                        <tr>
                            <td><span class="iranyekan">{{ $contract_subset->id }}</span></td>
                            <td><span class="iranyekan">{{ $contract_subset->name }}</span></td>
                            <td>
                                <span class="iranyekan">
                                     @if($contract_subset->inactive == 1)
                                        <i class="fa fa-times-circle red-color fa-1-6x"></i>
                                    @elseif($contract_subset->inactive == 0)
                                        <i class="fa fa-check-circle green-color fa-1-6x"></i>
                                    @endif
                                </span>
                            </td>
                            <td><span class="iranyekan">{{ $contract_subset->contract->name }}</span></td>
                            <td>
                                @if($contract_subset->parent)
                                    <span class="iranyekan">{{ $contract_subset->parent->name }}</span>
                                @else
                                    <span class="iranyekan">{{ "ندارد" }}</span>
                                @endif
                            </td>
                            <td>
                                @if($contract_subset->files)
                                    <a href="{{ route("ContractSubset.download",$contract_subset->id) }}"><i class="fa fa-download fa-1-6x"></i></a>
                                @else
                                    <span><i class="fa fa-times fa-1-6x red-color"></i></span>
                                @endif
                            </td>
                            <td>
                                @if($contract_subset->employees->isNotEmpty())
                                    <i class="fa fa-check-circle green-color fa-1-6x"></i>
                                @else
                                    <i class="fa fa-times-circle red-color fa-1-6x"></i>
                                @endif
                            </td>
                            <td><span class="iranyekan">{{ $contract_subset->user->name }}</span></td>
                            <td><span class="iranyekan">{{ verta($contract_subset->cretaed_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td><span class="iranyekan">{{ verta($contract_subset->updated_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td>
                                <div class="dropdown table-functions iranyekan">
                                    <button class="btn btn-outline-info dropdown-toggle iranyekan" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-cog fa-1-4x"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can("activation", "ContractSubset")
                                            <form class="w-100" id="activation-form-{{ $contract_subset->id }}" action="{{ route("ContractSubset.activation",$contract_subset->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                <button type="submit" form="activation-form-{{ $contract_subset->id }}" class="dropdown-item">
                                                    @if($contract_subset->inactive == 0)
                                                        <i class="fa fa-lock"></i>
                                                        <span>غیر فعال سازی</span>
                                                    @elseif($contract_subset->inactive == 1)
                                                        <i class="fa fa-lock-open"></i>
                                                        <span>فعال سازی</span>
                                                    @endif
                                                </button>
                                            </form>
                                        @endcan
                                        @can("edit", "ContractSubset")
                                            <div class="dropdown-divider"></div>
                                            <a role="button" href="{{ route("ContractSubset.edit",$contract_subset->id) }}" class="dropdown-item">
                                                <i class="fa fa-edit"></i>
                                                <span class="iranyekan">ویرایش</span>
                                            </a>
                                        @endcan
                                        @can("delete","ContractSubset")
                                            <div class="dropdown-divider"></div>
                                            <form class="w-100" id="delete-form-{{ $contract_subset->id }}" action="{{ route("ContractSubset.destroy",$contract_subset->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                @method("Delete")
                                                <button type="submit" form="delete-form-{{ $contract_subset->id }}" class="dropdown-item">
                                                    <i class="fa fa-trash"></i>
                                                    <span class="iranyekan">حذف</span>
                                                </button>
                                            </form>
                                        @endcan
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
    <div class="modal fade rtl" id="new_contract_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد زیرمجموعه جدید</h6>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("ContractSubset.store") }}" method="POST" enctype="multipart/form-data" v-on:submit="submit_form">
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
                                <label class="form-lbl iranyekan">
                                    قرارداد
                                </label>
                                <select class="form-control text-center iranyekan @error('contract_id') is-invalid @enderror selectpicker" data-container="body" title="انتخاب کنید" size="20" data-live-search="true" name="contract_id">
                                    @forelse($contract_headers as $header)
                                        <option @if($header->id == old("contract_id")) selected @endif value="{{ $header->id }}">{{ $header->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('contract_id')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    وابستگی
                                </label>
                                <select class="form-control text-center iranyekan @error('parent_id') is-invalid @enderror selectpicker" data-container="body" title="انتخاب کنید" size="20" data-live-search="true" name="parent_id">
                                    <option selected value="">ندارد</option>
                                    @forelse($contract_subsets as $subset)
                                        <option @if($subset->id == old("parent_id")) selected @endif value="{{ $subset->id }}">{{ $subset->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('parent_id')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    عنوان محل خدمت
                                </label>
                                <input class="form-control text-center iranyekan @error('workplace') is-invalid @enderror" type="text" name="workplace" value="{{ old("workplace") }}">
                                @error('workplace')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">عناوین کارکرد ماهیانه</label>
                                <select class="form-control iranyekan text-center selectpicker" data-container="body" data-live-search="true" id="performance_attributes_id" name="performance_attributes_id" title="انتخاب کنید" data-size="20">
                                    @forelse($table_attributes as $attribute)
                                        <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">عناوین وضعیت ماهیانه</label>
                                <select class="form-control iranyekan text-center selectpicker" data-container="body" data-live-search="true" id="invoice_attributes_id" name="invoice_attributes_id" title="انتخاب کنید" data-size="20">
                                    @forelse($table_attributes as $attribute)
                                        <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">عناوین روکش وضعیت</label>
                                <select class="form-control iranyekan text-center selectpicker" data-container="body" data-live-search="true" id="invoice_cover_id" name="invoice_cover_id" title="انتخاب کنید" data-size="20">
                                    @forelse($invoice_cover_titles as $title)
                                        <option value="{{ $title->id }}">{{ $title->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label class="col-form-label iranyekan black_color" for="short_name">گردش کارکرد</label>
                                <select class="form-control iranyekan text-center selectpicker" data-container="body" data-live-search="true" id="performance_flow_id" name="performance_flow_id" title="انتخاب کنید" data-size="20">
                                    @forelse($automation_flows as $p_flow)
                                        <option value="{{ $p_flow->id }}">{{ $p_flow->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label class="col-form-label iranyekan black_color" for="short_name">گردش وضعیت</label>
                                <select class="form-control iranyekan text-center selectpicker" data-container="body" data-live-search="true" id="invoice_flow_id" name="invoice_flow_id" title="انتخاب کنید" data-size="20">
                                    @forelse($automation_flows as $i_flow)
                                        <option value="{{ $i_flow->id }}">{{ $i_flow->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-group col-12 col-lg-4">
                                <label class="form-lbl iranyekan">
                                    روز مجاز ثبت کارکرد
                                </label>
                                <input class="form-control text-center iranyekan @error('registration_start_day') is-invalid @enderror" type="number" min="1" name="registration_start_day" value="{{ old("registration_start_day") }}">
                                @error('registration_start_day')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-lg-4">
                                <label class="form-lbl iranyekan">
                                    روز پایان ثبت کارکرد
                                </label>
                                <input class="form-control text-center iranyekan @error('registration_final_day') is-invalid @enderror" type="number" min="2" name="registration_final_day" value="{{ old("registration_final_day") }}">
                                @error('registration_final_day')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-lg-4">
                                <label class="form-lbl iranyekan">
                                    مجموع اضافه کار مجاز ماهانه
                                </label>
                                <input class="form-control text-center iranyekan @error('overtime_registration_limit') is-invalid @enderror" type="number" min="0" name="overtime_registration_limit" value="{{ old("overtime_registration_limit") }}">
                                @error('overtime_registration_limit')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">مستندات زیرمجموعه قرارداد</label>
                                <m-file-browser :accept="['png','jpg','bmp','tiff','pdf','xlsx','txt']" :size="325000"></m-file-browser>
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
