<?php

namespace App\Http\Controllers;

use App\Exports\NewPerformanceExport;
use App\Models\ContractSubset;
use App\Models\Performance;
use App\Models\PerformanceAutomation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class PerformanceController extends Controller
{

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"Performances");
        try {
            $performances = PerformanceAutomation::query()->with(["performances.employee","authorized_date","current_role","contract","user"])
                ->whereHas("user",function ($query){$query->where("id","=",Auth::id());})->orderBy("id","desc")->get();
            $contracts = ContractSubset::permitted_contracts();
            return view("staff.performances",["performances" => $performances,"contracts" => $contracts]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function create(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"Performances");
        try {
            $request->validate(["contract_id" => "required"],["contract_id.required" => "انتخاب قرارداد الزامی می باشد"]);
            $contract_subset = ContractSubset::query()->with(["contract","employees","performance_automation.performances.employee","performance_attribute.items"])->findOrFail($request->input("contract_id"));
            if (count($authorized_date = $contract_subset->entry_date_check()) == 0)
                return redirect()->back()->withErrors(["result" => "مهلت ایجاد و ارسال اطلاعات کارکرد ".verta()->format("F")." ماه ".$contract_subset->workplace." به اتمام رسیده است"]);
            if ($performance_automation = $contract_subset->check_automation($authorized_date)) {
                if ($performance_automation->is_committed == 1)
                    return redirect()->back()->withErrors(["result" => "کارکرد " . verta()->format("F") . " ماه " . $contract_subset->workplace . " تایید و ارسال نهایی شده است. جهت ویرایش و یا حذف، نیاز به ارجاع آن می باشد"]);
                else
                    return redirect()->back()->withErrors(["result" => "کارکرد " . verta()->format("F") . " ماه " . $contract_subset->workplace . " ایجاد شده است.لطفا پس از یافتن رکورد متناظر از طریق جدول، در منوی عملیات اقدام به ویرایش آن نمایید"]);

            }
            return view("staff.new_performance",["contract_subset" => $contract_subset,"authorized_date" => $authorized_date]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"Performances");
        try {
            $request->validate(["employees_data" => "required"], ["employees_data.required" => "اطلاعات کارکرد پرسنل ارسال نشده است"]);
            $employees_data = json_decode($request->input("employees_data"),true);
            if ($employees_data) {
                $contract_subset = ContractSubset::query()->findOrFail($employees_data["id"]);
                if (count($authorized_date = $contract_subset->entry_date_check()) == 0)
                    return redirect()->back()->withErrors(["result" => "مهلت تایید و ارسال نهایی اطلاعات کارکرد ".verta()->format("F")." ماه ".$contract_subset->workplace." به اتمام رسیده است"]);
                else{
                    DB::beginTransaction();
                    $automation = PerformanceAutomation::query()->firstOrCreate(["contract_subset_id" => $contract_subset->id,"authorized_date_id" => $authorized_date["id"]],[
                        "authorized_date_id" => $authorized_date["id"],
                        "role_id" => Auth::user()->role->id,
                        "user_id" => Auth::id(),
                        "contract_subset_id" => $contract_subset->id,
                        "attribute_id" => $contract_subset->performance_attributes_id,
                        "role_priority" => 1
                    ]);
                    foreach ($employees_data["employees"] as $employee){
                        if (!isset($employee["performance_data"]))
                            throw new \Exception("کارکرد ".$employee["first_name"] . " " . $employee["last_name"] . " دارای کد ملی " . $employee["national_code"] . " ارسال نشده است" );
                        Performance::query()->updateOrCreate(["performance_automation_id" => $automation->id,"employee_id" => $employee["id"]],[
                            "performance_automation_id" => $automation->id,
                            "employee_id" => $employee["id"],
                            "job_group" => $employee["job_group"],
                            "daily_wage" => $employee["daily_wage"],
                            "data" => json_encode($employee["performance_data"],JSON_UNESCAPED_UNICODE)
                        ]);
                    }
                    if ($request->filled("comment"))
                        $automation->comments()->create([
                            "user_id" => Auth::id(),
                            "comment" => $request->input("comment")
                        ]);
                    DB::commit();
                    return redirect()->route("Performances.index")->with(["result" => "success","message" => "saved"]);
                }
            }
            return redirect()->back()->withErrors(["result" => "فرمت اطلاعات کارکرد پرسنل صحیح نمی باشد"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function confirm($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('confirm',"Performances");
        try {
            DB::beginTransaction();
            $automation = PerformanceAutomation::query()->with(["authorized_date","contract"])->findOrFail($id);
            $automate = $automation->automate("forward");
            if($automate)
                $this->send_notification($automate["users"],$automate["data"]);
            $automation->signs()->updateOrCreate(["signable_id" => $automation->id,"user_id" => Auth::id()],[
                "user_id" => Auth::id(),
                "sign" => Auth::user()->sign
            ]);
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "confirmed"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"Performances");
        try {
            $automation = PerformanceAutomation::query()->with(["contract","attributes.items","performances.employee","authorized_date","comments" => function($query){$query->where("user_id",Auth::id());}])
                ->findOrFail($id);
            $employee_performances = $automation->performances->toArray();
            $automation->performances->map(function ($item) use ($employee_performances) {
                $search_data = array_column($employee_performances,"employee_id");
                $index = array_search($item->employee->id,$search_data);
                if (gettype($index) !== 'boolean') {
                    $item->employee["performance_data"] = json_decode($employee_performances[$index]["data"]);
                }
            });
            return view("staff.edit_performance",["automation" => $automation,"contract_subset" => $automation->contract,"authorized_date" => $automation->authorized_date]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"Performances");
        try {
            $request->validate(["employees_data" => "required"], ["employees_data.required" => "اطلاعات کارکرد پرسنل ارسال نشده است"]);
            $employees_data = json_decode($request->input("employees_data"),true);
            if ($employees_data) {
                $contract_subset = ContractSubset::query()->findOrFail($employees_data["contract"]["id"]);
                if (count($contract_subset->entry_date_check()) == 0)
                    return redirect()->back()->withErrors(["result" => "مهلت تایید و ارسال نهایی اطلاعات کارکرد ".verta()->format("F")." ماه ".$contract_subset->workplace." به اتمام رسیده است"]);
                else{
                    DB::beginTransaction();
                    $automation = PerformanceAutomation::query()->findOrFail($id);
                    foreach ($employees_data["performances"] as $item){
                        if (!isset($item["employee"]["performance_data"]))
                            throw new \Exception("کارکرد ".$item["employee"]["first_name"] . " " . $item["employee"]["last_name"] . " دارای کد ملی " . $item["employee"]["national_code"] . " ارسال نشده است" );
                        Performance::query()->updateOrCreate(["performance_automation_id" => $automation->id,"employee_id" => $item["employee"]["id"]],[
                            "performance_automation_id" => $automation->id,
                            "employee_id" => $item["employee"]["id"],
                            "job_group" => $item["employee"]["job_group"],
                            "daily_wage" => $item["employee"]["daily_wage"],
                            "data" => json_encode($item["employee"]["performance_data"],JSON_UNESCAPED_UNICODE)
                        ]);
                    }
                    if ($request->filled("comment"))
                        $automation->comments()->updateOrCreate(["commentable_id" => $automation->id,"user_id" => Auth::id()],[
                            "user_id" => Auth::id(),
                            "comment" => $request->input("comment")
                        ]);
                    DB::commit();
                    return redirect()->back()->with(["result" => "success","message" => "updated"]);
                }
            }
            return redirect()->back()->withErrors(["result" => "فرمت اطلاعات کارکرد پرسنل صحیح نمی باشد"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete',"Performances");
        try {
            DB::beginTransaction();
            $automation = PerformanceAutomation::query()->findOrFail($id);
            $automation->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function performance_export_excel($id,$authorized_date_id = null): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {

            return Excel::download(new NewPerformanceExport($id,$authorized_date_id), 'new_performance.xlsx');

            //return redirect()->back()->withErrors(["logical" => $error->getMessage()]);

    }
}
