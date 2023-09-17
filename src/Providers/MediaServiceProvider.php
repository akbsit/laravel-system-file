<?php namespace Akbsit\SystemFile\Providers;

use Akbsit\SystemFile\Observers\SystemFileObserver;
use Akbsit\SystemFile\Console\SystemFileSync;
use Akbsit\SystemFile\Models\SystemFile;

use Illuminate\Support\ServiceProvider;

/**
 * Class MediaServiceProvider
 * @package Akbsit\SystemFile\Providers
 */
class MediaServiceProvider extends ServiceProvider
{
    /* @return void */
    public function register(): void
    {
        $this->commands([
            SystemFileSync::class,
        ]);
    }

    /* @return void */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        SystemFile::observe(SystemFileObserver::class);
    }
}
