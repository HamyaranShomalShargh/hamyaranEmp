<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\Contract;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Throwable;

class StaffUserController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        Gate::authorize('index',"StaffUsers");
        $users = User::query()->with(["user","role"])->where("is_staff","=",1)->get();
        $roles = Role::all();
        return view("staff.users",["users" => $users,"roles" => $roles,"contracts" => Contract::Output()]);
    }

    public function store(StaffUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"StaffUsers");
        try{
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $validated["password"] = Hash::make($validated["password"]);
            $validated["is_staff"] = 1;
            $validated["is_admin"] = 0;
            $user = User::query()->create($validated);
            if ($validated["contracts"])
                $user->contracts()->syncWithPivotValues($validated["contracts"], ['user_id' => Auth::id()]);
            if ($request->hasFile("upload_file")){
                Storage::disk("staff_signs")->put($user->id,$request->file("upload_file"));
                $user->update(["sign" => $request->file("upload_file")->hashName(),"sign_hash" => Hash::make($user->id.$user->username)]);
            }
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "saved"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        Gate::authorize('edit',"StaffUsers");
        try {
            $user = User::query()->with(["contracts"])->findOrFail($id);
            $roles = Role::all();
            $sign = '';
            if ($user->sign && Storage::disk("staff_signs")->exists("$user->id/$user->sign"))
                $sign = base64_encode(Storage::disk("staff_signs")->get("$user->id/$user->sign"));
            return view("staff.edit_user",["user" => $user,"sign" => $sign,"roles" => $roles,"contracts" => Contract::Output()]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(StaffUserRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"StaffUsers");
        try{
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $validated["password"] = Hash::make($validated["password"]);
            $validated["is_staff"] = 1;
            $validated["is_admin"] = 0;
            $user = User::query()->findOrFail($id);
            $user->update($validated);
            if ($validated["contracts"])
                $user->contracts()->syncWithPivotValues($validated["contracts"], ['user_id' => Auth::id()]);
            if ($request->hasFile("upload_file")){
                Storage::disk("staff_signs")->deleteDirectory($user->id);
                Storage::disk("staff_signs")->put($user->id,$request->file("upload_file"));
                $user->update(["sign" => $request->file("upload_file")->hashName(),"sign_hash" => Hash::make($user->id.$user->username)]);
            }
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "updated"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete',"StaffUsers");
        try {
            $user = User::query()->with(["role","menu_headers","menu_items","menu_actions"])->findOrFail($id);
            foreach ($user->relationsToArray() as $relation){
                if ($relation != [] && $relation != null)
                    return redirect()->back()->with(["result" => "warning","message" => "relation_exists"]);
            }
            $user->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function status($id): \Illuminate\Http\RedirectResponse
    {
        $user = User::query()->findOrFail($id);
        return redirect()->back()->with(["result" => "success","message" => $this->activation($user)]);
    }
}
