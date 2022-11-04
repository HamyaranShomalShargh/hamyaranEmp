<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class UserPasswordResetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["old_password" => "string", "password" => "string[]"])] public function rules(): array
    {
        return [
            "old_password" => "required",
            "password" => ['required', 'min:8', 'confirmed'],
        ];
    }

    #[ArrayShape(["old_password.required" => "string", "password.required" => "string", "password.min" => "string", "password.confirmed" => "string"])] public function messages(): array
    {
        return [
            "old_password.required" => "لطفا گذرواژه فعلی را وارد نمایید",
            "password.required" => "لطفا گذرواژه جدید را وارد نمایید",
            "password.min" => "حداقل طول گذرواژه باید 8 کاراکتر باشد",
            "password.confirmed" => "تکرار گذرواژه جدید با آن همخوانی ندارد"
        ];
    }
}
