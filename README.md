# Laravel Query Logger
A simple service provider to log all database queries into a file for Laravel framework

This package supports Laravel 5.0 and newer versions.

You can also use this package with Lumen. 

## Installation

Import package using composer:

```shell
composer require ngoctp/laravel-query-logger --dev
```

### Add service to Laravel >=5.5

There's nothing to do with Laravel 5.5, it's automatically imported by extra field defined in composer.json

### Add service to Laravel <5.5

Put ServiceProvider to providers list in `config/app.php` file. 

It's the best to place before AppServiceProvider to log all queries from beginning.

```php
NgocTP\QueryLogger\ServiceProvider::class,
```

Publish configuration file using command:

```shell
php artisan vendor:publish --provider="NgocTP\QueryLogger\ServiceProvider"
```

### Add service to Lumen

If you're using Lumen, add below line to `bootstrap/app.php` file

```
$app->register(NgocTP\QueryLogger\ServiceProvider::class);
```


## Display queries

After installed successfully, you can open terminal and use `tail` command to display queries realtime to console

```
tail -f storage/logs/query_logger.log
```

That's all, thank you for using

Happy coding :)
