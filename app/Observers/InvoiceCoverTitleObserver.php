<?php

namespace App\Observers;

use App\Models\InvoiceCoverTitle;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class InvoiceCoverTitleObserver
{
    public bool $afterCommit = true;

    public function created(InvoiceCoverTitle $invoiceCoverTitle): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد عنوان روکش وضعیت",
            "model" => "InvoiceCoverTitle",
            "data" => json_encode($invoiceCoverTitle->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(InvoiceCoverTitle $invoiceCoverTitle): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش عنوان روکش وضعیت",
            "model" => "InvoiceCoverTitle",
            "data" => json_encode($invoiceCoverTitle->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(InvoiceCoverTitle $invoiceCoverTitle): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف عنوان روکش وضعیت",
            "model" => "InvoiceCoverTitle",
            "data" => json_encode($invoiceCoverTitle->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(InvoiceCoverTitle $invoiceCoverTitle): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی عنوان روکش وضعیت",
            "model" => "InvoiceCoverTitle",
            "data" => json_encode($invoiceCoverTitle->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(InvoiceCoverTitle $invoiceCoverTitle): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم عنوان روکش وضعیت",
            "model" => "InvoiceCoverTitle",
            "data" => json_encode($invoiceCoverTitle->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
