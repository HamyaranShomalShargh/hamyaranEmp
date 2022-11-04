<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordResetRequest;
use App\Models\EmailPasswordReset;
use App\Models\SmsPasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class EmailResetPasswordController extends Controller
{
    public function reset_form($email,$token): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $token_record = EmailPasswordReset::query()->where("token","=",$token)->where("email","=",$email)->first();
        if ($token_record) {
            $created_at = new Carbon($token_record->created_at);
            if ($created_at->diffInSeconds(Carbon::now()) <= 600)
                return view("auth.passwords.email_reset",["email" => $email,"token" => $token]);
        }
        abort(410);
    }

    public function reset(PasswordResetRequest $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $token_record = EmailPasswordReset::query()->where("token","=",$validated["token"])->first();
            if ($token_record){
                $created_at = new Carbon($token_record->created_at);
                if ($created_at->diffInSeconds(Carbon::now()) > 600)
                    throw ValidationException::withMessages([
                        'general_error' => 'زمان بازنشانی گذرواژه منقضی شده است',
                    ]);
                elseif ($token_record->email != $validated["email"])
                    throw ValidationException::withMessages([
                        'general_error' => 'آدرس ایمیل با کد اعتبارسنجی همخوانی ندارند',
                    ]);
                else{
                    $user = User::query()->where("email","=",$token_record->email)->first();
                    $user->update(["password" => Hash::make($validated["password"])]);
                    $token_record->delete();
                    DB::commit();
                    return view("auth.passwords.success_reset");
                }
            }
            else
                throw ValidationException::withMessages([
                    'general_error' => 'کد اعتبارسنجی معتبر نمی باشد',
                ]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
