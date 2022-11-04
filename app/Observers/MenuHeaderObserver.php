<?php

namespace App\Observers;

use App\Models\MenuHeader;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class MenuHeaderObserver
{
    public bool $afterCommit = true;

    public function created(MenuHeader $menuHeader): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد سرفصل منو",
            "model" => "MenuHeader",
            "data" => json_encode($menuHeader->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(MenuHeader $menuHeader): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش سرفصل منو",
            "model" => "MenuHeader",
            "data" => json_encode($menuHeader->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(MenuHeader $menuHeader): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف سرفصل منو",
            "model" => "MenuHeader",
            "data" => json_encode($menuHeader->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(MenuHeader $menuHeader): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی سرفصل منو",
            "model" => "MenuHeader",
            "data" => json_encode($menuHeader->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(MenuHeader $menuHeader): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم سرفصل منو",
            "model" => "MenuHeader",
            "data" => json_encode($menuHeader->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
