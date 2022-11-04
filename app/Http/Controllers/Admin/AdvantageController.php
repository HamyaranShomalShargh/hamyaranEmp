<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdvantageRequest;
use App\Models\Advantage;
use App\Models\AutomationFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class AdvantageController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $advantages = Advantage::query()->with("user")->get();
            $automation_flows = AutomationFlow::query()->where("inactive","=",0)->get();
            return view("admin.advantages",["advantages" => $advantages,"automation_flows" => $automation_flows]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(AdvantageRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $advantage = Advantage::query()->create([
                "name" => $validated["name"],
                "user_id" => Auth::id(),
                "automation_flow_id" => $validated["automation_flow_id"]
            ]);
            $texts = [];$files = [];
            $period = $request->filled("period") ? 1 : 0;
            if ($request->filled("advantage_list")){
                $advantage_attachments = json_decode($request->input("advantage_list"),true);
                foreach ($advantage_attachments as $attachment)
                    $attachment["kind"] == "text" ? $texts[] = $attachment["name"] : $files[] = $attachment["name"];
            }
            $advantage->attachments()->create([
                "texts" => json_encode($texts,JSON_UNESCAPED_UNICODE),
                "files" => json_encode($files,JSON_UNESCAPED_UNICODE),
                "period" => $period
            ]);
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
            $advantage = Advantage::query()->with(["user","attachments"])->findOrFail($id);
            $automation_flows = AutomationFlow::query()->where("inactive","=",0)->get();
            return view("admin.edit_advantage",["advantage" => $advantage,"attachments" => Advantage::make_items_list($id),"automation_flows" => $automation_flows]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }

    }

    public function update(AdvantageRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $advantage = Advantage::query()->findOrFail($id);
            $advantage->update([
                "name" => $validated["name"],
                "user_id" => Auth::id(),
                "automation_flow_id" => $validated["automation_flow_id"]
            ]);
            $advantage->attachments()->delete();
            $texts = [];$files = [];
            $period = $request->filled("period") ? 1 : 0;
            if ($request->filled("advantage_list")){
                $advantage_attachments = json_decode($request->input("advantage_list"),true);
                foreach ($advantage_attachments as $attachment)
                    $attachment["kind"] == "text" ? $texts[] = $attachment["name"] : $files[] = $attachment["name"];
            }
            $advantage->attachments()->create([
                "texts" => json_encode($texts,JSON_UNESCAPED_UNICODE),
                "files" => json_encode($files,JSON_UNESCAPED_UNICODE),
                "period" => $period
            ]);
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
            $advantage = Advantage::query()->findOrFail($id);
            $advantage->delete();
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
        $advantage = Advantage::query()->findOrFail($id);
        return redirect()->back()->with(["result" => "success","message" => $this->activation($advantage)]);
    }
}
