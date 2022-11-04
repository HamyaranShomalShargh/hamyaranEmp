<?php

namespace App\Observers;

use App\Models\MenuItem;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class MenuItemObserver
{
    public bool $afterCommit = true;

    public function created(MenuItem $menuItem): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد عنوان منو",
            "model" => "MenuItem",
            "data" => json_encode($menuItem->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(MenuItem $menuItem): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش عنوان منو",
            "model" => "MenuItem",
            "data" => json_encode($menuItem->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(MenuItem $menuItem): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف عنوان منو",
            "model" => "MenuItem",
            "data" => json_encode($menuItem->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(MenuItem $menuItem): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی عنوان منو",
            "model" => "MenuItem",
            "data" => json_encode($menuItem->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(MenuItem $menuItem): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم عنوان منو",
            "model" => "MenuItem",
            "data" => json_encode($menuItem->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
