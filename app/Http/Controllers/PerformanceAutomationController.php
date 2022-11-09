<?php

namespace App\Http\Controllers;

use App\Exports\NewPerformanceExport;
use App\Models\ContractSubset;
use App\Models\Employee;
use App\Models\Performance;
use App\Models\PerformanceAutomation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class PerformanceAutomationController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"PerformanceAutomation");
        try {
            $contracts = ContractSubset::permitted_contracts();
            if($contracts != []) {
                $performance_automation_inbox = PerformanceAutomation::query()->with(["authorized_date", "current_role", "contract","user","performances"])
                    ->whereHas("current_role", function ($query) {
                        $query->where("id", "=", Auth::user()->role->id);
                    })->whereHas("contract", function ($query) use ($contracts) {
                        $query->whereIn("contract_subset_id", $contracts->pluck("id"));
                    })->where("is_finished","=",0)->orderBy("id", "desc")->get();
                $performance_automation_outbox = PerformanceAutomation::query()->with(["authorized_date", "current_role", "contract","user"])
                    ->whereHas("signs", function ($query) {
                        $query->where("user_id", "=", Auth::id());
                    })->orderBy("id", "desc")->get();
                return view("staff.performance_automation",
                    [
                        "performance_automation_inbox" => $performance_automation_inbox,
                        "performance_automation_outbox" => $performance_automation_outbox,
                        "contracts" => $contracts,
                        "month_names" => $this->month_names()
                    ]);
            }
            else
                return redirect()->back()->withErrors(["result" => "به حساب کاربری شما هیچ قرارداد فعال و دارای گردش کارکردی اختصاص داده نشده است"]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function details($id,$outbox = null): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('details',"PerformanceAutomation");
        try {
            if ($outbox)
                $automation = PerformanceAutomation::query()->whereHas("signs", function ($query) {
                    $query->where("user_id", "=", Auth::id());
                })->with([
                    "contract",
                    "performances.employee",
                    "authorized_date",
                    "comments.user",
                    "attributes.items",
                    "signs.user"])->findOrFail($id);
            else
                $automation = PerformanceAutomation::query()->whereHas("current_role", function ($query) {
                    $query->where("id", "=", Auth::user()->role->id);
                })->with([
                    "contract",
                    "performances.employee",
                    "authorized_date",
                    "comments.user",
                    "attributes.items",
                    "signs.user"])->findOrFail($id);
            $automation->update(["is_read" => 1]);
            $final_role = $automation->final_automation_role();
            $employee_performances = $automation->performances->toArray();
            $automation->performances->map(function ($item) use ($employee_performances) {
                $search_data = array_column($employee_performances, "employee_id");
                $index = array_search($item->employee->id, $search_data);
                if (gettype($index) !== 'boolean') {
                    $item->employee["performance_data"] = json_decode($employee_performances[$index]["data"]);
                }
            });
            return view("staff.performance_details",
                [
                    "automation" => $automation,
                    "contract_subset" => $automation->contract,
                    "authorized_date" => $automation->authorized_date,
                    "final_role" => $final_role,
                    "outbox" => $outbox != null
                ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function agree(Request $request,$id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('agree',"PerformanceAutomation");
        try {
            $request->validate(["employees_data" => "required"], ["employees_data.required" => "اطلاعات کارکرد پرسنل ارسال نشده است"]);
            $employees_data = json_decode($request->input("employees_data"),true);
            if ($employees_data) {
                $automation = PerformanceAutomation::query()->findOrFail($id);
                foreach ($employees_data["performances"] as $item) {
                    Performance::query()->updateOrCreate(["performance_automation_id" => $automation->id, "employee_id" => $item["employee"]["id"]], [
                        "performance_automation_id" => $automation->id,
                        "employee_id" => $item["employee"]["id"],
                        "job_group" => $item["job_group"],
                        "daily_wage" => $item["daily_wage"],
                        "data" => json_encode($item["employee"]["performance_data"], JSON_UNESCAPED_UNICODE)
                    ]);
                    $employee_edit = Employee::query()->findOrFail($item["employee"]["id"]);
                    $employee_edit->update(["job_group" => $item["job_group"],"daily_wage" => $item["daily_wage"]]);
                }
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
                return redirect()->route("PerformanceAutomation.index")->with(["result" => "success","message" => "sent"]);
            }
            return redirect()->back()->withErrors(["result" => "فرمت اطلاعات کارکرد پرسنل صحیح نمی باشد"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function disagree(Request $request,$id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('disagree',"PerformanceAutomation");
        try {
            DB::beginTransaction();
            $automation = PerformanceAutomation::query()->findOrFail($id);
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
            return redirect()->route("PerformanceAutomation.index")->with(["result" => "success","message" => "referred"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function performance_export_excel($id,$authorized_date_id = null): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {

        return Excel::download(new NewPerformanceExport($id,$authorized_date_id,true), 'performance.xlsx');

    }
}
