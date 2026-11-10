<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Integrasi Gate dengan sistem Role & Permission
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }

            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('permissions')) {
                    return $user->hasPermission($ability) ? true : null;
                }
            } catch (\Throwable $e) {
                return null;
            }

            return null;
        });
    }
}
