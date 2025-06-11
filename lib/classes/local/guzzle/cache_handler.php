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

/**
 * Class to handle and generates CacheItemPoolInterface objects.
 *
 * This class will handle save, delete, cleanup etc. for the cache item.
 * For individual cache objects, this class will rely on {@cache_item} class.
 *
 * @package    core
 * @copyright  2022 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_handler {

    /**
     * This array will have the kay and value for that key.
     * Mainly individual data will be handled by {@cache_item} class for each array element.
     *
     * @var array $items cached items or the items currently in use by the cache pool.
     */
    private array $items;

    /**
     * This array will have the cache items which might need to persisted later.
     * It will not save the items in the cache pool using cache_item class until the commit is done for these elements.
     *
     * @var array $deferreditems cache items to be persisted later.
     */
    private array $deferreditems;

    /** @var string module name. */
    private string $module;

    /** @var string the directory for cache. */
    private string $dir;

    /**
     * Constructor for class cache_handler.
     * This class will accept the module which will determine the location of cached files.
     *
     * @param string $module module string for cache directory.
     */
    public function __construct(string $module = 'repository') {
        global $CFG;
        $this->module = $module;

        // Set the directory for cache.
        $this->dir = $CFG->cachedir . '/' . $module . '/';
        if (!file_exists($this->dir) && !mkdir($concurrentdirectory = $this->dir, $CFG->directorypermissions, true) &&
            !is_dir($concurrentdirectory)) {
            throw new \moodle_exception(sprintf('Directory "%s" was not created', $concurrentdirectory));
        }

    }

    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return a CacheItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key The key for which to return the corresponding Cache Item..
     * @param int|null $ttl Number of seconds for the cache item to live.
     * @return cache_item The corresponding Cache Item.
     */
    public function get_item(string$key, ?int $ttl = null): cache_item {
        return new cache_item($key, $this->module, $ttl);
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param string[] $keys An indexed array of keys of items to retrieve.
     * @return iterable
     *   An iterable collection of Cache Items keyed by the cache keys of
     *   each item. A Cache item will be returned for each key, even if that
     *   key is not found. However, if no keys are specified then an empty
     *   traversable MUST be returned instead.
     */
    public function get_items(array $keys = []): iterable {
        $items = [];

        foreach ($keys as $key) {
            $items[$key] = $this->has_item($key) ? clone $this->items[$key] : $this->get_item($key);
        }

        return $items;
    }

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key The key for which to check existence.
     * @return bool True if item exists in the cache, false otherwise.
     */
    public function has_item($key): bool {
        $this->assert_key_is_valid($key);

        return isset($this->items[$key]) && $this->items[$key]->isHit();
    }

    /**
     * Deletes all items in the pool.
     *
     * @return bool True if the pool was successfully cleared. False if there was an error.
     */
    public function clear(): bool {
        global $USER;

        if (isset($this->items)) {
            foreach ($this->items as $key => $item) {
                // Delete cache file.
                if ($dir = opendir($this->dir)) {
                    $filename = 'u' . $USER->id . '_' . md5(serialize($key));
                    $filename = $dir . $filename;
                    if (file_exists($filename) && $this->items[$key]->isHit()) {
                        @unlink($filename);
                    }
                    closedir($dir);
                }
            }
        }

        $this->items = [];
        $this->deferreditems = [];

        return true;
    }

    /**
     * Refreshes all items in the pool.
     *
     * @param int $ttl Seconds to live.
     * @return void
     */
    public function refresh(int $ttl): void {
        if ($dir = opendir($this->dir)) {
            while (false !== ($file = readdir($dir))) {
                if (!is_dir($file) && $file !== '.' && $file !== '..') {
                    $lasttime = @filemtime($this->dir . $file);
                    if (time() - $lasttime > $ttl) {
                        mtrace($this->dir . $file);
                        @unlink($this->dir . $file);
                    }
                }
            }
            closedir($dir);
        }
    }

    /**
     * Removes the item from the pool.
     *
     * @param string $key The key to delete.
     * @return bool True if the item was successfully removed. False if there was an error.
     */
    public function delete_item(string $key): bool {
        return $this->delete_items([$key]);
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param string[] $keys An array of keys that should be removed from the pool.
     * @return bool  True if the items were successfully removed. False if there was an error.
     */
    public function delete_items(array $keys): bool {
        global $USER;
        array_walk($keys, [$this, 'assert_key_is_valid']);

        foreach ($keys as $key) {
            // Delete cache file.
            if ($dir = opendir($this->dir)) {
                $filename = 'u' . $USER->id . '_' . md5(serialize($key));
                $filename = $dir . $filename;
                if (file_exists($filename)) {
                    @unlink($filename);
                }
            }

            unset($this->items[$key]);
        }

        return true;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param cache_item $item The cache item to save.
     * @return bool True if the item was successfully persisted. False if there was an error.
     */
    public function save(cache_item $item): bool {
        global $CFG, $USER;
        $key = $item->get_key();

        // File and directory setup.
        $filename = 'u' . $USER->id . '_' . md5(serialize($key));
        $fp = fopen($this->dir . $filename, 'wb');

        // Store the item.
        fwrite($fp, serialize($item->get()));
        fclose($fp);
        @chmod($this->dir . $filename, $CFG->filepermissions);

        $this->items[$key] = $item;

        return true;
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param cache_item $item The cache item to save.
     * @return bool False if the item could not be queued or if a commit was attempted and failed. True otherwise.
     */
    public function save_deferred(cache_item $item): bool {
        $this->deferreditems[$item->get_key()] = $item;

        return true;
    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool True if all not-yet-saved items were successfully saved or there were none. False otherwise.
     */
    public function commit(): bool {
        foreach ($this->deferreditems as $item) {
            $this->save($item);
        }

        $this->deferreditems = [];

        return true;
    }

    /**
     * Asserts that the given key is valid.
     * Some simple validation to make sure the passed key is a valid one.
     *
     * @param string $key The key to validate.
     */
    private function assert_key_is_valid(string $key): void {
        $invalidcharacters = '{}()/\\\\@:';

        if (!is_string($key) || preg_match("#[$invalidcharacters]#", $key)) {
            throw new \moodle_exception('Invalid cache key');
        }
    }

}
