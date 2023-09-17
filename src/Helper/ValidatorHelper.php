<?php namespace Akbsit\SystemFile\Helper;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ValidatorHelper
 * @package Akbsit\SystemFile\Helper
 */
class ValidatorHelper
{
    /**
     * @param Model       $oModel
     * @param string|null $sField
     *
     * @return string
     */
    public static function getUniqueRule($oModel, ?string $sField = null): string
    {
        $sResult = '';
        if (empty($oModel) || !$oModel instanceof Model) {
            return $sResult;
        }

        $sResult = 'unique:' . $oModel->getTable();
        if (!empty($sField) && $oModel->id) {
            $sResult .= ',' . $sField . ',' . $oModel->id;
        }

        return $sResult;
    }
}
