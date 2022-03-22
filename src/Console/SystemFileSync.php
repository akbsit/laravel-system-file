<?php namespace Falbar\SystemFile\Console;

use Carbon\Carbon;

/**
 * Class SystemFileSync
 * @package Falbar\SystemFile\Console
 */
class SystemFileSync extends AbstractSystemFileSync
{
    /* @var string */
    protected $signature = 'system-file:sync';

    /* @var string */
    protected $description = 'File synchronization';

    /**
     * Execute the console command
     *
     * @return void
     */
    public function handle(): void
    {
        $this->alert('Start handle');

        $this->info('Current date: ' . Carbon::now());

        $this->syncSystemFileFromStorage();

        $this->output->newLine(2);

        $this->info('Stop handle');
    }
}
