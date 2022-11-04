<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class PasswordResetRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->filled("mobile")) {
            return [
                "mobile" => ["required", "regex:/^09(1[0-9]|9[0-2]|2[0-2]|0[1-5]|41|3[0,3,5-9])\d{7}$/"],
                "token" => "required",
                'password' => ['required', 'min:8', 'confirmed'],
            ];
        }
        elseif ($this->filled("email")){
            return [
                "email" => ["required", "email"],
                "token" => "required",
                'password' => ['required', 'min:8', 'confirmed'],
            ];
        }
        else
            return [];
    }

    #[ArrayShape(["mobile.required" => "string", "mobile.regex" => "string", "email.required" => "string", "email.email" => "string", "token.required" => "string", "password.required" => "string", "password.min" => "string", "password.confirmed" => "string"])] public function messages(): array
    {
        return [
            "mobile.required" => "شماره موبایل مشخص نمی باشد",
            "mobile.regex" => "شماره موبایل دارای فرمت صحیح نمی باشد",
            "email.required" => "آدرس ایمیل مشخص نمی باشد",
            "email.email" => "آدرس ایمیل دارای فرمت صحیح نمی باشد",
            "token.required" => "کد تایید بازنشانی گذرواژه مشخص نمی باشد",
            "password.required" => "درج گذرواژه جدید الزامی می باشد",
            "password.min" => "طول گذرواژه جدید باید حداقل 8 کاراکتر باشد",
            "password.confirmed" => "درج تکرار گذرواژه جدید الزامی می باشد",
        ];
    }
}
