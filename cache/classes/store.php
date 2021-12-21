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
 * Cache store - base class
 *
 * This file is part of Moodle's cache API, affectionately called MUC.
 * It contains the components that are required in order to use caching.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Cache store interface.
 *
 * This interface defines the static methods that must be implemented by every cache store plugin.
 * To ensure plugins implement this class the abstract cache_store class implements this interface.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface cache_store_interface {
    /**
     * Static method to check if the store requirements are met.
     *
     * @return bool True if the stores software/hardware requirements have been met and it can be used. False otherwise.
     */
    public static function are_requirements_met();

    /**
     * Static method to check if a store is usable with the given mode.
     *
     * @param int $mode One of cache_store::MODE_*
     */
    public static function is_supported_mode($mode);

    /**
     * Returns the supported features as a binary flag.
     *
     * @param array $configuration The configuration of a store to consider specifically.
     * @return int The supported features.
     */
    public static function get_supported_features(array $configuration = array());

    /**
     * Returns the supported modes as a binary flag.
     *
     * @param array $configuration The configuration of a store to consider specifically.
     * @return int The supported modes.
     */
    public static function get_supported_modes(array $configuration = array());

    /**
     * Generates an instance of the cache store that can be used for testing.
     *
     * Returns an instance of the cache store, or false if one cannot be created.
     *
     * @param cache_definition $definition
     * @return cache_store|false
     */
    public static function initialise_test_instance(cache_definition $definition);

    /**
     * Generates the appropriate configuration required for unit testing.
     *
     * @return array Array of unit test configuration data to be used by initialise().
     */
    public static function unit_test_configuration();
}

