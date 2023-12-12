<?php

namespace Kevinrob\GuzzleCache\Storage;

use Illuminate\Contracts\Cache\Repository as Cache;
use Kevinrob\GuzzleCache\CacheEntry;

class LaravelCacheStorage implements CacheStorageInterface
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
            $cache = unserialize($this->cache->get($key, ''));
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
            $lifeTime = $this->getLifeTime($data);
            if ($lifeTime === 0) {
                return $this->cache->forever(
                    $key,
                    serialize($data)
                );
            } else if ($lifeTime > 0) {
                return $this->cache->add(
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
        return $this->cache->forget($key);
    }

    protected function getLifeTime(CacheEntry $data)
    {
        $version = app()->version();
        if (preg_match('/^\d+(\.\d+)?(\.\d+)?/', $version) && version_compare($version, '5.8.0') < 0) {
            // getTTL returns seconds, Laravel needs minutes before v5.8
            return $data->getTTL() / 60;
        }

        return $data->getTTL();
    }
}
