<?php namespace Akbsit\SystemFile\Console;

use Carbon\Carbon;

/**
 * Class SystemFileSync
 * @package Akbsit\SystemFile\Console
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
        $this->syncSystemFileFromDataBase();

        $this->completedMessage();

        $this->info('Stop handle');
    }

    /* @return void */
    private function completedMessage(): void
    {
        $this->output->newLine();

        if ($this->iFileStorageCount === $this->iFileDataBaseCount) {
            $sMessage = 'File count in storage: ' . $this->iFileStorageCount . PHP_EOL;
            $sMessage .= 'File count in database: ' . $this->iFileDataBaseCount;

            $this->output->success($sMessage);
        } else {
            $this->output->warning('Syncing completed');
        }

        $this->output->newLine(2);
    }
}
