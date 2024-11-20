<?php

namespace Kevinrob\GuzzleCache\Storage;

use Kevinrob\GuzzleCache\CacheEntry;

/**
 * This cache class is backed by a PHP Array.
 */
class VolatileRuntimeStorage implements CacheStorageInterface
{

    /**
     * @var CacheEntry[]
     */
    protected $cache = [];

    /**
     * @param string $key
     *
     * @return CacheEntry|null the data or false
     */
    public function fetch($key)
    {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        return;
    }

    /**
     * @param string $key
     * @param CacheEntry $data
     *
     * @return bool
     */
    public function save($key, CacheEntry $data)
    {
        $this->cache[$key] = $data;

        return true;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key)
    {
        if (true === array_key_exists($key, $this->cache)) {
            unset($this->cache[$key]);

            return true;
        }

        return false;
    }
}
