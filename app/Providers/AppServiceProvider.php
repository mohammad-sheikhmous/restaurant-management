<?php

namespace App\Providers;

use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();
    }

    private function configurePermissions()
    {
        foreach (config('roles.permissions.en') as $permission => $value) {
            Gate::define($permission, function ($auth) use ($permission) {
                return $auth->hasAccess($permission) ? Response::allow()
                    : Response::deny('you unauthorized to do this action');
            });
        }
    }
}
