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
     * Initialises a test instance for unit tests.
     *
     * This differs from initialise_test_instance in that it doesn't rely on interacting with the config table.
     *
     * @since 2.8
     * @param cache_definition $definition
     * @return cache_store|false
     */
    public static function initialise_unit_test_instance(cache_definition $definition);
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
     * Performs any necessary clean up when the store instance is being deleted.
     *
     * @deprecated since 2.5
     */
    public function cleanup() {
        debugging('This function has been renamed to instance_deleted. Please update your calls.', DEBUG_DEVELOPER);
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
     * Initialises a test instance for unit tests.
     *
     * This differs from initialise_test_instance in that it doesn't rely on interacting with the config table.
     * By default however it calls initialise_test_instance to support backwards compatibility.
     *
     * @since 2.8
     * @param cache_definition $definition
     * @return cache_store|false
     */
    public static function initialise_unit_test_instance(cache_definition $definition) {
        return static::initialise_test_instance($definition);
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
}
