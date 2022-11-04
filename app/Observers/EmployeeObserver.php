<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class EmployeeObserver
{
    public bool $afterCommit = true;

    public function created(Employee $employee): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد پرسنل",
            "model" => "Employee",
            "data" => json_encode($employee->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(Employee $employee): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش پرسنل",
            "model" => "Employee",
            "data" => json_encode($employee->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(Employee $employee): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف پرسنل",
            "model" => "Employee",
            "data" => json_encode($employee->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(Employee $employee): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی پرسنل",
            "model" => "Employee",
            "data" => json_encode($employee->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(Employee $employee): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم پرسنل",
            "model" => "Employee",
            "data" => json_encode($employee->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
