<?php

namespace App\Observers;

use App\Models\AutomationFlow;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AutomationFlowObserver
{
    public bool $afterCommit = true;

    public function created(AutomationFlow $automationFlow): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد گردش اتوماسیون",
            "model" => "AutomationFlow",
            "data" => json_encode($automationFlow->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(AutomationFlow $automationFlow): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش گردش اتوماسیون",
            "model" => "AutomationFlow",
            "data" => json_encode($automationFlow->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(AutomationFlow $automationFlow): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف گردش اتوماسیون",
            "model" => "AutomationFlow",
            "data" => json_encode($automationFlow->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(AutomationFlow $automationFlow): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی گردش اتوماسیون",
            "model" => "AutomationFlow",
            "data" => json_encode($automationFlow->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(AutomationFlow $automationFlow): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم گردش اتوماسیون",
            "model" => "AutomationFlow",
            "data" => json_encode($automationFlow->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
