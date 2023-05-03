<?php

namespace Kevinrob\GuzzleCache\Storage;

use Kevinrob\GuzzleCache\CacheEntry;
use Psr\SimpleCache\CacheInterface;

class Psr16CacheStorage implements CacheStorageInterface
{
    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($key)
    {
        $data = $this->cache->get($key);
        if ($data instanceof CacheEntry) {
            return $data;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function save($key, CacheEntry $data)
    {
        $ttl = $data->getTTL();
        if ($ttl === 0) {
        return $this->cache->set($key, $data);
        }
        return $this->cache->set($key, $data, $data->getTTL());
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return $this->cache->delete($key);
    }
}
