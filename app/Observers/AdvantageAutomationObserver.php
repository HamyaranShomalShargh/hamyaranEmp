<?php

namespace App\Observers;

use App\Models\AdvantageAutomation;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AdvantageAutomationObserver
{
    public bool $afterCommit = true;

    public function created(AdvantageAutomation $advantageAutomation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد اتوماسیون تغییرات مزایا",
            "model" => "AdvantageAutomation",
            "data" => json_encode($advantageAutomation->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(AdvantageAutomation $advantageAutomation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش اتوماسیون تغییرات مزایا",
            "model" => "AdvantageAutomation",
            "data" => json_encode($advantageAutomation->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(AdvantageAutomation $advantageAutomation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف اتوماسیون تغییرات مزایا",
            "model" => "AdvantageAutomation",
            "data" => json_encode($advantageAutomation->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(AdvantageAutomation $advantageAutomation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی اتوماسیون تغییرات مزایا",
            "model" => "AdvantageAutomation",
            "data" => json_encode($advantageAutomation->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(AdvantageAutomation $advantageAutomation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم اتوماسیون تغییرات مزایا",
            "model" => "AdvantageAutomation",
            "data" => json_encode($advantageAutomation->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
