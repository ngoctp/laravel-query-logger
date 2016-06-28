# Laravel Query Logger
A simple service provider to log all database queries into a file for Laravel framework
This package is compatible with Laravel >5.2

## Installation

Require this package with composer:

```
composer require ngoctp/laravel-query-logger
```

Then add ServiceProvider to the providers array in config/app.php
This should be placed before your AppServiceProvider

```
NgocTP\QuerryLogger\ServiceProvider::class,
```

Copy the package config by publish command:

```
php artisan vendor:publish --provider="NgocTP\QuerryLogger\ServiceProvider"
```
