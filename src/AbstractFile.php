<?php namespace Falbar\SystemFile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Falbar\SystemFile\Models\SystemFile as SystemFileModel;

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
    protected $sCollection;

    /* @var string */
    protected $sDir;

    /* @var bool */
    protected $bIsPartition;

    /* @var array */
    protected $arFileData = [];

    /* @return void */
    protected function initFileData(): void
    {
        Arr::set($this->arFileData, 'disk', self::DISK_PUBLIC);
        Arr::set($this->arFileData, 'uniqid', uniqid(null, true));
        $this->arProperties = null;
        $this->sCollection = SystemFileModel::COLLECTION_DEFAULT;
        $this->sDir = SystemFileModel::DIR_DEFAULT;
        $this->bIsPartition = false;

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
