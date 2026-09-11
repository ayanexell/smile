<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Access\Response;
// use Illuminate\Http\Response;
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
        $this->configureDefaults();

        Gate::define('isSuperAdmin', function ($user) {
            return $user->role->nama_role === "Super Admin" ? Response::allow() : Response::denyWithStatus(404);
        });

        Gate::define('isAdmin', function ($user) {
            return $user->role->nama_role === "Admin" ? Response::allow() : Response::denyWithStatus(404);
        });

        Gate::define('isKoordinator', function ($user) {
            return $user->role->nama_role === "Koordinator" ? Response::allow() : Response::denyWithStatus(404);
        });

        Gate::define('isUser', function ($user) {
            return $user->role->nama_role === "User" ? Response::allow() : Response::denyWithStatus(404);
        });

        Gate::define('isSuperAdminAndAdmin', function ($user) {
            return ($user->role->nama_role === "Super Admin" || $user->role->nama_role === 'Admin') ? Response::allow() : Response::denyWithStatus(404);
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        // \Illuminate\Validation\Rules\Password::defaults(fn (): ?Password => app()->isProduction()
        //     ? Password::min(12)
        //         ->mixedCase()
        //         ->letters()
        //         ->numbers()
        //         ->symbols()
        //         ->uncompromised()
        //     : null,
        // );
    }
}
