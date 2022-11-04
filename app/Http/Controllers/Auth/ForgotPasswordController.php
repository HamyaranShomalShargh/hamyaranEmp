<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SmsPasswordReset;
use \App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function showLinkRequestForm(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('auth.passwords.reset_request_form');
    }

    /**
     * @throws ValidationException
     */
    public function sendResetLinkEmail(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $this->validateInformation($request);
        if ($request->input("type") == "mobile") {
            DB::beginTransaction();
            $token_operation = SmsPasswordReset::send($request->input("mobile"));
            if ($token_operation["result"] == "exist"){
                DB::rollBack();
                throw ValidationException::withMessages([
                    'general_error' => 'آدرس بازنشانی قبلی تا 10 دقیقه معتبر می باشد',
                ]);
            }
            elseif ($token_operation["result"] == "operated") {
                $text = env('SMS_PASSWORD_RESET_TEXT')."\n\r".url(route("password.reset.sms",["mobile" => $request->input("mobile"),"token" => $token_operation["token"]]));
                if ($this->send_sms([$request->input("mobile")],$text) > 0) {
                    DB::commit();
                    return redirect()->back()->with(["success" => "sent"]);
                }
                else {
                    DB::rollBack();
                    throw ValidationException::withMessages([
                        'general_error' => 'خطا در ارسال پیامک !',
                    ]);
                }
            }
        } else {
            $response = $this->broker()->sendResetLink(
                $this->credentials($request)
            );

            return $response == Password::RESET_LINK_SENT
                ? $this->sendResetLinkResponse($request, $response)
                : $this->sendResetLinkFailedResponse($request, $response);
        }
    }

    /**
     * @throws ValidationException
     */
    protected function validateInformation(Request $request)
    {
        $request->validate(["type" => "required"],["type.required" => "انتخاب نوع ارسال الزامی می باشد."]);
        if ($request->input("type") == "mobile"){
            $request->validate(['mobile' => 'required','captcha' => 'required|captcha'],
                ["mobile.required" => "درج شماره موبایل الزامی می باشد","captcha.required" => "درج کد امنیتی الزامی می باشد"]);
            $user = User::query()->where("mobile","=",$request->input("mobile"))->first();
            if ($user){
                if ($user->inactive)
                    throw ValidationException::withMessages([
                        'general_error' => 'حساب کاربری مسدود می باشد',
                    ]);
                elseif ($user->mobile_verified_at == null)
                    throw ValidationException::withMessages([
                        'general_error' => 'شماره موبایل مورد تایید قرار نگرفته است',
                    ]);
            }
            else{
                throw ValidationException::withMessages([
                    'general_error' => 'کاربری با این شماره موبایل وجود ندارد',
                ]);
            }
        }
        elseif ($request->input("type") == "email"){
            $request->validate(['email' => 'required|email','captcha' => 'required|captcha'],
                ["email.required" => "درج آدرس ایمیل الزامی می باشد","email.email" => "فرمت آدرس ایمیل صحیح نمی باشد","captcha.required" => "درج کد امنیتی الزامی می باشد"]);
        }
        else{
            throw ValidationException::withMessages([
                'general_error' => 'نوع ارسال مشخص نمی باشد',
            ]);
        }
    }
}
