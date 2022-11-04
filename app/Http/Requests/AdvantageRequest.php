<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class AdvantageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["name" => "string", "period" => "string", "automation_flow_id" => "string", "advantage_list" => "string"])] public function rules(): array
    {
        return [
            "name" => "required",
            "period" => "sometimes|nullable",
            "automation_flow_id" => "required",
            "advantage_list" => "sometimes|nullable"
        ];
    }

    #[ArrayShape(["name.required" => "string", "automation_flow_id.required" => "string"])] public function messages(): array
    {
        return [
            "name.required" => "درج نام الزامی می باشد",
            "automation_flow_id.required" => "انتخاب گردش اتوماسیون الزامی می باشد"
        ];
    }
}
