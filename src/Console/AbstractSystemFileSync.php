<?php namespace Falbar\SystemFile\Console;

use Falbar\SystemFile\Helper\ModelHelper;
use Falbar\SystemFile\Models\SystemFile;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Command;

use DusanKasan\Knapsack\Collection;
use Closure;

/**
 * Class AbstractSystemFileSync
 * @package Falbar\SystemFile\Console
 */
abstract class AbstractSystemFileSync extends Command
{
    protected array $arDiskList = [];
    protected array $arDirectoryList = [];

    private int $iDeletedFileCount = 0;
    private int $iDeletedDirectoryCount = 0;

    /* @return void */
    protected function syncSystemFileFromStorage(): void
    {
        $this->initSystemFileFromStorage();

        if (empty($this->arDiskList)) {
            $this->output->newLine();
            $this->error('No disks found');

            return;
        }

        if (empty($this->arDirectoryList)) {
            $this->output->newLine();
            $this->error('No directories found');

            return;
        }

        foreach ($this->arDiskList as $sDisk) {
            $this->walkDiskFromStorage($sDisk);
        }

        if ($this->iDeletedFileCount) {
            $this->output->newLine();
            $this->warn('Deleted files count: ' . $this->iDeletedFileCount);
        }

        if ($this->iDeletedDirectoryCount) {
            if (!$this->iDeletedFileCount) {
                $this->output->newLine();
            }

            $this->warn('Deleted empty directories count: ' . $this->iDeletedDirectoryCount);
        }
    }

    /* @return void */
    private function initSystemFileFromStorage(): void
    {
        $this->arDiskList = ModelHelper::getDistinctFieldList(SystemFile::class, 'disk_name');
        $this->info('Set disks: ' . implode(', ', $this->arDiskList));

        $this->arDirectoryList = ModelHelper::getDistinctFieldList(SystemFile::class, 'dir');
        $this->info('Set directories: ' . implode(', ', $this->arDirectoryList));
    }

    /**
     * @param string $sDisk
     *
     * @return void
     */
    private function walkDiskFromStorage(string $sDisk): void
    {
        $this->output->newLine();
        $this->info('Start walk disk: ' . $sDisk);

        $oStorage = Storage::disk($sDisk);

        Collection::from($this->arDirectoryList)
            ->each($this->walkDirectoryFromStorage($sDisk, $oStorage))
            ->toArray();
    }

    /**
     * @param string            $sDisk
     * @param FilesystemAdapter $oStorage
     *
     * @return Closure
     */
    private function walkDirectoryFromStorage(string $sDisk, FilesystemAdapter $oStorage): Closure
    {
        return function ($sDirectory, $iKey) use ($sDisk, $oStorage) {
            $this->handleFileListFromStorage($sDirectory, $iKey, $sDisk, $oStorage);
            $this->handleEmptyDirectoryListFromStorage($sDirectory, $oStorage);
        };
    }

    /* @return void */
    private function handleFileListFromStorage(): void
    {
        $sDirectory = func_get_arg(0);
        $iKey = func_get_arg(1);
        $sDisk = func_get_arg(2);
        $oStorage = func_get_arg(3);

        if (!$oStorage->exists($sDirectory)) {
            return;
        }

        if ($iKey > 0) {
            $this->output->newLine();
        }

        $this->info('- walk directory: ' . $sDirectory);

        $arFileList = $oStorage->allFiles($sDirectory);
        $iFileListCount = count($arFileList);

        $this->info('-- files count found: ' . $iFileListCount);

        if (!$iFileListCount) {
            return;
        }

        Collection::from($arFileList)
            ->each($this->walkFileListFromStorage($sDisk, $oStorage, $sDirectory))
            ->toArray();
    }

    /* @return void */
    private function handleEmptyDirectoryListFromStorage(): void
    {
        $sDirectory = func_get_arg(0);
        $oStorage = func_get_arg(1);

        if (!$oStorage->exists($sDirectory)) {
            return;
        }

        $arDirectoryList = $oStorage->allDirectories($sDirectory);
        $iDirectoryListCount = count($arDirectoryList);

        $this->output->newLine();
        $this->info('-- directories count found: ' . $iDirectoryListCount);

        if ($iDirectoryListCount) {
            Collection::from($arDirectoryList)
                ->each($this->walkEmptyDirectoryListFromStorage($oStorage))
                ->toArray();
        }

        if (count($oStorage->allFiles($sDirectory))) {
            return;
        }

        $oStorage->deleteDirectory($sDirectory);
        $this->iDeletedDirectoryCount++;
    }

    /**
     * @param string            $sDisk
     * @param FilesystemAdapter $oStorage
     * @param string            $sDirectory
     *
     * @return Closure
     */
    private function walkFileListFromStorage(string $sDisk, FilesystemAdapter $oStorage, string $sDirectory): Closure
    {
        return function ($sFileItem) use ($sDisk, $oStorage, $sDirectory) {
            $this->output->newLine();
            $this->info('--- file: ' . $sFileItem);

            $oSystemFile = SystemFile::getByDiskName($sDisk)
                ->getByDir($sDirectory)
                ->getByFileName(basename($sFileItem));

            $sFullDirectory = dirname($sFileItem);
            if ($sFullDirectory !== $sDirectory) {
                $sPartition = ltrim($sFullDirectory, $sDirectory);
                $sPartition = str_replace('/', '', $sPartition);

                $oSystemFile = $oSystemFile
                    ->getByIsPartition()
                    ->getByPartition($sPartition);
            }

            $oSystemFile = $oSystemFile
                ->first();
            if (!empty($oSystemFile)) {
                $this->info('--- database: id=' . $oSystemFile->id);
                $this->info('--- check: valid');

                return;
            }

            $oStorage->delete($sFileItem);

            $this->warn('--- check: deleted (not found in database)');
            $this->iDeletedFileCount++;
        };
    }

    /**
     * @param FilesystemAdapter $oStorage
     *
     * @return Closure
     */
    private function walkEmptyDirectoryListFromStorage(FilesystemAdapter $oStorage): Closure
    {
        return function ($sDirectoryItem) use ($oStorage) {
            $arFileList = $oStorage->allFiles($sDirectoryItem);
            if (count($arFileList)) {
                return;
            }

            $oStorage->deleteDirectory($sDirectoryItem);

            $this->warn('--- check: deleted (empty ' . $sDirectoryItem . ' directory)');
            $this->iDeletedDirectoryCount++;
        };
    }
}
