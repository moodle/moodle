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
 * Cache API interfaces
 *
 * This file is part of Moodle's cache API, affectionately called MUC.
 * It contains the components that are requried in order to use caching.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Cache Loader.
 *
 * This cache loader interface provides the required structure for classes that wish to be interacted with as a
 * means of accessing and interacting with a cache.
 *
 * Can be implemented by any class wishing to be a cache loader.
 */
interface cache_loader {

    /**
     * Retrieves the value for the given key from the cache.
     *
     * @param string|int $key The key for the data being requested.
     * @param int $strictness One of IGNORE_MISSING or MUST_EXIST.
     * @return mixed The data retrieved from the cache, or false if the key did not exist within the cache.
     *      If MUST_EXIST was used then an exception will be thrown if the key does not exist within the cache.
     */
    public function get($key, $strictness = IGNORE_MISSING);

    /**
     * Retrieves an array of values for an array of keys.
     *
     * Using this function comes with potential performance implications.
     * Not all cache stores will support get_many/set_many operations and in order to replicate this functionality will call
     * the equivalent singular method for each item provided.
     * This should not deter you from using this function as there is a performance benefit in situations where the cache
     * store does support it, but you should be aware of this fact.
     *
     * @param array $keys The keys of the data being requested.
     * @param int $strictness One of IGNORE_MISSING or MUST_EXIST.
     * @return array An array of key value pairs for the items that could be retrieved from the cache.
     *      If MUST_EXIST was used and not all keys existed within the cache then an exception will be thrown.
     *      Otherwise any key that did not exist will have a data value of false within the results.
     */
    public function get_many(array $keys, $strictness = IGNORE_MISSING);

    /**
     * Sends a key => value pair to the cache.
     *
     * <code>
     * // This code will add four entries to the cache, one for each url.
     * $cache->set('main', 'http://moodle.org');
     * $cache->set('docs', 'http://docs.moodle.org');
     * $cache->set('tracker', 'http://tracker.moodle.org');
     * $cache->set('qa', 'http://qa.moodle.net');
     * </code>
     *
     * @param string|int $key The key for the data being requested.
     * @param mixed $data The data to set against the key.
     * @return bool True on success, false otherwise.
     */
    public function set($key, $data);

    /**
     * Sends several key => value pairs to the cache.
     *
     * Using this function comes with potential performance implications.
     * Not all cache stores will support get_many/set_many operations and in order to replicate this functionality will call
     * the equivalent singular method for each item provided.
     * This should not deter you from using this function as there is a performance benefit in situations where the cache store
     * does support it, but you should be aware of this fact.
     *
     * <code>
     * // This code will add four entries to the cache, one for each url.
     * $cache->set_many(array(
     *     'main' => 'http://moodle.org',
     *     'docs' => 'http://docs.moodle.org',
     *     'tracker' => 'http://tracker.moodle.org',
     *     'qa' => ''http://qa.moodle.net'
     * ));
     * </code>
     *
     * @param array $keyvaluearray An array of key => value pairs to send to the cache.
     * @return int The number of items successfully set. It is up to the developer to check this matches the number of items.
     *      ... if they care that is.
     */
    public function set_many(array $keyvaluearray);

    /**
     * Test is a cache has a key.
     *
     * The use of the has methods is strongly discouraged. In a high load environment the cache may well change between the
     * test and any subsequent action (get, set, delete etc).
     * Instead it is recommended to write your code in such a way they it performs the following steps:
     * <ol>
     * <li>Attempt to retrieve the information.</li>
     * <li>Generate the information.</li>
     * <li>Attempt to set the information</li>
     * </ol>
     *
     * Its also worth mentioning that not all stores support key tests.
     * For stores that don't support key tests this functionality is mimicked by using the equivalent get method.
     * Just one more reason you should not use these methods unless you have a very good reason to do so.
     *
     * @param string|int $key
     * @return bool True if the cache has the requested key, false otherwise.
     */
    public function has($key);

    /**
     * Test if a cache has at least one of the given keys.
     *
     * It is strongly recommended to avoid the use of this function if not absolutely required.
     * In a high load environment the cache may well change between the test and any subsequent action (get, set, delete etc).
     *
     * Its also worth mentioning that not all stores support key tests.
     * For stores that don't support key tests this functionality is mimicked by using the equivalent get method.
     * Just one more reason you should not use these methods unless you have a very good reason to do so.
     *
     * @param array $keys
     * @return bool True if the cache has at least one of the given keys
     */
    public function has_any(array $keys);

