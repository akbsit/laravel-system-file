<?php namespace Falbar\SystemFile\Observers;

use Falbar\SystemFile\Helper\ValidatorHelper;
use Falbar\SystemFile\Helper\MediaHelper;
use Falbar\SystemFile\Models\SystemFile;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

/**
 * Class SystemFileObserver
 * @package Falbar\SystemFile\Observers
 */
class SystemFileObserver
{
    const RULES_SAVING = [
        'uniqid'       => [
            'required',
            'string',
        ],
        'is_partition' => 'required|boolean',
        'sort'         => 'nullable|integer',
        'model_type'   => 'required|string',
        'model_id'     => 'required|integer',
        'disk_name'    => 'required|string',
        'collection'   => 'required|string',
        'dir'          => 'required|string',
        'mime_type'    => 'required|string',
        'origin_name'  => 'required|string',
        'file_name'    => 'required|string',
        'file_size'    => 'required|integer',
        'properties'   => 'nullable|array',
    ];

    /**
     * @param SystemFile $oSystemFile
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function saving(SystemFile $oSystemFile): void
    {
        $arRulesSaving = self::RULES_SAVING;
        $arRulesSaving['uniqid'][] = ValidatorHelper::getUniqueRule($oSystemFile, 'uniqid');

        Validator::make($oSystemFile->toArray(), $arRulesSaving)
            ->validate();
    }

    /**
     * @param SystemFile $oSystemFile
     *
     * @return void
     */
    public function deleted(SystemFile $oSystemFile): void
    {
        Storage::disk($oSystemFile->disk_name)
            ->delete(MediaHelper::getStorageFilePath($oSystemFile));
    }
}
