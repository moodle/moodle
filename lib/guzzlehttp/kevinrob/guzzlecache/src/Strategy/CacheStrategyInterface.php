<?php

namespace Kevinrob\GuzzleCache\Strategy;

use Kevinrob\GuzzleCache\CacheEntry;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface CacheStrategyInterface
{
    /**
     * Return a CacheEntry or null if no cache.
     *
     * @param RequestInterface $request
     *
     * @return CacheEntry|null
     */
    public function fetch(RequestInterface $request);

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return bool true if success
     */
    public function cache(RequestInterface $request, ResponseInterface $response);

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     *
     * @return bool true if success
     */
    public function update(RequestInterface $request, ResponseInterface $response);

    /**
     * @param RequestInterface $request
     *
     * @return bool
     */
    public function delete(RequestInterface $request);
}
