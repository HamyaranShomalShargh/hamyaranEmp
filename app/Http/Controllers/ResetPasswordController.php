<?php

namespace App\Http\Controllers;

use App\Models\EmailPasswordReset;
use App\Models\SmsPasswordReset;
use App\Models\User;
use App\Notifications\PasswordResetEmail;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    public function showForm(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('auth.passwords.reset_request_form');
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
            $user = User::query()->where("email","=",$request->input("email"))->first();
            if ($user){
                if ($user->inactive)
                    throw ValidationException::withMessages([
                        'general_error' => 'حساب کاربری مسدود می باشد',
                    ]);
                elseif ($user->email_verified_at == null)
                    throw ValidationException::withMessages([
                        'general_error' => 'آدرس ایمیل مورد تایید قرار نگرفته است',
                    ]);
            }
            else{
                throw ValidationException::withMessages([
                    'general_error' => 'کاربری با این آدرس ایمیل وجود ندارد',
                ]);
            }
        }
        else{
            throw ValidationException::withMessages([
                'general_error' => 'نوع ارسال مشخص نمی باشد',
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    public function sendResetLink(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            $this->validateInformation($request);
            DB::beginTransaction();
            if ($request->input("type") == "mobile") {
                $token_operation = SmsPasswordReset::send($request->input("mobile"));
                if ($token_operation["result"] == "exist") {
                    throw ValidationException::withMessages([
                        'general_error' => 'آدرس بازنشانی قبلی تا 10 دقیقه معتبر می باشد',
                    ]);
                } elseif ($token_operation["result"] == "operated") {
                    $text = env('SMS_PASSWORD_RESET_TEXT') . "\n\r" . url(route("password.reset.sms", ["mobile" => $request->input("mobile"), "token" => $token_operation["token"]]));
                    if ($this->send_sms([$request->input("mobile")], $text) > 0) {
                        DB::commit();
                        return redirect()->back()->with(["success_m" => "sent_mobile"]);
                    } else {
                        throw ValidationException::withMessages([
                            'general_error' => 'خطا در ارسال پیامک !',
                        ]);
                    }
                }
            } elseif ($request->input("type") == "email") {
                {
                    $user = User::query()->where("email","=",$request->input("email"))->first();
                    $token_operation = EmailPasswordReset::send($request->input("email"));
                    if ($token_operation["result"] == "exist") {
                        throw ValidationException::withMessages([
                            'general_error' => 'آدرس بازنشانی قبلی تا 10 دقیقه معتبر می باشد',
                        ]);
                    } elseif ($token_operation["result"] == "operated") {
                        Notification::send($user, new PasswordResetEmail($token_operation["token"]));
                        DB::commit();
                        return redirect()->back()->with(["success_e" => "sent_email"]);
                    }
                }
            }
        } catch (ValidationException $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'general_error' => $e->getMessage(),
            ]);
        }
    }
}
