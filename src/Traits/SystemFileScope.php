<?php namespace Akbsit\SystemFile\Traits;

use Akbsit\SystemFile\Models\SystemFile;

/**
 * Trait SystemFileScope
 * @package Akbsit\SystemFile\Traits
 *
 * @method static $this getByUniqID(string $sUniqID)
 * @method static $this getByFileName(string $sFileName)
 * @method static $this getByDiskName(string $sDiskName)
 * @method static $this getByCollection(string $sCollection)
 * @method static $this getByDir(string $sDir)
 * @method static $this getByExceptID(int $iID)
 * @method static $this getByModel(string $sModelType, int $iModelID)
 * @method static $this getByUniqFile(string $sUniqID, string $sFileName)
 * @method static $this getByPartition(string $sPartition)
 * @method static $this getByIsPartition()
 * @method static $this getByIsNotPartition()
 */
trait SystemFileScope
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder|SystemFile $oQuery
     * @param string                                           $sUniqID
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByUniqID($oQuery, string $sUniqID)
    {
        return $oQuery->where('uniqid', $sUniqID);
    }

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
     * @param string                                           $sUniqID
     * @param string                                           $sFileName
     *
     * @return \Illuminate\Database\Eloquent\Builder|SystemFile
     */
    public function scopeGetByUniqFile($oQuery, string $sUniqID, string $sFileName)
    {
        return $oQuery
            ->getByUniqID($sUniqID)
            ->getByFileName($sFileName);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|SystemFile $oQuery
     * @param string                                           $sPartition
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByPartition($oQuery, string $sPartition)
    {
        return $oQuery->where('uniqid', 'like', $sPartition . '%');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|SystemFile $oQuery
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByIsPartition($oQuery)
    {
        return $oQuery->where('is_partition', true);
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
