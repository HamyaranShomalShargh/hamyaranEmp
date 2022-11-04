<?php

namespace App\Providers;

use App\Models\Advantage;
use App\Models\AdvantageAutomation;
use App\Models\AutomationFlow;
use App\Models\CompanyInformation;
use App\Models\Contract;
use App\Models\ContractSubset;
use App\Models\DefaultTableAttribute;
use App\Models\Employee;
use App\Models\InvoiceAutomation;
use App\Models\InvoiceCoverTitle;
use App\Models\MenuAction;
use App\Models\MenuHeader;
use App\Models\MenuItem;
use App\Models\PerformanceAutomation;
use App\Models\Role;
use App\Models\TableAttribute;
use App\Models\User;
use App\Models\UserLog;
use App\Observers\AdvantageAutomationObserver;
use App\Observers\AdvantageObserver;
use App\Observers\AutomationFlowObserver;
use App\Observers\CompanyInformationObserver;
use App\Observers\ContractObserver;
use App\Observers\ContractSubsetObserver;
use App\Observers\DefaultTableAttributeObserver;
use App\Observers\EmployeeObserver;
use App\Observers\InvoiceAutomationObserver;
use App\Observers\InvoiceCoverTitleObserver;
use App\Observers\MenuActionObserver;
use App\Observers\MenuHeaderObserver;
use App\Observers\MenuItemObserver;
use App\Observers\PerformanceAutomationObserver;
use App\Observers\RoleObserver;
use App\Observers\TableAttributeObserver;
use App\Observers\UserLogObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        AdvantageAutomation::observe(AdvantageAutomationObserver::class);
        Advantage::observe(AdvantageObserver::class);
        AutomationFlow::observe(AutomationFlowObserver::class);
        CompanyInformation::observe(CompanyInformationObserver::class);
        Contract::observe(ContractObserver::class);
        ContractSubset::observe(ContractSubsetObserver::class);
        DefaultTableAttribute::observe(DefaultTableAttributeObserver::class);
        Employee::observe(EmployeeObserver::class);
        InvoiceAutomation::observe(InvoiceAutomationObserver::class);
        InvoiceCoverTitle::observe(InvoiceCoverTitleObserver::class);
        MenuAction::observe(MenuActionObserver::class);
        MenuHeader::observe(MenuHeaderObserver::class);
        MenuItem::observe(MenuItemObserver::class);
        PerformanceAutomation::observe(PerformanceAutomationObserver::class);
        Role::observe(RoleObserver::class);
        TableAttribute::observe(TableAttributeObserver::class);
        UserLog::observe(UserLogObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
