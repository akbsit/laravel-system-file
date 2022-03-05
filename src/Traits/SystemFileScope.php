<?php namespace Falbar\SystemFile\Traits;

use Falbar\SystemFile\Models\SystemFile;

/**
 * Trait SystemFileScope
 * @package Falbar\SystemFile\Traits
 *
 * @method static $this getByFileName(string $sFileName)
 * @method static $this getByDiskName(string $sDiskName)
 * @method static $this getByCollection(string $sCollection)
 * @method static $this getByDir(string $sDir)
 * @method static $this getByExceptID(int $iID)
 * @method static $this getByModel(string $sModelType, int $iModelID)
 * @method static $this getByIsNotPartition()
 */
trait SystemFileScope
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder|SystemFile $oQuery
     * @param string                                           $sFileName
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByFileName($oQuery, string $sFileName)
    {
        return $oQuery->where('file_name', $sFileName);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|SystemFile $oQuery
     * @param string                                           $sDiskName
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByDiskName($oQuery, string $sDiskName)
    {
        return $oQuery->where('disk_name', $sDiskName);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|SystemFile $oQuery
     * @param string                                           $sCollection
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByCollection($oQuery, string $sCollection)
    {
        return $oQuery->where('collection', $sCollection);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|SystemFile $oQuery
     * @param string                                           $sDir
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByDir($oQuery, string $sDir)
    {
        return $oQuery->where('dir', $sDir);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|SystemFile $oQuery
     * @param int                                              $iID
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByExceptID($oQuery, int $iID)
    {
        return $oQuery->where('id', '!=', $iID);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|SystemFile $oQuery
     * @param string                                           $sModelType
     * @param int                                              $iModelID
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByModel($oQuery, string $sModelType, int $iModelID)
    {
        return $oQuery
            ->where('model_type', $sModelType)
            ->where('model_id', $iModelID);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|SystemFile $oQuery
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByIsNotPartition($oQuery)
    {
        return $oQuery->where('is_partition', false);
    }
}
