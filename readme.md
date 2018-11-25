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
Publish env files into laravel root project and override existing env file for MySQL:

```bash
$ php artisan vendor:publish --tag=envmysql --force
```

Publish env files into laravel root project and override existing env file for SQLite:

```bash
$ php artisan vendor:publish --tag=envsqlite --force
```

Generate key within the project:
```bash
$ php artisan key:generate
```

Usage command to create database for all environments:

```bash
$ php artisan db:create --all
```

Usage command to drop database for all environments:

```bash
$ php artisan db:drop --all
```

## Configuration as needed

In order to make some change base on your preference of database connnection, let's check on all envfiles after publishing to root project.

## Be Aware

Every env files has these configuration code.

```
CHARSET="utf8mb4"
COLLATION="utf8mb4_unicode_ci"
DB_ENGINE="InnoDB ROW_FORMAT=DYNAMIC"

```
In config/database.php, change this line:

```php
'charset' => 'utf8mb4',
'collation' => 'utf8mb4_unicode_ci',
'engine' => null,
```
To
```php
'charset' => env('CHARSET', 'utf8mb4'),
'collation' => env('COLLATION', 'utf8mb4_unicode_ci'),
'engine' => env('DB_ENGINE', 'InnoDB ROW_FORMAT=DYNAMIC'),
```