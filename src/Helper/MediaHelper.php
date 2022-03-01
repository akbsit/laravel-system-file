<?php namespace Falbar\SystemFile\Helper;

use Falbar\SystemFile\Models\SystemFile;

/**
 * Class MediaHelper
 * @package Falbar\SystemFile\Helper
 */
class MediaHelper
{
    /**
     * @param string $sString
     *
     * @return string
     */
    public static function getPartitionDirs(string $sString): string
    {
        return implode('/', array_slice(str_split($sString, 3), 0, 3));
    }

    /**
     * @param SystemFile $oSystemFile
     *
     * @return string
     */
    public static function getStorageFilePath(SystemFile $oSystemFile): string
    {
        $sDirPath = $oSystemFile->dir;
        if ($oSystemFile->is_partition) {
            $sDirPath .= '/' . static::getPartitionDirs($oSystemFile->uniqid);
        }

        return $sDirPath . '/' . $oSystemFile->file_name;
    }
}
