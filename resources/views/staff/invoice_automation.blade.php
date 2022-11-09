@extends('layouts.staff_dashboard')
@push('scripts')
    <script>
        let table_data = @json($invoice_automation_inbox);
    </script>
@endpush
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-folder-cog fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">اتوماسیون وضعیت ماهانه پرسنل</h5>
                    <span>(جستجو ، مشاهده و ویرایش)</span>
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
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a role="tab" class="nav-link active iranyekan font-weight-bold font-size-lg" id="inbox-tab" data-toggle="tab" data-target="#inbox" type="button" aria-controls="inbox" aria-selected="true">
                        <i class="fa fa-inbox-in fa-1-2x"></i>
                        ورودی
                    </a>
                </li>
                <li class="nav-item">
                    <a role="tab" class="nav-link iranyekan font-weight-bold font-size-lg" id="outbox-tab" data-toggle="tab" data-target="#outbox" type="button" aria-controls="outbox" aria-selected="false">
                        <i class="fa fa-inbox-out fa-1-2x"></i>
                        خروجی
                    </a>
                </li>
            </ul>
            <div class="tab-content pt-3" id="myTabContent">
                <div class="tab-pane fade show active" id="inbox" role="tabpanel" aria-labelledby="inbox-tab">
                    <div class="page-header">
                        <div class="input-group">
                            <div class="input-group-append">
                                <button class="input-group-text search-filter-button" data-toggle="modal" data-target="#search_filter_modal" title="جستجوی پیشرفته" onclick="$('#search_filter_button').attr('data-table','inbox_table')"><i class="fa fa-filter fa-1-2x filter-icon"></i></button>
                            </div>
                            <input type="text" class="form-control text-center iranyekan" placeholder="جستجو با قرارداد ، سال و ماه" data-table="inbox_table" v-on:input="filter_table">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive p-3 pt-1" v-cloak>
                        <table id="inbox_table" class="table table-striped static-table" data-filter="[1,2,3]">
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
                            <tr v-for="record in table_data_records">
                                <td><span class="iranyekan">@{{ record.id }}</span></td>
                                <td><span class="iranyekan">@{{ record.contract.name + "(" + record.contract.workplace + ")" }}</span></td>
                                <td><span class="iranyekan">@{{ record.authorized_date.automation_year }}</span></td>
                                <td><span class="iranyekan">@{{ record.authorized_date.month_name }}</span></td>
                                <td><span class="iranyekan">@{{ record.current_role.name }}</span></td>
                                <td>
                                    <span v-if="record.is_finished === 0" class="iranyekan">در جریان</span>
                                    <span v-else class="iranyekan">تکمیل شده</span>
                                </td>
                                <td><span class="iranyekan">@{{ record.invoices.length }}</span></td>
                                <td><span class="iranyekan">@{{ record.user.name }}</span></td>
                                <td><span class="iranyekan">@{{ to_persian_date(record.created_at) }}</span></td>
                                <td><span class="iranyekan">@{{ to_persian_date(record.updated_at) }}</span></td>
                                <td>
                                    <div class="dropdown table-functions">
                                        <button class="btn btn-outline-info dropdown-toggle iranyekan" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-cog fa-1-4x"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            @can('details','InvoiceAutomation')
                                                <a role="button" :href="record.details_url" class="dropdown-item">
                                                    <i class="fa fa-list-ul"></i>
                                                    <span class="iranyekan">مشاهده جزئیات</span>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                            @endcan
                                            @can('disagree','InvoiceAutomation')
                                                <form class="w-100" :id="`disagree-form-${record.id}`" :action="record.disagree_url" method="POST" v-on:submit="submit_form">
                                                    @csrf
                                                    @method("put")
                                                    <button type="submit" :form="`disagree-form-${record.id}`" class="dropdown-item">
                                                        <i class="fa fa-times"></i>
                                                        <span class="iranyekan">عدم تایید و ارجاع</span>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="outbox" role="tabpanel" aria-labelledby="outbox-tab">
                    <div class="page-header">
                        <div class="input-group">
                            <div class="input-group-append">
                                <button class="input-group-text search-filter-button" data-toggle="modal" data-target="#search_filter_modal" title="جتجوی پیشرفته" onclick="$('#search_filter_button').attr('data-table','outbox_table')"><i class="fa fa-filter fa-1-2x filter-icon"></i></button>
                            </div>
                            <input type="text" class="form-control text-center iranyekan" placeholder="جستجو با قرارداد ، سال و ماه" data-table="outbox_table" v-on:input="filter_table">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive p-3" style="min: auto">
                        <table id="outbox_table" class="table table-striped static-table" data-filter="[1,2,3]">
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
                            @forelse($invoice_automation_outbox as $invoice_outbox)
                                <tr>
                                    <td><span class="iranyekan">{{ $invoice_outbox->id }}</span></td>
                                    <td><span class="iranyekan">{{ "{$invoice_outbox->contract->name}({$invoice_outbox->contract->workplace})" }}</span></td>
                                    <td><span class="iranyekan">{{ $invoice_outbox->authorized_date->automation_year }}</span></td>
                                    <td><span class="iranyekan">{{ $invoice_outbox->authorized_date->month_name }}</span></td>
                                    <td><span class="iranyekan">{{ $invoice_outbox->current_role->name }}</span></td>
                                    <td>
                                        @if($invoice_outbox->is_finished == 0 && $invoice_outbox->is_committed == 0)
                                            <span class="iranyekan">منتظر ارسال</span>
                                        @elseif($invoice_outbox->is_finished == 0 && $invoice_outbox->is_committed == 1)
                                            <span class="iranyekan">در جریان</span>
                                        @else
                                            <span class="iranyekan">تکمیل شده</span>
                                        @endif
                                    </td>
                                    <td><span class="iranyekan">{{ count($invoice_outbox->invoices) }}</span></td>
                                    <td><span class="iranyekan">{{ $invoice_outbox->user->name }}</span></td>
                                    <td><span class="iranyekan">{{ verta($invoice_outbox->created_at)->format("H:i:s Y/m/d") }}</span></td>
                                    <td><span class="iranyekan">{{ verta($invoice_outbox->updated_at)->format("H:i:s Y/m/d") }}</span></td>
                                    <td>
                                        <div class="dropdown table-functions">
                                            <button class="btn btn-outline-info dropdown-toggle iranyekan" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-cog fa-1-4x"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                @can('details','InvoiceAutomation')
                                                    <a role="button" href="{{ route("InvoiceAutomation.details",[$invoice_outbox->id,true]) }}" class="dropdown-item">
                                                        <i class="fa fa-list-ul"></i>
                                                        <span class="iranyekan">مشاهده جزئیات</span>
                                                    </a>
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
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="search_filter_modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">جستجو در کارکردهای ماهانه</h6>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label class="form-lbl iranyekan">
                                انتخاب قرارداد
                            </label>
                            <select class="form-control text-center iranyekan selectpicker" data-container="body" size="5" data-live-search="true" id="contract_id">
                                @forelse($contracts as $contract)
                                    <option value="{{ "{$contract->name} - $contract->workplace" }}">{{ "{$contract->name} - $contract->workplace" }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group col-12">
                            <label class="form-lbl iranyekan">
                                سال
                            </label>
                            <input type="text" class="form-control text-center iranyekan number_masked" data-mask="0000" id="year" value="{{ verta()->format("Y") }}">
                            <small class="iranyekan">عدد سال چهاررقمی (مثلا 1401)</small>
                        </div>
                        <div class="form-group col-12">
                            <label class="form-lbl iranyekan">
                                ماه
                            </label>
                            <select class="form-control text-center iranyekan selectpicker" data-container="body" size="5" data-live-search="true" id="month">
                                @foreach($month_names as $month)
                                    <option value="{{ $month }}">{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="search_filter_button" data-table="" class="btn btn-success submit_button" v-on:click="search_table_filter">
                        <i class="submit_button_icon fa fa-search fa-1-2x mr-1"></i>
                        <span class="iranyekan">جستجو</span>
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
