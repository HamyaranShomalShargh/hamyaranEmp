<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuItemRequest;
use App\Models\MenuAction;
use App\Models\MenuHeader;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MenuItemController extends Controller
{
    public function index()
    {
        try {
            $menu_items = MenuItem::query()->with(["user","menu_header"])->get();
            $menu_headers = MenuHeader::all();
            $menu_actions = MenuAction::all();
            return view("admin.menu_items",["menu_items" => $menu_items,"menu_headers" => $menu_headers,"menu_actions" => $menu_actions]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(MenuItemRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            if ($validated["main"]){
                $menu_action_ids = $validated["menu_action_id"];
                unset($validated["menu_action_id"]);
                $main_route = MenuAction::query()->findOrFail($validated["main"]);
                $validated["main_route"] = $main_route->action;
                unset($validated["main"]);
                $menu_item = MenuItem::query()->create($validated);
                $menu_item->actions()->sync($menu_action_ids);
            }
            else
                $menu_item = MenuItem::query()->create($validated);
            if ($request->hasFile('upload_file')) {
                $menu_item->update(["icon" => $request->file('upload_file')->hashName()]);
                Storage::disk('menu_item_icons')->put($menu_item->id, $request->file('upload_file'));
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
            $menu_item = MenuItem::query()->findOrFail($id);
            $menu_items = MenuItem::all();
            $menu_headers = MenuHeader::all();
            $menu_actions = MenuAction::all();
            return view("admin.edit_menu_item",["menu_item" => $menu_item,"menu_items" => $menu_items,"menu_headers" => $menu_headers,"menu_actions" => $menu_actions]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(MenuItemRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $menu_item = MenuItem::query()->findOrFail($id);
            $menu_item->actions()->detach();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            if ($validated["main"]){
                $menu_action_ids = $validated["menu_action_id"];
                unset($validated["menu_action_id"]);
                $main_route = MenuAction::query()->findOrFail($validated["main"]);
                $validated["main_route"] = $main_route->action;
                unset($validated["main"]);
                $menu_item->update($validated);
                $menu_item->actions()->sync($menu_action_ids);
            }
            else
                $menu_item->update($validated);
            if ($request->hasFile('upload_file')) {
                Storage::disk("menu_item_icons")->delete("{$menu_item->id}/$menu_item->icon");
                $menu_item->update(["icon" => $request->file('upload_file')->hashName()]);
                Storage::disk('menu_item_icons')->put($menu_item->id, $request->file('upload_file'));
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
            $menu_item = MenuItem::query()->findOrFail($id);
            if ($menu_item->role()->exists() || $menu_item->children()->exists())
                return redirect()->back()->with(["result" => "warning","message" => "relation_exists"]);
            $menu_item->actions()->detach();
            $menu_item->delete();
            if (Storage::disk("menu_item_icons")->exists("{$menu_item->id}"))
                Storage::disk("menu_item_icons")->deleteDirectory("{$menu_item->id}");
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
