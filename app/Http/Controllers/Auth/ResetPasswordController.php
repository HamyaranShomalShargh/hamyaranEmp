<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', 'min:8'],
        ];
    }
    protected function validationErrorMessages()
    {
        return [
            'token.required' => "کد اعتبارسنجی مشخص نمی باشد",
            'email.required' => "پست الکترونیکی مشخص نمی باشد",
            'email.email' => "فرمت پست الکترونیکی صحیح نمی باشد",
            'password.required' => "درج گذرواژه الزامی می باشد", 'password.confirmed' => "درج تکرار گذرواژه الزامی می باشد",
            'password.min' => "طول گذرواژه باید حداقل 8 کاراکتر باشد"
        ];
    }
}
