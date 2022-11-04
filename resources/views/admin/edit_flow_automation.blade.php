@extends('layouts.admin_dashboard')
@push('scripts')
    <script>
        let flow_data = @json($flow_list);
    </script>
@endpush
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-arrows-spin fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">مدیریت گردش های اتوماسیون</h5>
                    <span>(ویرایش)</span>
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
            <form id="update_form" class="p-3" action="{{route("AutomationFlow.update",$automation_flow->id)}}" method="post" data-type="create" v-on:submit="submit_form">
                @csrf
                @method("put")
                <div class="form-row">
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan black_color" for="name">
                            نام
                            <strong class="red-color">*</strong>
                        </label>
                        <input type="text" class="form-control iranyekan text-center @error('name') is-invalid @enderror" id="name" name="name" value="{{$automation_flow->name}}">
                        @error('name')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan black_color" for="short_name">
                            اضافه کردن عناوین شغلی
                            <strong class="red-color">*</strong>
                        </label>
                        <select class="form-control iranyekan text-center selectpicker @error('flow_roles') is-invalid @enderror" data-live-search="true" id="roles" title="انتخاب کنید" data-size="20" v-on:change="add_role_item">
                            @forelse($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @empty
                            @endforelse
                        </select>
                        @error('flow_roles')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan black_color">
                            گردش(اولویت به ترتیب ثبت)
                        </label>
                        <select hidden id="flow_roles" name="flow_roles[]" multiple>
                            <option selected v-for="role in roles_list" :value="role.id">@{{ role.name }}</option>
                        </select>
                        <ul class="list-group w-100 pl-0">
                            <li v-if="roles_list.length === 0"><h6 class="iranyekan text-center">انتخابی صورت نگرفته است</h6></li>
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" v-for="role in roles_list">
                                <div>
                                    <input :checked="role.main_role === 1" type="radio" name="main_role" :value="role.id" :id="role.slug">
                                    <label :for="role.slug" class="iranyekan mr-1">تایید کننده نهایی</label>
                                    <span>-</span>
                                    <span class="iranyekan text-center ml-1" style="font-weight: 700">@{{ role.name }}</span>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-light mr-2" data-function="up" :data-slug="role.slug" v-on:click="modify_role">
                                        <i class="fa fa-arrow-up gray-color fa-1-2x"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-light mr-2" data-function="down" :data-slug="role.slug" v-on:click="modify_role">
                                        <i class="fa fa-arrow-down gray-color fa-1-2x"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-light" data-function="remove" :data-slug="role.slug" v-on:click="modify_role">
                                        <i class="fa fa-times gray-color fa-1-2x"></i>
                                    </button>
                                </div>
                            </li>
                        </ul>
                        <small v-if="roles_list.length !== 0" class="iranyekan red-color">در صورت مشخص نکردن تایید کننده نهایی، عنوان شغلی انتهای لیست به عنوان تایید کننده نهایی در نظر گرفته می شود </small>
                    </div>
                    <div class="form-group col-12 form-button-row text-center pt-4 pb-2">
                        <button type="submit" form="update_form" class="btn btn-success submit_button">
                            <i class="submit_button_icon fa fa-database fa-1-2x mr-1"></i>
                            <span class="iranyekan">ارسال و ویرایش</span>
                        </button>
                        <a role="button" href="{{ route("AutomationFlow.index") }}" class="btn btn-outline-secondary iranyekan" data-dismiss="modal">
                            <i class="fa fa-arrow-turn-right fa-1-2x mr-1"></i>
                            <span class="iranyekan">بازگشت به لیست</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
