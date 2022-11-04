<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "ceo_user_id" => "sometimes|nullable",
            "substitute_user_id" => "sometimes|nullable",
            "user_id" => "sometimes|nullable",
            "ceo_title" => "sometimes|nullable",
            "substitute_title" => "sometimes|nullable",
            "name" => "sometimes|nullable",
            "short_name" => "sometimes|nullable",
            "description" => "sometimes|nullable",
            "registration_number" => "sometimes|nullable",
            "national_id" => "sometimes|nullable",
            "website" => "sometimes|nullable",
            "address" => "sometimes|nullable",
            "phone" => "sometimes|nullable",
            "fax" => "sometimes|nullable",
            "app_version" => "sometimes|nullable"
        ];
    }
}
