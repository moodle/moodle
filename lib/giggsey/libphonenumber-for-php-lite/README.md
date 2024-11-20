# libphonenumber for PHP (Lite) [![Build Status](https://github.com/giggsey/libphonenumber-for-php-lite/workflows/Tests/badge.svg)](https://github.com/giggsey/libphonenumber-for-php-lite/actions?query=workflow%3A%22Tests%22) [![codecov](https://codecov.io/gh/giggsey/libphonenumber-for-php-lite/branch/main/graph/badge.svg?token=S0TV2TAXOQ)](https://codecov.io/gh/giggsey/libphonenumber-for-php-lite) [![Mutation testing badge](https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fgiggsey%2Flibphonenumber-for-php-lite%2Fmain)](https://dashboard.stryker-mutator.io/reports/github.com/giggsey/libphonenumber-for-php-lite/main)


[![Total Downloads](https://poser.pugx.org/giggsey/libphonenumber-for-php-lite/downloads?format=flat-square)](https://packagist.org/packages/giggsey/libphonenumber-for-php-lite)
[![Downloads per month](https://img.shields.io/packagist/dm/giggsey/libphonenumber-for-php-lite.svg?style=flat-square)](https://packagist.org/packages/giggsey/libphonenumber-for-php-lite)
[![Latest Stable Version](https://img.shields.io/packagist/v/giggsey/libphonenumber-for-php-lite.svg?style=flat-square)](https://packagist.org/packages/giggsey/libphonenumber-for-php-lite)
[![License](https://img.shields.io/badge/license-Apache%202.0-red.svg?style=flat-square)](https://packagist.org/packages/giggsey/libphonenumber-for-php-lite)

## What is it?
A PHP library for parsing, formatting, storing and validating international phone numbers. This library is based on Google's [libphonenumber](https://github.com/google/libphonenumber).

This is a lite version that only includes the core Phone Number Utils. To make full use of the library, including geolocation, carrier information and short number info, use [giggsey/libphonenumber-for-php](https://github.com/giggsey/libphonenumber-for-php)

 - [Installation](#installation)
 - [Documentation](#documentation)
 - [Highlights of functionality](#highlights-of-functionality)
   - [Versioning](#versioning)
   - [Quick Examples](#quick-examples)
 - [FAQ](#faq)
   - [Problems with Invalid Numbers?](#problems-with-invalid-numbers)
 - [Generating data](#generating-data)

## Installation

PHP versions 8.0 and above are supported.

The PECL [mbstring](http://php.net/mbstring) extension is required.

It is recommended to use [composer](https://getcomposer.org) to install the library.

```bash
$ composer require giggsey/libphonenumber-for-php-lite
```

You can also use any other [PSR-4](http://www.php-fig.org/psr/psr-4/) compliant autoloader.

### PHP Version Policy

This library will be updated to use [supported versions of PHP](https://www.php.net/supported-versions.php) only. At the moment, the main [giggsey/libphonenumber-for-php](https://github.com/giggsey/libphonenumber-for-php) library supports older PHP versions.

## Documentation

 - [PhoneNumber Util](docs/PhoneNumberUtil.md)

# Highlights of functionality
* Parsing/formatting/validating phone numbers for all countries/regions of the world.
* `getNumberType` - gets the type of the number based on the number itself; able to distinguish Fixed-line, Mobile, Toll-free, Premium Rate, Shared Cost, VoIP and Personal Numbers (whenever feasible).
* `isNumberMatch` - gets a confidence level on whether two numbers could be the same.
* `getExampleNumber`/`getExampleNumberByType` - provides valid example numbers for all countries/regions, with the option of specifying which type of example phone number is needed.
* `isValidNumber` - full validation of a phone number for a region using length and prefix information.

## Versioning

This library will try to follow the same version numbers as Google. There could be additional releases where needed to fix critical issues that can not wait until the next release from Google.

This does mean that this project may not follow [Semantic Versioning](http://semver.org/), but instead Google's version policy. As a result, jumps in major versions may not actually contain any backwards
incompatible changes. Please read the release notes for such releases.

Google try to release their versions according to Semantic Versioning, as laid out of in their [Versioning Guide](https://github.com/google/libphonenumber#versioning-and-announcements).

## Quick Examples
Let's say you have a string representing a phone number from Switzerland. This is how you parse/normalize it into a PhoneNumber object:

```php
$swissNumberStr = "044 668 18 00";
$phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
try {
    $swissNumberProto = $phoneUtil->parse($swissNumberStr, "CH");
    var_dump($swissNumberProto);
} catch (\libphonenumber\NumberParseException $e) {
    var_dump($e);
}
```

At this point, swissNumberProto contains:

    class libphonenumber\PhoneNumber#9 (7) {
     private $countryCode =>
      int(41)
     private $nationalNumber =>
      double(446681800)
     private $extension =>
      NULL
     private $italianLeadingZero =>
      NULL
     private $rawInput =>
      NULL
     private $countryCodeSource =>
      NULL
     private $preferredDomesticCarrierCode =>
      NULL
    }

Now let us validate whether the number is valid:

```php
$isValid = $phoneUtil->isValidNumber($swissNumberProto);
var_dump($isValid); // true
```

There are a few formats supported by the formatting method, as illustrated below:

```php
// Produces "+41446681800"
echo $phoneUtil->format($swissNumberProto, \libphonenumber\PhoneNumberFormat::E164);

// Produces "044 668 18 00"
echo $phoneUtil->format($swissNumberProto, \libphonenumber\PhoneNumberFormat::NATIONAL);

// Produces "+41 44 668 18 00"
echo $phoneUtil->format($swissNumberProto, \libphonenumber\PhoneNumberFormat::INTERNATIONAL);
```

You could also choose to format the number in the way it is dialled from another country:

```php
// Produces "011 41 44 668 1800", the number when it is dialled in the United States.
echo $phoneUtil->formatOutOfCountryCallingNumber($swissNumberProto, "US");

// Produces "00 41 44 668 18 00", the number when it is dialled in Great Britain.
echo $phoneUtil->formatOutOfCountryCallingNumber($swissNumberProto, "GB");
```

## FAQ

#### Problems with Invalid Numbers?

This library uses phone number metadata from Google's [libphonenumber](https://github.com/google/libphonenumber). If this library is working as intended, it should provide the same result as the Java version of Google's project.

If you believe that a phone number is returning an incorrect result, first test it with [libphonenumber](https://github.com/google/libphonenumber) via their [Online Demo](https://libphonenumber.appspot.com/). If that returns the same result as this project, and you feel it is in error, raise it as an Issue with the libphonenumber project.

If Google's [Online Demo](https://libphonenumber.appspot.com/) gives a different result to the [libphonenumber-for-php demo](http://giggsey.com/libphonenumber/), then please raise an Issue on the [giggsey/libphonenumber-for-php](https://github.com/giggsey/libphonenumber-for-php) project.

If [giggsey/libphonenumber-for-php](https://github.com/giggsey/libphonenumber-for-php) differs from this library, please raise an issue here instead.

## Generating data

Generating the data is not normally needed, as this repository will generally always have the up to data metadata.

If you do need to generate the data, the commands are provided by [Phing](https://www.phing.info). Ensure you have all the dev composer dependencies installed, then run

```bash
$ vendor/bin/phing compile
```

This compile process clones the [libphonenumber](https://github.com/google/libphonenumber) project at the version specified in [METADATA-VERSION.txt](METADATA-VERSION.txt).
