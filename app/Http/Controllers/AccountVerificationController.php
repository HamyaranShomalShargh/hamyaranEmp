<?php

namespace App\Http\Controllers;

use App\Models\EmailPasswordReset;
use App\Models\SmsPasswordReset;
use App\Models\User;
use App\Notifications\EmailVerification;
use App\Notifications\PasswordResetEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Throwable;

class AccountVerificationController extends Controller
{
    public function show(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $user = auth()->user();
            if ($user->is_admin)
                return view("admin.account_verification",["user" => $user]);
            elseif ($user->is_staff)
                return view("staff.account_verification",["user" => $user]);
            else
                abort(404);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function send(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $user = User::query()->findOrFail(Auth::id());
            if ($request->filled("mobile")){
                $token_operation = SmsPasswordReset::send($user->mobile);
                if ($token_operation["result"] == "exist") {
                    throw ValidationException::withMessages([
                        'general_error' => 'لینک تاییدیه پیامک شده قبلی تا 10 دقیقه معتبر می باشد',
                    ]);
                } elseif ($token_operation["result"] == "operated") {
                    $text = env('SMS_VERIFICATION_TEXT') . "\n\r" . url(route("account.verification.mobile", ["mobile" => $user->mobile, "token" => $token_operation["token"]]));
                    if ($this->send_sms([$user->mobile], $text) > 0)
                        DB::commit();
                    else {
                        throw ValidationException::withMessages([
                            'general_error' => 'خطا در ارسال پیامک !',
                        ]);
                    }
                }
            }
            if ($request->filled("email")){
                $token_operation = EmailPasswordReset::send($user->email);
                if ($token_operation["result"] == "exist") {
                    throw ValidationException::withMessages([
                        'general_error' => 'لینک تاییدیه ایمیل شده قبلی تا 10 دقیقه معتبر می باشد',
                    ]);
                } elseif ($token_operation["result"] == "operated") {
                    Notification::send($user, new EmailVerification($token_operation["token"],$user->email));
                    DB::commit();
                }
            }
            return redirect()->back()->with(["result" => "success","message" => "sent"]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function verify_mobile($mobile,$token): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $token_record = SmsPasswordReset::query()->where("token","=",$token)->first();
            if ($token_record){
                $created_at = new Carbon($token_record->created_at);
                if ($created_at->diffInSeconds(Carbon::now()) > 600)
                    throw ValidationException::withMessages([
                        'general_error' => 'زمان فعال سازی شماره موبایل منقضی شده است',
                    ]);
                elseif ($token_record->mobile != $mobile)
                    throw ValidationException::withMessages([
                        'general_error' => 'شماره موبایل با کد اعتبارسنجی همخوانی ندارند',
                    ]);
                else{
                    $user = User::query()->where("mobile","=",$token_record->mobile)->first();
                    $user->update(["mobile_verified_at" => Carbon::now()]);
                    $token_record->delete();
                    return view("auth.success_verify");
                }
            }
            else
                throw ValidationException::withMessages([
                    'general_error' => 'کد اعتبارسنجی معتبر نمی باشد',
                ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function verify_email($email,$token): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $token_record = EmailPasswordReset::query()->where("token","=",$token)->first();
            if ($token_record){
                $created_at = new Carbon($token_record->created_at);
                if ($created_at->diffInSeconds(Carbon::now()) > 600)
                    throw ValidationException::withMessages([
                        'general_error' => 'زمان فعال سازی ایمیل منقضی شده است',
                    ]);
                elseif ($token_record->email != $email)
                    throw ValidationException::withMessages([
                        'general_error' => 'آدرس ایمیل با کد اعتبارسنجی همخوانی ندارند',
                    ]);
                else{
                    $user = User::query()->where("email","=",$token_record->email)->first();
                    $user->update(["email_verified_at" => Carbon::now()]);
                    $token_record->delete();
                    DB::commit();
                    return view("auth.success_verify");
                }
            }
            else
                throw ValidationException::withMessages([
                    'general_error' => 'کد اعتبارسنجی معتبر نمی باشد',
                ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
