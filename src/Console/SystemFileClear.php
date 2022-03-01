<?php namespace Falbar\SystemFile\Console;

use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use Falbar\SystemFile\Models\SystemFile;
use Carbon\Carbon;

/**
 * Class SystemFileClear
 * @package Falbar\SystemFile\Console
 */
class SystemFileClear extends Command
{
    const DISK_PUBLIC = 'public';

    /* @var string */
    protected $signature = 'system-file:clear {dirs : List directories for watching}';

    /* @var string */
    protected $description = 'Clear system file service';

    /**
     * Execute the console command
     *
     * @return void
     */
    public function handle(): void
    {
        $this->alert('Start handle');

        $this->info('Current date: ' . Carbon::now());

        $sDirs = (string)$this->argument('dirs');
        if (empty($sDirs)) {
            $this->output->newLine();
            $this->error('No dirs input');

            return;
        }

        $this->info('Set directories: ' . $sDirs);

        $this->output->newLine(2);

        $this->syncFiles($sDirs);
        $this->clearEmptyDirectories($sDirs);
        $this->info('Stop handle');
    }

    /**
     * @param string $sDirectoryList
     *
     * @return void
     */
    private function syncFiles(string $sDirectoryList): void
    {
        $iCountFiles = 0;

        Str::of($sDirectoryList)
            ->explode(',')
            ->each(function ($sDirectory) use (&$iCountFiles) {
                $oStorage = Storage::disk(self::DISK_PUBLIC);
                if ($oStorage->exists($sDirectory)) {
                    $arFileList = $oStorage->allFiles($sDirectory);
                    if (count($arFileList)) {
                        foreach ($arFileList as $sFileItem) {
                            $oSystemFile = SystemFile::where('dir', $sDirectory)
                                ->where('file_name', pathinfo($sFileItem, PATHINFO_BASENAME))
                                ->where('file_size', $oStorage->size($sFileItem))
                                ->first();
                            if (empty($oSystemFile)) {
                                $oStorage->delete($sFileItem);

                                $iCountFiles++;
                            }
                        }
                    }
                }
            });

        if ($iCountFiles) {
            $this->info('Delete count files: ' . $iCountFiles);
        }
    }

    /**
     * @param string $sDirectoryList
     *
     * @return void
     */
    private function clearEmptyDirectories(string $sDirectoryList): void
    {
        $iCountDelete = 0;

        Str::of($sDirectoryList)
            ->explode(',')
            ->each(function ($sDirectory) use (&$iCountDelete) {
                $oStorage = Storage::disk(self::DISK_PUBLIC);
                if ($oStorage->exists($sDirectory)) {
                    $arDirectoryList = $oStorage
                        ->allDirectories($sDirectory);
                    if (count($arDirectoryList)) {
                        foreach ($arDirectoryList as $sDirectoryItem) {
                            $arFileList = $oStorage->allFiles($sDirectoryItem);
                            if (!count($arFileList)) {
                                $oStorage->deleteDirectory($sDirectoryItem);

                                $iCountDelete++;
                            }
                        }
                    }

                    if (!count($oStorage->allFiles($sDirectory))) {
                        $oStorage->deleteDirectory($sDirectory);

                        $iCountDelete++;
                    }
                }
            });

        if ($iCountDelete) {
            $this->info('Delete count empty directories: ' . $iCountDelete);
        }
    }
}
