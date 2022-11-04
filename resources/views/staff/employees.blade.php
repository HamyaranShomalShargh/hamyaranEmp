@extends('layouts.staff_dashboard')
@push('scripts')
    <script>
        let table_data = @json($employees);
    </script>
@endpush
@section('content')
    <div class="page w-100 pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-StaffUsers fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">پرسنل</h5>
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
                <div class="form-row">
                    <div class="col-12 mb-2">
                        <label class="form-lbl iranyekan">
                            <input checked type="radio" name="employee_list_selection" value="contract" v-model="employee_list_selection">
                            قرارداد
                        </label>
                        <select class="form-control text-center selectpicker iranyekan" title="انتخاب کنید" data-size="10" data-live-search="true" v-on:change="reset_employees_table" id="contract_subset_id" v-on:change="reset_employees_table">
                            @forelse($contracts as $contract)
                                <optgroup label="{{ $contract["name"] }}">
                                    @forelse($contract["data"] as $subset)
                                        <option value="{{ $subset["id"] }}">
                                            @if($subset["child_name"])
                                                {{ $subset["contract_subset"]." - ".$subset["child_name"]."({$subset['workplace']})" }}
                                            @else
                                                {{ $subset["contract_subset"]."({$subset['workplace']})" }}
                                            @endif
                                        </option>
                                    @empty
                                    @endforelse
                                </optgroup>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>
            </div>
            <div class="page-header">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-info mr-2" data-toggle="modal" data-target="#add_employee_modal">
                            <i class="fa fa-user-plus fa-1-2x mr-1"></i>
                            <span class="iransans create-button">ایجاد</span>
                        </button>
                        <button class="btn btn-outline-info mr-2" data-toggle="modal" data-target="#select_contract_modal">
                            <i class="fa fa-users-cog fa-1-2x mr-1"></i>
                            <span class="iransans create-button">عملیات جمعی</span>
                        </button>
                    </div>
                    <input type="text" class="form-control text-center iranyekan" data-table="main-table" placeholder="جستجو با نام و کد ملی" v-on:input="filter_table">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
                    </div>
                </div>
            </div>
            <div style="overflow: hidden" class="p-3">
                <div id="table-scroll" class="table-scroll iranyekan">
                    <table id="main-table" class="main-table" v-cloak data-filter="[1,2,3]">
                        <thead class="bg-dark white-color">
                        <tr class="iransans">
                            <th scope="col"><span>شماره</span></th>
                            <th scope="col"><span>نام</span></th>
                            <th scope="col"><span>نام خانوادگی</span></th>
                            <th scope="col"><span>کد ملی</span></th>
                            <th scope="col"><span>قرارداد</span></th>
                            <th scope="col"><span>توسط</span></th>
                            <th scope="col"><span>وضعیت</span></th>
                            <th scope="col"><span>تاریخ ثبت</span></th>
                            <th scope="col"><span>تاریخ ویرایش</span></th>
                            <th scope="col"><span>عملیات</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="row in filtered_data" :key='row.id'>
                            <td>
                                <span>@{{ row.id }}</span>
                            </td>
                            <td>
                                <input class="form-control iranyekan text-center" readonly :id="`first_name_${row.id}`" v-on:click="editable_input" v-on:blur="read_only" :value="row.first_name"/>
                            </td>
                            <td>
                                <input class="form-control iranyekan text-center" readonly :id="`last_name_${row.id}`" v-on:click="editable_input" v-on:blur="read_only" :value="row.last_name"/>
                            </td>
                            <td>
                                <input class="form-control iranyekan text-center" readonly :id="`national_code_${row.id}`" v-on:click="editable_input" v-on:blur="read_only" :value="row.national_code"/>
                            </td>
                            <td>
                                <span>@{{ row.contract.name }}</span>
                            </td>
                            <td>
                                <span>@{{ row.user.name }}</span>
                            </td>
                            <td>
                                <span v-html="check_unemployed(row.unemployed)"></span>
                            </td>
                            <td>
                                <span>@{{ to_persian_date(row.created_at) }}</span>
                            </td>
                            <td>
                                <span>@{{ to_persian_date(row.updated_at) }}</span>
                            </td>
                            <td>
                                <div class="dropdown table-functions">
                                    <button class="btn btn-outline-info dropdown-toggle iranyekan" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-cog fa-1-4x"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <axios-button :class="'dropdown-item'" :route="'{{route("EmployeeEdit")}}'" :required="['#first_name','#last_name','#national_code']" :action="'edit'" :elements="['#first_name','#last_name','#national_code']"  :message="'آیا برای ویرایش اطلاعات اطمینان دارید؟'" :record_id="row.id">
                                            <i class="fas fa-edit fa-1-2x"></i>
                                            <span class="iranyekan">ویرایش جزئی</span>
                                        </axios-button>
                                        <div class="dropdown-divider"></div>
                                        <a :href="row.edit_url" class="dropdown-item">
                                            <i class="fas fa-user-edit fa-1-2x"></i>
                                            <span class="iranyekan">ویرایش کلی</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <axios-button :class="'dropdown-item'" :route="'{{route("EmployeeActivation")}}'" :action="'delete'" :message="'آیا برای فعال/غیرفعالسازی پرسنل اطمینان دارید؟'" :record_id="row.id">
                                            <i v-if="row.unemployed === 0" class="fas fa-lock fa-1-2x"></i>
                                            <i v-if="row.unemployed === 1" class="fas fa-lock-open fa-1-2x"></i>
                                            <span v-if="row.unemployed === 0" class="iranyekan">غیرفعال سازی</span>
                                            <span v-if="row.unemployed === 1" class="iranyekan">فعال سازی</span>
                                        </axios-button>
                                        <div class="dropdown-divider"></div>
                                        <axios-button :class="'dropdown-item'" :route="'{{route("EmployeeDelete")}}'" :action="'delete'" :message="'آیا برای حذف پرسنل اطمینان دارید؟'" :record_id="row.id">
                                            <i class="fas fa-trash fa-1-2x"></i>
                                            <span class="iranyekan">حذف پرسنل</span>
                                        </axios-button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="select_contract_modal" data-backdrop="static" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">عملیات جمعی</h5>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-12">
                            <label class="col-form-label iranyekan">
                                <input checked type="radio" name="source" value="contract">
                                قرارداد
                            </label>
                            <select class="form-control text-center selectpicker iranyekan mb-4" title="انتخاب قرارداد" data-size="10" data-live-search="true" id="main_overall_contract_subset_id">
                                @forelse($contracts as $contract)
                                    <optgroup label="{{ $contract["name"] }}">
                                        @forelse($contract["data"] as $subset)
                                            <option value="{{ $subset["id"] }}">
                                                @if($subset["child_name"])
                                                    {{ $subset["contract_subset"]." - ".$subset["child_name"]."({$subset['workplace']})" }}
                                                @else
                                                    {{ $subset["contract_subset"]."({$subset['workplace']})" }}
                                                @endif
                                            </option>
                                        @empty
                                        @endforelse
                                    </optgroup>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <ul class="nav nav-tabs employee-tab pr-2 pl-2" role="tablist">
                        <li class="nav-item active">
                            <a class="nav-link active" id="add_employee-tab" data-toggle="tab" href="#add_employee" role="tab" aria-controls="add_employee" aria-selected="true">
                                <i class="menu-header-icon fa fa-users-medical fa-2x" title="اضافه کردن پرسنل جدید"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="delete_employee-tab" data-toggle="tab" href="#delete_employee" role="tab" aria-controls="delete_employee" aria-selected="true">
                                <i class="menu-header-icon fa fa-users-slash fa-2x" title="حذف پرسنل قرارداد"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="change_employee-tab" data-toggle="tab" href="#change_employee" role="tab" aria-controls="change_employee" aria-selected="true">
                                <i class="menu-header-icon fa fa-refresh fa-2x" title="تغییر قرارداد پرسنل به صورت گروهی"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="advanced_search-tab" data-toggle="tab" href="#advanced_search" role="tab" aria-controls="advanced_search" aria-selected="true">
                                <i class="menu-header-icon fa fa-filter fa-2x" title="جستجوی پیشرفته"></i>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="add_employee" role="tabpanel" aria-labelledby="add_employee-tab">
                            <div class="form-row">
                                <div class="col-md-12">
                                    <h5 class="iranyekan mt-4">
                                        <i class="fa fa-users fa-1-2x"></i>
                                        به صورت گروهی
                                        <a href="{{ route("Employees.export_new_employee_excel") }}" class="iranyekan">(دانلود فایل اکسل نمونه)</a>
                                    </h5>
                                    <s-file-browser :file_box_name="'new_employee_excel_file'" :file_box_id="'new_employee_excel_file'" :filename_box_id="'new_excel_file_box'" :size="500000" :accept="['xlsx','xls']"></s-file-browser>
                                    <div class="text-center p-2">
                                        <axios-button :class="'btn btn-outline-primary'" :route="'{{ route("EmployeeAddAll") }}'" :action="'add'" :required="['#main_overall_contract_subset_id','#new_employee_excel_file']" :elements="['#main_overall_contract_subset_id','#new_employee_excel_file']"   :message="'آیا برای بارگذاری اطلاعات اطمینان دارید؟'">
                                            <span class="iranyekan">بارگذاری و ایجاد پرسنل جدید</span>
                                        </axios-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="delete_employee" role="tabpanel" aria-labelledby="delete_employee-tab">
                            <div class="form-row pt-3 pb-3">
                                <div class="col-12">
                                    <div class="col-12">
                                        <h5 class="iranyekan mt-4">
                                            <i class="fa fa-trash fa-1-2x"></i>
                                            حذف کلیه پرسنل قرارداد
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-12 text-center">
                                    <axios-button :class="'btn btn-outline-danger w-100'" :route="'{{ route("EmployeeDeleteAll") }}'" :action="'delete'" :required="['#main_overall_contract_subset_id']" :elements="['#main_overall_contract_subset_id']"  :message="'آیا برای حذف کلیه پرسنل قرارداد اطمینان دارید؟'">
                                        <span class="iranyekan">حذف پرسنل قرارداد</span>
                                    </axios-button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="change_employee" role="tabpanel" aria-labelledby="change_employee-tab">
                            <div class="form-row pt-3 pb-3">
                                <div class="col-12">
                                    <h5 class="iranyekan mt-4">
                                        <i class="fa fa-file-contract fa-1-2x"></i>
                                        تغییر گروهی قرارداد پرسنل
                                        <a href="{{ route("Employees.export_change_contract_employee_excel") }}" class="iranyekan">(دانلود فایل اکسل نمونه)</a>
                                    </h5>
                                </div>
                                <div class="col-12">
                                    <s-file-browser :file_box_name="'change_employee_contract_excel_file'" :file_box_id="'change_employee_contract_excel_file'" :filename_box_id="'change_excel_file_box'" :size="500000" :accept="['xlsx','xls']"></s-file-browser>
                                </div>
                                <div class="col-12 text-center p-3">
                                    <axios-button :class="'btn btn-outline-primary'" :route="'{{ route("EmployeeChangeContract") }}'" :action="'change'" :required="['#main_overall_contract_subset_id','#change_employee_contract_excel_file']" :elements="['#main_overall_contract_subset_id','#change_employee_contract_excel_file']"  :message="'آیا برای بارگذاری اطلاعات اطمینان دارید؟'">
                                        <span class="iranyekan">بارگذاری و تغییر قرارداد پرسنل</span>
                                    </axios-button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="advanced_search" role="tabpanel" aria-labelledby="advanced_search-tab">
                            <div class="form-row">
                                <div class="col-md-12">
                                    <h5 class="iranyekan mt-4 mb-3">
                                        <i class="fa fa-search fa-1-2x"></i>
                                        جستجوی پیشرفته در کل پرسنل
                                    </h5>
                                    <div class="col-12">
                                        <div class="input-group mb-2">
                                            <div class="input-group-append">
                                                <span class="input-group-text iranyekan" id="basic-addon1" style="min-width: 142px">
                                                    جستجو با نام
                                                </span>
                                            </div>
                                            <input type="text" id="filter_first_name" class="form-control iranyekan text-center">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group mb-2">
                                            <div class="input-group-append">
                                                <span class="input-group-text iranyekan" id="basic-addon1" style="min-width: 142px">
                                                    جستجو با نام خانوادگی
                                                </span>
                                            </div>
                                            <input type="text" id="filter_last_name" class="form-control iranyekan text-center">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group mb-2">
                                            <div class="input-group-append">
                                                <span class="input-group-text iranyekan" id="basic-addon1" style="min-width: 142px">
                                                    جستجو با کد ملی
                                                </span>
                                            </div>
                                            <input type="text" id="filter_national_code" class="form-control iranyekan text-center">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group mb-2">
                                            <div class="input-group-append">
                                                <span class="input-group-text iranyekan" id="basic-addon1" style="min-width: 142px">
                                                    جستجو با موبایل
                                                </span>
                                            </div>
                                            <input type="text" id="filter_mobile" class="form-control iranyekan text-center">
                                        </div>
                                    </div>
                                    <div class="text-center p-2">
                                        <axios-button :class="'btn btn-outline-primary'" :route="'{{ route("EmployeeSearch") }}'" :action="'search'" :elements="['#filter_first_name','#filter_last_name','#filter_national_code','#filter_mobile']"   :message="'آیا برای جستجو در اطلاعات اطمینان دارید؟'">
                                            <span class="iranyekan">جستجو در اطلاعات پرسنل</span>
                                        </axios-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary iranyekan" data-dismiss="modal">
                        <i class="fa fa-times fa-1-2x mr-1"></i>
                        <span class="iranyekan">بازگشت</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade rtl" id="add_employee_modal" data-backdrop="static" tabindex="-1" aria-labelledby="exampleModalCenterTitle" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد پرسنل جدید</h6>
                </div>
                <div class="modal-body scroll-style">
                    <div class="form-row">
                        <div class="col-md-12">
                            <label class="form-lbl iranyekan">
                                قرارداد
                            </label>
                            <select class="form-control text-center selectpicker iranyekan" title="انتخاب کنید" data-size="10" data-live-search="true" id="main_contract_subset_id">
                                @forelse($contracts as $contract)
                                    <optgroup label="{{ $contract["name"] }}">
                                        @forelse($contract["data"] as $subset)
                                            <option value="{{ $subset["id"] }}">
                                                @if($subset["child_name"])
                                                    {{ $subset["contract_subset"]." - ".$subset["child_name"]."({$subset['workplace']})" }}
                                                @else
                                                    {{ $subset["contract_subset"]."({$subset['workplace']})" }}
                                                @endif
                                            </option>
                                        @empty
                                        @endforelse
                                    </optgroup>
                                @empty
                                @endforelse
                            </select>
                            <label class="col-form-label iranyekan">نام</label>
                            <input class="form-control iranyekan text-center" type="text" id="new_first_name">
                            <label class="col-form-label iranyekan">نام خانوادگی</label>
                            <input class="form-control iranyekan text-center" type="text" id="new_last_name">
                            <label class="col-form-label iranyekan">جنسیت</label>
                            <select class="form-control iranyekan text-center" id="new_gender">
                                <option value="m">مرد</option>
                                <option value="f">زن</option>
                            </select>
                            <label class="col-form-label iranyekan">کد ملی</label>
                            <input class="form-control iranyekan text-center number_masked" type="text" data-mask="0000000000" id="new_national_code">
                            <label class="col-form-label iranyekan">شماره شناسنامه</label>
                            <input class="form-control iranyekan text-center number_masked" type="text" data-mask="0000000000" id="new_id_number">
                            <label class="col-form-label iranyekan">تاریخ تولد</label>
                            <input class="form-control iranyekan text-center date_masked" type="text" data-mask="0000/00/00" id="new_birth_date">
                            <label class="col-form-label iranyekan">محل تولد</label>
                            <input class="form-control iranyekan text-center" type="text" id="new_birth_city">
                            <label class="col-form-label iranyekan">تحصیلات</label>
                            <select class="form-control iranyekan text-center" id="new_education">
                                <option value="دیپلم و زیردیپلم">دیپلم و زیردیپلم</option>
                                <option value="فوق دیپلم">فوق دیپلم</option>
                                <option value="لیسانس">لیسانس</option>
                                <option value="فوق لیسانس">فوق لیسانس</option>
                                <option value="دکتری">دکتری</option>
                                <option value="فوق دکتری">فوق دکتری</option>
                            </select>
                            <label class="col-form-label iranyekan">خدمت سربازی</label>
                            <select class="form-control iranyekan text-center" id="new_military_status">
                                <option value="h">کارت پایان خدمت</option>
                                <option value="e">معاف</option>
                                <option value="n">انجام نشده</option>
                            </select>
                            <label class="col-form-label iranyekan">وضعیت تاهل</label>
                            <select class="form-control iranyekan text-center" id="new_marital_status">
                                <option value="m">متاهل</option>
                                <option value="s">مجرد</option>
                            </select>
                            <label class="col-form-label iranyekan">فرزند</label>
                            <input class="form-control iranyekan text-center" type="number" min="0" id="new_children_number">
                            <label class="col-form-label iranyekan">شماره بیمه</label>
                            <input class="form-control iranyekan text-center number_masked" data-mask="000000000000000000000" type="text" id="new_insurance_number">
                            <label class="col-form-label iranyekan">سابقه بیمه</label>
                            <input class="form-control iranyekan text-center" type="number" min="1" id="new_insurance_days">
                            <label class="col-form-label iranyekan">حقوق پایه</label>
                            <input class="form-control iranyekan text-center thousand_separator" type="text" id="new_basic_salary">
                            <label class="col-form-label iranyekan">دستمزد روزانه</label>
                            <input class="form-control iranyekan text-center thousand_separator" type="text" id="new_daily_wage">
                            <label class="col-form-label iranyekan">بن ماهیانه</label>
                            <input class="form-control iranyekan text-center thousand_separator" type="text" id="new_worker_credit">
                            <label class="col-form-label iranyekan">کمک هزینه مسکن</label>
                            <input class="form-control iranyekan text-center thousand_separator" type="text" id="new_housing_credit">
                            <label class="col-form-label iranyekan">حق اولاد</label>
                            <input class="form-control iranyekan text-center thousand_separator" type="text" id="new_child_credit">
                            <label class="col-form-label iranyekan">گروه شغلی</label>
                            <input class="form-control iranyekan text-center" type="number" min="1" id="new_job_group">
                            <label class="col-form-label iranyekan">نام بانک</label>
                            <input class="form-control iranyekan text-center" type="text" id="new_bank_name">
                            <label class="col-form-label iranyekan">شماره حساب</label>
                            <input class="form-control iranyekan text-center number_masked" type="text" data-mask="0000000000000000000000000" id="new_bank_account">
                            <label class="col-form-label iranyekan">شماره کارت</label>
                            <input class="form-control iranyekan text-center number_masked" type="text" data-mask="0000-0000-0000-0000" id="new_credit_card">
                            <label class="col-form-label iranyekan">شماره شبا</label>
                            <input class="form-control iranyekan text-center number_masked" type="text" data-mask="IR00-0000-0000-0000-0000-0000-00" id="new_sheba_number">
                            <label class="col-form-label iranyekan">تلفن</label>
                            <input class="form-control iranyekan text-center number_masked" type="text" data-mask="000-00000000" id="new_phone">
                            <label class="col-form-label iranyekan">موبایل</label>
                            <input class="form-control iranyekan text-center number_masked" type="text" data-mask="00000000000" id="new_mobile">
                            <label class="col-form-label iranyekan">آدرس</label>
                            <input class="form-control iranyekan text-center" type="text" id="new_address">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <axios-button :class="'btn btn-outline-primary'" :route="'{{ route("EmployeeAdd") }}'" :action="'add'" :required="['#main_contract_subset_id','#new_first_name','#new_last_name','#new_national_code']" :elements="['#new_first_name','#new_last_name','#new_gender','#new_national_code','#new_id_number','#new_birth_date','#new_birth_city','#new_education','#new_marital_status','#new_children_number','#new_insurance_number','#new_insurance_days','#new_military_status','#new_basic_salary','#new_daily_wage','#new_worker_credit','#new_housing_credit','#new_child_credit','#new_job_group','#new_bank_name','#new_bank_account','#new_credit_card','#new_sheba_number','#new_phone','#new_mobile','#new_address','#main_contract_subset_id']"  :message="'آیا برای ایجاد پرسنل جدید اطمینان دارید؟'">
                        <span class="iranyekan">ارسال و ایجاد پرسنل جدید</span>
                    </axios-button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="import_errors" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans" id="exampleModalLongTitle">مشکلات بارگذاری فایل اکسل</h6>
                </div>
                <div class="modal-body scroll-style">
                    <table class="table table-bordered text-center w-100 iransans">
                        <thead class="thead-dark">
                        <tr>
                            <th>ردیف فایل</th>
                            <th>مقدار</th>
                            <th>پیام خطا</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="error in import_errors" :key="error.row">
                            <td>@{{ error.row }}</td>
                            <td>@{{ error.value }}</td>
                            <td>@{{ error.message }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
@endsection
