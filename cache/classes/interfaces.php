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
     * Retrieves the value and actual version for the given key, with at least the required version.
     *
     * If there is no value for the key, or there is a value but it doesn't have the required
     * version, then this function will return false (or throw an exception if you set strictness
     * to MUST_EXIST).
     *
     * This function can be used to make it easier to support localisable caches (where the cache
     * could be stored on a local server as well as a shared cache). Specifying the version means
     * that it will automatically retrieve the correct version if available, either from the local
     * server or [if that has an older version] from the shared server.
     *
     * If the cached version is newer than specified version, it will be returned regardless. For
     * example, if you request version 4, but the locally cached version is 5, it will be returned.
     * If you request version 6, and the locally cached version is 5, then the system will look in
     * higher-level caches (if any); if there still isn't a version 6 or greater, it will return
     * null.
     *
     * You must use this function if you use set_versioned.
     *
     * @param string|int $key The key for the data being requested.
     * @param int $requiredversion Minimum required version of the data
     * @param int $strictness One of IGNORE_MISSING or MUST_EXIST.
     * @param mixed $actualversion If specified, will be set to the actual version number retrieved
     * @return mixed Data from the cache, or false if the key did not exist or was too old
     */
    public function get_versioned($key, int $requiredversion, int $strictness = IGNORE_MISSING, &$actualversion = null);

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
     * Sets the value for the given key with the given version.
     *
     * The cache does not store multiple versions - any existing version will be overwritten with
     * this one. This function should only be used if there is a known 'current version' (e.g.
     * stored in a database table). It only ensures that the cache does not return outdated data.
     *
     * This function can be used to help implement localisable caches (where the cache could be
     * stored on a local server as well as a shared cache). The version will be recorded alongside
     * the item and get_versioned will always return the correct version.
     *
     * The version number must be an integer that always increases. This could be based on the
     * current time, or a stored value that increases by 1 each time it changes, etc.
     *
     * If you use this function you must use get_versioned to retrieve the data.
     *
     * @param string|int $key The key for the data being set.
     * @param int $version Integer for the version of the data
     * @param mixed $data The data to set against the key.
     * @return bool True on success, false otherwise.
     */
    public function set_versioned($key, int $version, $data): bool;

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
     * However this doesn't guarantee consistent access. It will become the responsibility of the calling code to ensure
     * locks are acquired, checked, and released.
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
     * However this doesn't guarantee consistent access. It will become the responsibility of the calling code to ensure
     * locks are acquired, checked, and released.
     *
     * @param string|int $key
     * @return bool True if this code has the lock, false if there is a lock but this code doesn't have it,
     *      null if there is no lock.
     */
    public function check_lock_state($key);

    /**
     * Releases the lock for the given key.
     *
     * Please note that this happens automatically if the cache definition requires locking.
     * it is still made a public method so that adhoc caches can use it if they choose.
     * However this doesn't guarantee consistent access. It will become the responsibility of the calling code to ensure
     * locks are acquired, checked, and released.
     *
     * @param string|int $key
     * @return bool True if the lock has been released, false if there was a problem releasing the lock.
     */
    public function release_lock($key);
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
     * @param string $ownerid The identifier so we can check if we have the lock or if it is someone else.
     *      The use of this property is entirely optional and implementations can act as they like upon it.
     * @return bool True if the lock could be acquired, false otherwise.
     */
    public function acquire_lock($key, $ownerid);

    /**
     * Test if there is already a lock for the given key and if there is whether it belongs to the calling code.
     *
     * @param string $key The key we are locking.
     * @param string $ownerid The identifier so we can check if we have the lock or if it is someone else.
     * @return bool True if this code has the lock, false if there is a lock but this code doesn't have it, null if there
     *      is no lock.
     */
    public function check_lock_state($key, $ownerid);

    /**
     * Releases the lock on the given key.
     *
     * @param string $key The key we are locking.
     * @param string $ownerid The identifier so we can check if we have the lock or if it is someone else.
     *      The use of this property is entirely optional and implementations can act as they like upon it.
     * @return bool True if the lock has been released, false if there was a problem releasing the lock.
     */
    public function release_lock($key, $ownerid);
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
 * Cache store feature: keys are searchable.
 *
 * Cache stores can choose to implement this interface.
 * In order for a store to be usable as a session cache it must implement this interface.
 *
 * @since Moodle 2.4.4
 */
