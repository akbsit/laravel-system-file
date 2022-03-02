# laravel-system-file, [Packagist](https://packagist.org/packages/falbar/laravel-system-file)

## Установка

Для установки пакета нужно:

```bash
composer require falbar/laravel-system-file
```

Далее установить миграции:

```bash
php artisan migrate
```

## Подключение

Для подключения пакета к модели, необходимо добавить трейт `InteractsMedia`:

```php
use App\Classes\Service\SystemFile\Traits\InteractsMedia;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use InteractsMedia;
}
```

## Примеры использования

1. Загрузить изображение:

```php
User::first()
    ->addMedia('https://falbar.ru/storage/avatars/user1-afresipiv.png')
    ->setFileName('user1')
    ->put();
```

2. Получить первое изображение:

```php
$oSystemFile = User::first()->getMediaFirst();
```

3. Получить список изображений:

```php
$oSystemFileList = User::first()->getMedia();
```

## Список методов и свойств

* `media` - список файлов прикрепленных к модели;
* `addMedia($file)` - прикрепить файл к модели:
    * `$file` - объект/ссылка загружаемого файла.
* `getMedia(string $sCollection)` - список файлов коллекции (по умолчанию `default`);
* `getMediaFirst(string $sCollection)` - первый элемент коллекции (по умолчанию `default`).

#### media

* `getUrl()` - абсолютный путь файла;
* `getPath()` - путь до файла;
* `getWidthAndHeight()` - получить размеры файла (для картинок);
* `getWidth()` - получить ширину (для картинок);
* `getHeight()` - получить высоту (для картинок).

#### addMedia

* `setFile($file)` - установить объект/ссылку на файл;
* `setModel(Model $oModel)` - установить модель;
* `enablePartition()` - включить генерацию папок (пример: `73c/d53/dce`);
* `setOriginFileName(string $sOriginFileName)` - указать имя файла;
* `setFileName(string $sFileName)` - указать название файла;
* `setProperties(array $arProperties)` - задать свойства для файла;
* `toCollection(string $sCollection)` - указать коллекцию (по умолчанию `default`);
* `toDir(string $sDir)` - указать папку для хранения (по умолчанию `default`);
* `put()` - сохранить файл.

## Список консольных команд

#### Очистка файлов системы

 Название | Тип | Описание
:-------|:-------|:-------
dirs | string | Массив папок для отслеживания, передаются через запятую

```bash
php artisan system-file:clear <dirs>
```
