# guzzle-cache-middleware

[![Latest Stable Version](https://poser.pugx.org/kevinrob/guzzle-cache-middleware/v/stable)](https://packagist.org/packages/kevinrob/guzzle-cache-middleware) [![Total Downloads](https://poser.pugx.org/kevinrob/guzzle-cache-middleware/downloads)](https://packagist.org/packages/kevinrob/guzzle-cache-middleware) [![License](https://poser.pugx.org/kevinrob/guzzle-cache-middleware/license)](https://packagist.org/packages/kevinrob/guzzle-cache-middleware)
![Tests](https://github.com/Kevinrob/guzzle-cache-middleware/workflows/Tests/badge.svg) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Kevinrob/guzzle-cache-middleware/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Kevinrob/guzzle-cache-middleware/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/Kevinrob/guzzle-cache-middleware/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Kevinrob/guzzle-cache-middleware/?branch=master)


A HTTP Cache for [Guzzle](https://github.com/guzzle/guzzle) 6+. It's a simple Middleware to be added in the HandlerStack.

## Goals
- RFC 7234 compliance
- Performance and transparency
- Assured compatibility with PSR-7

## Built-in storage interfaces
- [Doctrine cache](https://github.com/doctrine/cache)
- [Laravel cache](https://laravel.com/docs/5.2/cache)
- [Flysystem](https://github.com/thephpleague/flysystem)
- [PSR6](https://github.com/php-fig/cache)
- [WordPress Object Cache](https://codex.wordpress.org/Class_Reference/WP_Object_Cache)

## Installation

`composer require kevinrob/guzzle-cache-middleware`

or add it the your `composer.json` and run `composer update kevinrob/guzzle-cache-middleware`.

# Why?
Performance. It's very common to do some HTTP calls to an API for rendering a page and it takes times to do it.

# How?
With a simple Middleware added at the top of the `HandlerStack` of Guzzle.

```php
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;

// Create default HandlerStack
$stack = HandlerStack::create();

// Add this middleware to the top with `push`
$stack->push(new CacheMiddleware(), 'cache');

// Initialize the client with the handler option
$client = new Client(['handler' => $stack]);
```

# Examples

## Doctrine/Cache
You can use a cache from `Doctrine/Cache`:
```php
[...]
use Doctrine\Common\Cache\FilesystemCache;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage;

[...]
$stack->push(
  new CacheMiddleware(
    new PrivateCacheStrategy(
      new DoctrineCacheStorage(
        new FilesystemCache('/tmp/')
      )
    )
  ),
  'cache'
);
```

You can use `ChainCache` for using multiple `CacheProvider` instances. With that provider, you have to sort the different caches from the faster to the slower. Like that, you can have a very fast cache.
```php
[...]
use Doctrine\Common\Cache\ChainCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage;

[...]
$stack->push(new CacheMiddleware(
  new PrivateCacheStrategy(
    new DoctrineCacheStorage(
      new ChainCache([
        new ArrayCache(),
        new FilesystemCache('/tmp/'),
      ])
    )
  )
), 'cache');
```

## Laravel cache
You can use a cache with Laravel, e.g. Redis, Memcache etc.:
```php
[...]
use Illuminate\Support\Facades\Cache;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Kevinrob\GuzzleCache\Storage\LaravelCacheStorage;

[...]

$stack->push(
  new CacheMiddleware(
    new PrivateCacheStrategy(
      new LaravelCacheStorage(
        Cache::store('redis')
      )
    )
  ),
  'cache'
);
```

## Flysystem
```php
[...]
use League\Flysystem\Adapter\Local;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Kevinrob\GuzzleCache\Storage\FlysystemStorage;

[...]

$stack->push(
  new CacheMiddleware(
    new PrivateCacheStrategy(
      new FlysystemStorage(
        new Local('/path/to/cache')
      )
    )
  ),
  'cache'
);
```

## WordPress Object Cache
```php
[...]
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Kevinrob\GuzzleCache\Storage\WordPressObjectCacheStorage;

[...]

$stack->push(
  new CacheMiddleware(
    new PrivateCacheStrategy(
      new WordPressObjectCacheStorage()
    )
  ),
  'cache'
);
```

## Public and shared
It's possible to add a public shared cache to the stack:
```php
[...]
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\PredisCache;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Kevinrob\GuzzleCache\Strategy\PublicCacheStrategy;
use Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage;

[...]
// Private caching
$stack->push(
  new CacheMiddleware(
    new PrivateCacheStrategy(
      new DoctrineCacheStorage(
        new FilesystemCache('/tmp/')
      )
    )
  ),
  'private-cache'
);

// Public caching
$stack->push(
  new CacheMiddleware(
    new PublicCacheStrategy(
      new DoctrineCacheStorage(
        new PredisCache(
          new Predis\Client('tcp://10.0.0.1:6379')
        )
      )
    )
  ),
  'shared-cache'
);
```

## Greedy caching
In some cases servers might send insufficient or no caching headers at all.
Using the greedy caching strategy allows defining an expiry TTL on your own while
disregarding any possibly present caching headers:
```php
[...]
use Kevinrob\GuzzleCache\KeyValueHttpHeader;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage;
use Doctrine\Common\Cache\FilesystemCache;

[...]
// Greedy caching
$stack->push(
  new CacheMiddleware(
    new GreedyCacheStrategy(
      new DoctrineCacheStorage(
        new FilesystemCache('/tmp/')
      ),
      1800, // the TTL in seconds
      new KeyValueHttpHeader(['Authorization']) // Optional - specify the headers that can change the cache key
    )
  ),
  'greedy-cache'
);
```

## Delegate caching
Because your client may call different apps, on different domains, you may need to define which strategy is suitable to your requests.

To solve this, all you have to do is to define a default cache strategy, and override it by implementing your own Request Matchers.

Here's an example:
```php
namespace App\RequestMatcher;

use Kevinrob\GuzzleCache\Strategy\Delegate\RequestMatcherInterface;
use Psr\Http\Message\RequestInterface;

class ExampleOrgRequestMatcher implements RequestMatcherInterface
{

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request)
    {
        return false !== strpos($request->getUri()->getHost(), 'example.org');
    }
}
```

```php
namespace App\RequestMatcher;

use Kevinrob\GuzzleCache\Strategy\Delegate\RequestMatcherInterface;
use Psr\Http\Message\RequestInterface;

class TwitterRequestMatcher implements RequestMatcherInterface
{

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request)
    {
        return false !== strpos($request->getUri()->getHost(), 'twitter.com');
    }
}
```

```php
require_once __DIR__ . '/vendor/autoload.php';

use App\RequestMatcher\ExampleOrgRequestMatcher;
use App\RequestMatcher\TwitterRequestMatcher;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Strategy;

$strategy = new Strategy\Delegate\DelegatingCacheStrategy($defaultStrategy = new Strategy\NullCacheStrategy());
$strategy->registerRequestMatcher(new ExampleOrgRequestMatcher(), new Strategy\PublicCacheStrategy());
$strategy->registerRequestMatcher(new TwitterRequestMatcher(), new Strategy\PrivateCacheStrategy());

$stack = HandlerStack::create();
$stack->push(new CacheMiddleware($strategy));
$guzzle = new Client(['handler' => $stack]);
```

With this example:
* All requests to `example.org` will be handled by `PublicCacheStrategy`
* All requests to `twitter.com` will be handled by `PrivateCacheStrategy`
* All other requests won't be cached.

## Drupal
See [Guzzle Cache](https://www.drupal.org/project/guzzle_cache) module.

# Links that talk about the project
- [Speeding Up APIs/Apps/Smart Toasters with HTTP Response Caching](https://apisyouwonthate.com/blog/speeding-up-apis-apps-smart-toasters-with-http-response-caching)
- [Caching HTTP-Requests with Guzzle 6 and PSR-6](http://a.kabachnik.info/caching-http-requests-with-guzzle-6-and-psr-6.html)

# Development

## Docker quick start

### Initialization
```bash
make init
```
### Running test
```bash
make test
```
### Entering container shell
```bash
make shell
```
