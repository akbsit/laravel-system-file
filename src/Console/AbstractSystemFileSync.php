<?php namespace Akbsit\SystemFile\Console;

use Akbsit\SystemFile\Helper\ModelHelper;
use Akbsit\SystemFile\Models\SystemFile;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Command;

use DusanKasan\Knapsack\Collection;
use Closure;

/**
 * Class AbstractSystemFileSync
 * @package Akbsit\SystemFile\Console
 */
abstract class AbstractSystemFileSync extends Command
{
    protected array $arDiskList = [];
    protected array $arDirectoryList = [];

    protected int $iFileStorageCount = 0;
    protected int $iFileDataBaseCount = 0;

    private int $iDeletedFileStorageCount = 0;
    private int $iDeletedDirectoryStorageCount = 0;
    private int $iDeletedFileDataBaseCount = 0;

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

        if ($this->iDeletedFileStorageCount) {
            $this->output->newLine();
            $this->warn('Deleted files count: ' . $this->iDeletedFileStorageCount);
        }

        if ($this->iDeletedDirectoryStorageCount) {
            if (!$this->iDeletedFileStorageCount) {
                $this->output->newLine();
            }

            $this->warn('Deleted empty directories count: ' . $this->iDeletedDirectoryStorageCount);
        }
    }

    /* @return void */
    protected function syncSystemFileFromDataBase(): void
    {
        $this->output->newLine();
        $this->info('Start walk database:');

        $this->iFileDataBaseCount = SystemFile::count();
        $this->info('- item count: ' . $this->iFileDataBaseCount);

        if ($this->iFileStorageCount === $this->iFileDataBaseCount) {
            return;
        }

        SystemFile::all()
            ->each($this->walkFileListFromDataBase());

        if ($this->iDeletedFileDataBaseCount) {
            $this->output->newLine();
            $this->warn('Deleted files count: ' . $this->iDeletedFileDataBaseCount);
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
        return function (string $sDirectory, int $iKey) use ($sDisk, $oStorage) {
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
        $this->iDeletedDirectoryStorageCount++;
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
        return function (string $sFileItem) use ($sDisk, $oStorage, $sDirectory) {
            $oSystemFile = SystemFile::getByDiskName($sDisk)
                ->getByDir($sDirectory)
                ->getByFileName(basename($sFileItem));

            $sFullDirectory = dirname($sFileItem);
            if ($sFullDirectory === $sDirectory) {
                $oSystemFile = $oSystemFile
                    ->getByIsNotPartition();
            } else {
                $sPartition = ltrim($sFullDirectory, $sDirectory);
                $sPartition = str_replace('/', '', $sPartition);

                $oSystemFile = $oSystemFile
                    ->getByIsPartition()
                    ->getByPartition($sPartition);
            }

            $oSystemFile = $oSystemFile
                ->first();
            if (!empty($oSystemFile)) {
                $this->iFileStorageCount++;

                return;
            }

            $oStorage->delete($sFileItem);

            $this->warn('--- check: deleted (file ' . $sFileItem . ' not exists in database)');
            $this->iDeletedFileStorageCount++;
        };
    }

    /**
     * @param FilesystemAdapter $oStorage
     *
     * @return Closure
     */
    private function walkEmptyDirectoryListFromStorage(FilesystemAdapter $oStorage): Closure
    {
        return function (string $sDirectoryItem) use ($oStorage) {
            $arFileList = $oStorage->allFiles($sDirectoryItem);
            if (count($arFileList)) {
                return;
            }

            $oStorage->deleteDirectory($sDirectoryItem);

            $this->warn('--- check: deleted (empty ' . $sDirectoryItem . ' directory)');
            $this->iDeletedDirectoryStorageCount++;
        };
    }

    /* @return Closure */
    private function walkFileListFromDataBase(): Closure
    {
        return function (SystemFile $oSystemFile) {
            if ($oSystemFile->fileExists()) {
                return true;
            }

            $oSystemFile->delete();

            $this->warn('-- check: deleted (id=' . $oSystemFile->id . ' not exists file in storage)');
            $this->iDeletedFileDataBaseCount++;
        };
    }
}