    /**
     * Test is a cache has all of the given keys.
     *
     * It is strongly recommended to avoid the use of this function if not absolutely required.
     * In a high load environment the cache may well change between the test and any subsequent action (get, set, delete etc).
     *
     * Its also worth mentioning that not all stores support key tests.
     * For stores that don't support key tests this functionality is mimicked by using the equivalent get method.
     * Just one more reason you should not use these methods unless you have a very good reason to do so.
     *
     * @param array $keys
     * @return bool True if the cache has all of the given keys, false otherwise.
     */
    public function has_all(array $keys);

    /**
     * Delete the given key from the cache.
     *
     * @param string|int $key The key to delete.
     * @param bool $recurse When set to true the key will also be deleted from all stacked cache loaders and their stores.
     *     This happens by default and ensure that all the caches are consistent. It is NOT recommended to change this.
     * @return bool True of success, false otherwise.
     */
    public function delete($key, $recurse = true);

    /**
     * Delete all of the given keys from the cache.
     *
     * @param array $keys The key to delete.
     * @param bool $recurse When set to true the key will also be deleted from all stacked cache loaders and their stores.
     *     This happens by default and ensure that all the caches are consistent. It is NOT recommended to change this.
     * @return int The number of items successfully deleted.
     */
    public function delete_many(array $keys, $recurse = true);
}

/**
 * Cache Loader supporting locking.
 *
 * This interface should be given to classes already implementing cache_loader that also wish to support locking.
 * It outlines the required structure for utilising locking functionality when using a cache.
 *
 * Can be implemented by any class already implementing the cache_loader interface.
 */
interface cache_loader_with_locking {

    /**
     * Acquires a lock for the given key.
     *
     * Please note that this happens automatically if the cache definition requires locking.
     * it is still made a public method so that adhoc caches can use it if they choose.
     * However this doesn't guarantee consistent access. It will become the reponsiblity of the calling code to ensure locks
     * are acquired, checked, and released.
     *
     * @param string|int $key
     * @return bool True if the lock could be acquired, false otherwise.
     */
    public function acquire_lock($key);

    /**
     * Checks if the cache loader owns the lock for the given key.
     *
     * Please note that this happens automatically if the cache definition requires locking.
     * it is still made a public method so that adhoc caches can use it if they choose.
     * However this doesn't guarantee consistent access. It will become the reponsiblity of the calling code to ensure locks
     * are acquired, checked, and released.
     *
     * @param string|int $key
     * @return bool True if this code has the lock, false if there is a lock but this code doesn't have it,
     *      null if there is no lock.
     */
    public function has_lock($key);

    /**
     * Releases the lock for the given key.
     *
     * Please note that this happens automatically if the cache definition requires locking.
     * it is still made a public method so that adhoc caches can use it if they choose.
     * However this doesn't guarantee consistent access. It will become the reponsiblity of the calling code to ensure locks
     * are acquired, checked, and released.
     *
     * @param string|int $key
     * @return bool True if the lock has been released, false if there was a problem releasing the lock.
     */
    public function release_lock($key);
}

