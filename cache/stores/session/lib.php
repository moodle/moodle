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
 * The library file for the session cache store.
 *
 * This file is part of the session cache store, it contains the API for interacting with an instance of the store.
 * This is used as a default cache store within the Cache API. It should never be deleted.
 *
 * @package    cachestore_session
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The session data store class.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class session_data_store extends cache_store {

    /**
     * Used for the actual storage.
     * @var array
     */
    private static $sessionstore = null;

    /**
     * Returns a static store by reference... REFERENCE SUPER IMPORTANT.
     *
     * @param string $id
     * @return array
     */
    protected static function &register_store_id($id) {
        if (is_null(self::$sessionstore)) {
            global $SESSION;
            if (!isset($SESSION->cachestore_session)) {
                $SESSION->cachestore_session = array();
            }
            self::$sessionstore =& $SESSION->cachestore_session;
        }
        if (!array_key_exists($id, self::$sessionstore)) {
            self::$sessionstore[$id] = array();
        }
        return self::$sessionstore[$id];
    }

    /**
     * Flushes the data belong to the given store id.
     * @param string $id
     */
    protected static function flush_store_by_id($id) {
        unset(self::$sessionstore[$id]);
        self::$sessionstore[$id] = array();
    }

    /**
     * Flushes the store of all data.
     */
    protected static function flush_store() {
        $ids = array_keys(self::$sessionstore);
        unset(self::$sessionstore);
        self::$sessionstore = array();
        foreach ($ids as $id) {
            self::$sessionstore[$id] = array();
        }
    }
}

