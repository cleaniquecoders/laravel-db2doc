
[![Build Status](https://travis-ci.org/cleaniquecoders/laravel-db2doc.svg?branch=master)](https://travis-ci.org/cleaniquecoders/laravel-db2doc) [![Latest Stable Version](https://poser.pugx.org/cleaniquecoders/laravel-db2doc/v/stable)](https://packagist.org/packages/cleaniquecoders/laravel-db2doc) [![Total Downloads](https://poser.pugx.org/cleaniquecoders/laravel-db2doc/downloads)](https://packagist.org/packages/cleaniquecoders/laravel-db2doc) [![License](https://poser.pugx.org/cleaniquecoders/laravel-db2doc/license)](https://packagist.org/packages/cleaniquecoders/laravel-db2doc)

## About Your Package

Laravel Db2Doc enable you to generate your database schema to markdown or JSON format. These to make your documentation for database much easier.

## Installation

1. In order to install `cleaniquecoders/laravel-db2doc` in your Laravel project, just run the *composer require* command from your terminal:

```
$ composer require cleaniquecoders/laravel-db2doc
```

2. Then in your `config/app.php` add the following to the providers array:

```php
CleaniqueCoders\LaravelDB2DOC\LaravelDB2DOCServiceProvider::class,
```

3. In the same `config/app.php` add the following to the aliases array:

```php
'LaravelDB2DOC' => CleaniqueCoders\LaravelDB2DOC\LaravelDB2DOCFacade::class,
```

## Usage

Run `php artisan db2doc`, to generate the document in JSON format by default. File generated at `storage/app/db2doc` directory.

You may pass `--database` option to specify which database connection to use. You also have the option `--format` to choose between JSON or Markdown format. By default it's in JSON format.

## Test

To run the test, type `vendor/bin/phpunit` in your terminal.

To have codes coverage, please ensure to install PHP XDebug then run the following command:

```
$ vendor/bin/phpunit -v --coverage-text --colors=never --stderr
```

## Contributing

Thank you for considering contributing to the `cleaniquecoders/laravel-db2doc`!

### Bug Reports

To encourage active collaboration, it is strongly encourages pull requests, not just bug reports. "Bug reports" may also be sent in the form of a pull request containing a failing test.

However, if you file a bug report, your issue should contain a title and a clear description of the issue. You should also include as much relevant information as possible and a code sample that demonstrates the issue. The goal of a bug report is to make it easy for yourself - and others - to replicate the bug and develop a fix.

Remember, bug reports are created in the hope that others with the same problem will be able to collaborate with you on solving it. Do not expect that the bug report will automatically see any activity or that others will jump to fix it. Creating a bug report serves to help yourself and others start on the path of fixing the problem.

## Coding Style

`cleaniquecoders/laravel-db2doc` follows the PSR-2 coding standard and the PSR-4 autoloading standard. 

You may use PHP CS Fixer in order to keep things standardised. PHP CS Fixer configuration can be found in `.php_cs`.

## License

This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).