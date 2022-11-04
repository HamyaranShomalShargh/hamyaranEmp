<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class MenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["name" => "string", "menu_header_id" => "string", "parent_id" => "string", "menu_action_id" => "string", "short_name" => "string", "route" => "string", "main" => "string", "priority" => "string", "upload_file" => "string"])] public function rules()
    {
        return [
            "name" => "required",
            "menu_header_id" => "required",
            "parent_id" => "sometimes|nullable",
            "menu_action_id" => "sometimes|nullable",
            "short_name" => "required",
            "route" => "sometimes|nullable",
            "main" => "sometimes|nullable",
            "priority" => "sometimes|nullable",
            "upload_file" => "sometimes|nullable|mimes:png"
        ];
    }
    #[ArrayShape(["name.required" => "string", "short_name.required" => "string", "menu_header_id.required" => "string", "menu_action_id.required" => "string", "main.required" => "string", "upload_file.mimes" => "string"])] public function messages()
    {
        return [
            "name.required" => "درج نام الزامی می باشد.",
            "short_name.required" => "درج نام مختصر الزامی می باشد.",
            "menu_header_id.required" => "انتخاب سرفصل منو الزامی می باشد.",
            "menu_action_id.required" => "انتخاب حداقل یک عنوان از عملیات وابسته الزامی می باشد.",
            "main.required" => "انتخاب عملیات اصلی الزامی می باشد.",
            "upload_file.mimes" => "فرمت فایل آپلود شده png نمی باشد."
        ];
    }
}
