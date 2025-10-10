<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Additional gates jika diperlukan
        Gate::define('manage-system', function (User $user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage-team', function (User $user) {
            return $user->hasRole(['admin', 'manager']);
        });

        Gate::define('approve-requests', function (User $user) {
            return $user->hasRole(['admin', 'manager']);
        });
    }
}