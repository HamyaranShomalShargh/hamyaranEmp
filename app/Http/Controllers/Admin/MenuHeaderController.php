<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuHeaderRequest;
use App\Models\MenuHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MenuHeaderController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $menu_headers = MenuHeader::query()->with("user")->get();
        return view("admin.menu_headers",["menu_headers" => $menu_headers]);
    }

    public function store(MenuHeaderRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            DB::beginTransaction();
            $menu_header = MenuHeader::query()->create($validated);
            if ($request->hasFile("upload_file")){
                Storage::disk("menu_header_icons")->put($menu_header->id,$request->file("upload_file"));
                $menu_header->update(["icon" => $request->file("upload_file")->hashName()]);
            }
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "saved"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $menu_header = MenuHeader::query()->findOrFail($id);
        return view("admin.edit_menu_header",["menu_header" => $menu_header]);
    }

    public function update(MenuHeaderRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            DB::beginTransaction();
            $menu_header = MenuHeader::query()->findOrFail($id);
            $menu_header->update($validated);
            if ($request->hasFile("upload_file")){
                Storage::disk("menu_header_icons")->deleteDirectory($menu_header->id);
                Storage::disk("menu_header_icons")->put($menu_header->id,$request->file("upload_file"));
                $menu_header->update(["icon" => $request->file("upload_file")->hashName()]);
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
            $menu_header = MenuHeader::query()->findOrFail($id);
            if ($menu_header->items()->exists())
                return redirect()->back()->with(["result" => "warning","message" => "relation_exists"]);
            if(Storage::disk("menu_header_icons")->exists("$menu_header->id/$menu_header->icon"))
                Storage::disk("menu_header_icons")->deleteDirectory($menu_header->id);
            $menu_header->delete();
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
        $menu_header = MenuHeader::query()->findOrFail($id);
        return redirect()->back()->with(["result" => "success","message" => $this->activation($menu_header)]);
    }
}
