<?php

namespace App\Observers;

use App\Models\PerformanceAutomation;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PerformanceAutomationObserver
{
    public bool $afterCommit = true;

    public function created(PerformanceAutomation $performanceAutomation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد اتوماسیون کارکرد ماهانه",
            "model" => "PerformanceAutomation",
            "data" => json_encode($performanceAutomation->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(PerformanceAutomation $performanceAutomation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش اتوماسیون کارکرد ماهانه",
            "model" => "PerformanceAutomation",
            "data" => json_encode($performanceAutomation->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(PerformanceAutomation $performanceAutomation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف اتوماسیون کارکرد ماهانه",
            "model" => "PerformanceAutomation",
            "data" => json_encode($performanceAutomation->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(PerformanceAutomation $performanceAutomation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی اتوماسیون کارکرد ماهانه",
            "model" => "PerformanceAutomation",
            "data" => json_encode($performanceAutomation->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(PerformanceAutomation $performanceAutomation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم اتوماسیون کارکرد ماهانه",
            "model" => "PerformanceAutomation",
            "data" => json_encode($performanceAutomation->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
