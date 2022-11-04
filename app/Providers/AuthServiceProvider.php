<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\MenuAction;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        foreach (MenuAction::all() as $action){
            Gate::define($action->action,function ($user,$model) use ($action){return $user->hasPermission($action->action,$model);});
        }
    }
}
