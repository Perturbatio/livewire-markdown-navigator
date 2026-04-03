# Render a directory of markdown files in a livewire component

[![Latest Version on Packagist](https://img.shields.io/packagist/v/perturbatio/livewire-markdown-navigator.svg?style=flat-square)](https://packagist.org/packages/perturbatio/livewire-markdown-navigator)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/perturbatio/livewire-markdown-navigator/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/perturbatio/livewire-markdown-navigator/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/perturbatio/livewire-markdown-navigator/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/perturbatio/livewire-markdown-navigator/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/perturbatio/livewire-markdown-navigator.svg?style=flat-square)](https://packagist.org/packages/perturbatio/livewire-markdown-navigator)

A livewire component that allows you to view markdown documentation stored in a given disk and path.

## Support Spatie's open source work

This package was NOT created by Spatie, but it was created using Spatie's [spatie/laravel-package-tools](https://github.com/spatie/laravel-package-tools),
without them, this package probably would have been possible, but I'm not sure I could have been as motivated to make it.
But I have used several of their other excellent packages.

They invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support them by [buying one of our paid products](https://spatie.be/open-source/support-us).

They highly appreciate you sending them a postcard from your hometown, mentioning which of their package(s) you are using (and maybe this one too). 
You'll find their address on [their contact page](https://spatie.be/about-us). They publish all received postcards on [their virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require perturbatio/livewire-markdown-navigator
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="livewire-markdown-navigator-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="livewire-markdown-navigator-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="livewire-markdown-navigator-views"
```

## Usage

```php
$livewireMarkdownNavigator = new Perturbatio\\LivewireMarkdownNavigator\LivewireMarkdownNavigator();
echo $livewireMarkdownNavigator->echoPhrase('Hello, Perturbatio\\LivewireMarkdownNavigator!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- _[Kris Kelly_](https://github.com/Perturbatio)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
