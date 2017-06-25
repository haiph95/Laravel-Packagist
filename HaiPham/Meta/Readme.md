# MetaData for Laravel Eloquent


Add this `code` to config/app.php

```php
'providers' => [
    App\HaiPham\Meta\MetaServiceProvider::class
]
```

If you are using Laravel, run the migration `php artisan migrate` to create the database table.


## Usage
Add the trait to all models that you want to attach meta data to:

```php
use Illuminate\Database\Eloquent\Model;
use App\HaiPham\Meta\MetaTrait;

class Products extends Model
{
    use MetaTrait;

    // model methods
}
```

Then use like this:

```php
$model = Products::find(1);
$model->getAllMeta();
$model->getMeta('some_key', 'optional default value'); // default value only returned if no meta found.
$model->updateMeta('some_key', 'New Value');
$model->deleteMeta('some_key');
$model->deleteAllMeta();
$model->addMeta('new_key', ['First Value']);
$model->appendMeta('new_key', 'Second Value');
```

### Thank you and Credits
Contributors
  - Michael Wilson - @[chrismichaels84](http://github.com/chrismichaels84) - Maintainer
  - Paweł Ciesielski - @[dzafel](http://github.com/dzafel)
  - Lukas Knuth - @[LukasKnuth](http://github.com/LukasKnuth)
 
Many thanks to [Boris Glumpler](https://github.com/shabushabu) and [ScubaClick](https://github.com/ScubaClick) for the original package!

