<?php

namespace App\Providers;

use App\Models\AdvantageAutomation;
use App\Models\CompanyInformation;
use App\Models\ContractSubset;
use App\Models\InvoiceAutomation;
use App\Models\MenuHeader;
use App\Models\PerformanceAutomation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind("path.public",function (){
            return base_path()."/public_html";
        });
    }

    public function boot()
    {
        Paginator::useBootstrap();
        View::composer(['admin.*'], function ($view) {
            $company_information = CompanyInformation::query()->first();
            $auth_user = User::query()->with("role")->findOrFail(Auth::id());
            $view->with([
                "company_information" => $company_information,
                "auth_user" => $auth_user,
            ]);
        });
        View::composer(['staff.*'], function ($view) {
            $company_information = CompanyInformation::query()->first();
            $menu_headers = MenuHeader::query()->with(["items.actions","items.children"])->orderBy("priority")->get();
            $role = Role::query()->with("menu_items.actions")->findOrFail(Auth::user()->role_id);
            $auth_user = User::query()->with("role")->findOrFail(Auth::id());
            $contracts = ContractSubset::permitted_contracts();
            $new_performance_notifications = [];
            $new_invoice_notifications = [];
            $new_advantage_notifications = [];
            if($contracts != []) {
                $performance_automation_inbox = PerformanceAutomation::query()->with(["authorized_date", "current_role", "contract", "user", "performances"])
                    ->whereHas("current_role", function ($query) {
                        $query->where("id", "=", Auth::user()->role->id);
                    })->whereHas("contract", function ($query) use ($contracts) {
                        $query->whereIn("contract_subset_id", $contracts->pluck("id"));
                    })->where("is_finished", "=", 0)->where("is_read","=",0)->orderBy("id", "desc")->get();
                $invoice_automation_inbox = InvoiceAutomation::query()->with(["authorized_date", "current_role", "contract", "user", "invoices"])
                    ->whereHas("current_role", function ($query) {
                        $query->where("id", "=", Auth::user()->role->id);
                    })->whereHas("contract", function ($query) use ($contracts) {
                        $query->whereIn("contract_subset_id", $contracts->pluck("id"));
                    })->where("is_finished", "=", 0)->where("is_read","=",0)->orderBy("id", "desc")->get();
                $advantage_automation_inbox = AdvantageAutomation::query()->with(["current_role", "employee.contract", "user","advantage"])
                    ->whereHas("current_role", function ($query) {
                        $query->where("id", "=", Auth::user()->role->id);
                    })->whereHas("employee.contract", function ($query) use ($contracts) {
                        $query->whereIn("contract_subset_id", $contracts->pluck("id"));
                    })->where("is_finished", "=", 0)->where("is_read","=",0)->orderBy("id", "desc")->get();
                foreach ($performance_automation_inbox as $performance) {
                    if ($performance->is_referred)
                        $message = "کارکرد ".$performance->contract->name."(".$performance->contract->workplace . ") در " . $performance->authorized_date->month_name. " ماه سال " . $performance->authorized_date->automation_year." به صندوق اتوماسیون شما ارجاع شد";
                    else
                        $message = "کارکرد ".$performance->contract->name."(".$performance->contract->workplace . ") در " . $performance->authorized_date->month_name. " ماه سال " . $performance->authorized_date->automation_year." به صندوق اتوماسیون شما ارسال شد";
                    $new_performance_notifications[] = ["message" => $message, "action" => route("PerformanceAutomation.details", $performance->id)];
                }
                foreach ($invoice_automation_inbox as $invoice) {
                    if ($invoice->is_referred)
                        $message = "وضعیت ".$invoice->contract->name."(".$invoice->contract->workplace . ") در " . $invoice->authorized_date->month_name. " ماه سال " . $invoice->authorized_date->automation_year." به صندوق اتوماسیون شما ارجاع شد";
                    else
                        $message = "وضعیت ".$invoice->contract->name."(".$invoice->contract->workplace . ") در " . $invoice->authorized_date->month_name. " ماه سال " . $invoice->authorized_date->automation_year." به صندوق اتوماسیون شما ارسال شد";
                    $new_invoice_notifications[] = ["message" => $message, "action" => route("InvoiceAutomation.details", $invoice->id)];
                }
                foreach ($advantage_automation_inbox as $advantage) {
                    if ($advantage->is_referred)
                        $message = "درخواست تغییرات مزایای ".$advantage->advantage->name." ".$advantage->employee->first_name." ".$advantage->employee->last_name." به صندوق اتوماسیون شما ارجاع شد";
                    else
                        $message = "درخواست تغییرات مزایای ".$advantage->advantage->name." ".$advantage->employee->first_name." ".$advantage->employee->last_name." به صندوق اتوماسیون شما ارسال شد";
                    $new_advantage_notifications[] = ["message" => $message, "action" => route("AdvantageAutomation.details", $advantage->id)];
                }
            }
            $view->with([
                "company_information" => $company_information,
                "auth_user" => $auth_user,
                "menu_headers" => $menu_headers,
                "role" => $role,
                "new_performance_notifications" => $new_performance_notifications,
                "new_invoice_notifications" => $new_invoice_notifications,
                "new_advantage_notifications" => $new_advantage_notifications
            ]);
        });
    }
}
