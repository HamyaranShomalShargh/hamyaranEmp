@extends('layouts.staff_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-table-pivot fa-1-6x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">تغییرات مزایای پرسنل</h5>
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
                            <span class="iransans create-button">وضعیت جدید</span>
                        </button>
                    </div>
                    <input type="text" class="form-control text-center iranyekan" placeholder="جستجو با قرارداد ، سال و ماه" data-table="search_table" v-on:input="filter_table">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
                    </div>
                </div>
            </div>
            <div class="table-responsive p-3" style="min: auto">
                <table id="search_table" class="table table-striped static-table" data-filter="[1,2,3]">
                    <thead class="bg-dark white-color">
                    <tr class="iransans">
                        <th scope="col"><span>شماره</span></th>
                        <th scope="col"><span>پرسنل</span></th>
                        <th scope="col"><span>کد ملی</span></th>
                        <th scope="col"><span>قرارداد</span></th>
                        <th scope="col"><span>درخواست</span></th>
                        <th scope="col"><span>نوع</span></th>
                        <th scope="col"><span>موقعیت</span></th>
                        <th scope="col"><span>وضعیت</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($automations as $automation)
                        <tr>
                            <td><span class="iranyekan">{{ $automation->id }}</span></td>
                            <td><span class="iranyekan">{{ "{$automation->employee->first_name} {$automation->employee->last_name}" }}</span></td>
                            <td><span class="iranyekan">{{ $automation->employee->national_code }}</span></td>
                            <td><span class="iranyekan">{{ $automation->contract }}</span></td>
                            <td><span class="iranyekan">{{ $automation->advantage_form }}</span></td>
                            <td>
                                <span class="iranyekan">
                                    @if($automation->type == "add")
                                        {{ "اضافه" }}
                                    @else
                                        {{ "حذف" }}
                                    @endif
                                </span>
                            </td>
                            <td><span class="iranyekan">{{ $automation->current_role->name }}</span></td>
                            <td>
                                @if($automation->is_finished == 0 && $automation->is_committed == 0)
                                    <span class="iranyekan">منتظر ارسال</span>
                                @elseif($automation->is_finished == 0 && $automation->is_committed == 1)
                                    <span class="iranyekan">در جریان</span>
                                @else
                                    <span class="iranyekan">تکمیل شده</span>
                                @endif
                            </td>
                            <td><span class="iranyekan">{{ $automation->user->name }}</span></td>
                            <td><span class="iranyekan">{{ verta($automation->cretaed_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td><span class="iranyekan">{{ verta($automation->updated_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td>
                                @if($automation->role_priority == 1 && $automation->is_committed == 0)
                                    <div class="dropdown table-functions">
                                        <button class="btn btn-outline-info dropdown-toggle iranyekan" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-cog fa-1-4x"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            @can("confirm","AdvantageForms")
                                                <form class="w-100" id="confirm-form-{{ $automation->id }}" action="{{ route("AdvantageForms.confirm",$automation->id) }}" method="POST" v-on:submit="submit_form">
                                                    @csrf
                                                    <button type="submit" form="confirm-form-{{ $automation->id }}" class="dropdown-item">
                                                        <i class="fa fa-check-circle"></i>
                                                        <span class="iranyekan">تایید و ارسال نهایی</span>
                                                    </button>
                                                </form>
                                            @endcan
                                            @can("edit","AdvantageForms")
                                                <div class="dropdown-divider"></div>
                                                <a role="button" href="{{ route("AdvantageForms.edit",$automation->id) }}" class="dropdown-item">
                                                    <i class="fa fa-edit"></i>
                                                    <span class="iranyekan">ویرایش</span>
                                                </a>
                                            @endcan
                                            @can("delete","AdvantageForms")
                                                <div class="dropdown-divider"></div>
                                                <form class="w-100" id="delete-form-{{ $automation->id }}" action="{{ route("AdvantageForms.destroy",$automation->id) }}" method="POST" v-on:submit="submit_form">
                                                    @csrf
                                                    @method("Delete")
                                                    <button type="submit" form="delete-form-{{ $automation->id }}" class="dropdown-item">
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
@endsection
@section('modals')
    <div class="modal fade rtl" id="new_performance_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد درخواست تغییر مزایای جدید</h6>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("AdvantageForms.create") }}" method="GET">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    انتخاب پرسنل
                                </label>
                                <select class="form-control text-center iranyekan @error('employee_id') is-invalid @enderror selectpicker" data-container="body" size="5" data-live-search="true" name="employee_id">
                                    @forelse($employees as $employee)
                                        <option @if(old("employee_id") && old("employee_id") == $employee->id) selected @endif value="{{ $employee->id }}">{{ "{$employee->first_name} {$employee->last_name} ({$employee->national_code})" }}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('employee_id')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    انتخاب فرم تغییرات مزایا
                                </label>
                                <select class="form-control text-center iranyekan @error('advantage_id') is-invalid @enderror selectpicker" data-container="body" size="5" data-live-search="true" name="advantage_id">
                                    @forelse($advantages as $advantage)
                                        <option @if(old("advantage_id") && old("advantage_id") == $advantage->id) selected @endif value="{{ $advantage->id }}">{{ $advantage->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('advantage_id')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                        <i class="submit_button_icon fa fa-arrow-right fa-1-2x mr-1"></i>
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