/**
 * Cache store.
 *
 * This interface outlines the requirements for a cache store plugin.
 * It must be implemented by all such plugins and provides a reference to interacting with cache stores.
 *
 * Must be implemented by all cache store plugins.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface cache_store {

    /**#@+
     * Constants for features a cache store can support
     */
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
    /**#@-*/

    /**#@+
     * Constants for the modes of a cache store
     */
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
    /**#@-*/

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
     * Returns true if this cache store instance supports multiple identifiers.
     *
     * @return bool
     */
    public function supports_multiple_indentifiers();

    /**
     * Returns true if this cache store instance promotes data guarantee.
     *
     * @return bool
     */
    public function supports_data_guarantee();

    /**
     * Returns true if this cache store instance supports ttl natively.
     *
     * @return bool
     */
    public function supports_native_ttl();

    /**
     * Used to control the ability to add an instance of this store through the admin interfaces.
     *
     * @return bool True if the user can add an instance, false otherwise.
     */
    public static function can_add_instance();

    /**
     * Constructs an instance of the cache store.
     *
     * This method should not create connections or perform and processing, it should be used
     *
     * @param string $name The name of the cache store
     * @param array $configuration The configuration for this store instance.
     */
    public function __construct($name, array $configuration = array());

    /**
     * Initialises a new instance of the cache store given the definition the instance is to be used for.
     *
     * This function should prepare any given connections etc.
     *
     * @param cache_definition $definition
     */
    public function initialise(cache_definition $definition);

    /**
     * Returns true if this cache store instance has been initialised.
     * @return bool
     */
    public function is_initialised();

    /**
     * Returns true if this cache store instance is ready to use.
     * @return bool
     */
    public function is_ready();

    /**
     * Retrieves an item from the cache store given its key.
     *
     * @param string $key The key to retrieve
     * @return mixed The data that was associated with the key, or false if the key did not exist.
     */
    public function get($key);

    /**
     * Retrieves several items from the cache store in a single transaction.
     *
     * If not all of the items are available in the cache then the data value for those that are missing will be set to false.
     *
     * @param array $keys The array of keys to retrieve
     * @return array An array of items from the cache. There will be an item for each key, those that were not in the store will
     *      be set to false.
     */
    public function get_many($keys);

    /**
     * Sets an item in the cache given its key and data value.
     *
     * @param string $key The key to use.
     * @param mixed $data The data to set.
     * @return bool True if the operation was a success false otherwise.
     */
    public function set($key, $data);

    /**
     * Sets many items in the cache in a single transaction.
     *
     * @param array $keyvaluearray An array of key value pairs. Each item in the array will be an associative array with two
     *      keys, 'key' and 'value'.
     * @return int The number of items successfully set. It is up to the developer to check this matches the number of items
     *      sent ... if they care that is.
     */
    public function set_many(array $keyvaluearray);

    /**
     * Deletes an item from the cache store.
     *
     * @param string $key The key to delete.
     * @return bool Returns true if the operation was a success, false otherwise.
     */
    public function delete($key);

    /**
     * Deletes several keys from the cache in a single action.
     *
     * @param array $keys The keys to delete
     * @return int The number of items successfully deleted.
     */
    public function delete_many(array $keys);

    /**
     * Purges the cache deleting all items within it.
     *
     * @return boolean True on success. False otherwise.
     */
    public function purge();

    /**
     * Performs any necessary clean up when the store instance is being deleted.
     */
    public function cleanup();

    /**
     * Generates an instance of the cache store that can be used for testing.
     *
     * Returns an instance of the cache store, or false if one cannot be created.
     *
     * @param cache_definition $definition
     * @return cache_store|false
     */
    public static function initialise_test_instance(cache_definition $definition);
}

/**
 * Cache store feature: locking
 *
 * This is a feature that cache stores can implement if they wish to support locking themselves rather
 * than having the cache loader handle it for them.
 *
 * Can be implemented by classes already implementing cache_store.
 */
interface cache_is_lockable {

    /**
     * Acquires a lock on the given key for the given identifier.
     *
     * @param string $key The key we are locking.
     * @param string $identifier The identifier so we can check if we have the lock or if it is someone else.
     * @return bool True if the lock could be acquired, false otherwise.
     */
    public function acquire_lock($key, $identifier);

    /**
     * Test if there is already a lock for the given key and if there is whether it belongs to the calling code.
     *
     * @param string $key The key we are locking.
     * @param string $identifier The identifier so we can check if we have the lock or if it is someone else.
     * @return bool True if this code has the lock, false if there is a lock but this code doesn't have it, null if there
     *      is no lock.
     */
    public function has_lock($key, $identifier);

    /**
     * Releases the lock on the given key.
     *
     * @param string $key The key we are locking.
     * @param string $identifier The identifier so we can check if we have the lock or if it is someone else.
     * @return bool True if the lock has been released, false if there was a problem releasing the lock.
     */
    public function release_lock($key, $identifier);
}

