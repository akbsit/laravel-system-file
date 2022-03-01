<?php namespace Falbar\SystemFile;

use Falbar\SystemFile\Models\SystemFile as SystemFileModel;
use Falbar\SystemFile\Helper\MediaHelper;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use League\Flysystem\FileNotFoundException;

/**
 * Class File
 * @package Falbar\SystemFile
 */
class File extends AbstractFile
{
    /* @return SystemFileModel|null */
    public function put(): ?SystemFileModel
    {
        $oResult = null;
        if (empty($this->file) || empty($this->oModel)) {
            return $oResult;
        }

        $sUniqid = Arr::get($this->arFileData, 'uniqid');
        $sDisk = Arr::get($this->arFileData, 'disk');

        $sSavePath = $this->sDir;
        if ($this->bIsPartition) {
            $sSavePath .= '/' . MediaHelper::getPartitionDirs($sUniqid);
        }

        $oStorage = Storage::disk($sDisk);

        $sPath = $oStorage->putFileAs($sSavePath, $this->file, $this->sFileName);
        if (empty($sPath)) {
            return $oResult;
        }

        $iSort = (int)SystemFileModel::max('sort');
        $iSort++;

        try {
            $sMimeType = $oStorage->getMimetype($sPath);
            $iSize = $oStorage->getSize($sPath);
        } catch (FileNotFoundException $oException) {
            $oStorage->delete($sPath);
            return $oResult;
        }

        /* @var SystemFileModel $oSystemFile */
        $oSystemFile = SystemFileModel::create([
            'uniqid'       => $sUniqid,
            'is_partition' => $this->bIsPartition,
            'sort'         => $iSort,
            'model_type'   => get_class($this->oModel),
            'model_id'     => $this->oModel->getAttribute('id'),
            'disk_name'    => $sDisk,
            'collection'   => $this->sCollection,
            'dir'          => $this->sDir,
            'mime_type'    => $sMimeType,
            'origin_name'  => $this->sOriginFileName,
            'file_name'    => $this->sFileName,
            'file_size'    => $iSize,
            'properties'   => $this->arProperties,
        ]);
        if (empty($oSystemFile)) {
            $oStorage->delete($sPath);
            return $oResult;
        }

        return $oSystemFile;
    }

    /**
     * @param UploadedFile|SymfonyFile|string $file
     *
     * @return File
     */
    public function setFile($file): self
    {
        $this->file = $file;

        $this->initFileData();

        return $this;
    }

    /**
     * @param Model $oModel
     *
     * @return File
     */
    public function setModel(Model $oModel): self
    {
        $this->oModel = $oModel;

        return $this;
    }

    /**
     * @param string $sOriginFileName
     *
     * @return File
     */
    public function setOriginFileName(string $sOriginFileName): self
    {
        if (empty($this->file) || empty($sOriginFileName)) {
            return $this;
        }

        $this->sOriginFileName = $sOriginFileName . '.' . $this->getExtension();

        return $this;
    }

    /**
     * @param string $sFileName
     *
     * @return File
     */
    public function setFileName(string $sFileName): self
    {
        if (empty($this->file) || empty($sFileName)) {
            return $this;
        }

        $this->sFileName = $sFileName . '.' . $this->getExtension();

        return $this;
    }

    /**
     * @param array|null $arProperties
     *
     * @return File
     */
    public function setProperties(?array $arProperties): self
    {
        $this->arProperties = $arProperties;

        return $this;
    }

    /**
     * @param string $sCollection
     *
     * @return File
     */
    public function toCollection(string $sCollection): self
    {
        $this->sCollection = $sCollection;

        return $this;
    }

    /**
     * @param string $sDir
     *
     * @return File
     */
    public function toDir(string $sDir): self
    {
        $this->sDir = $sDir;

        return $this;
    }

    /* @return File */
    public function enablePartition(): self
    {
        $this->bIsPartition = true;

        return $this;
    }
}
