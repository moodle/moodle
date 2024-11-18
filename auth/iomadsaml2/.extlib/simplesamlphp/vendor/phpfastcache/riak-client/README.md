# Riak Client for PHP (PhpFastCache Fork)
#### (Fork of the official basho/riak due to maintainer significant inactivity)

[![Packagist](https://img.shields.io/packagist/v/phpfastcache/riak-client.svg?maxAge=2592000)](https://packagist.org/packages/phpfastcache/riak-client)

**Riak PHP Client** is a library which makes it easy to communicate with [Riak](http://basho.com/riak/), an open source, distributed database that focuses on high availability, horizontal scalability, and *predictable*
latency. This library communicates with Riak's HTTP interface using the cURL extension. If you want to communicate with Riak using the Protocol Buffers interface, use the [Official PHP PB Client](https://github.com/basho/riak-phppb-client). Both Riak and this library are maintained by [Basho Technologies](http://www.basho.com/). 

To see other clients available for use with Riak visit our [Documentation Site](http://docs.basho.com/riak/latest/dev/using/libraries)


1. [Installation](#installation)
1. [Documentation](#documentation)
1. [Contributing](#contributing)
	* [An honest disclaimer](#an-honest-disclaimer)
1. [Roadmap](#roadmap)
1. [License and Authors](#license-and-authors)


## Installation

### Dependencies
- PHP 5.4+ (Up to PHP 7.2 with the 1.4.4 update provided by PhpFastCache)
- PHP Extensions: curl, json and openssl [required for security features]
- Riak 2.1+
- [Composer PHP Dependency Manager](https://getcomposer.org/)

### Composer Install

This library has been added to [Packagist](https://packagist.org/packages/phpfastcache/) to simplify the installation process. Run the following [composer](https://getcomposer.org/) command:

```console
$ composer require "phpfastcache/riak-client": "^1.4.4"
```

Alternately, manually add the following to your `composer.json`, in the `require` section:

```javascript
"require": {
    "phpfastcache/riak-client": "^1.4.4"
}
```

And then run `composer update` to ensure the module is installed.

## Documentation

* Master: [![Build Status](https://secure.travis-ci.org/basho/riak-php-client.png?branch=master)](http://travis-ci.org/basho/riak-php-client)

A fully traversable version of the API documentation for this library can be found on [Github Pages](http://basho.github.io/riak-php-client). 

### Example Usage

Below is a short example of using the client. More substantial sample code is available [in examples](/examples).

```php
// lib classes are included via the Composer autoloader files
use Basho\Riak;
use Basho\Riak\Node;
use Basho\Riak\Command;

// define the connection info to our Riak nodes
$nodes = (new Node\Builder)
    ->onPort(10018)
    ->buildCluster(['riak1.company.com', 'riak2.company.com', 'riak3.company.com',]);

// instantiate the Riak client
$riak = new Riak($nodes);

// build a command to be executed against Riak
$command = (new Command\Builder\StoreObject($riak))
    ->buildObject('some_data')
    ->buildBucket('users')
    ->build();
    
// Receive a response object
$response = $command->execute();

// Retrieve the Location of our newly stored object from the Response object
$object_location = $response->getLocation();
```

## Contributing

This repo's maintainers are engineers at Basho and we welcome your contribution to the project! You can start by reviewing [CONTRIBUTING.md](CONTRIBUTING.md) for information on everything from testing to coding standards.

### An honest disclaimer

Due to our obsession with stability and our rich ecosystem of users, community updates on this repo may take a little longer to review. 

The most helpful way to contribute is by reporting your experience through issues. Issues may not be updated while we review internally, but they're still incredibly appreciated.

Thank you for being part of the community! We love you for it. 

## Roadmap

* Current develop & master branches contain feature support for Riak version 2.1+
* Add support for Riak TS Q2 2016

## License and Authors

### Active authors
* Author: Georges.L (https://github.com/Geolim4)

### Former authors
* Author: Christopher Mancini (https://github.com/christophermancini)
* Author: Alex Moore (https://github.com/alexmoore)
* Author: Luke Bakken (https://github.com/lukebakken)

Copyright (c) 2015 Basho Technologies, Inc. Licensed under the Apache License, Version 2.0 (the "License"). For more details, see [License](License).