/**
 * The Session store class.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_session extends session_data_store implements cache_is_key_aware, cache_is_searchable {

    /**
     * The name of the store
     * @var store
     */
    protected $name;

    /**
     * The store id (should be unique)
     * @var string
     */
    protected $storeid;

    /**
     * The store we use for data.
     * @var array
     */
    protected $store;

    /**
     * The ttl if there is one. Hopefully not.
     * @var int
     */
    protected $ttl = 0;

    /**
     * Constructs the store instance.
     *
     * Noting that this function is not an initialisation. It is used to prepare the store for use.
     * The store will be initialised when required and will be provided with a cache_definition at that time.
     *
     * @param string $name
     * @param array $configuration
     */
    public function __construct($name, array $configuration = array()) {
        $this->name = $name;
    }

    /**
     * Returns the supported features as a combined int.
     *
     * @param array $configuration
     * @return int
     */
    public static function get_supported_features(array $configuration = array()) {
        return self::SUPPORTS_DATA_GUARANTEE +
               self::SUPPORTS_NATIVE_TTL +
               self::IS_SEARCHABLE;
    }

    /**
     * Returns false as this store does not support multiple identifiers.
     * (This optional function is a performance optimisation; it must be
     * consistent with the value from get_supported_features.)
     *
     * @return bool False
     */
    public function supports_multiple_identifiers() {
        return false;
    }

    /**
     * Returns the supported modes as a combined int.
     *
     * @param array $configuration
     * @return int
     */
    public static function get_supported_modes(array $configuration = array()) {
        return self::MODE_SESSION;
    }

    /**
     * Returns true if the store requirements are met.
     *
     * @return bool
     */
    public static function are_requirements_met() {
        return true;
    }

    /**
     * Returns true if the given mode is supported by this store.
     *
     * @param int $mode One of cache_store::MODE_*
     * @return bool
     */
    public static function is_supported_mode($mode) {
        return ($mode === self::MODE_SESSION);
    }

    /**
     * Initialises the cache.
     *
     * Once this has been done the cache is all set to be used.
     *
     * @param cache_definition $definition
     */
    public function initialise(cache_definition $definition) {
        $this->storeid = $definition->generate_definition_hash();
        $this->store = &self::register_store_id($this->name.'-'.$definition->get_id());
        $this->ttl = $definition->get_ttl();
        $this->check_ttl();
    }

    /**
     * Returns true once this instance has been initialised.
     *
     * @return bool
     */
    public function is_initialised() {
        return (is_array($this->store));
    }

    /**
     * Returns true if this store instance is ready to be used.
     * @return bool
     */
    public function is_ready() {
        return true;
    }

    /**
     * Retrieves an item from the cache store given its key.
     *
     * @param string $key The key to retrieve
     * @return mixed The data that was associated with the key, or false if the key did not exist.
     */
    public function get($key) {
        if (isset($this->store[$key])) {
            if ($this->ttl === 0) {
                return $this->store[$key][0];
            } else if ($this->store[$key][1] >= (cache::now() - $this->ttl)) {
                return $this->store[$key][0];
            } else {
                // Element is present but has expired.
                $this->check_ttl();
            }
        }
        return false;
    }

    /**
     * Retrieves several items from the cache store in a single transaction.
     *
     * If not all of the items are available in the cache then the data value for those that are missing will be set to false.
     *
     * @param array $keys The array of keys to retrieve
     * @return array An array of items from the cache. There will be an item for each key, those that were not in the store will
     *      be set to false.
     */
    public function get_many($keys) {
        $return = array();
        $maxtime = 0;
        if ($this->ttl != 0) {
            $maxtime = cache::now() - $this->ttl;
        }

        $hasexpiredelements = false;
        foreach ($keys as $key) {
            $return[$key] = false;
            if (isset($this->store[$key])) {
                if ($this->ttl == 0) {
                    $return[$key] = $this->store[$key][0];
                } else if ($this->store[$key][1] >= $maxtime) {
                    $return[$key] = $this->store[$key][0];
                } else {
                    $hasexpiredelements = true;
                }
            }
        }
        if ($hasexpiredelements) {
            // There are some elements that are present but have expired.
            $this->check_ttl();
        }
        return $return;
    }

    /**
     * Sets an item in the cache given its key and data value.
     *
     * @param string $key The key to use.
     * @param mixed $data The data to set.
     * @return bool True if the operation was a success false otherwise.
     */
    public function set($key, $data) {
        if ($this->ttl === 0) {
            $this->store[$key] = array($data, 0);
        } else {
            $this->store[$key] = array($data, cache::now());
        }
        return true;
    }

    /**
     * Sets many items in the cache in a single transaction.
     *
     * @param array $keyvaluearray An array of key value pairs. Each item in the array will be an associative array with two
     *      keys, 'key' and 'value'.
     * @return int The number of items successfully set. It is up to the developer to check this matches the number of items
     *      sent ... if they care that is.
     */
    public function set_many(array $keyvaluearray) {
        $count = 0;
        foreach ($keyvaluearray as $pair) {
            $key = $pair['key'];
            $data = $pair['value'];
            $count++;
            if ($this->ttl === 0) {
                $this->store[$key] = array($data, 0);
            } else {
                $this->store[$key] = array($data, cache::now());
            }
        }
        return $count;
    }

    /**
     * Checks if the store has a record for the given key and returns true if so.
     *
     * @param string $key
     * @return bool
     */
    public function has($key) {
        if (isset($this->store[$key])) {
            if ($this->ttl == 0) {
                return true;
            } else if ($this->store[$key][1] >= (cache::now() - $this->ttl)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true if the store contains records for all of the given keys.
     *
     * @param array $keys
     * @return bool
     */
    public function has_all(array $keys) {
        $maxtime = 0;
        if ($this->ttl != 0) {
            $maxtime = cache::now() - $this->ttl;
        }

        foreach ($keys as $key) {
            if (!isset($this->store[$key])) {
                return false;
            }
            if ($this->ttl != 0 && $this->store[$key][1] < $maxtime) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns true if the store contains records for any of the given keys.
     *
     * @param array $keys
     * @return bool
     */
    public function has_any(array $keys) {
        $maxtime = 0;
        if ($this->ttl != 0) {
            $maxtime = cache::now() - $this->ttl;
        }

        foreach ($keys as $key) {
            if (isset($this->store[$key]) && ($this->ttl == 0 || $this->store[$key][1] >= $maxtime)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Deletes an item from the cache store.
     *
     * @param string $key The key to delete.
     * @return bool Returns true if the operation was a success, false otherwise.
     */
    public function delete($key) {
        if (!isset($this->store[$key])) {
            return false;
        }
        unset($this->store[$key]);
        return true;
    }

    /**
     * Deletes several keys from the cache in a single action.
     *
     * @param array $keys The keys to delete
     * @return int The number of items successfully deleted.
     */
    public function delete_many(array $keys) {
        // The number of items that have been successfully deleted.
        $count = 0;
        foreach ($keys as $key) {
            unset($this->store[$key]);
            $count++;
        }
        return $count;
    }

    /**
     * Purges the cache deleting all items within it.
     *
     * @return boolean True on success. False otherwise.
     */
    public function purge() {
        $this->store = array();
        return true;
    }

    /**
     * Returns true if the user can add an instance of the store plugin.
     *
     * @return bool
     */
    public static function can_add_instance() {
        return false;
    }

    /**
     * Performs any necessary clean up when the store instance is being deleted.
     */
    public function instance_deleted() {
        $this->purge();
    }

    /**
     * Generates an instance of the cache store that can be used for testing.
     *
     * @param cache_definition $definition
     * @return false
     */
    public static function initialise_test_instance(cache_definition $definition) {
        // Do something here perhaps.
        $cache = new cachestore_session('Session test');
        $cache->initialise($definition);
        return $cache;
    }

    /**
     * Returns the name of this instance.
     * @return string
     */
    public function my_name() {
        return $this->name;
    }

    /**
     * Removes expired elements.
     * @return int number of removed elements
     */
    protected function check_ttl() {
        if ($this->ttl === 0) {
            return 0;
        }
        $maxtime = cache::now() - $this->ttl;
        $count = 0;
        for ($value = reset($this->store); $value !== false; $value = next($this->store)) {
            if ($value[1] >= $maxtime) {
                // We know that elements are sorted by ttl so no need to continue.
                break;
            }
            $count++;
        }
        if ($count) {
            // Remove first $count elements as they are expired.
            $this->store = array_slice($this->store, $count, null, true);
        }
        return $count;
    }

    /**
     * Finds all of the keys being stored in the cache store instance.
     *
     * @return array
     */
    public function find_all() {
        $this->check_ttl();
        return array_keys($this->store);
    }

    /**
     * Finds all of the keys whose keys start with the given prefix.
     *
     * @param string $prefix
     * @return array An array of keys.
     */
    public function find_by_prefix($prefix) {
        $return = array();
        foreach ($this->find_all() as $key) {
            if (strpos($key, $prefix) === 0) {
                $return[] = $key;
            }
        }
        return $return;
    }

    /**
     * This store supports native TTL handling.
     * @return bool
     */
    public function store_supports_native_ttl() {
        return true;
    }
}
