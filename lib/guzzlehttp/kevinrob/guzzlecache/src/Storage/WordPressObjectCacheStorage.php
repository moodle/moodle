<?php

namespace Kevinrob\GuzzleCache\Storage;

use Kevinrob\GuzzleCache\CacheEntry;

class WordPressObjectCacheStorage implements CacheStorageInterface
{
    /**
     * @var string
     */
    private $group;

    /**
     * @param string $group
     */
    public function __construct($group = 'guzzle')
    {
        $this->group = $group;
    }

    /**
     * @param string $key
     *
     * @return CacheEntry|null the data or false
     */
    public function fetch($key)
    {
        try {
            $cache = unserialize(wp_cache_get($key, $this->group));
            if ($cache instanceof CacheEntry) {
                return $cache;
            }
        } catch (\Exception $ignored) {
            // Don't fail if we can't load it
        }

        return null;
    }

    /**
     * @param string $key
     * @param CacheEntry $data
     *
     * @return bool
     */
    public function save($key, CacheEntry $data)
    {
        try {
            return wp_cache_set($key, serialize($data), $this->group, $data->getTTL());
        } catch (\Exception $ignored) {
            // Don't fail if we can't save it
        }

        return false;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key)
    {
        try {
            return wp_cache_delete($key, $this->group);
        } catch (\Exception $ignored) {
            // Don't fail if we can't delete it
        }

        return false;
    }
}
