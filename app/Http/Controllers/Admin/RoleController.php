<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\MenuHeader;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class RoleController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $menu_headers = MenuHeader::query()->with(["items.actions","items.children"])->get();
            $roles = Role::query()->with("user")->get();
            return view("admin.roles", ["roles" => $roles, "menu_headers" => $menu_headers]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(RoleRequest $request): \Illuminate\Http\RedirectResponse
    {
        try{
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            DB::beginTransaction();
            $role = Role::query()->create([
                "name" => $validated["name"],
                "user_id" => auth()->id()
            ]);
            foreach ($validated["role_menu"] as $menu_string) {
                $exploded = explode("#",$menu_string);
                $role->menu_items()->attach([$exploded[0] => ["menu_action_id" => $exploded[1],"route" =>  $exploded[2],"user_id" => Auth::id()]]);
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
        try {
            $menu_headers = MenuHeader::query()->with(["items.actions","items.children"])->get();
            $role = Role::query()->with("menu_items.actions")->findOrFail($id);
            return view("admin.edit_role", ["role" => $role, "menu_headers" => $menu_headers]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(RoleRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $role = Role::query()->findOrFail($id);
            $role->update([
                "name" => $validated["name"],
                "user_id" => auth()->id()
            ]);
            $role->menu_items()->detach();
            foreach ($validated["role_menu"] as $menu_string) {
                $exploded = explode("#",$menu_string);
                $role->menu_items()->attach([$exploded[0] => ["menu_action_id" => $exploded[1],"route" =>  $exploded[2],"user_id" => Auth::id()]]);
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
        try {
            DB::beginTransaction();
            $role = Role::query()->findOrFail($id);
            if ($role->users()->exists())
                return redirect()->back()->with(["result" => "warning","message" => "relation_exists"]);
            $role->menu_items()->detach();
            $role->delete();
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
        $user = Role::query()->findOrFail($id);
        return redirect()->back()->with(["result" => "success","message" => $this->activation($user)]);
    }
}
