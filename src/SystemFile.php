<?php namespace Falbar\SystemFile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

/**
 * Class SystemFile
 * @package Falbar\SystemFile
 */
class SystemFile
{
    /**
     * @param Model                           $oModel
     * @param UploadedFile|SymfonyFile|string $file
     *
     * @return File
     */
    public function create(Model $oModel, $file): File
    {
        /* @var File $oFileContainer */
        $oFileContainer = app(File::class);

        return $oFileContainer
            ->setModel($oModel)
            ->setFile($file);
    }
}
