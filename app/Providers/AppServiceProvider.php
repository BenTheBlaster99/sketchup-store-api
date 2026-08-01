<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        if (! $this->shouldPrefixIndexPhp()) {
            return;
        }

        // Wasmer PHPIx can't evaluate RewriteRule .htaccess, so browsers must
        // hit dynamic routes via /index.php/... Static assets stay unprefixed.
        config([
            'livewire.asset_url' => '/index.php/livewire/livewire.min.js',
        ]);

        URL::formatPathUsing(function (string $path) {
            $staticPrefixes = [
                '/js/',
                '/css/',
                '/fonts/',
                '/build/',
                '/vendor/',
                '/favicon',
                '/robots',
                '/storage/',
            ];

            foreach ($staticPrefixes as $prefix) {
                if (str_starts_with($path, $prefix)) {
                    return $path;
                }
            }

            if ($path === '/' || str_starts_with($path, '/index.php')) {
                return $path;
            }

            return '/index.php'.$path;
        });
    }

    private function shouldPrefixIndexPhp(): bool
    {
        if (filter_var(env('WASMER_PATH_INFO', false), FILTER_VALIDATE_BOOL)) {
            return true;
        }

        return str_contains((string) config('app.url'), 'wasmer.app');
    }
}
