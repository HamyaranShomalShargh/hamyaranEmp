@extends('layouts.staff_dashboard')
@push('scripts')
    <script>
        let table_data = @json($contract_subset);
    </script>
@endpush
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-calendar-check fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">کارکرد ماهانه پرسنل</h5>
                    <span>(ایجاد)</span>
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
                    <div class="input-group-prepend" style="border-radius: 0">
                        <a href="{{ route("Performances.performance_export_excel",$contract_subset->id) }}" class="btn btn-outline-info mr-2" style="border-radius: 0.25rem">
                            <i class="fa fa-download fa-1-2x mr-1"></i>
                            <span class="iransans create-button">دانلود</span>
                        </a>
                        @can("create","Performances")
                            <button class="btn btn-outline-primary mr-2" data-toggle="modal" data-target="#upload_performance_excel_modal" style="border-radius: 0.25rem">
                                <i class="fa fa-upload fa-1-2x mr-1"></i>
                                <span class="iransans create-button">بارگذاری</span>
                            </button>
                        @endcan
                    </div>
                    <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-table-cells fa-1-2x"></i></span>
                    </div>
                    <input type="text" class="form-control iranyekan" readonly value="{{ "کارکرد ماهانه پرسنل ".$contract_subset->name."(".$contract_subset->workplace.") ".$authorized_date["month_name"]." ماه سال ".$authorized_date["automation_year"] }}">
                    <div class="input-group-prepend bg-white">
                        <span class="input-group-text bg-dark" style="border-radius: 0" id="basic-addon1">
                            <span class="iranyekan white-color">
                                <i class="fa fa-users"></i>
                                <span>تعداد پرسنل</span>
                            </span>
                        </span>
                    </div>
                    <div class="input-group-prepend bg-white">
                        <span class="input-group-text bg-secondary white-color" style="border-radius: 0" id="basic-addon1">
                            <span class="iranyekan">{{ count($contract_subset->employees) }}</span>
                        </span>
                    </div>
                    <div class="input-group-prepend bg-white">
                        <span class="input-group-text bg-dark" style="border-radius: 0" id="basic-addon1">
                            <span class="iranyekan white-color">
                                <i class="fa fa-timer"></i>
                                <span>اضافه کار(مجاز و مجموع)</span>
                            </span>
                        </span>
                    </div>
                    <div class="input-group-prepend bg-white">
                        <span class="input-group-text bg-secondary white-color" style="border-radius: 0" id="basic-addon1">
                            <span class="iranyekan">{{ $contract_subset->overtime_registration_limit }}</span>
                        </span>
                    </div>
                    <div class="input-group-prepend bg-white">
                        <span class="input-group-text bg-secondary white-color" id="basic-addon1">
                            <span class="iranyekan" v-text="total_extra_work"></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="page-header">
                <div class="input-group">
                    <input type="text" class="form-control text-center iranyekan" placeholder="جستجو با نام و کد ملی" data-table="main-table" v-on:input="filter_table">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
                    </div>
                </div>
            </div>
            <div style="overflow: hidden" class="p-3">
                <div id="table-scroll" class="table-scroll">
                    <table id="main-table" class="main-table" v-cloak data-filter="[0,1]">
                    <thead class="bg-dark white-color">
                    <tr class="iransans">
                        <th scope="col"><span>نام</span></th>
                        <th scope="col"><span>کد ملی</span></th>
                        <th scope="col" v-for="attribute in table_data_records.performance_attribute.items"><span>@{{ attribute.name }}</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="employee in table_data_records.employees">
                        <td><span class="iranyekan">@{{ employee.first_name + " " + employee.last_name }}</span></td>
                        <td><span class="iranyekan">@{{ employee.national_code }}</span></td>
                        <td v-for="(attribute,index) in table_data_records.performance_attribute.items" style="min-width: 90px"><input class="form-control iranyekan text-center" :type="attribute['kind']" min="0" :value="get_employee_performance_value(employee.id,index)" v-on:input="set_employee_performance_value($event,employee.id,index)"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </div>
            <form id="main_submit_form" class="p-3" action="{{ route("Performances.store") }}" data-json="employees_data" method="post" v-on:submit="submit_form">
                @csrf
                <input type="hidden" id="employees_data" name="employees_data">
                <div class="form-row">
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan">توضیحات</label>
                        <textarea class="form-control iranyekan w-100" style="height: 80px" name="comment">{{ old("comment") }}</textarea>
                    </div>
                </div>
            </form>
            <div class="w-100 form-button-row text-center pt-4 pb-4 position-sticky" style="z-index: 1000;bottom: 0">
                @can("create","Performances")
                    <button type="button" class="btn btn-success submit_button" data-method="new" v-on:click="performance_validation">
                        <i class="submit_button_icon fa fa-database fa-1-2x mr-2"></i>
                        <span class="iranyekan">ذخیره کارکرد</span>
                        <button id="submit_validated_form" type="submit" hidden form="main_submit_form"></button>
                    </button>
                @endcan
                <a role="button" href="{{ route("Performances.index") }}" class="btn btn-outline-secondary iranyekan" data-dismiss="modal">
                    <i class="fa fa-arrow-turn-right fa-1-2x mr-1"></i>
                    <span class="iranyekan">بازگشت به لیست</span>
                </a>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="upload_performance_excel_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">بارگذاری کارکرد ماهانه</h6>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label class="form-lbl iranyekan">
                                فایل اکسل کارکرد
                            </label>
                            <s-file-browser :accept='["xlsx","xls"]' :size="500000"></s-file-browser>
                            <input type="hidden" value="{{ $contract_subset->id }}" id="contract_id">
                            @error('upload_file')
                            <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <axios-button :class="'btn btn-outline-primary'" :route="'{{ route("PerformancePreImport") }}'" :action="'load'" :required="['#upload_file']" :elements="['#upload_file','#contract_id']"  :message="'آیا برای بارگذاری فایل کارکرد اطمینان دارید؟'">
                        <i class="fa fa-database fa-1-2x mr-2"></i>
                        <span class="iranyekan">بارگذاری کارکرد</span>
                    </axios-button>
                    <button type="button" class="btn btn-outline-secondary iranyekan" data-dismiss="modal">
                        <i class="fa fa-times fa-1-2x mr-2"></i>
                        <span class="iranyekan">انصراف</span>
                    </button>
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
