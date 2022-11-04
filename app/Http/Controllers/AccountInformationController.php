<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountInformationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class AccountInformationController extends Controller
{
    public function show(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $user = auth()->user();
            if ($user->is_admin)
                return view("admin.account_information",["user" => $user]);
            elseif ($user->is_staff)
                return view("staff.account_information",["user" => $user]);
            else
                abort(404);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function update(AccountInformationRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $user = User::query()->findOrFail(Auth::id());
            $old_mobile = $user->mobile;$old_email = $user->email;
            $user->update($validated);
            $old_email != $user->email ?: $user->update(["email_verified_at" => null]);
            $old_mobile != $user->mobile ?: $user->update(["mobile_verified_at" => null]);
            DB::commit();
            return redirect()->back()->with(["result" =>  "success" , "message" => "updated"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
