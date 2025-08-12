<?php

namespace App\Providers;

use App\Models\Destinasi;
use App\Policies\DestinasiPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Contracts\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Destinasi::class, DestinasiPolicy::class);

        if (str_contains(request()->url(), 'ngrok-free.app')) {
            URL::forceScheme('https');
        }
    }
}
