<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

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
        Paginator::useBootstrapFive();

        Request::macro('queryTrimmed', function (string $key, string $default = ''): string {
            return trim((string) $this->query($key, $default));
        });

        Request::macro('queryEnum', function (string $key, array $allowed): ?string {
            $value = $this->queryTrimmed($key);

            return in_array($value, $allowed, true) ? $value : null;
        });
    }
}
