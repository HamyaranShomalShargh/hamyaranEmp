<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyInformationRequest;
use App\Models\CompanyInformation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CompanyInformationController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $users = User::query()->where("is_staff","=",1)->get();
            $company_information = CompanyInformation::query()->first();
            return view("admin.company_information",["company_information" => $company_information,"users" => $users]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(CompanyInformationRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $validated = $request->validated();
            DB::beginTransaction();
            $company_information = CompanyInformation::query()->first();
            $company_information->update($validated);
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "updated"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
