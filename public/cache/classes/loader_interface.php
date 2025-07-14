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

namespace core_cache;

/**
 * Cache Loader.
 *
 * This cache loader interface provides the required structure for classes that wish to be interacted with as a
 * means of accessing and interacting with a cache.
 *
 * Can be implemented by any class wishing to be a cache loader.
 * @package core_cache
 * @copyright Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface loader_interface {
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

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(loader_interface::class, \cache_loader::class);
