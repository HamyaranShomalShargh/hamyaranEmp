<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class DefaultTableAttributeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    #[ArrayShape(["name" => "string", "type" => "string", "category" => "string", "kind" => "string"])] public function rules(): array
    {
        return [
            "name" => "required",
            "type" => "required",
            "category" => "required",
            "kind" => "required"
        ];
    }

    #[ArrayShape(["name.required" => "string", "type.required" => "string", "category.required" => "string", "kind.required" => "string"])] public function messages(): array
    {
        return [
            "name.required" => "درج نام عنوان الزامی است",
            "type.required" => "انتخاب نوع عنوان الزامی است",
            "category.required" => "انتخاب دسته بندی عنوان الزامی است",
            "kind.required" => "انتخاب نوع مقدار عنوان الزامی است",
        ];
    }
}
