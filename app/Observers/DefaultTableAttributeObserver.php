<?php

namespace App\Observers;

use App\Models\DefaultTableAttribute;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class DefaultTableAttributeObserver
{
    public bool $afterCommit = true;

    public function created(DefaultTableAttribute $defaultTableAttribute): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد عنوان ورودی پیشفرض",
            "model" => "DefaultTableAttribute",
            "data" => json_encode($defaultTableAttribute->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(DefaultTableAttribute $defaultTableAttribute): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش عنوان ورودی پیشفرض",
            "model" => "DefaultTableAttribute",
            "data" => json_encode($defaultTableAttribute->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(DefaultTableAttribute $defaultTableAttribute): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف عنوان ورودی پیشفرض",
            "model" => "DefaultTableAttribute",
            "data" => json_encode($defaultTableAttribute->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(DefaultTableAttribute $defaultTableAttribute): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی عنوان ورودی پیشفرض",
            "model" => "DefaultTableAttribute",
            "data" => json_encode($defaultTableAttribute->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(DefaultTableAttribute $defaultTableAttribute): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم عنوان ورودی پیشفرض",
            "model" => "DefaultTableAttribute",
            "data" => json_encode($defaultTableAttribute->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
