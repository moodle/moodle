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
     * The maximum size for the store, or false if there isn't one.
     * @var bool
     */
    protected $maxsize = false;

    /**
     * Where this cache uses simpledata and we don't need to serialize it.
     * @var bool
     */
    protected $simpledata = false;

    /**
     * The number of items currently being stored.
     * @var int
     */
    protected $storecount = 0;

    /**
     * igbinary extension available.
     * @var bool
     */
    protected $igbinaryfound = false;

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
               self::IS_SEARCHABLE +
               self::SUPPORTS_MULTIPLE_IDENTIFIERS +
               self::DEREFERENCES_OBJECTS;
    }

    /**
     * Returns true as this store does support multiple identifiers.
     * (This optional function is a performance optimisation; it must be
     * consistent with the value from get_supported_features.)
     *
     * @return bool true
     */
    public function supports_multiple_identifiers() {
        return true;
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
        $keyarray = $definition->generate_multi_key_parts();
        $this->storeid = $keyarray['mode'].'/'.$keyarray['component'].'/'.$keyarray['area'].'/'.$keyarray['siteidentifier'];
        $this->store = &self::register_store_id($this->storeid);
        $maxsize = $definition->get_maxsize();
        $this->simpledata = $definition->uses_simple_data();
        $this->igbinaryfound = extension_loaded('igbinary');
        if ($maxsize !== null) {
            // Must be a positive int.
            $this->maxsize = abs((int)$maxsize);
            $this->storecount = count($this->store);
        }
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
     * Uses igbinary serializer if igbinary extension is loaded.
     * Fallback to PHP serializer.
     *
     * @param mixed $data
     * The value to be serialized.
     * @return string a string containing a byte-stream representation of
     * value that can be stored anywhere.
     */
    protected function serialize($data) {
        if ($this->igbinaryfound) {
            return igbinary_serialize($data);
        } else {
            return serialize($data);
        }
    }

    /**
     * Uses igbinary unserializer if igbinary extension is loaded.
     * Fallback to PHP unserializer.
     *
     * @param string $str
     * The serialized string.
     * @return mixed The converted value is returned, and can be a boolean,
     * integer, float, string,
     * array or object.
     */
    protected function unserialize($str) {
        if ($this->igbinaryfound) {
            return igbinary_unserialize($str);
        } else {
            return unserialize($str);
        }
    }

    /**
     * Retrieves an item from the cache store given its key.
     *
     * @param string $key The key to retrieve
     * @return mixed The data that was associated with the key, or false if the key did not exist.
     */
    public function get($key) {
        if (!is_array($key)) {
            $key = array('key' => $key);
        }

        $key = $key['key'];
        if (isset($this->store[$key])) {
            if ($this->store[$key]['serialized']) {
                return $this->unserialize($this->store[$key]['data']);
            } else {
                return $this->store[$key]['data'];
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

        foreach ($keys as $key) {
            if (!is_array($key)) {
                $key = array('key' => $key);
            }
            $key = $key['key'];
            $return[$key] = false;
            if (isset($this->store[$key])) {
                if ($this->store[$key]['serialized']) {
                    $return[$key] = $this->unserialize($this->store[$key]['data']);
                } else {
                    $return[$key] = $this->store[$key]['data'];
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
     * @param bool $testmaxsize If set to true then we test the maxsize arg and reduce if required.
     * @return bool True if the operation was a success false otherwise.
     */
    public function set($key, $data, $testmaxsize = true) {
        if (!is_array($key)) {
            $key = array('key' => $key);
        }
        $key = $key['key'];
        $testmaxsize = ($testmaxsize && $this->maxsize !== false);
        if ($testmaxsize) {
            $increment = (!isset($this->store[$key]));
        }

        if ($this->simpledata || is_scalar($data)) {
            $this->store[$key]['data'] = $data;
            $this->store[$key]['serialized'] = false;
        } else {
            $this->store[$key]['data'] = $this->serialize($data);
            $this->store[$key]['serialized'] = true;
        }

        if ($testmaxsize && $increment) {
            $this->storecount++;
            if ($this->storecount > $this->maxsize) {
                $this->reduce_for_maxsize();
            }
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
            if (!is_array($pair['key'])) {
                $pair['key'] = array('key' => $pair['key']);
            }
            // Don't test the maxsize here. We'll do it once when we are done.
            $this->set($pair['key']['key'], $pair['value'], false);
            $count++;
        }
        if ($this->maxsize !== false) {
            $this->storecount += $count;
            if ($this->storecount > $this->maxsize) {
                $this->reduce_for_maxsize();
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
        if (is_array($key)) {
            $key = $key['key'];
        }
        return isset($this->store[$key]);
    }

    /**
     * Returns true if the store contains records for all of the given keys.
     *
     * @param array $keys
     * @return bool
     */
    public function has_all(array $keys) {
        foreach ($keys as $key) {
            if (!is_array($key)) {
                $key = array('key' => $key);
            }
            $key = $key['key'];
            if (!isset($this->store[$key])) {
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
        foreach ($keys as $key) {
            if (!is_array($key)) {
                $key = array('key' => $key);
            }
            $key = $key['key'];

            if (isset($this->store[$key])) {
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
        if (!is_array($key)) {
            $key = array('key' => $key);
        }
        $key = $key['key'];
        $result = isset($this->store[$key]);
        unset($this->store[$key]);
        if ($this->maxsize !== false) {
            $this->storecount--;
        }
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
            if (!is_array($key)) {
                $key = array('key' => $key);
            }
            $key = $key['key'];
            if (isset($this->store[$key])) {
                $count++;
            }
            unset($this->store[$key]);
        }
        if ($this->maxsize !== false) {
            $this->storecount -= $count;
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
        // Don't worry about checking if we're using max size just set it as thats as fast as the check.
        $this->storecount = 0;
        return true;
    }

    /**
     * Reduces the size of the array if maxsize has been hit.
     *
     * This function reduces the size of the store reducing it by 10% of its maxsize.
     * It removes the oldest items in the store when doing this.
     * The reason it does this an doesn't use a least recently used system is purely the overhead such a system
     * requires. The current approach is focused on speed, MUC already adds enough overhead to static/session caches
     * and avoiding more is of benefit.
     *
     * @return int
     */
    protected function reduce_for_maxsize() {
        $diff = $this->storecount - $this->maxsize;
        if ($diff < 1) {
            return 0;
        }
        // Reduce it by an extra 10% to avoid calling this repetitively if we are in a loop.
        $diff += floor($this->maxsize / 10);
        $this->store = array_slice($this->store, $diff, null, true);
        $this->storecount -= $diff;
        return $diff;
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
     * @return cachestore_static
     */
    public static function initialise_test_instance(cache_definition $definition) {
        // Do something here perhaps.
        $cache = new cachestore_static('Static store');
        $cache->initialise($definition);
        return $cache;
    }

    /**
     * Generates the appropriate configuration required for unit testing.
     *
     * @return array Array of unit test configuration data to be used by initialise().
     */
    public static function unit_test_configuration() {
        return array();
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
