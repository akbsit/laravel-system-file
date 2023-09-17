<?php namespace Akbsit\SystemFile;

use Akbsit\SystemFile\Models\SystemFile as SystemFileModel;
use Akbsit\SystemFile\Helper\CollectionHelper;
use Akbsit\SystemFile\Helper\MediaHelper;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

/**
 * Class File
 * @package Akbsit\SystemFile
 */
class File extends AbstractFile
{
    /* @inheritDoc */
    public function put(): ?SystemFileModel
    {
        if (empty($this->file) || empty($this->oModel) || !$this->availableDisk()) {
            return null;
        }

        $sFileName = $this->generateFileName();
        if (empty($sFileName)) {
            return null;
        }

        $sUniqid = CollectionHelper::get($this->arFileData, 'uniqid');

        $sSavePath = $this->sDir;
        if ($this->bIsPartition) {
            $sSavePath .= '/' . MediaHelper::getPartitionDirs($sUniqid);
        }

        $oStorage = Storage::disk($this->sDisk);

        $sPath = $oStorage->putFileAs($sSavePath, $this->file, $sFileName);
        if (empty($sPath)) {
            return null;
        }

        $iSort = (int)SystemFileModel::max('sort');
        $iSort++;

        $arParamList = [
            'uniqid'       => $sUniqid,
            'is_partition' => $this->bIsPartition,
            'sort'         => $iSort,
            'model_type'   => get_class($this->oModel),
            'model_id'     => $this->oModel->getAttribute('id'),
            'disk_name'    => $this->sDisk,
            'collection'   => $this->sCollection,
            'dir'          => $this->sDir,
            'mime_type'    => $oStorage->mimeType($sPath),
            'origin_name'  => $this->sOriginFileName,
            'file_name'    => $sFileName,
            'file_size'    => $oStorage->size($sPath),
            'properties'   => $this->arProperties,
        ];

        $oSystemFile = SystemFileModel::create($arParamList);
        if (empty($oSystemFile)) {
            $oStorage->delete($sPath);

            return null;
        }

        $this->syncSingleFile($oSystemFile);

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
     * @param string $sDisk
     *
     * @return File
     */
    public function toDisk(string $sDisk): self
    {
        $this->sDisk = $sDisk;

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

    /* @return File */
    public function single(): self
    {
        $this->bIsSingle = true;

        return $this;
    }
}
