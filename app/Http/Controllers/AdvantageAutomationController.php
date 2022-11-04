<?php

namespace App\Http\Controllers;

use App\Models\AdvantageAutomation;
use App\Models\ContractSubset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Throwable;

class AdvantageAutomationController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"AdvantageAutomation");
        try {
            $contracts = ContractSubset::permitted_contracts();
            if ($contracts != []) {
                $advantage_automation_inbox = AdvantageAutomation::query()->with(["employee.contract", "current_role", "user", "advantage"])
                    ->whereHas("current_role", function ($query) {
                        $query->where("id", "=", Auth::user()->role->id);
                    })->whereHas("employee.contract", function ($query) use ($contracts) {
                        $query->whereIn("contract_subset_id", $contracts->pluck("id"));
                    })->where("is_finished", "=", 0)->orderBy("id", "desc")->get();
                $advantage_automation_outbox = AdvantageAutomation::query()->with(["employee.contract", "current_role", "user", "advantage"])
                    ->whereHas("signs", function ($query) {
                        $query->where("user_id", "=", Auth::id());
                    })->where("is_finished","=",1)->orderBy("id", "desc")->get();
                return view("staff.advantage_automation", [
                    "advantage_automation_inbox" => $advantage_automation_inbox,
                    "advantage_automation_outbox" => $advantage_automation_outbox
                ]);
            }
            else
                return redirect()->back()->withErrors(["result" => "به حساب کاربری شما هیچ قرارداد فعال و دارای گردش اتوماسیون اختصاص داده نشده است"]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function details($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('details',"AdvantageAutomation");
        try {
            $automation = AdvantageAutomation::query()->where("role_id","=",Auth::user()->role->id)->with([
                "employee.contract",
                "advantage.attachments",
                "advantage.attachments",
                "comments.user","signs.user"])->findOrFail($id);
            $automation->update(["is_read" => 1]);
            $final_role = $automation->final_automation_role();
            return view("staff.advantage_details",
                [
                    "automation" => $automation,
                    "final_role" => $final_role
                ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function agree(Request $request,$id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('agree',"AdvantageAutomation");
        try {
            DB::beginTransaction();
            $automation = AdvantageAutomation::query()->with("advantage.automation_flow.details")->findOrFail($id);
            if ($request->filled("comment"))
                $automation->comments()->updateOrCreate(["commentable_id" => $automation->id,"user_id" => Auth::id()],[
                    "user_id" => Auth::id(),
                    "comment" => $request->input("comment")
                ]);
            $automate = $automation->automate("forward");
            if($automate)
                $this->send_notification($automate["users"],$automate["data"]);
            $automation->signs()->updateOrCreate(["signable_id" => $automation->id,"user_id" => Auth::id()],[
                "user_id" => Auth::id(),
                "sign" => Auth::user()->sign
            ]);
            DB::commit();
            return redirect()->route("AdvantageAutomation.index")->with(["result" => "success","message" => "sent"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function disagree(Request $request,$id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('disagree',"AdvantageAutomation");
        try {
            DB::beginTransaction();
            $automation = AdvantageAutomation::query()->with("advantage.automation_flow.details")->findOrFail($id);
            if ($request->filled("comment"))
                $automation->comments()->updateOrCreate(["commentable_id" => $automation->id,"user_id" => Auth::id()],[
                    "user_id" => Auth::id(),
                    "comment" => $request->input("comment")
                ]);
            if ($sign = $automation->signs()->where("user_id","=",Auth::id()))
                $sign->delete();
            $automate = $automation->automate("backward");
            if($automate)
                $this->send_notification($automate["users"],$automate["data"]);
            DB::commit();
            return redirect()->route("AdvantageAutomation.index")->with(["result" => "success","message" => "referred"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