/**
 * Cache store feature: key awareness.
 *
 * This is a feature that cache stores and cache loaders can both choose to implement.
 * If a cache store implements this then it will be made responsible for tests for items within the cache.
 * If the cache store being used doesn't implement this then it will be the responsibility of the cache loader to use the
 * equivalent get methods to mimick the functionality of these tests.
 *
 * Cache stores should only override these methods if they natively support such features or if they have a better performing
 * means of performing these tests than the handling that would otherwise take place in the cache_loader.
 *
 * Can be implemented by classes already implementing cache_store.
 */
interface cache_is_key_aware {

    /**
     * Test is a cache has a key.
     *
     * The use of the has methods is strongly discouraged. In a high load environment the cache may well change between the
     * test and any subsequent action (get, set, delete etc).
     * Instead it is recommended to write your code in such a way they it performs the following steps:
     * <ol>
     * <li>Attempt to retrieve the information.</li>
     * <li>Generate the information.</li>
     * <li>Attempt to set the information</li>
     * </ol>
     *
     * Its also worth mentioning that not all stores support key tests.
     * For stores that don't support key tests this functionality is mimicked by using the equivalent get method.
     * Just one more reason you should not use these methods unless you have a very good reason to do so.
     *
     * @param string|int $key
     * @return bool True if the cache has the requested key, false otherwise.
     */
    public function has($key);

    /**
     * Test if a cache has at least one of the given keys.
     *
     * It is strongly recommended to avoid the use of this function if not absolutely required.
     * In a high load environment the cache may well change between the test and any subsequent action (get, set, delete etc).
     *
     * Its also worth mentioning that not all stores support key tests.
     * For stores that don't support key tests this functionality is mimicked by using the equivalent get method.
     * Just one more reason you should not use these methods unless you have a very good reason to do so.
     *
     * @param array $keys
     * @return bool True if the cache has at least one of the given keys
     */
    public function has_any(array $keys);

    /**
     * Test is a cache has all of the given keys.
     *
     * It is strongly recommended to avoid the use of this function if not absolutely required.
     * In a high load environment the cache may well change between the test and any subsequent action (get, set, delete etc).
     *
     * Its also worth mentioning that not all stores support key tests.
     * For stores that don't support key tests this functionality is mimicked by using the equivalent get method.
     * Just one more reason you should not use these methods unless you have a very good reason to do so.
     *
     * @param array $keys
     * @return bool True if the cache has all of the given keys, false otherwise.
     */
    public function has_all(array $keys);
}

/**
 * Cache Data Source.
 *
 * The cache data source interface can be implemented by any class within Moodle.
 * If implemented then the class can be reference in a cache definition and will be used to load information that cannot be
 * retrieved from the cache. As part of its retrieval that information will also be loaded into the cache.
 *
 * This allows developers to created a complete cache solution that can be used through code ensuring consistent cache
 * interaction and loading. Allowing them in turn to centralise code and help keeps things more easily maintainable.
 *
 * Can be implemented by any class.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface cache_data_source {

    /**
     * Returns an instance of the data source class that the cache can use for loading data using the other methods
     * specified by this interface.
     *
     * @param cache_definition $definition
     * @return object
     */
    public static function get_instance_for_cache(cache_definition $definition);

    /**
     * Loads the data for the key provided ready formatted for caching.
     *
     * @param string|int $key The key to load.
     * @return mixed What ever data should be returned, or false if it can't be loaded.
     */
    public function load_for_cache($key);

    /**
     * Loads several keys for the cache.
     *
     * @param array $keys An array of keys each of which will be string|int.
     * @return array An array of matching data items.
     */
    public function load_many_for_cache(array $keys);
}

/**
 * Cacheable object.
 *
 * This interface can be implemented by any class that is going to be passed into a cache and allows it to take control of the
 * structure and the information about to be cached, as well as how to deal with it when it is retrieved from a cache.
 * Think of it like serialisation and the __sleep and __wakeup methods.
 * This is used because cache stores are responsible for how they interact with data and what they do when storing it. This
 * interface ensures there is always a guaranteed action.
 */
interface cacheable_object {

    /**
     * Prepares the object for caching. Works like the __sleep method.
     *
     * @return mixed The data to cache, can be anything except a class that implements the cacheable_object... that would
     *      be dumb.
     */
    public function prepare_to_cache();

    /**
     * Takes the data provided by prepare_to_cache and reinitialises an instance of the associated from it.
     *
     * @param mixed $data
     * @return object The instance for the given data.
     */
    public static function wake_from_cache($data);
}
