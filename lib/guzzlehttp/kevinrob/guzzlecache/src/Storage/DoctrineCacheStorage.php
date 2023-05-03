<?php

namespace Kevinrob\GuzzleCache\Storage;

use Doctrine\Common\Cache\Cache;
use Kevinrob\GuzzleCache\CacheEntry;

class DoctrineCacheStorage implements CacheStorageInterface
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($key)
    {
        try {
            $cache = unserialize($this->cache->fetch($key));
            if ($cache instanceof CacheEntry) {
                return $cache;
            }
        } catch (\Exception $ignored) {
            return;
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function save($key, CacheEntry $data)
    {
        try {
            $lifeTime = $data->getTTL();
            if ($lifeTime >= 0) {
                return $this->cache->save(
                    $key,
                    serialize($data),
                    $lifeTime
                );
            }
        } catch (\Exception $ignored) {
            // No fail if we can't save it the storage
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        try {
            return $this->cache->delete($key);
        } catch (\Exception $ignored) {
            // Don't fail if we can't delete it
        }

        return false;
    }
}
