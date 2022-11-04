<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["name" => "string", "role_menu" => "string"])] public function rules(): array
    {
        return [
            "name" => "required",
            "role_menu" => "required",
        ];
    }
    #[ArrayShape(["name.required" => "string", "role_menu.required" => "string"])] public function messages(): array
    {
        return [
            "name.required" => "درج نام برای عنوان شغلی الزامی می باشد.",
            "role_menu.required" => "انتخاب حداقل یک آیتم منو برای عنوان شغلی الزامی می باشد.",
        ];
    }
}
