<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected string $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username(): string
    {
        return 'username';
    }
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'captcha' => 'required|captcha'
        ],[
            $this->username().".required" => "درج نام کاربری الزامی می باشد",
            "password.required" => "درج کلمه عبور الزامی می باشد.",
            "captcha.required" => "لطفا کد امنیتی را وارد نمایید",
            "captcha.captcha" => "کد امنیتی وارد شده صحیح نمی باشد."
        ]);
    }
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            "login_failed" => ["اطلاعات وارد شده در سیستم موجود نمی باشد!"]
        ]);
    }
    protected function authenticated(Request $request, $user): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        if ($user->inactive) {
            $this->logout($request);
            return redirect()->route("login")->withErrors(["login_failed" => "حساب کاربری شما مسدود می باشد"]);
        }
        $user->update([
            "last_ip_address" => $request->ip(),
            "last_activity" => Carbon::now()
        ]);
        return redirect("Dashboard/");
    }
}
