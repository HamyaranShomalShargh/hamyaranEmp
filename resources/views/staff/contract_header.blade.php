@extends('layouts.staff_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-file-contract fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">سرفصل قرارداد</h5>
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
                            <span class="iransans create-button">سرفصل جدید</span>
                        </button>
                    </div>
                    <input type="text" class="form-control text-center iranyekan" placeholder="جستجو با نام سرفصل">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
                    </div>
                </div>
            </div>
            <div class="table-responsive p-3">
                <table class="table table-striped static-table">
                    <thead class="bg-dark white-color">
                    <tr class="iransans">
                        <th scope="col"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col"><span>وضعیت</span></th>
                        <th scope="col"><span>شروع</span></th>
                        <th scope="col"><span>پایان</span></th>
                        <th scope="col"><span>مستندات</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($contract_headers as $contract_header)
                        <tr>
                            <td><span class="iranyekan">{{ $contract_header->id }}</span></td>
                            <td><span class="iranyekan">{{ $contract_header->name }}</span></td>
                            <td>
                                <span class="iranyekan">
                                     @if($contract_header->inactive == 1)
                                        <i class="fa fa-times-circle red-color fa-1-6x"></i>
                                    @elseif($contract_header->inactive == 0)
                                        <i class="fa fa-check-circle green-color fa-1-6x"></i>
                                    @endif
                                </span>
                            </td>
                            <td><span class="iranyekan">{{ $contract_header->start_date }}</span></td>
                            <td><span class="iranyekan">{{ $contract_header->end_date }}</span></td>
                            <td>
                                @if($contract_header->files)
                                    <a href="{{ route("ContractHeader.download",$contract_header->id) }}"><i class="fa fa-download fa-1-6x"></i></a>
                                @else
                                    <span><i class="fa fa-times fa-1-6x red-color"></i></span>
                                @endif
                            </td>
                            <td><span class="iranyekan">{{ $contract_header->user->name }}</span></td>
                            <td><span class="iranyekan">{{ verta($contract_header->cretaed_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td><span class="iranyekan">{{ verta($contract_header->updated_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td>
                                <div class="dropdown table-functions iranyekan">
                                    <button class="btn btn-outline-info dropdown-toggle iranyekan" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-cog fa-1-4x"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can("activation", "ContractHeader")
                                            <form class="w-100" id="activation-form-{{ $contract_header->id }}" action="{{ route("ContractHeader.activation",$contract_header->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                <button type="submit" form="activation-form-{{ $contract_header->id }}" class="dropdown-item">
                                                    @if($contract_header->inactive == 0)
                                                        <i class="fa fa-lock"></i>
                                                        <span>غیر فعال سازی</span>
                                                    @elseif($contract_header->inactive == 1)
                                                        <i class="fa fa-lock-open"></i>
                                                        <span>فعال سازی</span>
                                                    @endif
                                                </button>
                                            </form>
                                        @endcan
                                        @can("edit", "ContractHeader")
                                            <div class="dropdown-divider"></div>
                                            <a role="button" href="{{ route("ContractHeader.edit",$contract_header->id) }}" class="dropdown-item">
                                                <i class="fa fa-edit"></i>
                                                <span class="iranyekan">ویرایش</span>
                                            </a>
                                        @endcan
                                        @can("delete","ContractHeader")
                                            <div class="dropdown-divider"></div>
                                            <form class="w-100" id="delete-form-{{ $contract_header->id }}" action="{{ route("ContractHeader.destroy",$contract_header->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                @method("Delete")
                                                <button type="submit" form="delete-form-{{ $contract_header->id }}" class="dropdown-item">
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
                    <h6 class="modal-title iransans">ایجاد سرفصل جدید</h6>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("ContractHeader.store") }}" method="POST" enctype="multipart/form-data" v-on:submit="submit_form">
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
                                    شماره قراداد
                                </label>
                                <input class="form-control text-center iranyekan @error('number') is-invalid @enderror" type="text" name="number" value="{{ old("number") }}">
                                @error('number')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    شروع قرارداد
                                    <strong class="red-color">*</strong>
                                    <small class="text-muted">(فرمت صحیح : 1401/01/01)</small>
                                </label>
                                <input class="form-control text-center iranyekan @error('start_date') is-invalid @enderror persian_datepicker_range_from date_masked" data-mask="0000/00/00" type="text" name="start_date" value="{{ old("start_date") }}">
                                @error('start_date')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    پایان قرارداد
                                    <strong class="red-color">*</strong>
                                    <small class="text-muted">(فرمت صحیح : 1401/01/01)</small>
                                </label>
                                <input class="form-control text-center iranyekan @error('end_date') is-invalid @enderror persian_datepicker_range_to date_masked" data-mask="0000/00/00" type="text" name="end_date" value="{{ old("end_date") }}">
                                @error('end_date')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">مستندات قرارداد</label>
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
