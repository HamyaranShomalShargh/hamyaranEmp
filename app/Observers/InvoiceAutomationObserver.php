<?php

namespace App\Observers;

use App\Models\InvoiceAutomation;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class InvoiceAutomationObserver
{
    public bool $afterCommit = true;

    public function created(InvoiceAutomation $invoiceAutomation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ایجاد",
            "description" => "ایجاد وضعیت ماهانه",
            "model" => "InvoiceAutomation",
            "data" => json_encode($invoiceAutomation->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function updated(InvoiceAutomation $invoiceAutomation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "ویرایش",
            "description" => "ویرایش وضعیت ماهانه",
            "model" => "InvoiceAutomation",
            "data" => json_encode($invoiceAutomation->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function deleted(InvoiceAutomation $invoiceAutomation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف وضعیت ماهانه",
            "model" => "InvoiceAutomation",
            "data" => json_encode($invoiceAutomation->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function restored(InvoiceAutomation $invoiceAutomation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "بازیابی",
            "description" => "بازیابی وضعیت ماهانه",
            "model" => "InvoiceAutomation",
            "data" => json_encode($invoiceAutomation->toArray()),
            "ip" => Request::ip()
        ]);
    }

    public function forceDeleted(InvoiceAutomation $invoiceAutomation): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف دائم",
            "description" => "حذف دائم وضعیت ماهانه",
            "model" => "InvoiceAutomation",
            "data" => json_encode($invoiceAutomation->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
