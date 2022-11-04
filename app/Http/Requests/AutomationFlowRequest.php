<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class AutomationFlowRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["name" => "string", "flow_roles" => "string", "main_role" => "string"])] public function rules(): array
    {
        return [
            "name" => "required",
            "flow_roles" => "required",
            "main_role" => "sometimes|nullable|numeric"
        ];
    }

    #[ArrayShape(["name.required" => "string", "flow_roles.required" => "string"])] public function messages(): array
    {
        return [
            "name.required" => "درج نام الزامی می باشد",
            "flow_roles.required" => "انتخاب حداقل یک عنوان شغلی برای گردش الزامی می باشد"
        ];
    }
}
