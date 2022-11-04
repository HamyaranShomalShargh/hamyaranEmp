<?php

namespace App\Observers;

use App\Models\Advantage;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AdvantageObserver
{
    public bool $afterCommit = true;

    public function created(Advantage $advantage): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد نمونه فرم تغییرات مزایا",
            "model" => "Advantage",
            "data" => json_encode($advantage->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(Advantage $advantage): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش نمونه فرم تغییرات مزایا",
            "model" => "Advantage",
            "data" => json_encode($advantage->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(Advantage $advantage): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف نمونه فرم تغییرات مزایا",
            "model" => "Advantage",
            "data" => json_encode($advantage->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(Advantage $advantage): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی نمونه فرم تغییرات مزایا",
            "model" => "Advantage",
            "data" => json_encode($advantage->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(Advantage $advantage): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم نمونه فرم تغییرات مزایا",
            "model" => "Advantage",
            "data" => json_encode($advantage->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
