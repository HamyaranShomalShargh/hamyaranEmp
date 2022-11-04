<?php

namespace App\Observers;

use App\Models\Role;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class RoleObserver
{
    public bool $afterCommit = true;

    public function created(Role $role): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد عنوان شغلی",
            "model" => "Role",
            "data" => json_encode($role->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(Role $role): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش عنوان شغلی",
            "model" => "Role",
            "data" => json_encode($role->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(Role $role): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف عنوان شغلی",
            "model" => "Role",
            "data" => json_encode($role->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(Role $role): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی عنوان شغلی",
            "model" => "Role",
            "data" => json_encode($role->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(Role $role): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم عنوان شغلی",
            "model" => "Role",
            "data" => json_encode($role->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
