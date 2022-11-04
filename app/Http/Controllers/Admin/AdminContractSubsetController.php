<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractSubsetRequest;
use App\Models\AutomationFlow;
use App\Models\Contract;
use App\Models\ContractSubset;
use App\Models\InvoiceCoverTitle;
use App\Models\TableAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AdminContractSubsetController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $contract_subsets = ContractSubset::query()->with(["user", "contract", "children","parent", "employees"])->get();
            $contract_headers = Contract::all();
            $automation_flows = AutomationFlow::query()->with(["user","details"])->where("inactive","=",0)->get();
            $invoice_cover_titles = InvoiceCoverTitle::all();
            $table_attributes = TableAttribute::all();
            return view("admin.contract_subsets", [
                "contract_subsets" => $contract_subsets,
                "contract_headers" => $contract_headers,
                "automation_flows" => $automation_flows,
                "table_attributes" => $table_attributes,
                "invoice_cover_titles" => $invoice_cover_titles
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(ContractSubsetRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            DB::beginTransaction();
            $contract_subset = ContractSubset::query()->create($validated);
            if ($request->hasFile('upload_files')) {
                foreach ($request->file('upload_files') as $file)
                    Storage::disk('contract_subset_docs')->put($contract_subset->id, $file);
                $contract_subset->update(["files" => 1]);
            }
            DB::commit();
            return redirect()->back()->with(["result" =>  "success" , "message" => "saved"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $contract_subsets = ContractSubset::all();
            $contract_headers = Contract::all();
            $contract_subset = ContractSubset::query()->with(["contract","employees.user","parent"])->findOrFail($id);
            $automation_flows = AutomationFlow::query()->with(["user","details"])->where("inactive","=",0)->get();
            $table_attributes = TableAttribute::all();
            $invoice_cover_titles = InvoiceCoverTitle::all();
            return view("admin.edit_contract_subset",[
                "contract_subset" => $contract_subset,
                "contract_subsets" => $contract_subsets,
                "contract_headers" => $contract_headers,
                "automation_flows" => $automation_flows,
                "table_attributes" => $table_attributes,
                "invoice_cover_titles" => $invoice_cover_titles
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(ContractSubsetRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            DB::beginTransaction();
            $contract_subset = ContractSubset::query()->findOrFail($id);
            $contract_subset->update($validated);
            if ($request->hasFile('upload_files')) {
                foreach ($request->file('upload_files') as $file)
                    Storage::disk('contract_subset_docs')->put($contract_subset->id, $file);
                $contract_subset->update(["files" => 1]);
            }
            DB::commit();
            return redirect()->back()->with(["result" =>  "success" , "message" => "updated"]);
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
            $contract_subset = ContractSubset::query()->findOrFail($id);
            $contract_subset->employees()->delete();
            Storage::disk("contract_subset_docs")->deleteDirectory($id);
            $contract_subset->delete();
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
        $contract_subset = ContractSubset::query()->findOrFail($id);
        return redirect()->back()->with(["result" => "success","message" => $this->activation($contract_subset)]);
    }

    public function download_docs($id): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        $status = $this->download($id,"contract_docs","private");
        if ($status["success"]) {
            $zip_file = Storage::disk("contract_docs")->path("/zip/{$id}/docs.zip");
            $zip_file_name = "contract_docs_" . verta()->format("Y-m-d H-i-s") . ".zip";
            return Response::download($zip_file,$zip_file_name,[],'inline');
        }
        else
            return redirect()->back()->withErrors(["logical" => $status["message"]]);
    }
}
