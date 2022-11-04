<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class UserObserver
{
    public bool $afterCommit = true;

    public function created(User $user): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد کاربران",
            "model" => "User",
            "data" => json_encode($user->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(User $user): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش کاربران",
            "model" => "User",
            "data" => json_encode($user->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(User $user): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف کاربران",
            "model" => "User",
            "data" => json_encode($user->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(User $user): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی کاربران",
            "model" => "User",
            "data" => json_encode($user->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(User $user): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم کاربران",
            "model" => "User",
            "data" => json_encode($user->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
