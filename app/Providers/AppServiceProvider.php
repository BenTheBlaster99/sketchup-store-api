<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        // Prefer livewire.js — on Wasmer the min.js route has been unreliable.
        config([
            'livewire.asset_url' => '/index.php/livewire/livewire.js',
        ]);

        // Ensure the non-min script route exists even when APP_DEBUG=false.
        Livewire::setScriptRoute(function ($handle) {
            return Route::get('/livewire/livewire.js', $handle);
        });

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
