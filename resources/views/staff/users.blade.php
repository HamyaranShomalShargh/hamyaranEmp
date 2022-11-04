@extends('layouts.staff_dashboard')
@section('content')
    <div class="page w-100 pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-StaffUsers fa-1-4x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">کاربران</h5>
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
                        <button class="btn btn-outline-info mr-2" data-toggle="modal" data-target="#new_user_modal">
                            <i class="fa fa-plus-circle fa-1-2x mr-1"></i>
                            <span class="iransans create-button">کاربر جدید</span>
                        </button>
                    </div>
                    <input type="text" class="form-control text-center iranyekan" placeholder="جستجو با نام ، نام خانوادگی ، نام کاربری و عنوان شغلی">
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
                        <th scope="col"><span>نام کاربری</span></th>
                        <th scope="col"><span>عنوان شغلی</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>آخرین بازدید</span></th>
                        <th scope="col"><span>آی پی</span></th>
                        <th scope="col"><span>وضعیت</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td><span class="iranyekan">{{$user->id}}</span></td>
                            <td><span class="iranyekan">{{$user->name}}</span></td>
                            <td><span class="iranyekan">{{$user->username}}</span></td>
                            <td><span class="iranyekan">{{$user->role->name}}</span></td>
                            <td><span class="iranyekan">{{$user->user->name}}</span></td>
                            <td>
                                @if($user->last_activity)
                                    <span class="iranyekan">{{verta($user->last_activity)->format("H:i:s Y/m/d")}}</span>
                                @else
                                    <span class="iranyekan"></span>
                                @endif
                            </td>
                            <td><span class="iranyekan">{{$user->last_ip_address}}</span></td>
                            <td>
                                <span class="iranyekan">
                                    @if($user->inactive == 1)
                                        <i class="fa fa-times-circle red-color fa-1-6x"></i>
                                    @elseif($user->inactive == 0)
                                        <i class="fa fa-check-circle green-color fa-1-6x"></i>
                                    @endif
                                </span>
                            </td>
                            <td><span class="iranyekan">{{verta($user->created_at)->format("H:i:s Y/m/d")}}</span></td>
                            <td><span class="iranyekan">{{verta($user->updated_at)->format("H:i:s Y/m/d")}}</span></td>
                            <td>
                                <div class="dropdown table-functions iranyekan">
                                    <button class="btn btn-outline-info dropdown-toggle iranyekan" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-cog fa-1-4x"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can("activation", "StaffUsers")
                                            <form class="w-100" id="activation-form-{{ $user->id }}" action="{{ route("StaffUsers.activation",$user->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                <button type="submit" form="activation-form-{{ $user->id }}" class="dropdown-item">
                                                    @if($user->inactive == 0)
                                                        <i class="fa fa-lock"></i>
                                                        <span>غیر فعال سازی</span>
                                                    @elseif($user->inactive == 1)
                                                        <i class="fa fa-lock-open"></i>
                                                        <span>فعال سازی</span>
                                                    @endif
                                                </button>
                                            </form>
                                        @endcan
                                        @can("edit", "StaffUsers")
                                            <div class="dropdown-divider"></div>
                                            <a role="button" href="{{ route("StaffUsers.edit",$user->id) }}" class="dropdown-item">
                                                <i class="fa fa-edit"></i>
                                                <span class="iranyekan">ویرایش</span>
                                            </a>
                                        @endcan
                                        @can("delete","StaffUsers")
                                            <div class="dropdown-divider"></div>
                                            <form class="w-100" id="delete-form-{{ $user->id }}" action="{{ route("StaffUsers.destroy",$user->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                @method("Delete")
                                                <button type="submit" form="delete-form-{{ $user->id }}" class="dropdown-item">
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
                        <tr><td colspan="11"><span class="iransans">اطلاعاتی وجود ندارد</span></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="new_user_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">ایجاد کاربر جدید</h5>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" action="{{route("StaffUsers.store")}}" method="post" data-type="create" v-on:submit="submit_form" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    نام و نام خانوادگی
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control iranyekan text-center @error('name') is-invalid @enderror" type="text" name="name" value="{{ old("name") }}">
                                @error('name')
                                <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    نام کاربری
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center @error('username') is-invalid @enderror" type="text" autocomplete="off" name="username" value="{{ old("username") }}">
                                @error('username')
                                <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    کلمه عبور
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center @error('password') is-invalid @enderror" type="password" name="password" autocomplete="new-password" value="{{ old("password") }}">
                                @error('password')
                                <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    تکرار کلمه عبور
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center" type="password" name="password_confirmation" autocomplete="new-password" value="{{ old("password") }}">
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    عنوان شغلی
                                    <strong class="red-color">*</strong>
                                </label>
                                <select class="form-control text-center @error('role_id') is-invalid @enderror selectpicker iranyekan" title="انتخاب کنید" data-size="20" data-live-search="true" name="role_id">
                                    @forelse($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('role_id')
                                <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    قرارداد های مجاز
                                    <strong class="red-color">*</strong>
                                </label>
                                <select class="form-control text-center @error('contracts') is-invalid @enderror selectpicker iranyekan" title="انتخاب کنید" multiple data-size="20" data-live-search="true" name="contracts[]">
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
                                @error('contracts')
                                <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    پست الکترونیکی
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center @error('email') is-invalid @enderror" type="text" name="email" value="{{ old("email") }}">
                                @error('email')
                                <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="form-lbl iranyekan">
                                    موبایل
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control iranyekan text-center @error('mobile') is-invalid @enderror" type="text" name="mobile" value="{{ old("mobile") }}">
                                @error('mobile')
                                <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="col-form-label iranyekan black_color" for="upload_file">اسکن امضا</label>
                                <s-file-browser :accept='["png"]' :size="325000"></s-file-browser>
                                @error('upload_file')
                                <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="main_submit_form" class="btn btn-success">
                        <i class="fa fa-database fa-1-2x mr-1"></i>
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
