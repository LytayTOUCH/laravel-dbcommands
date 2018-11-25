# Laravel DbCommands Package

This package provides you with a simple tool to make laravel command to create/drop database without touching directly on database engine.

## Installation

Via Composer

```bash
$ composer require aseancode/dbcommands
```

If you do not run Laravel 5.5 (or higher), then add the service provider in `config/app.php`:

```php
// config/app.php
'providers' => [
    ...
    AseanCode\DbCommands\DbCommandsServiceProvider::class,
    ...
];
```
Publish env files into laravel root project:

```bash
$ php artisan vendor:publish --tag=envfiles
```

Generate key within the project:
```bash
$ php artisan key:generate
```

## Configuration as needed

In order to make some change base on your preference of database connnection, let's check on all envfiles after publishing to root project.