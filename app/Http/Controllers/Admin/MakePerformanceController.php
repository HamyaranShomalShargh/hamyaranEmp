<?php

namespace App\Http\Controllers\Admin;

use App\Exports\NewPerformanceExport;
use App\Http\Controllers\Controller;
use App\Models\AutomationAuthorizedDate;
use App\Models\ContractSubset;
use App\Models\Employee;
use App\Models\Performance;
use App\Models\PerformanceAutomation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class MakePerformanceController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $contracts = ContractSubset::query()->with(["contract", "employees"])->where("inactive", "=", 0)->whereHas("performance_flow")->whereHas("employees")
                ->whereHas("performance_attribute")->whereHas("invoice_attribute")->get();
            return view("admin.make_performance", ["contracts" => $contracts, "month_names" => $this->month_names()]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function get_information(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $request->validate([
                "id" => "required",
                "year" => "required",
                "month" => "required"
            ],[
                "id.required" => "انتخاب سازمان الزامی می باشد",
                "year.required" => "انتخاب سازمان الزامی می باشد",
                "month.required" => "انتخاب سازمان الزامی می باشد"
            ]);
            $id = $request->input("id");
            $year = $request->input("year");
            $month = $request->input("month");
            $contract = ContractSubset::query()->with(["performance_automation.authorized_date"])->findOrFail($id);
            $authorized_dates = $contract->performance_automation()->with("authorized_date")->whereHas("authorized_date",function ($query)use ($year,$month){
                $query->where("automation_year",$year)->where("automation_month",$month);
            })->get();
            if ($authorized_dates->isEmpty()) {
                $contract_subset = ContractSubset::query()->with(["contract","employees" => function($query){
                    $query->where("employees.unemployed","=",0);
                },"performance_attribute.items","users" => function($query){
                    $query->where("users.is_staff",1);
                }])->findOrFail($id);
                $contract_subset->employees->map(function ($item) {
                    $item["performance_data"] = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
                });
                Session::put("contract_subset",$contract_subset);
                Session::put("authorized_date",AutomationAuthorizedDate::query()->firstOrCreate(["automation_year" => $year,"automation_month" => $month],["month_name" => $this->month_names()[$month - 1]]));
                return redirect()->route("MakePerformance.create");

            }
            else{
                return redirect()->back()->withErrors(["logical" => "کارکرد این سازمان در سال و ماه انتخاب شده قبلا ثبت شده است"]);
            }
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view("admin.new_performance",[
            "contract_subset" => Session::get("contract_subset"),
            "authorized_date" => Session::get("authorized_date")
        ]);
    }
    public function performance_export_excel($id,$authorized_date_id = null): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            return Excel::download(new NewPerformanceExport($id,$authorized_date_id), 'new_performance.xlsx');
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "employees_data" => "required",
                "authorized_date_id" => "required",
                "user_id" => "required"
            ], [
                "employees_data.required" => "اطلاعات کارکرد پرسنل ارسال نشده است",
                "authorized_date_id.required" => "سال و ماه کارکرد مشخص نمی باشد",
                "user_id.required" => "کاربر ثبت کننده اتوماسیون انتخاب نشده است"
            ]);
            $employees_data = json_decode($request->input("employees_data"),true);
            if ($employees_data) {
                $contract_subset = ContractSubset::query()->findOrFail($employees_data["id"]);
                $user = User::query()->with("role")->findOrFail($request->user_id);
                DB::beginTransaction();
                $automation = PerformanceAutomation::query()->firstOrCreate(["contract_subset_id" => $contract_subset->id,"authorized_date_id" => $request->authorized_date_id],[
                    "authorized_date_id" => $request->authorized_date_id,
                    "role_id" => $user->role->id,
                    "user_id" => $user->id,
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
                        "user_id" => $user->id,
                        "comment" => $request->input("comment")
                    ]);
                DB::commit();
                return redirect()->route("MakePerformance.index")->with(["result" => "success","message" => "saved"]);
            }
            return redirect()->back()->withErrors(["result" => "فرمت اطلاعات کارکرد پرسنل صحیح نمی باشد"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
