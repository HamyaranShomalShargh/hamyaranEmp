<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="">
    <title>
    </title>
    <!-- Styles -->
    <link href="{{asset("/css/app.css")}}" rel="stylesheet">
    <style>
        *{
            font-size: 14px;!important;
            font-family: IRANSans,'sans-serif';
        }
        .table th,.table td{
            text-align: center;
            border-color: #525252 !important;
        }
        .table tr td span{
            font-size: 17px;
        }
        .sign_container{
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
        }
        .sign_box{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px 10px;
            border: 2px dotted #bdbdbd;
            min-width: 90px;
            margin-bottom: 10px;
        }
        .sign{
            max-width: 150px;
            height: auto;
        }
        @media print {
            @page  {
                size: A4 portrait;
            }
        }
    </style>
</head>
<body class="antialiased rtl">
<h6 class="iransans font-weight-bold text-center mb-3">شرکت همیاران شمال شرق</h6>
<h3 class="d-flex flex-row justify-content-between align-items-center mb-4 p-3" style="border: 1px solid;border-color: #525252!important;">
    <span class="text-left" style="font-size: 60%;width: 33%">{{ "خلاصه وضعیت " . $automation->authorized_date->month_name . " ماه سال " .  $automation->authorized_date->automation_year}}</span>
    <span class="text-center" style="font-size: 80%;width: 33%">{{ $automation->contract->workplace }}</span>
    <span class="text-right" style="font-size: 60%;width: 33%">{{"تاریخ چاپ : " . verta()->format("Y/m/d")}}</span>
</h3>
<table class="table table-bordered table-striped border border-dark w-100">
    <thead>
    <tr>
        <th style="width: 10%">ردیف</th>
        <th style="width: 60%">عنوان</th>
        <th style="width: 30%">مبلغ(ریال)</th>
    </tr>
    </thead>
    <tbody>
    @forelse($automation->cover_titles->items as $item)
        <tr>
            <td><span>{{ $loop->iteration }}</span></td>
            <td><span>{{ $item->name }}</span></td>
            <td>
                <span>
                    @if($automation->cover && array_search($item->id,array_column(json_decode($automation->cover->data,true),"id")) >= 0)
                        @if(is_numeric(json_decode($automation->cover->data,true)[array_search($item->id,array_column(json_decode($automation->cover->data,true),"id"))]["value"]))
                            {{ number_format(json_decode($automation->cover->data,true)[array_search($item->id,array_column(json_decode($automation->cover->data,true),"id"))]["value"]) }}
                        @else
                            {{ json_decode($automation->cover->data,true)[array_search($item->id,array_column(json_decode($automation->cover->data,true),"id"))]["value"] }}
                        @endif
                    @else
                        {{ "0" }}
                    @endif
                </span>
            </td>
        </tr>
    @empty
    @endforelse
    </tbody>
</table>
@if($automation->signs->isNotEmpty())
    <div class="w-100">
        <label class="mt-3 col-form-label iran_yekan black_color font-weight-bold" for="project_name">امضاء شده توسط</label>
        <div class="sign_container">
            @forelse($automation->signs as $sign)
                <div class="sign_box iran_yekan bg-light mr-4 align-self-stretch">
                    <span class="black-color font-weight-bold" style="height: 10%">{{$sign->user->role->name}}</span>
                    @if($signs != [] && in_array($sign->user->id,array_column($signs,"id")) !== false)
                        <div class="w-100 d-flex align-items-center justify-content-center" style="height: 70%">
                            <img class="p-4 sign" alt="sign" src="{{ "data:image/png;base64,".$signs[array_search($sign->user->id,array_column($signs,"id"))]["sign"] }}"/>
                        </div>
                    @else
                        <span class="p-3 font-weight-bold">بدون امضاء</span>
                    @endif
                    <span style="height: 10%">{{$sign->user->name}}</span>
                    <span style="height: 10%" class="text-muted" dir="ltr" style="font-size: 10px">{{verta($sign->created_at)->format("Y/m/d H:i:s")}}</span>
                </div>
            @empty
            @endforelse
        </div>
    </div>
@endif
<script type="text/javascript" src="{{asset("/js/app.js")}}"></script>
<script>
    $(document).ready(function (){
        window.onafterprint = window.close;
        window.print();
    });
</script>
</body>
</html>
