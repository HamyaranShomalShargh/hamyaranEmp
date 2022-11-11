@extends('layouts.staff_dashboard')
@push('scripts')
    @can("edit_values","InvoiceAutomation")
        <script>
            let edit_values_permission = true;
        </script>
    @else
        <script>
            let edit_values_permission = false;
        </script>
    @endcan
    @if($automation->is_finished == 1 || $outbox)
        <script>
            if(typeof edit_values_permission !== "undefined")
                edit_values_permission = false;
        </script>
    @endif
    <script>
        let table_data = @json($automation);
        let cover_items = @json($automation->cover_titles->items);
    </script>
    @if($automation->cover->data)
        <script>
            let cover_data = @json(json_decode($automation->cover->data,true));
        </script>
    @endif
@endpush
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-folder-cog fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">جزئیات اتوماسیون وضعیت ماهانه پرسنل</h5>
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
                        <a href="{{ route("InvoiceAutomation.Invoice_export_excel",[$automation->contract->id,$automation->authorized_date_id,$automation->id]) }}" class="btn btn-outline-info mr-2" style="border-radius: 0.25rem">
                            <i class="fa fa-download fa-1-2x mr-1"></i>
                            <span class="iransans create-button">دانلود</span>
                        </a>
                        @can("edit_values","InvoiceAutomation")
                            @if($automation->is_finished == "0" && !$outbox)
                                <button class="btn btn-outline-primary mr-2" data-toggle="modal" data-target="#upload_performance_excel_modal" style="border-radius: 0.25rem">
                                    <i class="fa fa-upload fa-1-2x mr-1"></i>
                                    <span class="iransans create-button">بارگذاری</span>
                                </button>
                            @endif
                        @endcan
                        <button class="btn btn-outline-secondary mr-2" data-toggle="modal" data-target="#invoice_cover" style="border-radius: 0.25rem">
                            <i class="fa fa-list-alt fa-1-2x mr-1"></i>
                            <span class="iransans create-button">روکش وضعیت</span>
                        </button>
                    </div>
                    <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-table-cells fa-1-2x"></i></span>
                    </div>
                    <input type="text" class="form-control iranyekan" readonly value="{{ "وضعیت ماهانه پرسنل ".$automation->contract->name."(".$automation->contract->workplace.") ".$automation->authorized_date->month_name." ماه سال ".$automation->authorized_date->automation_year }}">
                </div>
            </div>
            <div class="page-header">
                <div class="input-group">
                    <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fa fa-line-height mr-1"></i>
                            <input type="checkbox" v-on:change="change_table_height">
                        </span>
                    </div>
                    <input type="text" class="form-control text-center iranyekan" placeholder="جستجو با نام و کد ملی" data-table="main-table" v-on:input="filter_table">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
                    </div>
                </div>
            </div>
            <div style="overflow: hidden" class="p-3">
                <div id="table-scroll" class="table-scroll low-height-table">
                    <table id="main-table" class="main-table" v-cloak data-filter="[0,1]">
                        <thead class="bg-dark white-color">
                        <tr class="iransans">
                            <th scope="col"><span>نام</span></th>
                            <th scope="col"><span>کد ملی</span></th>
                            <th scope="col"><span>گروه شغلی</span></th>
                            <th scope="col" v-for="attribute in table_data_records.attributes.items"><span>@{{ attribute.name }}</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in table_data_records.invoices">
                            <td><span class="iranyekan">@{{ item.employee.first_name + " " + item.employee.last_name }}</span></td>
                            <td><span class="iranyekan">@{{ item.employee.national_code }}</span></td>
                            <td><span class="iranyekan">@{{ item.job_group }}</span></td>
                            <td v-for="(attribute,index) in table_data_records.attributes.items" :style="[attribute.category === 'function' ? {background:'#ffedc8'} : attribute.category === 'advantage' ? {background: '#dbffe5'} : attribute.category === 'deduction' ? {background: '#ffe5e5'} : {background: '#ffffff'}]">
                                <input v-if="can_edit_values === true" class="form-control iranyekan text-center" :class="attribute['kind'] === 'number' ? 'thousand_separator' : ''" min="0" :value="get_employee_invoice_value(item.employee.id,index)" v-on:input="set_employee_invoice_value($event,item.employee.id,index)"/>
                                <span v-else class="iranyekan text-center" v-text="get_employee_invoice_value(item.employee.id,index).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,')"></span>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot class="position-sticky bottom bg-dark">
                        <tr>
                            <td colspan="54">
                                <div class="w-100 d-flex flex-row align-items-center justify-content-start">
                                    <div class="mr-2">
                                        <i class="fas fa-square fa-1-2x" style="color: #FFEDC8FF"></i>
                                        <span class="iranyekan">کارکرد</span>
                                    </div>
                                    <div class="mr-2">
                                        <i class="fas fa-square fa-1-2x" style="color: #DBFFE5FF"></i>
                                        <span class="iranyekan">مزایا</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-square fa-1-2x" style="color: #FFE5E5FF"></i>
                                        <span class="iranyekan">کسورات</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @if($automation->is_finished == 0 && !$outbox)
                <form id="main_submit_form" class="p-3" action="{{ route("InvoiceAutomation.agree",$automation->id) }}" data-json="employees_data" method="post" v-on:submit="submit_form">
                    @csrf
                    @method("put")
                    <input type="hidden" id="employees_data" name="employees_data">
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label class="col-form-label iranyekan">ثبت توضیحات</label>
                            <textarea class="form-control iranyekan w-100" style="height: 80px" name="comment">{{ old("comment") }}</textarea>
                        </div>
                    </div>
                </form>
            @endif
            @if($automation->comments->isNotEmpty())
                <div class="form-group col-12">
                    <label class="col-form-label iranyekan">توضیحات ثبت شده</label>
                    <div class="comments_container">
                        @forelse($automation->comments as $comment)
                            <div class="comment_box iranyekan">
                                <div class="commenter">
                                    <i class="fa fa-user-circle fa-2x mr-2"></i>
                                    <span class="text-muted">{{$comment->user->name."(".$comment->user->role->name.")"}}</span>
                                </div>
                                <p class="mt-2 comment">{{$comment->comment}}</p>
                                <span class="time-left" dir="ltr">{{verta($comment->created_at)->format("Y/m/d H:i:s")}}</span>
                            </div>
                        @empty
                        @endforelse
                    </div>
                </div>
            @endif
            @if($automation->signs->isNotEmpty())
                <div class="form-group col-12">
                    <label class="col-form-label iranyekan black_color" for="project_name">امضاء شده توسط</label>
                    <div class="sign_container">
                        @forelse($automation->signs as $sign)
                            <div class="sign_box iranyekan bg-light mr-4 align-self-stretch">
                                <i class="fa fa-user-circle fa-2x mb-2"></i>
                                <span class="text-muted">{{$sign->user->role->name}}</span>
                                <span>{{$sign->user->name}}</span>
                                <span class="text-muted" dir="ltr" style="font-size: 10px">{{verta($sign->created_at)->format("Y/m/d H:i:s")}}</span>
                            </div>
                        @empty
                        @endforelse
                    </div>
                </div>
            @endif
            <div class="w-100 form-button-row text-center pt-4 pb-4 position-sticky" style="z-index: 1000;bottom: 0">
                @if($automation->is_finished == 0  && !$outbox)
                    <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                        <i class="submit_button_icon fa fa-check fa-1-2x mr-2"></i>
                        @if(auth()->user()->role->id == $final_role)
                            <span class="iranyekan">ذخیره، تایید و خاتمه</span>
                        @else
                            <span class="iranyekan">ذخیره، تایید و ارسال</span>
                        @endif
                    </button>
                    <form class="d-inline" id="refer_form" action="{{ route("InvoiceAutomation.disagree",$automation->id) }}" method="post" v-on:submit="submit_form">
                        @csrf
                        @method('put')
                        <button type="submit" form="refer_form" class="btn btn-danger">
                            <i class="submit_button_icon fa fa-times fa-1-2x mr-2"></i>
                            <span class="iranyekan">عدم تایید و ارجاع</span>
                        </button>
                    </form>
                @endif
                <a role="button" href="{{ route("InvoiceAutomation.index") }}" class="btn btn-outline-secondary iranyekan" data-dismiss="modal">
                    <i class="fa fa-arrow-turn-right fa-1-2x mr-2"></i>
                    <span class="iranyekan">بازگشت به لیست</span>
                </a>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade" id="invoice_cover" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans" id="exampleModalLongTitle">مقادیر روکش وضعیت</h6>
                </div>
                <div class="modal-body scroll-style">
                    <div class="form-row rtl">
                        <div class="form-group col-12" v-for="item in invoice_cover_items">
                            <label class="col-form-label iranyekan">@{{ item.name }}</label>
                            <span class="form-control iranyekan text-center" v-text="Number(get_cover_value(item.id)).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,')"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route("InvoiceAutomation.print_cover",$automation->id) }}" target="_blank" role="button" class="btn btn-dark">چاپ</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
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
                            <input type="hidden" value="{{ $automation->contract->id }}" id="contract_id">
                            <input type="hidden" value="{{ $automation->id }}" id="automation_id">
                            <input type="hidden" value="created" id="type">
                            @error('upload_file')
                            <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <axios-button :class="'btn btn-outline-primary'" :route="'{{ route("InvoicePreImport") }}'" :action="'load'" :required="['#upload_file']" :elements="['#upload_file','#contract_id','#automation_id','#type']"  :message="'آیا برای بارگذاری فایل کارکرد اطمینان دارید؟'">
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
