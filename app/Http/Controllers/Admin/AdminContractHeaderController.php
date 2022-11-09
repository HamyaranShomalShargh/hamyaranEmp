<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractHeaderRequest;
use App\Models\Contract;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AdminContractHeaderController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $contract_header = Contract::with("user")->get();
            return view("admin.contract_header",["contract_headers" => $contract_header]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(ContractHeaderRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $validated = $request->validated();
            DB::beginTransaction();
            $validated["user_id"] = Auth::id();
            $validated["start_date"] = $this->to_gregorian($validated["start_date"]);
            $validated["end_date"] = $this->to_gregorian($validated["end_date"]);
            $contract_header = Contract::query()->create($validated);
            if ($request->hasFile('upload_files')) {
                foreach ($request->file('upload_files') as $file)
                    Storage::disk('contract_docs')->put($contract_header->id, $file);
                $contract_header->update(["files" => 1]);
            }
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "saved"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $contract_header = Contract::query()->findOrFail($id);
            return view("admin.edit_contract_header",["contract_header" => $contract_header]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(ContractHeaderRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $validated = $request->validated();
            DB::beginTransaction();
            $validated["user_id"] = Auth::id();
            $validated["start_date"] = $this->to_gregorian($validated["start_date"]);
            $validated["end_date"] = $this->to_gregorian($validated["end_date"]);
            $contract_header = Contract::query()->findOrFail($id);
            $contract_header->update($validated);
            if ($request->hasFile('upload_files')) {
                foreach ($request->file('upload_files') as $file)
                    Storage::disk('contract_docs')->put($contract_header->id, $file);
                $contract_header->update(["files" => 1]);
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
            $contract_header = Contract::query()->findOrFail($id);
            if ($contract_header->subsets()->exists())
                return redirect()->back()->with(["result" => "warning","message" => "relation_exists"]);
            else{
                DB::beginTransaction();
                Storage::disk("contract_docs")->deleteDirectory($id);
                $contract_header->delete();
                DB::commit();
                return redirect()->back()->with(["result" => "success","message" => "deleted"]);
            }
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function status($id): \Illuminate\Http\RedirectResponse
    {
        $contract_header = Contract::query()->findOrFail($id);
        return redirect()->back()->with(["result" => "success","message" => $this->activation($contract_header)]);
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
