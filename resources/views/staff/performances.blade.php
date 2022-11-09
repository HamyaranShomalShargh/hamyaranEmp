@extends('layouts.staff_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-calendar-check fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">کارکرد ماهانه پرسنل</h5>
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
                        <button class="btn btn-outline-info mr-2" data-toggle="modal" data-target="#new_performance_modal">
                            <i class="fa fa-plus-circle fa-1-2x mr-1"></i>
                            <span class="iransans create-button">کارکرد جدید</span>
                        </button>
                    </div>
                    <input type="text" class="form-control text-center iranyekan" placeholder="جستجو با قرارداد ، سال و ماه" data-table="main-table" v-on:input="filter_table">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
                    </div>
                </div>
            </div>
            <div style="overflow: hidden" class="p-3">
                <div id="table-scroll" class="table-scroll">
                    <table id="main-table" class="main-table" v-cloak data-filter="[1,2,3]">
                    <thead class="bg-dark white-color">
                    <tr class="iransans">
                        <th scope="col"><span>شماره</span></th>
                        <th scope="col"><span>قرارداد</span></th>
                        <th scope="col"><span>سال</span></th>
                        <th scope="col"><span>ماه</span></th>
                        <th scope="col"><span>موقعیت</span></th>
                        <th scope="col"><span>وضعیت</span></th>
                        <th scope="col"><span>پرسنل</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($performances as $performance)
                        <tr>
                            <td><span class="iranyekan">{{ $performance->id }}</span></td>
                            <td><span class="iranyekan">{{ "{$performance->contract->name}({$performance->contract->workplace})" }}</span></td>
                            <td><span class="iranyekan">{{ $performance->authorized_date->automation_year }}</span></td>
                            <td><span class="iranyekan">{{ $performance->authorized_date->month_name }}</span></td>
                            <td><span class="iranyekan">{{ $performance->current_role->name }}</span></td>
                            <td>
                                @if($performance->is_finished == 0 && $performance->is_committed == 0)
                                    <span class="iranyekan">منتظر ارسال</span>
                                @elseif($performance->is_finished == 0 && $performance->is_committed == 1)
                                    <span class="iranyekan">در جریان</span>
                                @else
                                    <span class="iranyekan">تکمیل شده</span>
                                @endif
                            </td>
                            <td><span class="iranyekan">{{ count($performance->performances) }}</span></td>
                            <td><span class="iranyekan">{{ $performance->user->name }}</span></td>
                            <td><span class="iranyekan">{{ verta($performance->created_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td><span class="iranyekan">{{ verta($performance->updated_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td>
                                @if($performance->role_priority == 1 && $performance->is_committed == 0)
                                    <div class="dropdown table-functions iranyekan">
                                        <button class="btn btn-outline-info dropdown-toggle iranyekan" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-cog fa-1-4x"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            @can("confirm","Performances")
                                                <form class="w-100" id="confirm-form-{{ $performance->id }}" action="{{ route("Performances.confirm",$performance->id) }}" method="POST" v-on:submit="submit_form">
                                                    @csrf
                                                    <button type="submit" form="confirm-form-{{ $performance->id }}" class="dropdown-item">
                                                        <i class="fa fa-check-circle"></i>
                                                        <span class="iranyekan">تایید و ارسال نهایی</span>
                                                    </button>
                                                </form>
                                            @endcan
                                            @can("edit","Performances")
                                                <div class="dropdown-divider"></div>
                                                <a role="button" href="{{ route("Performances.edit",$performance->id) }}" class="dropdown-item">
                                                    <i class="fa fa-edit"></i>
                                                    <span class="iranyekan">ویرایش</span>
                                                </a>
                                            @endcan
                                            @can("delete","Performances")
                                                <div class="dropdown-divider"></div>
                                                <form class="w-100" id="delete-form-{{ $performance->id }}" action="{{ route("Performances.destroy",$performance->id) }}" method="POST" v-on:submit="submit_form">
                                                    @csrf
                                                    @method("Delete")
                                                    <button type="submit" form="delete-form-{{ $performance->id }}" class="dropdown-item">
                                                        <i class="fa fa-trash"></i>
                                                        <span class="iranyekan">حذف</span>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                @else
                                    <i class="fa fa-times-circle red-color fa-1-2x"></i>
                                @endif
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="new_performance_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد کارکرد ماهانه جدید</h6>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("Performances.create") }}" method="GET">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    انتخاب قرارداد
                                </label>
                                <select class="form-control text-center iranyekan @error('contract_id') is-invalid @enderror selectpicker" data-container="body" size="5" data-live-search="true" name="contract_id">
                                    @forelse($contracts as $contract)
                                        <option value="{{ $contract->id }}">{{ "{$contract->name} - $contract->workplace" }}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('contract_id')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                        <i class="submit_button_icon fa fa-database fa-1-2x mr-1"></i>
                        <span class="iranyekan">انتخاب و ادامه</span>
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
