<?php

namespace Kevinrob\GuzzleCache\Strategy;

use Kevinrob\GuzzleCache\KeyValueHttpHeader;
use Kevinrob\GuzzleCache\Storage\CacheStorageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * This strategy represents a "public" or "shared" HTTP client.
 * You can share the storage between applications.
 *
 * For example, a response with cache-control header "private, max-age=60"
 * will be NOT cached by this strategy.
 *
 * The rules applied are from RFC 7234.
 *
 * @see https://tools.ietf.org/html/rfc7234
 */
class PublicCacheStrategy extends PrivateCacheStrategy
{
    public function __construct(CacheStorageInterface $cache = null)
    {
        parent::__construct($cache);

        array_unshift($this->ageKey, 's-maxage');
    }

    /**
     * {@inheritdoc}
     */
    protected function getCacheObject(RequestInterface $request, ResponseInterface $response)
    {
        $cacheControl = new KeyValueHttpHeader($response->getHeader('Cache-Control'));
        if ($cacheControl->has('private')) {
            return;
        }

        return parent::getCacheObject($request, $response);
    }
}
