<?php namespace Falbar\SystemFile\Helper;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ModelHelper
 * @package Falbar\SystemFile\Helper
 */
class ModelHelper
{
    /**
     * @param string $sClassName
     * @param string $sField
     *
     * @return array
     */
    public static function getDistinctFieldList(string $sClassName, string $sField): array
    {
        if (empty($sClassName) || !class_exists($sClassName) || empty($sField)) {
            return [];
        }

        /* @var Model $oModel */
        $oModel = app($sClassName);
        if (!in_array($sField, $oModel->getFillable())) {
            return [];
        }

        return $oModel::select($sField)
            ->distinct()
            ->get()
            ->map(function ($oModelItem) use ($sField) {
                return $oModelItem->$sField;
            })
            ->toArray();
    }
}
