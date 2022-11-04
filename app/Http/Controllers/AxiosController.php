<?php

namespace App\Http\Controllers;

use App\Imports\ContractChangeEmployeeImport;
use App\Imports\NewEmployeesImport;
use App\Imports\PerformanceAutomationImport;
use App\Imports\PreInvoiceImport;
use App\Imports\PrePerformanceImport;
use App\Models\AdvantageAutomation;
use App\Models\ContractSubset;
use App\Models\Employee;
use App\Models\InvoiceAutomation;
use App\Models\PerformanceAutomation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AxiosController extends Controller
{
    function EmployeeAdd(Request $request): array
    {
        Gate::authorize('create',"Employees");
        try {
            $duplicate = Employee::duplicates($request->input("new_national_code"));
            if ($duplicate){
                $response["result"] = "fail";
                $response["message"] = "کد ملی {$request->input('new_national_code')} در سیستم موجود می باشد";
                $response["data"] = Employee::query()->with(["contract","user"])->get()->toArray();;
                return $response;
            }
            $response = [];
            $id = $request->input("main_contract_subset_id");
            $contract_subset = ContractSubset::query()->findOrFail($id);
            $contract_subset->employees()->create([
                "first_name" => $request->input('new_first_name'),
                "last_name" => $request->input('new_last_name'),
                "gender" => $request->input('new_gender'),
                "national_code" => $request->input('new_national_code'),
                "id_number" => $request->input('new_id_number'),
                "birth_date" => $request->input('new_birth_date'),
                "birth_city" => $request->input('new_birth_city'),
                "education" => $request->input('new_education'),
                "marital_status" => $request->input('new_marital_status'),
                "children_number" => $request->filled('new_children_number') ? $request->input('new_children_number') : 0,
                "insurance_number" => $request->input('new_insurance_number'),
                "insurance_days" => $request->input('new_insurance_days'),
                "military_status" => $request->input('new_military_status'),
                "basic_salary" => $request->filled('new_basic_salary') ? $request->input('new_basic_salary') : 0.00,
                "daily_wage" => $request->filled('new_daily_wage') ? $request->input('new_daily_wage') : 0.00,
                "worker_credit" => $request->filled('new_worker_credit') ? $request->input('new_worker_credit') : 0.00,
                "housing_credit" => $request->filled('new_housing_credit') ? $request->input('new_housing_credit') : 0.00,
                "child_credit" => $request->filled('new_child_credit') ? $request->input('new_child_credit') : 0.00,
                "job_group" => $request->filled('new_job_group') ? $request->input('new_job_group') : 1,
                "bank_name" => $request->input('new_bank_name'),
                "bank_account" => $request->input('new_bank_account'),
                "credit_card" => $request->input('new_credit_card'),
                "sheba_number" => $request->input('new_sheba_number'),
                "phone" => $request->input('new_phone'),
                "mobile" => $request->input('new_mobile'),
                "address" => $request->input('new_address'),
                "user_id" => Auth::id()
            ]);
            $response["result"] = "success";
            $response["message"] = "عملیات ذخیره سازی با موفقیت انجام شد";
            $response["data"] = Employee::query()->with(["contract","user"])->get()->toArray();
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }
    function EmployeeAddAll(Request $request): array
    {
        Gate::authorize('create',"Employees");
        try {
            DB::beginTransaction();
            $response = [];
            $import_errors = [];
            $id = $request->input("main_overall_contract_subset_id");
            $import = new NewEmployeesImport($id);
            $import->import($request->file("new_employee_excel_file")->store("tmp"));
            if (count($import->failures()->toArray()) > 0){
                foreach ($import->failures() as $failure){
                    foreach ($failure->errors() as $error)
                        $import_errors [] = ["row" => $failure->row(),"message" => $error,"value" => $failure->values()[3]];
                }
            }
            Storage::deleteDirectory("tmp");
            if (count($import_errors) > 0)
                $message = "عملیات ذخیره سازی با موفقیت انجام شد اما ثبت اطلاعات فایل اکسل به طور کامل انجام نشد";
            else
                $message = "عملیات ذخیره سازی با موفقیت انجام شد";
            $response["result"] = "success";
            $response["message"] = $message;
            $response["import_errors"] = $import_errors;
            $response["data"] = Employee::query()->with(["contract","user"])->get()->toArray();
            DB::commit();
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }
    public function EmployeeEdit(Request $request): array
    {
        Gate::authorize('edit',"Employees");
        try {
            DB::beginTransaction();
            $employee = Employee::query()->findOrFail($request->input("id"));
            $duplicate = Employee::duplicates($request->input("national_code"));
            if ($employee->national_code != $request->input("national_code") && $duplicate) {
                $response["result"] = "fail";
                $response["message"] = "کد ملی {$request->input('new_national_code')} در سیستم موجود می باشد";
                $response["data"] = Employee::query()->with(["contract","user"])->get()->toArray();;
                return $response;
            }
            $employee->update([
                "first_name" => $request->input("first_name"),
                "last_name" => $request->input("last_name"),
                "national_code" => $request->input("national_code")
            ]);
            DB::commit();
            $response["result"] = "success";
            $response["message"] = "عملیات ویرایش با موفقیت انجام شد";
            $response["data"] = Employee::query()->with(["contract","user"])->get()->toArray();
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }
    function EmployeeChangeContract(Request $request): array
    {
        Gate::authorize('edit',"Employees");
        try {
            DB::beginTransaction();
            $response = [];
            $import_errors = [];
            $id = $request->input("main_overall_contract_subset_id");
            $import = new ContractChangeEmployeeImport($id);
            $import->import($request->file("change_employee_contract_excel_file")->store("tmp"));
            if (count($import->failures()->toArray()) > 0){
                foreach ($import->failures() as $failure){
                    foreach ($failure->errors() as $error)
                        $import_errors [] = ["row" => $failure->row(),"message" => $error,"value" => $failure->values()[3]];
                }
            }
            Storage::deleteDirectory("tmp");
            if (count($import_errors) > 0)
                $message = "عملیات تغییر قرارداد با موفقیت انجام شد اما ثبت اطلاعات فایل اکسل به طور کامل انجام نشد";
            else
                $message = "عملیات تغییر قرارداد با موفقیت انجام شد";
            $response["result"] = "success";
            $response["message"] = $message;
            $response["import_errors"] = $import_errors;
            $response["data"] = Employee::query()->with(["contract","user"])->get()->toArray();
            DB::commit();
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }
    public function EmployeeDeleteAll(Request $request): array
    {
        Gate::authorize('delete',"Employees");
        try {
            DB::beginTransaction();
            $id = $request->input("main_overall_contract_subset_id");
            $contract_subset = ContractSubset::query()->findOrFail($id);
            foreach ($contract_subset->employees() as $employee){
                if ($employee->performances()->exists() || $employee->invoices()->exists() || $employee->advantages()->exists()){
                    $response["result"] = "fail";
                    $response["message"] = "به دلیل وجود رابطه با اطلاعات دیگر،امکان حذف پرسنل این قرارداد وجود ندارد";
                    $response["data"] = Employee::query()->with(["contract","user"])->get()->toArray();
                    return $response;
                }
            }
            $contract_subset->employees()->delete();
            DB::commit();
            $response["result"] = "success";
            $response["message"] = "عملیات حذف پرسنل با موفقیت انجام شد";
            $response["data"] = Employee::query()->with(["contract","user"])->get()->toArray();
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }
    public function EmployeeDelete(Request $request): array
    {
        Gate::authorize('delete',"Employees");
        try {
            DB::beginTransaction();
            $employee = Employee::query()->findOrFail($request->input("id"));
            if (!$employee->performances()->exists() && !$employee->invoices()->exists() && !$employee->advantages()->exists()){
                $employee->delete();
                DB::commit();
                $response["result"] = "success";
                $response["message"] = "عملیات حذف پرسنل با موفقیت انجام شد";
            }
            else{
                DB::rollBack();
                $response["result"] = "fail";
                $response["message"] = "به دلیل وجود رابطه با اطلاعات دیگر،امکان حذف آن وجود ندارد";
            }
            $response["data"] = Employee::query()->with(["contract","user"])->get()->toArray();
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }
    public function EmployeeActivation(Request $request): array
    {
        Gate::authorize('activation',"Employees");
        try {
            DB::beginTransaction();
            $employee = Employee::query()->findOrFail($request->input("id"));
            if ($employee->unemployed == 1)
                $employee->update(["unemployed" => 0]);
            else
                $employee->update(["unemployed" => 1]);
            $response["message"] = match($employee->unemployed){
                0 => "عملیات فعالسازی پرسنل با موفقیت انجام شد",
                1 => "عملیات غیرفعالسازی پرسنل با موفقیت انجام شد",
                default => "unknown"
            };
            DB::commit();
            $response["result"] = "success";
            $response["data"] = Employee::query()->with(["contract","user"])->get()->toArray();
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }
    public function EmployeeSearch(Request $request): array
    {
        try {
            $first_name = $request->input("filter_first_name");
            $last_name = $request->input("filter_last_name");
            $national_code = $request->input("filter_national_code");
            $mobile = $request->input("filter_mobile");
            $filtered_employee = Employee::query()->with(["contract","user"])->when($request->filled("filter_first_name"),function ($query) use ($first_name){
                $query->where("first_name","like","%{$first_name}%");
            })->when($request->filled("filter_last_name"),function ($query) use ($last_name){
                $query->where("last_name","like","%{$last_name}%");
            })->when($request->filled("filter_national_code"),function ($query) use ($national_code){
                $query->where("national_code","like","%{$national_code}%");
            })->when($request->filled("filter_mobile"),function ($query) use ($mobile){
                $query->where("mobile","like","%{$mobile}%");
            })->get();
            if($filtered_employee->count() == 0){
                $response["message"] = "با توجه به اطلاعات وارد شده موردی یافت نشد";
                $response["filtered_data"] = [];
            }
            else{
                $response["message"] = "تعداد ".$filtered_employee->count()." مورد یافت شد و در جدول اصلی ثبت گردید";
                $response["filtered_data"] = $filtered_employee->toArray();
            }
            $response["result"] = "success";
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }
    public function PerformancePreImport(Request $request): array
    {
        try {
            $request->validate(
                ["upload_file" => "required|mimes:xlsx","contract_id" => "required"],
                ["upload_file.required" => "فایلی باگذاری نشده است","upload_file.mimes" => "فرمت فایل باگذاری شده صحیح نمی باشد","contract_id.required" => "شماره قرارداد مشخص نمی باشد"]
            );
            $response = [];
            $import_errors = [];
            $id = $request->input("contract_id");
            $import = new PrePerformanceImport($id,$request->filled("authorized_date_id") ? $request->input("authorized_date_id") : null);
            $import->import($request->file("upload_file")->store("tmp"));
            if (count($import->failures()->toArray()) > 0){
                foreach ($import->failures() as $failure){
                    foreach ($failure->errors() as $error)
                        $import_errors [] = ["row" => $failure->row(),"message" => $error,"value" => $failure->values()[2]];
                }
            }
            Storage::deleteDirectory("tmp");
            if (count($import_errors) > 0)
                $message = "عملیات بارگذاری فایل کارکرد با موفقیت انجام شد اما ثبت اطلاعات فایل به طور کامل انجام نشد";
            else
                $message = "عملیات بارگذاری فایل کارکرد با موفقیت انجام شد";
            $response["result"] = "success";
            $response["message"] = $message;
            $response["import_errors"] = $import_errors;
            $response["data"] = $import->getResult();
            return $response;
        }
        catch (Throwable $error){
            $contract_subset = ContractSubset::query()->with(["employees","contract","performance_automation.performances.employee","performance_automation.attributes.items"])->findOrFail($request->input("contract_id"))->toArray();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = $contract_subset;
            return $response;
        }
    }
    public function PerformanceAutomationImport(Request $request): array
    {
        try {
            $request->validate(
                ["upload_file" => "required|mimes:xlsx","contract_id" => "required"],
                ["upload_file.required" => "فایلی باگذاری نشده است","upload_file.mimes" => "فرمت فایل باگذاری شده صحیح نمی باشد","contract_id.required" => "شماره قرارداد مشخص نمی باشد"]
            );
            $response = [];
            $import_errors = [];
            $id = $request->input("contract_id");
            $import = new PerformanceAutomationImport($id,$request->filled("authorized_date_id") ?: $request->input("authorized_date_id"));
            $import->import($request->file("upload_file")->store("tmp"));
            if (count($import->failures()->toArray()) > 0){
                foreach ($import->failures() as $failure){
                    foreach ($failure->errors() as $error)
                        $import_errors [] = ["row" => $failure->row(),"message" => $error,"value" => $failure->values()[2]];
                }
            }
            Storage::deleteDirectory("tmp");
            if (count($import_errors) > 0)
                $message = "عملیات بارگذاری فایل کارکرد با موفقیت انجام شد اما ثبت اطلاعات فایل به طور کامل انجام نشد";
            else
                $message = "عملیات بارگذاری فایل کارکرد با موفقیت انجام شد";
            $response["result"] = "success";
            $response["message"] = $message;
            $response["import_errors"] = $import_errors;
            $response["data"] = $import->getResult();
            return $response;
        }
        catch (Throwable $error){
            $contract_subset = ContractSubset::query()->with(["employees","contract","performance_automation.performances.employee","performance_automation.attributes.items"])->findOrFail($request->input("contract_id"))->toArray();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = $contract_subset;
            return $response;
        }
    }
    public function NewAutomationData(Request $request): array
    {
        try {
            switch ($request->type){
                case "performance":{
                    $contracts = ContractSubset::permitted_contracts();
                    if($contracts != []) {
                        $performance_automation_inbox = PerformanceAutomation::query()->with(["authorized_date", "current_role", "contract", "user", "performances"])
                            ->whereHas("current_role", function ($query) {
                                $query->where("id", "=", Auth::user()->role->id);
                            })->whereHas("contract", function ($query) use ($contracts) {
                                $query->whereIn("contract_subset_id", $contracts->pluck("id"));
                            })->where("is_finished", "=", 0)->orderBy("id", "desc")->get();
                        $response["result"] = "success";
                        $response["message"] = "بروزرسانی اتوماسیون کارکرد با موفقیت انجام شد";
                        $response["data"] = $performance_automation_inbox->toArray();
                    }
                    else{
                        $response["result"] = "fail";
                        $response["message"] = "خطا در بروزرسانی لیست اتوماسیون(قراردادی وجود ندارد)";
                        $response["data"] = [];
                    }
                    return $response;
                }
                case "invoice":{
                    $contracts = ContractSubset::permitted_contracts();
                    if($contracts != []) {
                        $invoice_automation_inbox = InvoiceAutomation::query()->with(["authorized_date", "current_role", "contract", "user", "invoices"])
                            ->whereHas("current_role", function ($query) {
                                $query->where("id", "=", Auth::user()->role->id);
                            })->whereHas("contract", function ($query) use ($contracts) {
                                $query->whereIn("contract_subset_id", $contracts->pluck("id"));
                            })->where("is_finished", "=", 0)->orderBy("id", "desc")->get();
                        $response["result"] = "success";
                        $response["message"] = "بروزرسانی اتوماسیون وضعیت با موفقیت انجام شد";
                        $response["data"] = $invoice_automation_inbox->toArray();
                    }
                    else{
                        $response["result"] = "fail";
                        $response["message"] = "خطا در بروزرسانی لیست اتوماسیون(قراردادی وجود ندارد)";
                        $response["data"] = [];
                    }
                    return $response;
                }
                case "advantage":{
                    $contracts = ContractSubset::permitted_contracts();
                    if ($contracts != []) {
                        $advantage_automation_inbox = AdvantageAutomation::query()->with(["employee.contract", "current_role", "user", "advantage"])
                            ->whereHas("current_role", function ($query) {
                                $query->where("id", "=", Auth::user()->role->id);
                            })->whereHas("employee.contract", function ($query) use ($contracts) {
                                $query->whereIn("contract_subset_id", $contracts->pluck("id"));
                            })->where("is_finished", "=", 0)->orderBy("id", "desc")->get();
                        $response["result"] = "success";
                        $response["message"] = "بروزرسانی اتوماسیون تغییرات مزایا با موفقیت انجام شد";
                        $response["data"] = $advantage_automation_inbox->toArray();
                    }
                    else{
                        $response["result"] = "fail";
                        $response["message"] = "خطا در بروزرسانی لیست اتوماسیون(قراردادی وجود ندارد)";
                        $response["data"] = [];
                    }
                    return $response;
                }
                default: {
                    $response["result"] = "fail";
                    $response["message"] = "خطا در دریافت نوع درخواست";
                    $response["data"] = null;
                    return $response;
                }
            }
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = null;
            return $response;
        }
    }

    public function InvoicePreImport(Request $request): array
    {
        try {
            $request->validate(
                ["upload_file" => "required|mimes:xlsx","contract_id" => "required"],
                ["upload_file.required" => "فایلی باگذاری نشده است","upload_file.mimes" => "فرمت فایل باگذاری شده صحیح نمی باشد","contract_id.required" => "شماره قرارداد مشخص نمی باشد"]
            );
            $response = [];
            $import_errors = [];
            $id = $request->input("contract_id");
            $import = new PreInvoiceImport($id, $request->input("performance_automation_id"),$request->filled("invoice_automation_id") ?: $request->input("invoice_automation_id"));
            $import->import($request->file("upload_file")->store("tmp"));
            if (count($import->failures()->toArray()) > 0){
                foreach ($import->failures() as $failure){
                    foreach ($failure->errors() as $error)
                        $import_errors [] = ["row" => $failure->row(),"message" => $error,"value" => $failure->values()[2]];
                }
            }
            Storage::deleteDirectory("tmp");
            if (count($import_errors) > 0)
                $message = "عملیات بارگذاری فایل وضعیت با موفقیت انجام شد اما ثبت اطلاعات فایل به طور کامل انجام نشد";
            else
                $message = "عملیات بارگذاری فایل وضعیت با موفقیت انجام شد";
            $response["result"] = "success";
            $response["message"] = $message;
            $response["import_errors"] = $import_errors;
            $response["data"] = $import->getResult();
            return $response;
        }
        catch (Throwable $error){
            $contract_subset = ContractSubset::query()->with(["employees","contract","performance_automation.performances.employee","performance_automation.attributes.items"])->findOrFail($request->input("contract_id"))->toArray();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = $contract_subset;
            return $response;
        }
    }
}
