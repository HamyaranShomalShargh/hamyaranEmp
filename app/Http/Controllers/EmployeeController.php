<?php

namespace App\Http\Controllers;

use App\Exports\ContractChangeEmployeeExport;
use App\Exports\NewEmployeesExport;
use App\Http\Requests\EmployeeRequest;
use App\Models\Contract;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class EmployeeController extends Controller
{

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"Employees");
        try {
            $employees = Employee::query()->with(["contract","user"])->get();
            return view("staff.employees", ["employees" => $employees,"contracts" => Contract::Output()]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"Employees");
        try {
            $employee = Employee::query()->findOrFail($id);
            return view("staff.edit_employee", ["employee" => $employee,"contracts" => Contract::Output()]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(EmployeeRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"Employees");
        try{
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $employee = Employee::query()->findOrFail($id);
            $employee->update($validated);
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "updated"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroy($id)
    {}

    public function export_new_employee_excel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new NewEmployeesExport(), 'new_employees.xlsx');
    }
    public function export_change_contract_employee_excel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new ContractChangeEmployeeExport(), 'change_contract_employees.xlsx');
    }
}
