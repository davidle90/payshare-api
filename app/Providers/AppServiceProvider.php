<?php

namespace App\Providers;

use App\Policies\V1\GroupPolicy;
use App\Policies\V1\PaymentPolicy;
use App\Policies\V1\UserPolicy;
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
        Gate::define('show-all-groups', [GroupPolicy::class, 'showAll']);
        Gate::define('show-group', [GroupPolicy::class, 'show']);
        Gate::define('store-group', [GroupPolicy::class, 'store']);
        Gate::define('update-group', [GroupPolicy::class, 'update']);
        Gate::define('delete-group', [GroupPolicy::class, 'delete']);
        Gate::define('member-group', [GroupPolicy::class, 'is_member']);

        Gate::define('store-user', [UserPolicy::class, 'store']);
        Gate::define('update-user', [UserPolicy::class, 'update']);
        Gate::define('delete-user', [UserPolicy::class, 'delete']);

        Gate::define('show-all-payments', [PaymentPolicy::class, 'showAll']);
        Gate::define('show-payment', [PaymentPolicy::class, 'show']);
        Gate::define('store-payment', [PaymentPolicy::class, 'store']);
        Gate::define('update-payment', [PaymentPolicy::class, 'update']);
        Gate::define('delete-payment', [PaymentPolicy::class, 'delete']);
        Gate::define('payment-group', [PaymentPolicy::class, 'is_payment_group']);

    }
}
