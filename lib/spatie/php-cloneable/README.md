# A trait that allows you to clone readonly properties in PHP 8.1

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/php-cloneable.svg?style=flat-square)](https://packagist.org/packages/spatie/php-cloneable)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/spatie/php-cloneable/run-tests.yml?label=tests&branch=main)](https://github.com/spatie/php-cloneable/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/spatie/php-cloneable/php-cs-fixer.yml?label=code%20style&branch=main)](https://github.com/spatie/php-cloneable/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/php-cloneable.svg?style=flat-square)](https://packagist.org/packages/spatie/php-cloneable)

This package provides a trait that allows you to clone objects with readonly properties in PHP 8.1. You can read an in-depth explanation as to why this is necessary [here](https://stitcher.io/blog/cloning-readonly-properties-in-php-81).

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/php-cloneable.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/php-cloneable)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/php-cloneable
```

## Usage

In PHP 8.1, readonly properties aren't allowed to be overridden as soon as they are initialized. That also means that cloning an object and changing one of its readonly properties isn't allowed. It's likely that PHP will get some kind of `clone with` functionality in the future, but for now you can work around this issue by using this package.

```php
class Post
{
    use Cloneable;

    public readonly string $title;
    
    public readonly string $author;

    public function __construct(string $title, string $author)
    {
        $this->title = $title;
        $this->author = $author;
    }
}
```

The `Spatie\Cloneable\Cloneable` adds a `with` method to whatever class you want to be cloneable, which you can pass one or more parameters. Note that you're required to use named arguments.

```php
$postA = new Post(title: 'a', author: 'Brent');

$postB = $postA->with(title: 'b');
$postC = $postA->with(title: 'c', author: 'Freek');
```

A common practice would be to implement specific `with*` methods on the classes themselves:

```php
class Post
{
    // …

    public function withTitle(string $title): self
    {
        return $this->with(title: $title);
    }

    public function withAuthor(string $author): self
    {
        return $this->with(author: $author);
    }
}
```

### Caveats

- This package will skip calling the constructor when cloning an object, meaning any logic in the constructor won't be executed.
- The `with` method will do a shallow clone, meaning that nested objects aren't cloned as well.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Brent Roose](https://github.com/brendt_gd)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
