<?php

namespace Kevinrob\GuzzleCache\Strategy\Delegate;

use Kevinrob\GuzzleCache\Strategy\CacheStrategyInterface;
use Kevinrob\GuzzleCache\Strategy\NullCacheStrategy;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class DelegatingCacheStrategy implements CacheStrategyInterface
{
    /**
     * @var array
     */
    private $requestMatchers = [];

    /**
     * @var CacheStrategyInterface
     */
    private $defaultCacheStrategy;

    /**
     * DelegatingCacheStrategy constructor.
     */
    public function __construct(CacheStrategyInterface $defaultCacheStrategy = null)
    {
        $this->defaultCacheStrategy = $defaultCacheStrategy ?: new NullCacheStrategy();
    }

    /**
     * @param CacheStrategyInterface $defaultCacheStrategy
     */
    public function setDefaultCacheStrategy(CacheStrategyInterface $defaultCacheStrategy)
    {
        $this->defaultCacheStrategy = $defaultCacheStrategy;
    }

    /**
     * @param RequestMatcherInterface $requestMatcher
     * @param CacheStrategyInterface  $cacheStrategy
     */
    final public function registerRequestMatcher(RequestMatcherInterface $requestMatcher, CacheStrategyInterface $cacheStrategy)
    {
        $this->requestMatchers[] = [
            $requestMatcher,
            $cacheStrategy,
        ];
    }

    /**
     * @param RequestInterface $request
     * @return CacheStrategyInterface
     */
    private function getStrategyFor(RequestInterface $request)
    {
        /**
         * @var RequestMatcherInterface $requestMatcher
         * @var CacheStrategyInterface $cacheStrategy
         */
        foreach ($this->requestMatchers as $requestMatcher) {
            list($requestMatcher, $cacheStrategy) = $requestMatcher;
            if ($requestMatcher->matches($request)) {
                return $cacheStrategy;
            }
        }

        return $this->defaultCacheStrategy;
    }

    /**
     * @inheritDoc
     */
    public function fetch(RequestInterface $request)
    {
        return $this->getStrategyFor($request)->fetch($request);
    }

    /**
     * @inheritDoc
     */
    public function cache(RequestInterface $request, ResponseInterface $response)
    {
        return $this->getStrategyFor($request)->cache($request, $response);
    }

    /**
     * @inheritDoc
     */
    public function update(RequestInterface $request, ResponseInterface $response)
    {
        return $this->getStrategyFor($request)->update($request, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(RequestInterface $request)
    {
        return $this->getStrategyFor($request)->delete($request);
    }
}
