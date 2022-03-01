<?php namespace Falbar\SystemFile\Providers;

use Falbar\SystemFile\Observers\SystemFileObserver;
use Falbar\SystemFile\Console\SystemFileClear;
use Falbar\SystemFile\Models\SystemFile;

use Illuminate\Support\ServiceProvider;

/**
 * Class MediaServiceProvider
 * @package Falbar\SystemFile\Providers
 */
class MediaServiceProvider extends ServiceProvider
{
    /* @return void */
    public function register(): void
    {
        $this->commands([
            SystemFileClear::class,
        ]);
    }

    /* @return void */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        SystemFile::observe(SystemFileObserver::class);
    }
}
