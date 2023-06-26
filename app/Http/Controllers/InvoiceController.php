<?php

namespace App\Http\Controllers;

use App\Exports\NewInvoiceExport;
use App\Exports\NewPerformanceExport;
use App\Models\ContractSubset;
use App\Models\Invoice;
use App\Models\InvoiceAutomation;
use App\Models\InvoiceCoverTitleData;
use App\Models\Performance;
use App\Models\PerformanceAutomation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class InvoiceController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"Invoices");
        try {
            $invoices = InvoiceAutomation::query()->with(["invoices.employee","authorized_date","current_role","contract","user"])
                ->whereHas("user",function ($query){$query->where("id","=",Auth::id());})->orderBy("id","desc")->get();
            $contracts = ContractSubset::permitted_contracts();
            return view("staff.invoices",["invoices" => $invoices,"contracts" => $contracts,"months" => $this->month_names()]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function create(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"Invoices");

            $request->validate(["contract_id" => "required","year" => "required","month" => "required"],
                [
                    "contract_id.required" => "انتخاب قرارداد الزامی می باشد",
                    "year.required" => "انتخاب سال الزامی می باشد",
                    "month.required" => "انتخاب ماه الزامی می باشد",
                ]
            );
            $year = $request->input("year");$month = $request->input("month");
            $performance_exist = PerformanceAutomation::DateValidation($year,$month,$request->input("contract_id"));
            switch ($performance_exist["result"]){
                case "empty":{
                    return redirect()->back()->withErrors(["result" => "کارکرد ماهیانه این قرارداد در سال و ماه انتخاب شده وجود ندارد"]);
                }
                case "not_finished":{
                    return redirect()->back()->withErrors(["result" => "گردش اتوماسیون کارکرد ماهیانه این قرارداد در سال و ماه انتخاب شده منتظر تایید ".$performance_exist["data"]["current_role"]["name"]." بوده و به اتمام نرسیده است"]);
                }
                default:{
                    $contract_subset = ContractSubset::query()->with([
                        "contract",
                        "invoice_cover.items",
                        "performance_automation.authorized_date",
                        "performance_automation.performances.employee",
                        "invoice_attribute.items"])->findOrFail($request->input("contract_id"));
                    if ($invoice_automation = $contract_subset->check_invoice_automation($request->input("year"),$request->input("month"))) {
                        if ($invoice_automation->is_committed == 1)
                            return redirect()->back()->withErrors(["result" => "وضعیت " . verta()->format("F") . " ماه " . $contract_subset->workplace . " تایید و ارسال نهایی شده است. جهت ویرایش و یا حذف، نیاز به ارجاع آن می باشد"]);
                        else
                            return redirect()->back()->withErrors(["result" => "وضعیت " . verta()->format("F") . " ماه " . $contract_subset->workplace . " ایجاد شده است.لطفا پس از یافتن رکورد متناظر از طریق جدول، در منوی عملیات اقدام به ویرایش آن نمایید"]);
                    }
                    else
                        return view("staff.new_invoice",["automation" => $performance_exist["data"]]);

                }
            }

    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create', "Invoices");
        try {
            $request->validate(["employees_data" => "required","invoice_cover_data" => "required"],
                ["employees_data.required" => "اطلاعات وضعیت پرسنل ارسال نشده است","invoice_cover_data.required" => "اطلاعات روکش وضعیت ارسال نشده است"]);
            $employees_data = json_decode($request->input("employees_data"), true);
            $invoice_cover_data = json_decode($request->input("invoice_cover_data"), true);
            if ($employees_data && $invoice_cover_data) {
                $performance_automation = PerformanceAutomation::query()->with(["contract.invoice_attribute","contract.invoice_cover"])->findOrFail($employees_data["id"]);
                if ($performance_automation->authorized_date()->exists()) {
                    DB::beginTransaction();
                    $automation = InvoiceAutomation::query()->firstOrCreate([
                        "contract_subset_id" => $performance_automation->contract_subset_id,
                        "authorized_date_id" => $performance_automation->authorized_date_id], [
                        "authorized_date_id" => $performance_automation->authorized_date_id,
                        "role_id" => Auth::user()->role->id,
                        "user_id" => Auth::id(),
                        "contract_subset_id" => $performance_automation->contract_subset_id,
                        "attribute_id" => $performance_automation->contract->invoice_attribute->id,
                        "invoice_cover_title_id" => $performance_automation->contract->invoice_cover->id,
                        "role_priority" => 1
                    ]);
                    foreach ($employees_data["performances"] as $item) {
                        if (!isset($item["employee"]["invoice_data"]))
                            throw new \Exception("وضعیت " . $item["employee"]["first_name"] . " " . $item["employee"]["last_name"] . " دارای کد ملی " . $item["employee"]["national_code"] . " ارسال نشده است");
                        Invoice::query()->updateOrCreate(["invoice_automation_id" => $automation->id, "employee_id" => $item["employee"]["id"]], [
                            "invoice_automation_id" => $automation->id,
                            "employee_id" => $item["employee"]["id"],
                            "job_group" => $item["job_group"],
                            "data" => json_encode($item["employee"]["invoice_data"], JSON_UNESCAPED_UNICODE)
                        ]);
                    }
                    InvoiceCoverTitleData::query()->updateOrCreate(["invoice_automation_id" => $automation->id], [
                        "invoice_automation_id" => $automation->id,
                        "data" => json_encode($invoice_cover_data, JSON_UNESCAPED_UNICODE)
                    ]);
                    if ($request->filled("comment"))
                        $automation->comments()->create([
                            "user_id" => Auth::id(),
                            "comment" => $request->input("comment")
                        ]);
                    DB::commit();
                    return redirect()->route("Invoices.index")->with(["result" => "success", "message" => "saved"]);
                }
                else
                    return redirect()->back()->withErrors(["result" => "وضعیت ماهیانه این قرارداد در سال و ماه انتخاب شده وجود ندارد"]);
            }
            else
                return redirect()->back()->withErrors(["result" => "فرمت اطلاعات وضعیت پرسنل و یا روکش وضعیت صحیح نمی باشد"]);
        } catch (Throwable $error) {
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function confirm($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('confirm',"Invoices");
        try {
            DB::beginTransaction();
            $automation = InvoiceAutomation::query()->with(["authorized_date","contract"])->findOrFail($id);
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
        Gate::authorize('edit',"Invoices");
        try {
            $automation = InvoiceAutomation::query()->with([
                "invoices.employee",
                "attributes.items" => function($query){
                    $query->orderBy("table_attribute_items.category");
                },
                "contract.invoice_cover.items",
                "cover",
                "authorized_date","comments" => function($query){$query->where("user_id",Auth::id());}])
                ->findOrFail($id);
            $employee_invoices = $automation->invoices->toArray();
            $automation->invoices->map(function ($item) use ($employee_invoices) {
                $search_data = array_column($employee_invoices,"employee_id");
                $index = array_search($item->employee->id,$search_data);
                if (gettype($index) !== 'boolean') {
                    $item->employee["invoice_data"] = json_decode($employee_invoices[$index]["data"]);
                }
            });
            return view("staff.edit_invoice",["automation" => $automation,"authorized_date" => $automation->authorized_date]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"Invoices");
        try {
            $request->validate(["employees_data" => "required","invoice_cover_data" => "required"],
                ["employees_data.required" => "اطلاعات وضعیت پرسنل ارسال نشده است","invoice_cover_data.required" => "اطلاعات روکش وضعیت ارسال نشده است"]);
            $employees_data = json_decode($request->input("employees_data"),true);
            $invoice_cover_data = json_decode($request->input("invoice_cover_data"), true);
            if ($employees_data && $invoice_cover_data) {
                DB::beginTransaction();
                $automation = InvoiceAutomation::query()->findOrFail($id);
                foreach ($employees_data["invoices"] as $item) {
                    if (!isset($item["employee"]["invoice_data"]))
                        throw new \Exception("وضعیت " . $item["employee"]["first_name"] . " " . $item["employee"]["last_name"] . " دارای کد ملی " . $item["employee"]["national_code"] . " ارسال نشده است");
                    Invoice::query()->updateOrCreate(["invoice_automation_id" => $automation->id, "employee_id" => $item["employee"]["id"]], [
                        "invoice_automation_id" => $automation->id,
                        "employee_id" => $item["employee"]["id"],
                        "job_group" => $item["job_group"],
                        "data" => json_encode($item["employee"]["invoice_data"], JSON_UNESCAPED_UNICODE)
                    ]);
                }
                InvoiceCoverTitleData::query()->updateOrCreate(["invoice_automation_id" => $automation["id"]], [
                    "invoice_automation_id" => $automation["id"],
                    "data" => json_encode($invoice_cover_data, JSON_UNESCAPED_UNICODE)
                ]);
                if ($request->filled("comment"))
                    $automation->comments()->updateOrCreate(["commentable_id" => $automation->id,"user_id" => Auth::id()],[
                        "user_id" => Auth::id(),
                        "comment" => $request->input("comment")
                    ]);
                DB::commit();
                return redirect()->back()->with(["result" => "success","message" => "updated"]);
            }
            return redirect()->back()->withErrors(["result" => "فرمت اطلاعات وضعیت پرسنل صحیح نمی باشد"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete',"Invoices");
        try {
            DB::beginTransaction();
            $automation = InvoiceAutomation::query()->findOrFail($id);
            $automation->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function invoice_export_excel($id,$authorized_date_id = null,$automation_id = null): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            return Excel::download(new NewInvoiceExport($id,$authorized_date_id,$automation_id), 'new_invoice.xlsx');
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
