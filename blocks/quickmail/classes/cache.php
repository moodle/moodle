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

/**
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_quickmail_cache {
    public static $name = 'block_quickmail';
    public $store;
    public function __construct($store) {
        $this->store = $store;
    }

    /**
     * Instantiates and returns a quickmail cache instance for the given store name
     *
     * @param  string  $storename   provider name in block config
     * @return self
     */
    public static function store($storename) {
        $store = self::get_cache_store($storename);
        $instance = new self($store);
        return $instance;
    }

    /**
     * Returns the given key, or default value if missing
     *
     * @param  string|int  $key
     * @param  mixed   $default   a default value to return, can also be a closure
     * @return mixed
     */
    public function get($key, $default = null) {
        $value = $this->store->get($key);
        // If missing (moodle returns false as no value).
        if ($value === false) {
            // And the default is a closure.
            if (is_callable($default)) {
                // Return closure.
                return call_user_func($default);
            }

            // Otherwise, default or null if no default given.
            return $default === null ? null : $default;
        }

        return $value;
    }

    /**
     * Reports whether or not the given cache key exists in the store
     *
     * @param  string|int  $key
     * @return bool
     */
    public function check($key) {
        $value = $this->get($key);

        return $value !== null;
    }

    /**
     * Stores a value in the cache only if an existing value does not exist
     *
     * @param  string|int  $key
     * @param  mixed   $value   can be a closure
     * @return mixed
     */
    public function add($key, $value) {
        $existingvalue = $this->get($key);

        if ($existingvalue !== null) {
            return $existingvalue;
        }

        $newvalue = $this->put($key, $value);

        return $newvalue;
    }

    /**
     * Stores a value in the cache, overriding the existing value if any exists
     *
     * @param  string|int  $key
     * @param  mixed   $value   can be a closure
     * @return mixed
     */
    public function put($key, $value) {
        // If the value is a closure.
        if (is_callable($value)) {
            $value = call_user_func($value);
        }

        $this->store->set($key, $value);

        return $value;
    }

    /**
     * Stores a value in the cache only if an existing value does not exist
     *
     * (Similar to add() for now...)
     *
     * @param  string|int  $key
     * @param  mixed   $value   can be a closure
     * @return mixed
     */
    public function remember($key, $value) {
        $existing = $this->get($key);

        if ($existing === null) {
            $existing = $this->put($key, $value);
        }

        return $existing;
    }

    /**
     * Fetches and then deletes an item from the cache
     *
     * @param  string|int  $key
     * @return mixed
     */
    public function pull($key) {
        $value = $this->get($key);

        $this->forget($key);

        return $value;
    }

    /**
     * Deletes an item from the cache
     *
     * @param  string|int  $key
     * @return bool  result of deletion
     */
    public function forget($key) {
        $result = $this->store->delete($key);

        return $result;
    }

    /**
     * Instantiates and returns a moodle cache instance for the given "store name" (provider name)
     *
     * @param  string  $storename   provider name in block config
     * @return cache object
     */
    public static function get_cache_store($storename) {
        $cachestore = \cache::make(self::$name, $storename);

        return $cachestore;
    }
}
