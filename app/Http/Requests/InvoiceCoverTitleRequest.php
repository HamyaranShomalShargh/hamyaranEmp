<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class InvoiceCoverTitleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    #[ArrayShape(["name" => "string", "invoice_cover_list" => "string"])] public function rules(): array
    {
        return [
            "name" => "required",
            "invoice_cover_list" => "required"
        ];
    }

    #[ArrayShape(["name.required" => "string", "invoice_cover_list.required" => "string"])] public function messages(): array
    {
        return [
            "name.required" => "درج نام برای عناوین الزامی می باشد",
            "invoice_cover_list.required" => "ایجاد حداقل یک عنوان الزامی می باشد"
        ];
    }
}
