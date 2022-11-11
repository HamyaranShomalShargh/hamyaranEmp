<?php

namespace App\Http\Controllers;

use App\Exports\NewInvoiceExport;
use App\Models\ContractSubset;
use App\Models\InvoiceAutomation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Stmt\TryCatch;
use Throwable;

class InvoiceAutomationController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"InvoiceAutomation");
        try {
            $contracts = ContractSubset::permitted_contracts();
            if($contracts != []) {
                $invoice_automation_inbox = InvoiceAutomation::query()->with(["authorized_date", "current_role", "contract","user","invoices"])
                    ->whereHas("current_role", function ($query) {
                        $query->where("id", "=", Auth::user()->role->id);
                    })->whereHas("contract", function ($query) use ($contracts) {
                        $query->whereIn("contract_subset_id", $contracts->pluck("id"));
                    })->where("is_finished","=",0)->orderBy("id", "desc")->get();
                $invoice_automation_outbox = InvoiceAutomation::query()->with(["authorized_date", "current_role", "contract","user","invoices"])
                    ->whereHas("signs", function ($query) {
                        $query->where("user_id", "=", Auth::id());
                    })->orderBy("id", "desc")->get();
                return view("staff.invoice_automation",
                    [
                        "invoice_automation_inbox" => $invoice_automation_inbox,
                        "invoice_automation_outbox" => $invoice_automation_outbox,
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
        Gate::authorize('details',"InvoiceAutomation");
        try {
            if ($outbox)
                $automation = InvoiceAutomation::query()->whereHas("signs", function ($query) {
                    $query->where("user_id", "=", Auth::id());
                })->with([
                    "contract",
                    "invoices.employee",
                    "attributes.items" => function($query){
                        $query->orderBy("table_attribute_items.category");
                    },
                    "authorized_date",
                    "cover_titles.items",
                    "cover",
                    "comments.user","signs.user"])->findOrFail($id);
            else
                $automation = InvoiceAutomation::query()->whereHas("current_role", function ($query) {
                    $query->where("id", "=", Auth::user()->role->id);
                })->with([
                    "contract",
                    "invoices.employee",
                    "attributes.items" => function($query){
                        $query->orderBy("table_attribute_items.category");
                    },
                    "authorized_date",
                    "cover_titles.items",
                    "cover",
                    "comments.user","signs.user"])->findOrFail($id);
            $automation->update(["is_read" => 1]);
            $final_role = $automation->final_automation_role();
            $employee_invoices = $automation->invoices->toArray();
            $automation->invoices->map(function ($item) use ($employee_invoices) {
                $search_data = array_column($employee_invoices, "employee_id");
                $index = array_search($item->employee->id, $search_data);
                if (gettype($index) !== 'boolean') {
                    $item->employee["invoice_data"] = json_decode($employee_invoices[$index]["data"]);
                }
            });
            return view("staff.invoice_details",
                [
                    "automation" => $automation,
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
        Gate::authorize('agree',"InvoiceAutomation");
        try {
            DB::beginTransaction();
            $automation = InvoiceAutomation::query()->findOrFail($id);
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
            return redirect()->route("InvoiceAutomation.index")->with(["result" => "success","message" => "sent"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function disagree(Request $request,$id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('disagree',"InvoiceAutomation");
        try {
            DB::beginTransaction();
            $automation = InvoiceAutomation::query()->findOrFail($id);
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
            return redirect()->route("InvoiceAutomation.index")->with(["result" => "success","message" => "referred"]);
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
    public function print_cover($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $automation = InvoiceAutomation::query()->with([
                "contract.employees",
                "contract",
                "authorized_date",
                "cover_titles.items",
                "cover",
                "comments.user","signs.user"])->findOrFail($id);
            $signs = [];
            foreach ($automation->signs as $sign){
                if (Storage::disk("staff_signs")->exists("{$sign->user->id}/{$sign->user->sign}"))
                    $signs[] = ["id" => $sign->user->id,"sign" => base64_encode(Storage::disk("staff_signs")->get("{$sign->user->id}/{$sign->user->sign}"))];
            }
            return view("staff.print_invoice_cover",["automation" => $automation,"signs" => $signs]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
