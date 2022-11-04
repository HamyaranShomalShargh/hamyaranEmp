@extends('layouts.staff_dashboard')
@section('content')
    <div class="page pt-3">
        <div class="w-100 content-window bg-menu rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-square-list fa-1-6x mr-1"></i>
                    <h5 class="iransans d-inline-block m-0">
                        جزئیات اتوماسیون تغییرات مزایا
                    </h5>
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
                    <div class="input-group-append">
                        <span class="input-group-text iranyekan" id="basic-addon1">مشخصات پرسنل</span>
                    </div>
                    <span type="text" class="form-control iranyekan">{{ "{$automation->employee->first_name} {$automation->employee->last_name} - {$automation->employee->national_code} - {$automation->contract}" }}</span>
                    <div class="input-group-append">
                        <span class="input-group-text iranyekan" id="basic-addon1">نوع درخواست</span>
                    </div>
                    <span type="text" class="form-control iranyekan">
                        @if($automation->type == "add")
                            {{ "اضافه کردن" }}
                        @else
                            {{ "حذف کردن" }}
                        @endif
                    </span>
                    <div class="input-group-append">
                        <span class="input-group-text iranyekan" id="basic-addon1">ماه شروع</span>
                    </div>
                    <span type="text" class="form-control iranyekan">
                        {{ $automation->start_month != null ? $automation->start_month : "ندارد"}}
                    </span>
                    <div class="input-group-append">
                        <span class="input-group-text iranyekan" id="basic-addon1">ماه پایان</span>
                    </div>
                    <span type="text" class="form-control iranyekan">
                        {{ $automation->end_month != null ? $automation->end_month : "ندارد"}}
                    </span>
                </div>
            </div>
            <form class="p-3" id="main_submit_form" action="{{ route("AdvantageAutomation.agree",$automation->id) }}" method="post" v-on:submit="submit_form">
                @csrf
                @method("put")
                <div class="form-row">
                    @forelse(json_decode($automation->texts,true) as $text)
                        <div class="form-group col-12">
                            <label class="form-lbl iranyekan">{{ $text["name"] }}</label>
                            <input type="text" class="form-control iranyekan text-center" readonly value="{{ $text["value"] }}">
                        </div>
                    @empty
                    @endforelse
                    @forelse(json_decode($automation->files,true) as $file)
                        <div class="form-group col-12">
                            <label class="form-lbl iranyekan">
                                {{ $file["name"] }}
                                <a href="{{ route("AdvantageForms.files",[$automation->id,"{$file['name']}"]) }}" class="iransans green-color">(دانلود فایل(های) آپلود شده به صورت بسته)</a>
                            </label>
                            <ul class="list-group w-100 p-0">
                                @forelse($file["value"] as $item)
                                    <li class="list-group-item">
                                        <a class="iranyekan" href="{{ route("AdvantageForms.file",[$automation->id,"$item"]) }}">{{ "دانلود فایل شماره " . $loop->iteration }}</a>
                                    </li>
                                @empty
                                @endforelse
                            </ul>
                        </div>
                    @empty
                    @endforelse
                    <div class="form-group col-12">
                        <label class="col-form-label iranyekan">ثبت توضیحات</label>
                        <textarea class="form-control iranyekan w-100" style="height: 80px" name="comment">{{ old("comment") }}</textarea>
                    </div>
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
                        @if($automation->is_finished == 0)
                            <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                                <i class="submit_button_icon fa fa-check fa-1-2x mr-2"></i>
                                @if(auth()->user()->role->id == $final_role)
                                    <span class="iranyekan">ذخیره، تایید و خاتمه</span>
                                @else
                                    <span class="iranyekan">ذخیره، تایید و ارسال</span>
                                @endif
                            </button>
                            <form class="d-inline" id="refer_form" action="{{ route("AdvantageAutomation.disagree",$automation->id) }}" method="post" v-on:submit="submit_form">
                                @csrf
                                @method('put')
                                <button type="submit" form="refer_form" class="btn btn-danger">
                                    <i class="submit_button_icon fa fa-times fa-1-2x mr-2"></i>
                                    <span class="iranyekan">عدم تایید و ارجاع</span>
                                </button>
                            </form>
                        @endif
                        <a role="button" href="{{ route("AdvantageAutomation.index") }}" class="btn btn-outline-secondary iranyekan" data-dismiss="modal">
                            <i class="fa fa-arrow-turn-right fa-1-2x mr-2"></i>
                            <span class="iranyekan">بازگشت به لیست</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
