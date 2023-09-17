<?php namespace Akbsit\SystemFile\Models;

use Akbsit\SystemFile\Traits\SystemFileScope as SystemFileModel;
use Akbsit\SystemFile\Helper\CollectionHelper;
use Akbsit\SystemFile\Helper\MediaHelper;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Class SystemFile
 * @package Akbsit\SystemFile\Models
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 * @mixin \Illuminate\Database\Eloquent\Builder
 *
 * @property int            $id
 * @property string         $uniqid
 * @property bool           $is_partition
 * @property int            $sort
 * @property string         $model_type
 * @property int            $model_id
 * @property string         $disk_name
 * @property string         $collection
 * @property string         $dir
 * @property string         $mime_type
 * @property string         $origin_name
 * @property string         $file_name
 * @property int            $file_size
 * @property string         $properties
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class SystemFile extends Model
{
    use SystemFileModel;

    const COLLECTION_DEFAULT = 'default';
    const DIR_DEFAULT = 'default';

    /* @var string */
    protected $table = 'system_files';

    /* @var array */
    protected $fillable = [
        'uniqid',
        'is_partition',
        'sort',
        'model_type',
        'model_id',
        'disk_name',
        'collection',
        'dir',
        'mime_type',
        'origin_name',
        'file_name',
        'file_size',
        'properties',
    ];

    /* @var array */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /* @var array */
    protected $casts = [
        'properties' => 'array',
    ];

    /* @return string */
    public function getUrl(): string
    {
        return Storage::disk($this->disk_name)
            ->url(MediaHelper::getStorageFilePath($this));
    }

    /* @return string */
    public function getPath(): string
    {
        return Storage::disk($this->disk_name)
            ->path(MediaHelper::getStorageFilePath($this));
    }

    /* @return array */
    public function getWidthAndHeight(): array
    {
        $sPath = $this->getPath();
        if (empty($sPath)) {
            return [];
        }

        [$iWidth, $iHeight] = getimagesize($sPath);

        return [
            'width'  => $iWidth,
            'height' => $iHeight,
        ];
    }

    /* @return int|null */
    public function getWidth(): ?int
    {
        $arData = $this->getWidthAndHeight();
        if (empty($arData)) {
            return null;
        }

        return CollectionHelper::get($arData, 'width');
    }

    /* @return int|null */
    public function getHeight(): ?int
    {
        $arData = $this->getWidthAndHeight();
        if (empty($arData)) {
            return null;
        }

        return CollectionHelper::get($arData, 'height');
    }

    /* @return bool */
    public function fileExists(): bool
    {
        return Storage::disk($this->disk_name)
            ->exists(MediaHelper::getStorageFilePath($this));
    }

    /**
     * @param string $sValue
     *
     * @return void
     */
    protected function setUniqidAttribute(string $sValue): void
    {
        if (empty($this->uniqid)) {
            $this->attributes['uniqid'] = $sValue;
        }
    }
}
