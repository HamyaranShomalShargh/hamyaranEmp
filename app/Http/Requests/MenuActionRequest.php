<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class MenuActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["name" => "string", "action" => "string"])] public function rules(): array
    {
        return [
            "name" => "required",
            "action" => "required",
        ];
    }
    #[ArrayShape(["name.required" => "string", "action.required" => "string"])] public function messages(): array
    {
        return [
            "name.required" => "وارد کردن نام الزامی می باشد.",
            "action.required" => "وارد کردن عملیات الزامی می باشد."
        ];
    }
}
