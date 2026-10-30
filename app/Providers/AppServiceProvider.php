<?php

namespace App\Providers;

use App\Models\RIE\ExtensionPlan;
use App\Models\User;
use App\Policies\ExtensionPlanPolicy;
use Illuminate\Auth\Events\Login;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(191);

        Paginator::useBootstrapFive();

        // Extension Plan authorization policy (Self vs RIE In-Charge oversight)
        Gate::policy(ExtensionPlan::class, ExtensionPlanPolicy::class);

        // Implicitly grant "SuperAdmin" role all permission checks
        Gate::before(function ($user, $ability) {
            return ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) ? true : null;
        });

        // Automatically ensure SuperAdmin role and authority matrix on login
        Event::listen(Login::class, function (Login $event) {
            if ($event->user instanceof User && method_exists($event->user, 'isDesignatedSuperAdmin') && $event->user->isDesignatedSuperAdmin()) {
                $event->user->ensureSuperAdminState();
            }
        });
    }
}
