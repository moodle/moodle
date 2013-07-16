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
 * The library file for the static cache store.
 *
 * This file is part of the static cache store, it contains the API for interacting with an instance of the store.
 * This is used as a default cache store within the Cache API. It should never be deleted.
 *
 * @package    cachestore_static
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The static data store class
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class static_data_store extends cache_store {

    /**
     * An array for storage.
     * @var array
     */
    private static $staticstore = array();

    /**
     * Returns a static store by reference... REFERENCE SUPER IMPORTANT.
     *
     * @param string $id
     * @return array
     */
    protected static function &register_store_id($id) {
        if (!array_key_exists($id, self::$staticstore)) {
            self::$staticstore[$id] = array();
        }
        return self::$staticstore[$id];
    }

    /**
     * Flushes the store of all values for belonging to the store with the given id.
     * @param string $id
     */
    protected static function flush_store_by_id($id) {
        unset(self::$staticstore[$id]);
        self::$staticstore[$id] = array();
    }

    /**
     * Flushes all of the values from all stores.
     *
     * @copyright  2012 Sam Hemelryk
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    protected static function flush_store() {
        $ids = array_keys(self::$staticstore);
        unset(self::$staticstore);
        self::$staticstore = array();
        foreach ($ids as $id) {
            self::$staticstore[$id] = array();
        }
    }
}

/**
 * The static store class.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_static extends static_data_store implements cache_is_key_aware, cache_is_searchable {

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
        return self::MODE_REQUEST;
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
        return ($mode === self::MODE_REQUEST);
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
        $this->store = &self::register_store_id($this->storeid);
        $this->ttl = $definition->get_ttl();
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
            if ($this->ttl == 0) {
                return $this->store[$key][0];
            } else if ($this->store[$key][1] >= (cache::now() - $this->ttl)) {
                return $this->store[$key][0];
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
        if ($this->ttl != 0) {
            $maxtime = cache::now() - $this->ttl;
        }

        foreach ($keys as $key) {
            $return[$key] = false;
            if (isset($this->store[$key])) {
                if ($this->ttl == 0) {
                    $return[$key] = $this->store[$key][0];
                } else if ($this->store[$key][1] >= $maxtime) {
                    $return[$key] = $this->store[$key][0];
                }
            }
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
        if ($this->ttl == 0) {
            $this->store[$key][0] = $data;
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
            $this->set($pair['key'], $pair['value']);
            $count++;
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
        $result = isset($this->store[$key]);
        unset($this->store[$key]);
        return $result;
    }

    /**
     * Deletes several keys from the cache in a single action.
     *
     * @param array $keys The keys to delete
     * @return int The number of items successfully deleted.
     */
    public function delete_many(array $keys) {
        $count = 0;
        foreach ($keys as $key) {
            if (isset($this->store[$key])) {
                $count++;
            }
            unset($this->store[$key]);
        }
        return $count;
    }

    /**
     * Purges the cache deleting all items within it.
     *
     * @return boolean True on success. False otherwise.
     */
    public function purge() {
        $this->flush_store_by_id($this->storeid);
        $this->store = &self::register_store_id($this->storeid);
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
        $cache = new cachestore_static('Static store');
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
     * Finds all of the keys being stored in the cache store instance.
     *
     * @return array
     */
    public function find_all() {
        return array_keys($this->store);
    }

    /**
     * Finds all of the keys whose keys start with the given prefix.
     *
     * @param string $prefix
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
}
