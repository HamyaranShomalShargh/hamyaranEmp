<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class AccountInformationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    #[ArrayShape(["name" => "string", "username" => "string", "mobile" => "string[]", "email" => "string"])] public function rules(): array
    {
        return [
            "name" => "required",
            "username" => "required",
            "mobile" => ["required","regex:/^09(1[0-9]|9[0-2]|2[0-2]|0[1-5]|41|3[0,3,5-9])\d{7}$/","unique:users,mobile,".auth()->user()->id],
            "email" => ["required","email","unique:users,email,".auth()->user()->id],
        ];
    }

    #[ArrayShape(["name.required" => "string", "username.required" => "string", "mobile.required" => "string", "mobile.regex" => "string", "mobile.unique" => "string", "email.required" => "string", "email.email" => "string", "email.unique" => "string"])] public function messages(): array
    {
        return [
            "name.required" => "درج نام الزامی می باشد",
            "username.required" => "درج نام کاربری الزامی می باشد",
            "mobile.required" => "درج تلفن همراه الزامی می باشد",
            "mobile.regex" => "فرمت تلفن همراه صحیح نمی باشد",
            "mobile.unique" => "تلفن همراه تکراری می باشد",
            "email.required" => "درج پست الکترونیکی الزامی می باشد",
            "email.email" => "فرمت پست الکترونیکی صحیح نمی باشد",
            "email.unique" => "پست الکترونیکی تکراری می باشد",
        ];
    }
}
