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

namespace core\navigation;

use core_cache\cache;
use core_cache\session_cache;
use core\shutdown_manager;

/**
 * The navigation_cache class is used for global and settings navigation data.
 *
 * It provides an easy access to the session cache with TTL of 1800 seconds.
 *
 * Example use:
 * <code php>
 * if (!$cache->viewdiscussion()) {
 *     // Code to do stuff and produce cachable content
 *     $cache->viewdiscussion = has_capability('mod/forum:viewdiscussion', $coursecontext);
 * }
 * $content = $cache->viewdiscussion;
 * </code>
 *
 * @package   core
 * @category  navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class navigation_cache {
    /** @var session_cache The session cache instance */
    protected $cache;
    /** @var array The current cache area data */
    protected $session = [];

    /**
     * @var string A unique string to segregate this particular cache.
     * It can either be unique to start a fresh cache or shared to use an existing cache.
     */
    protected $area;
    /** @var int cache time information */
    #[\core\attribute\deprecated(null, since: '4.5', reason: 'This constant is no longer needed.', mdl: 'MDL-79628')]
    public const CACHETIME = 0;
    /** @var int cache user id */
    #[\core\attribute\deprecated(null, since: '4.5', reason: 'This constant is no longer needed.', mdl: 'MDL-79628')]
    public const CACHEUSERID = 1;
    /** @var int cache value */
    public const CACHEVALUE = 2;
    /** @var null|array An array of cache areas to expire on shutdown */
    public static $volatilecaches = null;

    /**
     * Contructor for the cache. Requires a area string be passed in.
     *
     * @param string $area The unique string to segregate this particular cache.
     * @param int $timeout Deprecated since Moodle 4.5. The number of seconds to time the information out after
     */
    public function __construct($area, $timeout = null) {
        if ($timeout !== null) {
            debugging(
                'The timeout argument has been deprecated. Please remove it from your method calls.',
                DEBUG_DEVELOPER,
            );
        }
        global $USER;
        $this->area = "user_{$USER->id}_{$area}";
        $this->cache = cache::make('core', 'navigation_cache');
    }

    /**
     * Ensure the navigation cache is initialised
     *
     * This is called for each access and ensures that no data is put into the cache before it is required.
     */
    protected function ensure_navigation_cache_initialised() {
        if (empty($this->session)) {
            $this->session = $this->cache->get($this->area);
            if (!is_array($this->session)) {
                $this->session = [];
            }
        }
    }

    /**
     * Magic Method to retrieve a cached item by simply calling using = cache->key
     *
     * @param mixed $key The identifier for the cached information
     * @return mixed|void The cached information or void if not found
     */
    public function __get($key) {
        if (!$this->cached($key)) {
            return;
        }
        return unserialize($this->session[$key][self::CACHEVALUE]);
    }

    /**
     * Magic method that simply uses {@see navigation_cache::set()} to store an item in the cache
     *
     * @param string|int $key The key to store the information against
     * @param mixed $information The information to cache
     */
    public function __set($key, $information) {
        $this->set($key, $information);
    }

    /**
     * Sets some information in the session cache for later retrieval
     *
     * @param string|int $key
     * @param mixed $information
     */
    public function set($key, $information) {
        $this->ensure_navigation_cache_initialised();
        $information = serialize($information);
        $this->session[$key] = [self::CACHEVALUE => $information];
        $this->cache->set($this->area, $this->session);
    }
    /**
     * Check the existence of the identifier in the cache
     *
     * @param string|int $key The identifier to check
     * @return bool True if the item exists in the cache, false otherwise
     */
    public function cached($key) {
        $this->ensure_navigation_cache_initialised();
        return isset($this->session[$key]) &&
            is_array($this->session[$key]);
    }
    /**
     * Compare something to it's equivilant in the cache
     *
     * @param string $key  The key to check
     * @param mixed $value The value to compare
     * @param bool $serialise Whether to serialise the value before comparison
     *              this should only be set to false if the value is already
     *              serialised
     * @return bool True if the value is the same as the cached one, false otherwise
     */
    public function compare($key, $value, $serialise = true) {
        if ($this->cached($key)) {
            if ($serialise) {
                $value = serialize($value);
            }
            return $this->session[$key][self::CACHEVALUE] === $value;
        }
        return false;
    }
    /**
     * Deletes the entire cache area, forcing a fresh cache to be created
     */
    public function clear() {
        $this->cache->delete($this->area);
        $this->session = [];
    }
    /**
     * Marks the cache as volatile (likely to change)
     *
     * Any caches marked as volatile will be destroyed on shutdown by {@see navigation_node::destroy_volatile_caches()}
     *
     * @param bool $setting True to mark the cache as volatile, false to remove the volatile flag
     */
    public function volatile($setting = true) {
        if (self::$volatilecaches === null) {
            self::$volatilecaches = [];
            shutdown_manager::register_function(['navigation_cache', 'destroy_volatile_caches']);
        }

        if ($setting) {
            self::$volatilecaches[$this->area] = $this->area;
        } else if (array_key_exists($this->area, self::$volatilecaches)) {
            unset(self::$volatilecaches[$this->area]);
        }
    }

    /**
     * Destroys all caches marked as volatile
     *
     * This function is static and works with the static volatilecaches property of navigation cache.
     * It manually resets the cached areas back to an empty array.
     */
    public static function destroy_volatile_caches() {
        if (is_array(self::$volatilecaches) && count(self::$volatilecaches) > 0) {
            $cache = cache::make('core', 'navigation_cache');
            foreach (self::$volatilecaches as $area) {
                $cache->delete($area);
            }
            self::$volatilecaches = null;
        }
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(navigation_cache::class, \navigation_cache::class);
