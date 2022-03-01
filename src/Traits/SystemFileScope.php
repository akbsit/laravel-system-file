<?php namespace Falbar\SystemFile\Traits;

use Falbar\SystemFile\Models\SystemFile;

/**
 * Trait SystemFileScope
 * @package Falbar\SystemFile\Traits
 *
 * @method static $this getByUniqID(string $sUniqID)
 * @method static $this getByFileName(string $sFileName)
 * @method static $this getByUniqFile(string $sUniqID, string $sFileName)
 */
trait SystemFileScope
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $oQuery
     * @param string                                $sUniqID
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByUniqID($oQuery, string $sUniqID)
    {
        return $oQuery->where('uniqid', $sUniqID);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $oQuery
     * @param string                                $sFileName
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByFileName($oQuery, string $sFileName)
    {
        return $oQuery->where('file_name', $sFileName);
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
        return $oQuery->getByUniqID($sUniqID)->getByFileName($sFileName);
    }
}
