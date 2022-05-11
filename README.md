# laravel-system-file, [Packagist](https://packagist.org/packages/falbar/laravel-system-file)

## Install

To install package, you need run command:

```bash
composer require falbar/laravel-system-file
```

Next install migrations:

```bash
php artisan migrate
```

## Usage

To connect package to the model, you need to add a trait `InteractsMedia`:

```php
use Falbar\SystemFile\Traits\InteractsMedia;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use InteractsMedia;
}
```

## Examples

1. Upload image:

```php
User::first()
    ->addMedia('https://falbar.ru/storage/avatars/user1-afresipiv.png')
    ->setFileName('user1')
    ->put();
```

2. Attach one file to the model (the old one will be overwritten when re-uploading):

```php
User::first()
    ->addMedia('https://falbar.ru/storage/avatars/user1-afresipiv.png')
    ->setFileName('user1')
    ->single()
    ->put();
```

3. Get first image:

```php
$oSystemFile = User::first()->getMediaFirst();
```

4. Get images list:

```php
$oSystemFileList = User::first()->getMedia();
```

## Methods and properties

* `media` - list of files attached to the model;
* `addMedia($file)` - attach file to the model:
    * `$file` - object/link of the uploaded file.
* `mediaExists(string $sCollection)` - check for attached files (by default `default`);
* `getMedia(string $sCollection)` - list of collection files (by default `default`);
* `getMediaFirst(string $sCollection)` - first element of the collection (by default `default`).

#### media

* `getUrl()` - absolute file path;
* `getPath()` - path to the file;
* `getWidthAndHeight()` - get file sizes (for images);
* `getWidth()` - get width (for images);
* `getHeight()` - get height (for pictures);
* `fileExists()` - check for the presence of the file physically.

#### addMedia

* `setFile($file)` - set object/link to a file;
* `setModel(Model $oModel)` - set model;
* `enablePartition()` - enable folder generation (example: `73c/d53/dce`);
* `single()` - add one file to the model (all others are deleted if they were previously attached);
* `setOriginFileName(string $sOriginFileName)` - set origin file name;
* `setFileName(string $sFileName)` - set file name;
* `setProperties(array $arProperties)` - set file properties;
* `toDisk(string $sDisk)` - set storage disk (by default `public`);
* `toCollection(string $sCollection)` - set collection (by default `default`);
* `toDir(string $sDir)` - set storage folder (by default `default`);
* `put()` - save file.

## Console commands

#### File synchronization

```bash
php artisan system-file:sync
```