/**
 * Abstract cache store class.
 *
 * All cache store plugins must extend this base class.
 * It lays down the foundation for what is required of a cache store plugin.
 *
 * @since Moodle 2.4
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class cache_store implements cache_store_interface {

    // Constants for features a cache store can support

    /**
     * Supports multi-part keys
     */
    const SUPPORTS_MULTIPLE_IDENTIFIERS = 1;
    /**
     * Ensures data remains in the cache once set.
     */
    const SUPPORTS_DATA_GUARANTEE = 2;
    /**
     * Supports a native ttl system.
     */
    const SUPPORTS_NATIVE_TTL = 4;

    /**
     * The cache is searchable by key.
     */
    const IS_SEARCHABLE = 8;

    /**
     * The cache store dereferences objects.
     *
     * When set, loaders will assume that all data coming from this store has already had all references
     * resolved.  So even for complex object structures it will not try to remove references again.
     */
    const DEREFERENCES_OBJECTS = 16;

    // Constants for the modes of a cache store

    /**
     * Application caches. These are shared caches.
     */
    const MODE_APPLICATION = 1;
    /**
     * Session caches. Just access to the PHP session.
     */
    const MODE_SESSION = 2;
    /**
     * Request caches. Static caches really.
     */
    const MODE_REQUEST = 4;
    /**
     * Static caches.
     */
    const STATIC_ACCEL = '** static accel. **';

    /**
     * Returned from get_last_io_bytes if this cache store doesn't support counting bytes read/sent.
     */
    const IO_BYTES_NOT_SUPPORTED = -1;

    /**
     * Constructs an instance of the cache store.
     *
     * The constructor should be responsible for creating anything needed by the store that is not
     * specific to a definition.
     * Tasks such as opening a connection to check it is available are best done here.
     * Tasks that are definition specific such as creating a storage area for the definition data
     * or creating key tables and indexs are best done within the initialise method.
     *
     * Once a store has been constructed the cache API will check it is ready to be intialised with
     * a definition by called $this->is_ready().
     * If the setup of the store failed (connection could not be established for example) then
     * that method should return false so that the store instance is not selected for use.
     *
     * @param string $name The name of the cache store
     * @param array $configuration The configuration for this store instance.
     */
    abstract public function __construct($name, array $configuration = array());

    /**
     * Returns the name of this store instance.
     * @return string
     */
    abstract public function my_name();

    /**
     * Initialises a new instance of the cache store given the definition the instance is to be used for.
     *
     * This function should be used to run any definition specific setup the store instance requires.
     * Tasks such as creating storage areas, or creating indexes are best done here.
     *
     * Its important to note that the initialise method is expected to always succeed.
     * If there are setup tasks that may fail they should be done within the __construct method
     * and should they fail is_ready should return false.
     *
     * @param cache_definition $definition
     */
    abstract public function initialise(cache_definition $definition);

    /**
     * Returns true if this cache store instance has been initialised.
     * @return bool
     */
    abstract public function is_initialised();

    /**
     * Returns true if this cache store instance is ready to use.
     * @return bool
     */
    public function is_ready() {
        return forward_static_call(array($this, 'are_requirements_met'));
    }

    /**
     * Retrieves an item from the cache store given its key.
     *
     * @param string $key The key to retrieve
     * @return mixed The data that was associated with the key, or false if the key did not exist.
     */
    abstract public function get($key);

    /**
     * Retrieves several items from the cache store in a single transaction.
     *
     * If not all of the items are available in the cache then the data value for those that are missing will be set to false.
     *
     * @param array $keys The array of keys to retrieve
     * @return array An array of items from the cache. There will be an item for each key, those that were not in the store will
     *      be set to false.
     */
    abstract public function get_many($keys);

    /**
     * Sets an item in the cache given its key and data value.
     *
     * @param string $key The key to use.
     * @param mixed $data The data to set.
     * @return bool True if the operation was a success false otherwise.
     */
    abstract public function set($key, $data);

    /**
     * Sets many items in the cache in a single transaction.
     *
     * @param array $keyvaluearray An array of key value pairs. Each item in the array will be an associative array with two
     *      keys, 'key' and 'value'.
     * @return int The number of items successfully set. It is up to the developer to check this matches the number of items
     *      sent ... if they care that is.
     */
    abstract public function set_many(array $keyvaluearray);

    /**
     * Deletes an item from the cache store.
     *
     * @param string $key The key to delete.
     * @return bool Returns true if the operation was a success, false otherwise.
     */
    abstract public function delete($key);

    /**
     * Deletes several keys from the cache in a single action.
     *
     * @param array $keys The keys to delete
     * @return int The number of items successfully deleted.
     */
    abstract public function delete_many(array $keys);

    /**
     * Purges the cache deleting all items within it.
     *
     * @return boolean True on success. False otherwise.
     */
    abstract public function purge();

    /**
     * @deprecated since 2.5
     * @see \cache_store::instance_deleted()
     */
    public function cleanup() {
        throw new coding_exception('cache_store::cleanup() can not be used anymore.' .
            ' Please use cache_store::instance_deleted() instead.');
    }

    /**
     * Performs any necessary operation when the store instance has been created.
     *
     * @since Moodle 2.5
     */
    public function instance_created() {
        // By default, do nothing.
    }

    /**
     * Performs any necessary operation when the store instance is being deleted.
     *
     * This method may be called before the store has been initialised.
     *
     * @since Moodle 2.5
     * @see cleanup()
     */
    public function instance_deleted() {
        if (method_exists($this, 'cleanup')) {
            // There used to be a legacy function called cleanup, it was renamed to instance delete.
            // To be removed in 2.6.
            $this->cleanup();
        }
    }

    /**
     * Returns true if the user can add an instance of the store plugin.
     *
     * @return bool
     */
    public static function can_add_instance() {
        return true;
    }

    /**
     * Returns true if the store instance guarantees data.
     *
     * @return bool
     */
    public function supports_data_guarantee() {
        return $this::get_supported_features() & self::SUPPORTS_DATA_GUARANTEE;
    }

    /**
     * Returns true if the store instance supports multiple identifiers.
     *
     * @return bool
     */
    public function supports_multiple_identifiers() {
        return $this::get_supported_features() & self::SUPPORTS_MULTIPLE_IDENTIFIERS;
    }

    /**
     * Returns true if the store instance supports native ttl.
     *
     * @return bool
     */
    public function supports_native_ttl() {
        return $this::get_supported_features() & self::SUPPORTS_NATIVE_TTL;
    }

    /**
     * Returns true if the store instance is searchable.
     *
     * @return bool
     */
    public function is_searchable() {
        return in_array('cache_is_searchable', class_implements($this));
    }

    /**
     * Returns true if the store automatically dereferences objects.
     *
     * @return bool
     */
    public function supports_dereferencing_objects() {
        return $this::get_supported_features() & self::DEREFERENCES_OBJECTS;
    }

    /**
     * Creates a clone of this store instance ready to be initialised.
     *
     * This method is used so that a cache store needs only be constructed once.
     * Future requests for an instance of the store will be given a cloned instance.
     *
     * If you are writing a cache store that isn't compatible with the clone operation
     * you can override this method to handle any situations you want before cloning.
     *
     * @param array $details An array containing the details of the store from the cache config.
     * @return cache_store
     */
    public function create_clone(array $details = array()) {
        // By default we just run clone.
        // Any stores that have an issue with this will need to override the create_clone method.
        return clone($this);
    }

    /**
     * Can be overridden to return any warnings this store instance should make to the admin.
     *
     * This should be used to notify things like configuration conflicts etc.
     * The warnings returned here will be displayed on the cache configuration screen.
     *
     * @return string[] An array of warning strings from the store instance.
     */
    public function get_warnings() {
        return array();
    }

    /**
     * Estimates the storage size used within this cache if the given value is stored with the
     * given key.
     *
     * This function is not exactly accurate; it does not necessarily take into account all the
     * overheads involved. It is only intended to give a good idea of the relative size of
     * different caches.
     *
     * The default implementation serializes both key and value and sums the lengths (as a rough
     * estimate which is probably good enough for everything unless the cache offers compression).
     *
     * @param mixed $key Key
     * @param mixed $value Value
     * @return int Size in bytes
     */
    public function estimate_stored_size($key, $value): int {
        return strlen(serialize($key)) + strlen(serialize($value));
    }

    /**
     * Gets the amount of memory/storage currently used by this cache store if known.
     *
     * This value should be obtained quickly from the store itself, if available.
     *
     * This is the total memory usage of the entire store, not for ther specific cache in question.
     *
     * Where not supported (default), will always return null.
     *
     * @return int|null Amount of memory used in bytes or null
     */
    public function store_total_size(): ?int {
        return null;
    }

    /**
     * Gets the amount of memory used by this specific cache within the store, if known.
     *
     * This function may be slow and should not be called in normal usage, only for administration
     * pages. The value is usually an estimate, and may not be available at all.
     *
     * When estimating, a number of sample items will be used for the estimate. If set to 50
     * (default), then this function will retrieve 50 random items and use that to estimate the
     * total size.
     *
     * The return value has the following fields:
     * - supported (true if any other values are completed)
     * - items (number of items)
     * - mean (mean size of one item in bytes)
     * - sd (standard deviation of item size in bytes, based on sample)
     * - margin (95% confidence margin for mean - will be 0 if exactly computed)
     *
     * @param int $samplekeys Number of samples to use
     * @return stdClass Object with information about the store size
     */
    public function cache_size_details(int $samplekeys = 50): stdClass {
        $result = (object)[
            'supported' => false,
            'items' => 0,
            'mean' => 0,
            'sd' => 0,
            'margin' => 0
        ];

        // If this cache isn't searchable, we don't know the answer.
        if (!$this->is_searchable()) {
            return $result;
        }
        $result->supported = true;

        // Get all the keys for the cache.
        $keys = $this->find_all();
        $result->items = count($keys);

        // Don't do anything else if there are no items.
        if ($result->items === 0) {
            return $result;
        }

        // Select N random keys.
        $exact = false;
        if ($result->items <= $samplekeys) {
            $samples = $keys;
            $exact = true;
        } else {
            $indexes = array_rand($keys, $samplekeys);
            $samples = [];
            foreach ($indexes as $index) {
                $samples[] = $keys[$index];
            }
        }

        // Get the random items from cache and estimate the size of each.
        $sizes = [];
        foreach ($samples as $samplekey) {
            $value = $this->get($samplekey);
            $sizes[] = $this->estimate_stored_size($samplekey, $value);
        }
        $number = count($sizes);

        // Calculate the mean and standard deviation.
        $result->mean = array_sum($sizes) / $number;
        $squarediff = 0;
        foreach ($sizes as $size) {
            $squarediff += ($size - $result->mean) ** 2;
        }
        $squarediff /= $number;
        $result->sd = sqrt($squarediff);

        // If it's not exact, also calculate the confidence interval.
        if (!$exact) {
            // 95% confidence has a Z value of 1.96.
            $result->margin = (1.96 * $result->sd) / sqrt($number);
        }

        return $result;
    }

    /**
     * Returns true if this cache store instance is both suitable for testing, and ready for testing.
     *
     * Cache stores that support being used as the default store for unit and acceptance testing should
     * override this function and return true if there requirements have been met.
     *
     * @return bool
     */
    public static function ready_to_be_used_for_testing() {
        return false;
    }

    /**
     * Gets the number of bytes read from or written to cache as a result of the last action.
     *
     * This includes calls to the functions get(), get_many(), set(), and set_many(). The number
     * is reset by calling any of these functions.
     *
     * This should be the actual number of bytes of the value read from or written to cache,
     * giving an impression of the network or other load. It will not be exactly the same amount
     * as netowrk traffic because of protocol overhead, key text, etc.
     *
     * If not supported, returns IO_BYTES_NOT_SUPPORTED.
     *
     * @return int Bytes read (or 0 if none/not supported)
     * @since Moodle 4.0
     */
    public function get_last_io_bytes(): int {
        return self::IO_BYTES_NOT_SUPPORTED;
    }
}
