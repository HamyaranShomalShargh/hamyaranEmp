<?php

namespace App\Observers;

use App\Models\TableAttribute;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class TableAttributeObserver
{
    public bool $afterCommit = true;

    public function created(TableAttribute $tableAttribute): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد عنوان ورودی",
            "model" => "TableAttribute",
            "data" => json_encode($tableAttribute->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(TableAttribute $tableAttribute): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش عنوان ورودی",
            "model" => "TableAttribute",
            "data" => json_encode($tableAttribute->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(TableAttribute $tableAttribute): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف عنوان ورودی",
            "model" => "TableAttribute",
            "data" => json_encode($tableAttribute->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(TableAttribute $tableAttribute): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی عنوان ورودی",
            "model" => "TableAttribute",
            "data" => json_encode($tableAttribute->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(TableAttribute $tableAttribute): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم عنوان ورودی",
            "model" => "TableAttribute",
            "data" => json_encode($tableAttribute->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
