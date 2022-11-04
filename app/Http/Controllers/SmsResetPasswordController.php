<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordResetRequest;
use App\Models\SmsPasswordReset;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class SmsResetPasswordController extends Controller
{
    public function reset_form($mobile,$token): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $token_record = SmsPasswordReset::query()->where("token","=",$token)->where("mobile","=",$mobile)->first();
        if ($token_record) {
            $created_at = new Carbon($token_record->created_at);
            if ($created_at->diffInSeconds(Carbon::now()) <= 600)
                return view("auth.passwords.sms_reset",["mobile" => $mobile,"token" => $token]);
        }
        abort(410);
    }

    public function reset(PasswordResetRequest $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $validated = $request->validated();
            $token_record = SmsPasswordReset::query()->where("token","=",$validated["token"])->first();
            if ($token_record){
                $created_at = new Carbon($token_record->created_at);
                if ($created_at->diffInSeconds(Carbon::now()) > 600)
                    throw ValidationException::withMessages([
                        'general_error' => 'زمان بازنشانی گذرواژه منقضی شده است',
                    ]);
                elseif ($token_record->mobile != $validated["mobile"])
                    throw ValidationException::withMessages([
                        'general_error' => 'شماره موبایل با کد اعتبارسنجی همخوانی ندارند',
                    ]);
                else{
                    $user = User::query()->where("mobile","=",$token_record->mobile)->first();
                    $user->update(["password" => Hash::make($validated["password"])]);
                    $token_record->delete();
                    return view("auth.passwords.success_reset");
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
