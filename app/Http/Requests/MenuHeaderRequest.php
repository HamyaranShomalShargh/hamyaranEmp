<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class MenuHeaderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["name" => "string", "short_name" => "string", "slug" => "string", "priority" => "string", "upload_file" => "string"])] public function rules(): array
    {
        return [
            "name" => "required",
            "short_name" => "sometimes|nullable",
            "slug" => "required",
            "priority" => "sometimes|nullable|digits_between:0,10000",
            "upload_file" => "sometimes|nullable|mimes:png"
        ];
    }
    #[ArrayShape(["name.required" => "string", "slug.required" => "string", "priority.digits" => "string", "upload_file.mimes" => "string"])] public function messages(): array
    {
        return [
            "name.required" => "وارد کردن نام الزامی می باشد",
            "slug.required" => "وارد کردن مشخصه الزامی می باشد",
            "priority.digits" => "فرمت عدد اولویت نمایش صحیح نمی باشد",
            "upload_file.mimes" => "فرمت فایل آپلود شده png نمی باشد"
        ];
    }
}
