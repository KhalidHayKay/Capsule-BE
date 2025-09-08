<?php

namespace App\Providers;

use App\Models\User;
use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Scramble::ignoreDefaultRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('viewApiDocs', function (?User $user = null) {
            $token    = request()->query('token');
            $envToken = env('APP_DOCS_ACCESS_TOKEN');

            if (! $envToken) {
                abort(403, 'App docs access token is not configured');
            }

            if ($token === $envToken) {
                return true;
            }

            return $user && in_array($user->email, ['john@example.com']);
        });

    }
}
