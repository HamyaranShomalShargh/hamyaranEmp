<?php

namespace App\Observers;

use App\Models\ContractSubset;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ContractSubsetObserver
{
    public bool $afterCommit = true;

    public function created(ContractSubset $contractSubset): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد زیرمجموعه قرارداد",
            "model" => "ContractSubset",
            "data" => json_encode($contractSubset->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(ContractSubset $contractSubset): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش زیرمجموعه قرارداد",
            "model" => "ContractSubset",
            "data" => json_encode($contractSubset->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(ContractSubset $contractSubset): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف زیرمجموعه قرارداد",
            "model" => "ContractSubset",
            "data" => json_encode($contractSubset->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(ContractSubset $contractSubset): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی زیرمجموعه قرارداد",
            "model" => "ContractSubset",
            "data" => json_encode($contractSubset->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(ContractSubset $contractSubset): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم زیرمجموعه قرارداد",
            "model" => "ContractSubset",
            "data" => json_encode($contractSubset->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
