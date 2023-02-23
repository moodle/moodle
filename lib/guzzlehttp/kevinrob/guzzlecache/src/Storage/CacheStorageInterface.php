<?php

namespace Kevinrob\GuzzleCache\Storage;

use Kevinrob\GuzzleCache\CacheEntry;

interface CacheStorageInterface
{
    /**
     * @param string $key
     *
     * @return CacheEntry|null the data or false
     */
    public function fetch($key);

    /**
     * @param string     $key
     * @param CacheEntry $data
     *
     * @return bool
     */
    public function save($key, CacheEntry $data);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key);
}
