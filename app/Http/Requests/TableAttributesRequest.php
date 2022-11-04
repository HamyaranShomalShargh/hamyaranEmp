<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class TableAttributesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["name" => "string", "type" => "string", "attributes_list" => "string"])] public function rules()
    {
        return [
            "name" => "required",
            "type" => "required",
            "attributes_list" => "required"
        ];
    }

    #[ArrayShape(["name.required" => "string", "type.required" => "string", "attributes_list.required" => "string"])] public function messages()
    {
        return [
            "name.required" => "درج نام برای عناوین الزامی می باشد",
            "type.required" => "انتخاب نوع عناوین الزامی می باشد",
            "attributes_list.required" => "ایجاد حداقل یک عنوان الزامی می باشد"
        ];
    }
}
