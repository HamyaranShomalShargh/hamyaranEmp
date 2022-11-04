<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractHeaderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "name" => "required",
            "number" => "sometimes|nullable",
            "start_date" => "required|jdate:Y/m/d",
            "end_date" => "required|jdate:Y/m/d|jdate_after:{$this->input('start_date')},Y/m/d",
            "upload_files.*" => "sometimes|nullable|mimes:png,jpg,bmp,tiff,pdf,xlsx,xls,txt"
        ];
    }

    public function messages()
    {
        return [
            "name.required" => "درج نام قرارداد الزامی می باشد",
            "start_date.required" => "درج تاریخ شروع قرارداد الزامی می باشد",
            "start_date.jdate" => "فرمت تاریخ شروع قرارداد صحیح نمی باشد",
            "end_date.required" => "درج تاریخ پایان قرارداد الزامی می باشد",
            "end_date.jdate" => "فرمت تاریخ پایان قرارداد صحیح نمی باشد",
            "end_date.jdate_after" => "تاریخ پایان قرارداد باید بعد از شروع قرارداد باشد",
            "upload_files.*.mimes" => "فرمت فایل(های) آپلود شده قابل قبول نمی باشد"
        ];
    }
}