interface cache_is_searchable {
    /**
     * Finds all of the keys being used by the cache store.
     *
     * @return array.
     */
    public function find_all();

    /**
     * Finds all of the keys whose keys start with the given prefix.
     *
     * @param string $prefix
     */
    public function find_by_prefix($prefix);
}

/**
 * Cache store feature: configurable.
 *
 * This feature should be implemented by all cache stores that are configurable when adding an instance.
 * It requires the implementation of methods required to convert form data into the a configuration array for the
 * store instance, and then the reverse converting configuration data into an array that can be used to set the
 * data for the edit form.
 *
 * Can be implemented by classes already implementing cache_store.
 */
interface cache_is_configurable {

    /**
     * Given the data from the add instance form this function creates a configuration array.
     *
     * @param stdClass $data
     * @return array
     */
    public static function config_get_configuration_array($data);

    /**
     * Allows the cache store to set its data against the edit form before it is shown to the user.
     *
     * @param moodleform $editform
     * @param array $config
     */
    public static function config_set_edit_form_data(moodleform $editform, array $config);
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
 * Versionable cache data source.
 *
 * This interface extends the main cache data source interface to add an extra required method if
 * the data source is to be used for a versioned cache.
 *
 * @package core_cache
 */
interface cache_data_source_versionable extends cache_data_source {
    /**
     * Loads the data for the key provided ready formatted for caching.
     *
     * If there is no data for that key, or if the data for the required key has an older version
     * than the specified $requiredversion, then this returns null.
     *
     * If there is data then $actualversion should be set to the actual version number retrieved
     * (may be the same as $requiredversion or newer).
     *
     * @param string|int $key The key to load.
     * @param int $requiredversion Minimum required version
     * @param mixed $actualversion Should be set to the actual version number retrieved
     * @return mixed What ever data should be returned, or false if it can't be loaded.
     */
    public function load_for_cache_versioned($key, int $requiredversion, &$actualversion);
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

/**
 * Cache lock interface
 *
 * This interface needs to be inherited by all cache lock plugins.
 */
interface cache_lock_interface {
    /**
     * Constructs an instance of the cache lock given its name and its configuration data
     *
     * @param string $name The unique name of the lock instance
     * @param array $configuration
     */
    public function __construct($name, array $configuration = array());

    /**
     * Acquires a lock on a given key.
     *
     * @param string $key The key to acquire a lock for.
     * @param string $ownerid An unique identifier for the owner of this lock. It is entirely optional for the cache lock plugin
     *      to use this. Each implementation can decide for themselves.
     * @param bool $block If set to true the application will wait until a lock can be acquired
     * @return bool True if the lock can be acquired false otherwise.
     */
    public function lock($key, $ownerid, $block = false);

    /**
     * Releases the lock held on a certain key.
     *
     * @param string $key The key to release the lock for.
     * @param string $ownerid An unique identifier for the owner of this lock. It is entirely optional for the cache lock plugin
     *      to use this. Each implementation can decide for themselves.
     * @param bool $forceunlock If set to true the lock will be removed if it exists regardless of whether or not we own it.
     */
    public function unlock($key, $ownerid, $forceunlock = false);

    /**
     * Checks the state of the given key.
     *
     * Returns true if the key is locked and belongs to the ownerid.
     * Returns false if the key is locked but does not belong to the ownerid.
     * Returns null if there is no lock
     *
     * @param string $key The key we are checking for.
     * @param string $ownerid The identifier so we can check if we have the lock or if it is someone else.
     * @return bool True if this code has the lock, false if there is a lock but this code doesn't have it, null if there
     *      is no lock.
     */
    public function check_state($key, $ownerid);

    /**
     * Cleans up any left over locks.
     *
     * This function MUST clean up any locks that have been acquired and not released during processing.
     * Although the situation of acquiring a lock and not releasing it should be insanely rare we need to deal with it.
     * Things such as unfortunate timeouts etc could cause this situation.
     */
    public function __destruct();
}
