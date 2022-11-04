<?php

namespace App\Observers;

use App\Models\MenuAction;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class MenuActionObserver
{
    public bool $afterCommit = true;

    public function created(MenuAction $menuAction): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد عملیات منو",
            "model" => "MenuAction",
            "data" => json_encode($menuAction->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(MenuAction $menuAction): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش عملیات منو",
            "model" => "MenuAction",
            "data" => json_encode($menuAction->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(MenuAction $menuAction): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف عملیات منو",
            "model" => "MenuAction",
            "data" => json_encode($menuAction->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(MenuAction $menuAction): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی عملیات منو",
            "model" => "MenuAction",
            "data" => json_encode($menuAction->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(MenuAction $menuAction): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم عملیات منو",
            "model" => "MenuAction",
            "data" => json_encode($menuAction->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
