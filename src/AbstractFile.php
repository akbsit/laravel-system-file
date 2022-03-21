<?php namespace Falbar\SystemFile;

use Falbar\SystemFile\Models\SystemFile as SystemFileModel;
use Falbar\SystemFile\Helper\CollectionHelper;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use DusanKasan\Knapsack\Collection;
use Exception;

/**
 * Class AbstractFile
 * @package Falbar\SystemFile
 */
abstract class AbstractFile
{
    const DISK_PUBLIC = 'public';

    const PREFIX_TEMP_NAME_LINK = 'system_file_link';

    /* @var UploadedFile|SymfonyFile|string */
    protected $file;

    /* @var Model */
    protected $oModel;

    /* @var string */
    protected $sOriginFileName;

    /* @var string */
    protected $sFileName;

    /* @var array|null */
    protected $arProperties;

    /* @var string */
    protected $sDisk;

    /* @var string */
    protected $sCollection;

    /* @var string */
    protected $sDir;

    /* @var bool */
    protected $bIsPartition;

    /* @var bool */
    protected $bIsSingle;

    /* @var array */
    protected $arFileData = [];

    /* @return SystemFileModel|null */
    abstract protected function put(): ?SystemFileModel;

    /* @return void */
    protected function initFileData(): void
    {
        $this->arFileData['uniqid'] = uniqid(null, true);
        $this->arProperties = null;
        $this->sDisk = self::DISK_PUBLIC;
        $this->sCollection = SystemFileModel::COLLECTION_DEFAULT;
        $this->sDir = SystemFileModel::DIR_DEFAULT;
        $this->bIsPartition = false;
        $this->bIsSingle = false;

        if (is_string($this->file)) {
            $sImageContents = file_get_contents($this->file);
            if (empty($sImageContents)) {
                return;
            }

            $sTempPath = tempnam(sys_get_temp_dir(), self::PREFIX_TEMP_NAME_LINK . '_');
            file_put_contents($sTempPath, $sImageContents);
            $this->file = new UploadedFile(
                $sTempPath,
                pathinfo($this->file, PATHINFO_BASENAME),
                mime_content_type($sTempPath)
            );
        }

        if ($this->file instanceof UploadedFile ||
            $this->file instanceof SymfonyFile) {
            $this->sOriginFileName = $this->getFilename();
            $this->sFileName = $this->getHashName();

            return;
        }

        $this->file = null;
    }

    /* @return string */
    protected function getExtension(): string
    {
        $sResult = '';
        if (empty($this->file)) {
            return $sResult;
        }

        if ($this->file instanceof UploadedFile) {
            $sResult = $this->file->extension();
        } elseif ($this->file instanceof SymfonyFile) {
            $sResult = $this->file->getExtension();
        }

        return $sResult;
    }

    /* @return string */
    protected function generateFileName(): string
    {
        $sFileName = $this->sFileName;
        if (empty($sFileName)) {
            return '';
        }

        $iCounter = 0;
        while (SystemFileModel::getByIsNotPartition()->getByFileName($sFileName)->exists()) {
            $iCounter++;

            $arFileName = explode('.', $sFileName);
            if (count($arFileName) < 2) {
                return '';
            }

            $sFileName = implode('', [
                CollectionHelper::first($arFileName),
                ++$iCounter, '.',
                CollectionHelper::last($arFileName),
            ]);
        }

        return $sFileName;
    }

    /* @return bool */
    protected function availableDisk(): bool
    {
        $sDisk = $this->sDisk;
        if (empty($sDisk)) {
            return false;
        }

        $arDiskList = config('filesystems.disks');
        if (empty($arDiskList) || !is_array($arDiskList)) {
            return false;
        }

        return Collection::from($arDiskList)
            ->filter(function ($arValue) {
                $sDriver = CollectionHelper::get($arValue, 'driver');

                return $sDriver === 'local';
            })
            ->has($sDisk);
    }

    /**
     * @param SystemFileModel $oSystemFile
     *
     * @return void
     */
    protected function syncSingleFile(SystemFileModel $oSystemFile): void
    {
        if (!$this->bIsSingle) {
            return;
        }

        SystemFileModel::getByModel($oSystemFile->model_type, $oSystemFile->model_id)
            ->getByDiskName($oSystemFile->disk_name)
            ->getByCollection($oSystemFile->collection)
            ->getByDir($oSystemFile->dir)
            ->getByExceptID($oSystemFile->id)
            ->get()
            ->each(function ($oSystemFileItem) {
                try {
                    /* @var SystemFileModel $oSystemFileItem */
                    $oSystemFileItem->delete();
                } catch (Exception $oException) {
                    return true;
                }
            });
    }

    /* @return string */
    private function getFilename(): string
    {
        $sResult = '';
        if (empty($this->file)) {
            return $sResult;
        }

        if ($this->file instanceof UploadedFile) {
            $sResult = $this->file->getClientOriginalName();
        } elseif ($this->file instanceof SymfonyFile) {
            $sResult = $this->file->getFilename();
        }

        return $sResult;
    }

    /* @return string */
    private function getHashName(): string
    {
        $sResult = '';
        if (empty($this->file)) {
            return $sResult;
        }

        if ($this->file instanceof UploadedFile) {
            $sResult = $this->file->hashName();
        } elseif ($this->file instanceof SymfonyFile) {
            $sResult = Str::random(40) . '.' . $this->getExtension();
        }

        return Str::lower($sResult);
    }
}
