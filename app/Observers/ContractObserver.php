<?php

namespace App\Observers;

use App\Models\Contract;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ContractObserver
{
    public bool $afterCommit = true;

    public function created(Contract $contract): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد سرفصل قرارداد",
            "model" => "Contract",
            "data" => json_encode($contract->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(Contract $contract): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش سرفصل قرارداد",
            "model" => "Contract",
            "data" => json_encode($contract->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(Contract $contract): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف سرفصل قرارداد",
            "model" => "Contract",
            "data" => json_encode($contract->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(Contract $contract): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی سرفصل قرارداد",
            "model" => "Contract",
            "data" => json_encode($contract->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(Contract $contract): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم سرفصل قرارداد",
            "model" => "Contract",
            "data" => json_encode($contract->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
