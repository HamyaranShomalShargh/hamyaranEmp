<?php

namespace App\Observers;

use App\Models\CompanyInformation;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class CompanyInformationObserver
{
    public bool $afterCommit = true;

    public function created(CompanyInformation $companyInformation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد اطلاعات سازمان",
            "model" => "CompanyInformation",
            "data" => json_encode($companyInformation->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(CompanyInformation $companyInformation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش اطلاعات سازمان",
            "model" => "CompanyInformation",
            "data" => json_encode($companyInformation->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(CompanyInformation $companyInformation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف اطلاعات سازمان",
            "model" => "CompanyInformation",
            "data" => json_encode($companyInformation->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(CompanyInformation $companyInformation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی اطلاعات سازمان",
            "model" => "CompanyInformation",
            "data" => json_encode($companyInformation->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(CompanyInformation $companyInformation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم اطلاعات سازمان",
            "model" => "CompanyInformation",
            "data" => json_encode($companyInformation->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
