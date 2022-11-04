<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserPasswordResetController extends Controller
{
    public function show(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $user = Auth::user();
            if ($user->is_admin)
                return view("admin.change_password");
            elseif ($user->is_staff)
                return view("staff.change_password");
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function change(UserPasswordResetRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $user = User::query()->findOrFail(Auth::id());
            $validated = $request->validated();
            if(Hash::check($validated["old_password"],$user->password)){
                $user->update(["password" => Hash::make($validated["password"])]);
                DB::commit();
                return redirect()->back()->with(["result" =>  "success" , "message" => "updated"]);
            }
            else
                return redirect()->back()->withErrors(["logical" => "گذرواژه فعلی صحیح نمی باشد"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
