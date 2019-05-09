# Laravel Internationalization

A lightweight and easy to use internationalization library to enhance your Laravel application.

## Installation

Use composer to install and use this package in your project.

Install them with

```bash
composer require "dmx/laravel-intl"
```

and you are ready to go!

## Usage

You can use the the provided classes directly for your own purpose.

### Locale

```php
$myLocale = new DMX\Application\Intl\Locale('en', 'GB', 'utf8');

echo $myLocale->toISO15897String();
echo $myLocale->toIETFLanguageTag();
```

This will generate the following output:
```
en_GB.utf8
en-GB
```

## Development - Getting Started

See the [CONTRIBUTING](CONTRIBUTING.md) file.

## Changelog

See the [CHANGELOG](CHANGELOG.md) file.

## License
 
See the [LICENSE](LICENSE.md) file.
