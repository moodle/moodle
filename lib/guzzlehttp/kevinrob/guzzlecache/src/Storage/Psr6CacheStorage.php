<?php

namespace Kevinrob\GuzzleCache\Storage;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Kevinrob\GuzzleCache\CacheEntry;

class Psr6CacheStorage implements CacheStorageInterface
{
    /**
     * The cache pool.
     *
     * @var CacheItemPoolInterface
     */
    protected $cachePool;

    /**
     * The last item retrieved from the cache.
     *
     * This item is transiently stored so that save() can reuse the cache item
     * usually retrieved by fetch() beforehand, instead of requesting it a second time.
     *
     * @var CacheItemInterface|null
     */
    protected $lastItem;

    /**
     * @param CacheItemPoolInterface $cachePool
     */
    public function __construct(CacheItemPoolInterface $cachePool)
    {
        $this->cachePool = $cachePool;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($key)
    {
        $item = $this->cachePool->getItem($key);
        $this->lastItem = $item;

        $cache = $item->get();

        if ($cache instanceof CacheEntry) {
            return $cache;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function save($key, CacheEntry $data)
    {
        if ($this->lastItem && $this->lastItem->getKey() == $key) {
            $item = $this->lastItem;
        } else {
            $item = $this->cachePool->getItem($key);
        }

        $this->lastItem = null;

        $item->set($data);

        $ttl = $data->getTTL();
        if ($ttl === 0) {
            // No expiration
            $item->expiresAfter(null);
        } else {
            $item->expiresAfter($ttl);
        }

        return $this->cachePool->save($item);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key)
    {
        if (null !== $this->lastItem && $this->lastItem->getKey() === $key) {
            $this->lastItem = null;
        }

        return $this->cachePool->deleteItem($key);
    }
}
