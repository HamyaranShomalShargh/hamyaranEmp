@extends('layouts.admin_dashboard')
@push('scripts')
    <script>
        let report_data = @json($user->log);
    </script>
@endpush
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-chart-simple-horizontal fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">
                        {{ "گزارشات کاربران - " . $user->name}}
                    </h5>
                    <span>(مشاهده)</span>
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
                    <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
                    </div>
                    <input type="text" class="form-control text-center iranyekan" data-table="main_table" placeholder="جستجو با شرح" v-on:input="filter_table">
                </div>
            </div>
            <div id="table_responsive" class="table-responsive p-3">
                <table id="main_table" class="table table-striped static-table" data-filter="[2]">
                    <thead class="bg-dark white-color">
                    <tr class="iransans">
                        <th scope="col"><span>شماره</span></th>
                        <th scope="col"><span>عملیات</span></th>
                        <th scope="col"><span>شرح</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($user->log as $report)
                        <tr>
                            <td><span class="iranyekan">{{ $report->id }}</span></td>
                            <td><span class="iranyekan">{{ $report->operation }}</span></td>
                            <td><span class="iranyekan">{{ $report->description }}</span></td>
                            <td><span class="iranyekan">{{ verta($report->cretaed_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td>
                                <button class="btn-sm btn-outline-info mr-2" data-toggle="modal" data-target="#record_data" data-id="{{ $report->id }}" v-on:click="show_record_data($event)">
                                    <span class="iransans create-button font-size-sm">اطلاعات رکورد</span>
                                </button>
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
    <div class="modal fade rtl" id="record_data" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">اطلاعات رکورد</h6>
                </div>
                <div class="modal-body">
                    <div id="json_data" class="form-row">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary iranyekan" data-dismiss="modal">
                        <i class="fa fa-times fa-1-2x mr-1"></i>
                        <span class="iranyekan">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
