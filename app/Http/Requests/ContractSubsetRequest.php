<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class ContractSubsetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => "required",
            "contract_id" => "required",
            "parent_id" => "sometimes|nullable",
            "workplace" => "sometimes|nullable",
            "registration_start_day" => "sometimes|nullable",
            "registration_final_day" => "sometimes|nullable",
            "invoice_flow_id" => "sometimes|nullable",
            "performance_flow_id" => "sometimes|nullable",
            "overtime_registration_limit" => "sometimes|nullable",
            "performance_attributes_id" => "sometimes|nullable",
            "invoice_attributes_id" => "sometimes|nullable",
            "invoice_cover_id" => "required",
            "upload_file.*" => "sometimes|nullable|mimes:png,jpg,bmp,tiff,pdf,xlsx,xls,txt",
        ];
    }
    #[ArrayShape(["name.required" => "string", "contract_id.required" => "string", "upload_files.*.mimes" => "string", "invoice_cover_id.required" => "string"])] public function messages(): array
    {
        return [
            "name.required" => "درج نام قرارداد الزامی می باشد",
            "contract_id.required" => "انتخاب قرارداد الزامی می باشد",
            "upload_files.*.mimes" => "فرمت فایل(های) مستندات قابل قبول نمی باشد",
            "invoice_cover_id.required" => "انتخاب عناوین روکش وضعیت الزامی می باشد"
        ];
    }
}
