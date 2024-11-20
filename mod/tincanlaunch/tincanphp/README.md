A PHP library for implementing the Experience API (Tin Can API).

[![Build Status](https://travis-ci.org/RusticiSoftware/TinCanPHP.png)](https://travis-ci.org/RusticiSoftware/TinCanPHP)
[![Latest Stable Version](https://poser.pugx.org/rusticisoftware/tincan/v/stable)](https://packagist.org/packages/rusticisoftware/tincan)
[![License](https://poser.pugx.org/rusticisoftware/tincan/license)](https://packagist.org/packages/rusticisoftware/tincan)
[![Total Downloads](https://poser.pugx.org/rusticisoftware/tincan/downloads)](https://packagist.org/packages/rusticisoftware/tincan)

For hosted API documentation, basic usage instructions, supported version listing, etc. visit the main project website at:

http://rusticisoftware.github.io/TinCanPHP/

For more information about the Experience API visit:

http://experienceapi.com/

Requires PHP 5.5 or later. (If you must run something older you should look at the 0.x release series or the PHP_5_2 branch.)

### Installation

TinCanPHP is available via [Composer](http://getcomposer.org).

```
php composer.phar require rusticisoftware/tincan:@stable
```

When not using Composer, require the autoloader:

```php
require 'path/to/TinCan/autoload.php';
```

### Testing

Tests are implemented using the latest stable version of PHPUnit. It will be installed when using Composer. Configure the LRS endpoint and credentials by copying the `tests/config/config.dist.php` to `tests/config/config.php` then setting the values for your LRS.

Once configured run:

```
vendor/bin/phpunit
```

### API Doc Generation

Documentation can be output using [phpDocumentor2](http://phpdoc.org). It will be installed when using Composer. To generate documentation:

```
vendor/bin/phpdoc
```

From the root of the repository after running `php composer.phar update`. Documentation will be output to `doc/api`.

If you do not have the default timezone set in your `php.ini` file you can create one in the base of the repo and use the `PHPRC` environment variable to point to it. Use something like:

```
export PHPRC="/path/to/repos/TinCanPHP/php.ini"
```

And set the timezone in that file using:

```
[PHP]

date.timezone = "US/Central"
```

### Certificate Generation

These instructions are for creating the requisite public/private key pair and certificate on a Mac with OpenSSL installed. See <https://www.openssl.org/docs/HOWTO/certificates.txt> and <https://www.openssl.org/docs/HOWTO/keys.txt>.

Generate a private key (which contains a public key) without a password (not recommended):

    openssl genrsa -out privkey.pem 2048

To generate a private key with a password:

    openssl genrsa -des3 -out privkey.pem 2048

Create a certificate signing request:

     openssl req -new -key privkey.pem -out cert.csr

To create a self signed certificate (as opposed to one signed by a CA), primarily for testing purposes:

    openssl req -new -x509 -key privkey.pem -out cacert.pem -days 1095
