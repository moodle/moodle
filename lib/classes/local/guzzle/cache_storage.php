<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace core\local\guzzle;

use Kevinrob\GuzzleCache\CacheEntry;
use Kevinrob\GuzzleCache\Storage\CacheStorageInterface;

/**
 * Cache storage handler to handle cache objects, TTL etc.
 *
 * @package    core
 * @copyright  2022 safatshahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_storage implements CacheStorageInterface {

    /**
     * The cache pool.
     *
     * @var cache_handler
     */
    protected $cachepool;

    /**
     * The last item retrieved from the cache.
     *
     * This item is transiently stored so that save() can reuse the cache item
     * usually retrieved by fetch() beforehand, instead of requesting it a second time.
     *
     * @var cache_item|null
     */
    protected $lastitem;

    /**
     * TTL for the cache.
     *
     * @var int|null time to live.
     */
    private int $ttl;

    public function __construct(cache_handler $cachepool, ?int $ttl = null) {
        $this->cachepool = $cachepool;
        $this->ttl = $ttl;
    }

    public function fetch($key): ?CacheEntry {
        // Refresh the cache files.
        if ($this->ttl) {
            $this->cachepool->refresh($this->ttl);
        }
        $item = $this->cachepool->get_item($key, $this->ttl);
        $this->lastitem = $item;

        $cache = $item->get();

        if ($cache instanceof CacheEntry) {
            return $cache;
        }

        return null;
    }

    public function save($key, CacheEntry $data): bool {
        if ($this->lastitem && $this->lastitem->get_key() === $key) {
            $item = $this->lastitem;
        } else {
            $item = $this->cachepool->get_item($key);
        }

        $this->lastitem = null;

        $item->set($data);

        // Check if the TTL is set, otherwise use from data.
        $ttl = $this->ttl ?? $data->getTTL();

        if ($ttl === 0) {
            // No expiration.
            $item->expires_after(null);
        } else {
            $item->expires_after($ttl);
        }

        return $this->cachepool->save($item);
    }

    public function delete($key): bool {
        if (null !== $this->lastitem && $this->lastitem->get_key() === $key) {
            $this->lastitem = null;
        }

        return $this->cachepool->delete_item($key);
    }

}
