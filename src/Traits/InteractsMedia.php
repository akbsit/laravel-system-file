<?php namespace Falbar\SystemFile\Traits;

use Falbar\SystemFile\Models\SystemFile;
use Falbar\SystemFile as Media;

use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Illuminate\Http\UploadedFile;

/**
 * Trait InteractsMedia
 * @package Falbar\SystemFile\Traits
 *
 * @property \Illuminate\Database\Eloquent\Collection|SystemFile $media
 */
trait InteractsMedia
{
    /* @return \Illuminate\Database\Eloquent\Relations\MorphMany */
    public function media()
    {
        return $this->morphMany(SystemFile::class, 'model');
    }

    /**
     * @param UploadedFile|SymfonyFile|string $file
     *
     * @return \Falbar\SystemFile\File
     */
    public function addMedia($file)
    {
        /* @var \Illuminate\Database\Eloquent\Model $this */
        return app(Media::class)->create($this, $file);
    }

    /**
     * @param string $sCollection
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMedia(string $sCollection = SystemFile::COLLECTION_DEFAULT)
    {
        return $this->media()
            ->where('collection', $sCollection)
            ->get();
    }

    /**
     * @param string $sCollection
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getMediaFirst(string $sCollection = SystemFile::COLLECTION_DEFAULT)
    {
        return $this->media()
            ->where('collection', $sCollection)
            ->first();
    }
}
