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

use core\exception\coding_exception;

/**
 * A session cache.
 *
 * This class is used for session caches returned by the cache::make methods.
 *
 * It differs from the application loader in a couple of noteable ways:
 *    1. Sessions are always expected to exist.
 *       Because of this we don't ever use the static acceleration array.
 *    2. Session data for a loader instance (store + definition) is consolidate into a
 *       single array for storage within the store.
 *       Along with this we embed a lastaccessed time with the data. This way we can
 *       check sessions for a last access time.
 *    3. Session stores are required to support key searching and must
 *       implement searchable_cache_interface. This ensures stores used for the cache can be
 *       targetted for garbage collection of session data.
 *
 * This cache class should never be interacted with directly. Instead you should always use the cache::make methods.
 * It is technically possible to call those methods through this class however there is no guarantee that you will get an
 * instance of this class back again.
 *
 * @todo we should support locking in the session as well. Should be pretty simple to set up.
 *
 * @internal don't use me directly.
 * @method store|searchable_cache_interface get_store() Returns the cache store which must implement
 *                                                      both searchable_cache_interface.
 *
 * @package    core_cache
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class session_cache extends cache {
    /**
     * The user the session has been established for.
     * @var int
     */
    protected static $loadeduserid = null;

    /**
     * The userid this cache is currently using.
     * @var int
     */
    protected $currentuserid = null;

    /**
     * The session id we are currently using.
     * @var array
     */
    protected $sessionid = null;

    /**
     * The session data for the above session id.
     * @var array
     */
    protected $session = null;

    /**
     * Constant used to prefix keys.
     */
    const KEY_PREFIX = 'sess_';

    /**
     * This is the key used to track last access.
     */
    const LASTACCESS = '__lastaccess__';

    /**
     * Override the cache::construct method.
     *
     * This function gets overriden so that we can process any invalidation events if need be.
     * If the definition doesn't have any invalidation events then this occurs exactly as it would for the cache class.
     * Otherwise we look at the last invalidation time and then check the invalidation data for events that have occured
     * between then now.
     *
     * You should not call this method from your code, instead you should use the cache::make methods.
     *
     * @param definition $definition
     * @param store $store
     * @param loader_interface|data_source_interface $loader
     */
    public function __construct(definition $definition, store $store, $loader = null) {
        // First up copy the loadeduserid to the current user id.
        $this->currentuserid = self::$loadeduserid;
        $this->set_session_id();
        parent::__construct($definition, $store, $loader);

        // This will trigger check tracked user. If this gets removed a call to that will need to be added here in its place.
        $this->set(self::LASTACCESS, cache::now());

        $this->handle_invalidation_events();
    }

    /**
     * Sets the session id for the loader.
     */
    protected function set_session_id() {
        $this->sessionid = preg_replace('#[^a-zA-Z0-9_]#', '_', session_id());
    }

    /**
     * Returns the prefix used for all keys.
     * @return string
     */
    protected function get_key_prefix() {
        return 'u' . $this->currentuserid . '_' . $this->sessionid;
    }

    /**
     * Parses the key turning it into a string (or array is required) suitable to be passed to the cache store.
     *
     * This function is called for every operation that uses keys. For this reason we use this function to also check
     * that the current user is the same as the user who last used this cache.
     *
     * On top of that if prepends the string 'sess_' to the start of all keys. The _ ensures things are easily identifiable.
     *
     * @param string|int $key As passed to get|set|delete etc.
     * @return string|array String unless the store supports multi-identifiers in which case an array if returned.
     */
    protected function parse_key($key) {
        $prefix = $this->get_key_prefix();
        if ($key === self::LASTACCESS) {
            return $key . $prefix;
        }
        return $prefix . '_' . parent::parse_key($key);
    }

    /**
     * Check that this cache instance is tracking the current user.
     */
    protected function check_tracked_user() {
        if (isset($_SESSION['USER']->id) && $_SESSION['USER']->id !== null) {
            // Get the id of the current user.
            $new = $_SESSION['USER']->id;
        } else {
            // No user set up yet.
            $new = 0;
        }
        if ($new !== self::$loadeduserid) {
            // The current user doesn't match the tracked userid for this request.
            if (!is_null(self::$loadeduserid)) {
                // Purge the data we have for the old user.
                // This way we don't bloat the session.
                $this->purge();
            }
            self::$loadeduserid = $new;
            $this->currentuserid = $new;
        } else if ($new !== $this->currentuserid) {
            // The current user matches the loaded user but not the user last used by this cache.
            $this->purge_current_user();
            $this->currentuserid = $new;
        }
    }

    /**
     * Purges the session cache of all data belonging to the current user.
     */
    public function purge_current_user() {
        $keys = $this->get_store()->find_by_prefix($this->get_key_prefix());
        $this->get_store()->delete_many($keys);
    }

    /**
     * Retrieves the value for the given key from the cache.
     *
     * @param string|int $key The key for the data being requested.
     *      It can be any structure although using a scalar string or int is recommended in the interests of performance.
     *      In advanced cases an array may be useful such as in situations requiring the multi-key functionality.
     * @param int $requiredversion Minimum required version of the data or cache::VERSION_NONE
     * @param int $strictness One of IGNORE_MISSING | MUST_EXIST
     * @param mixed &$actualversion If specified, will be set to the actual version number retrieved
     * @return mixed|false The data from the cache or false if the key did not exist within the cache.
     * @throws coding_exception
     */
    protected function get_implementation($key, int $requiredversion, int $strictness, &$actualversion = null) {
        // Check the tracked user.
        $this->check_tracked_user();

        // Use parent code.
        return parent::get_implementation($key, $requiredversion, $strictness, $actualversion);
    }

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
     *      It can be any structure although using a scalar string or int is recommended in the interests of performance.
     *      In advanced cases an array may be useful such as in situations requiring the multi-key functionality.
     * @param mixed $data The data to set against the key.
     * @return bool True on success, false otherwise.
     */
    public function set($key, $data) {
        $this->check_tracked_user();
        $loader = $this->get_loader();
        if ($loader !== false) {
            // We have a loader available set it there as well.
            // We have to let the loader do its own parsing of data as it may be unique.
            $loader->set($key, $data);
        }
        if (is_object($data) && $data instanceof cacheable_object_interface) {
            $data = new cached_object($data);
        } else if (!$this->get_store()->supports_dereferencing_objects() && !is_scalar($data)) {
            // If data is an object it will be a reference.
            // If data is an array if may contain references.
            // We want to break references so that the cache cannot be modified outside of itself.
            // Call the function to unreference it (in the best way possible).
            $data = $this->unref($data);
        }
        // We dont' support native TTL here as we consolidate data for sessions.
        if ($this->has_a_ttl() && !$this->store_supports_native_ttl()) {
            $data = new ttl_wrapper($data, $this->get_definition()->get_ttl());
        }
        $success = $this->get_store()->set($this->parse_key($key), $data);
        if ($this->perfdebug) {
            helper::record_cache_set(
                $this->get_store(),
                $this->get_definition(),
                1,
                $this->get_store()->get_last_io_bytes()
            );
        }
        return $success;
    }

    /**
     * Delete the given key from the cache.
     *
     * @param string|int $key The key to delete.
     * @param bool $recurse When set to true the key will also be deleted from all stacked cache loaders and their stores.
     *     This happens by default and ensure that all the caches are consistent. It is NOT recommended to change this.
     * @return bool True of success, false otherwise.
     */
    public function delete($key, $recurse = true) {
        $parsedkey = $this->parse_key($key);
        if ($recurse && $this->get_loader() !== false) {
            // Delete from the bottom of the stack first.
            $this->get_loader()->delete($key, $recurse);
        }
        return $this->get_store()->delete($parsedkey);
    }

    /**
     * Retrieves an array of values for an array of keys.
     *
     * Using this function comes with potential performance implications.
     * Not all cache stores will support get_many/set_many operations and in order to replicate this functionality will call
     * the equivalent singular method for each item provided.
     * This should not deter you from using this function as there is a performance benefit in situations where the cache store
     * does support it, but you should be aware of this fact.
     *
     * @param array $keys The keys of the data being requested.
     *      Each key can be any structure although using a scalar string or int is recommended in the interests of performance.
     *      In advanced cases an array may be useful such as in situations requiring the multi-key functionality.
     * @param int $strictness One of IGNORE_MISSING or MUST_EXIST.
     * @return array An array of key value pairs for the items that could be retrieved from the cache.
     *      If MUST_EXIST was used and not all keys existed within the cache then an exception will be thrown.
     *      Otherwise any key that did not exist will have a data value of false within the results.
     * @throws coding_exception
     */
    public function get_many(array $keys, $strictness = IGNORE_MISSING) {
        $this->check_tracked_user();
        $parsedkeys = [];
        $keymap = [];
        foreach ($keys as $key) {
            $parsedkey = $this->parse_key($key);
            $parsedkeys[$key] = $parsedkey;
            $keymap[$parsedkey] = $key;
        }
        $result = $this->get_store()->get_many($parsedkeys);
        if ($this->perfdebug) {
            $readbytes = $this->get_store()->get_last_io_bytes();
        }
        $return = [];
        $missingkeys = [];
        $hasmissingkeys = false;
        foreach ($result as $parsedkey => $value) {
            $key = $keymap[$parsedkey];
            if ($value instanceof ttl_wrapper) {
                /* @var ttl_wrapper $value */
                if ($value->has_expired()) {
                    $this->delete($keymap[$parsedkey]);
                    $value = false;
                } else {
                    $value = $value->data;
                }
            }
            if ($value instanceof cached_object) {
                /* @var cached_object $value */
                $value = $value->restore_object();
            } else if (!$this->get_store()->supports_dereferencing_objects() && !is_scalar($value)) {
                // If data is an object it will be a reference.
                // If data is an array if may contain references.
                // We want to break references so that the cache cannot be modified outside of itself.
                // Call the function to unreference it (in the best way possible).
                $value = $this->unref($value);
            }
            $return[$key] = $value;
            if ($value === false) {
                $hasmissingkeys = true;
                $missingkeys[$parsedkey] = $key;
            }
        }
        if ($hasmissingkeys) {
            // We've got missing keys - we've got to check any loaders or data sources.
            $loader = $this->get_loader();
            $datasource = $this->get_datasource();
            if ($loader !== false) {
                foreach ($loader->get_many($missingkeys) as $key => $value) {
                    if ($value !== false) {
                        $return[$key] = $value;
                        unset($missingkeys[$parsedkeys[$key]]);
                    }
                }
            }
            $hasmissingkeys = count($missingkeys) > 0;
            if ($datasource !== false && $hasmissingkeys) {
                // We're still missing keys but we've got a datasource.
                foreach ($datasource->load_many_for_cache($missingkeys) as $key => $value) {
                    if ($value !== false) {
                        $return[$key] = $value;
                        unset($missingkeys[$parsedkeys[$key]]);
                    }
                }
                $hasmissingkeys = count($missingkeys) > 0;
            }
        }
        if ($hasmissingkeys && $strictness === MUST_EXIST) {
            throw new coding_exception('Requested key did not exist in any cache stores and could not be loaded.');
        }
        if ($this->perfdebug) {
            $hits = 0;
            $misses = 0;
            foreach ($return as $value) {
                if ($value === false) {
                    $misses++;
                } else {
                    $hits++;
                }
            }
            helper::record_cache_hit($this->get_store(), $this->get_definition(), $hits, $readbytes);
            helper::record_cache_miss($this->get_store(), $this->get_definition(), $misses);
        }
        return $return;
    }

    /**
     * Delete all of the given keys from the cache.
     *
     * @param array $keys The key to delete.
     * @param bool $recurse When set to true the key will also be deleted from all stacked cache loaders and their stores.
     *     This happens by default and ensure that all the caches are consistent. It is NOT recommended to change this.
     * @return int The number of items successfully deleted.
     */
    public function delete_many(array $keys, $recurse = true) {
        $parsedkeys = array_map([$this, 'parse_key'], $keys);
        if ($recurse && $this->get_loader() !== false) {
            // Delete from the bottom of the stack first.
            $this->get_loader()->delete_many($keys, $recurse);
        }
        return $this->get_store()->delete_many($parsedkeys);
    }

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
    public function set_many(array $keyvaluearray) {
        $this->check_tracked_user();
        $loader = $this->get_loader();
        if ($loader !== false) {
            // We have a loader available set it there as well.
            // We have to let the loader do its own parsing of data as it may be unique.
            $loader->set_many($keyvaluearray);
        }
        $data = [];
        $definitionid = $this->get_definition()->get_ttl();
        $simulatettl = $this->has_a_ttl() && !$this->store_supports_native_ttl();
        foreach ($keyvaluearray as $key => $value) {
            if (is_object($value) && $value instanceof cacheable_object_interface) {
                $value = new cached_object($value);
            } else if (!$this->get_store()->supports_dereferencing_objects() && !is_scalar($value)) {
                // If data is an object it will be a reference.
                // If data is an array if may contain references.
                // We want to break references so that the cache cannot be modified outside of itself.
                // Call the function to unreference it (in the best way possible).
                $value = $this->unref($value);
            }
            if ($simulatettl) {
                $value = new ttl_wrapper($value, $definitionid);
            }
            $data[$key] = [
                'key' => $this->parse_key($key),
                'value' => $value,
            ];
        }
        $successfullyset = $this->get_store()->set_many($data);
        if ($this->perfdebug && $successfullyset) {
            helper::record_cache_set(
                $this->get_store(),
                $this->get_definition(),
                $successfullyset,
                $this->get_store()->get_last_io_bytes()
            );
        }
        return $successfullyset;
    }

    /**
     * Purges the cache store, and loader if there is one.
     *
     * @return bool True on success, false otherwise
     */
    public function purge() {
        $this->get_store()->purge();
        if ($this->get_loader()) {
            $this->get_loader()->purge();
        }
        return true;
    }

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
     * @param bool $tryloadifpossible If set to true, the cache doesn't contain the key, and there is another cache loader or
     *      data source then the code will try load the key value from the next item in the chain.
     * @return bool True if the cache has the requested key, false otherwise.
     */
    public function has($key, $tryloadifpossible = false) {
        $this->check_tracked_user();
        $parsedkey = $this->parse_key($key);
        $store = $this->get_store();
        if ($this->has_a_ttl() && !$this->store_supports_native_ttl()) {
            // The data has a TTL and the store doesn't support it natively.
            // We must fetch the data and expect a ttl wrapper.
            $data = $store->get($parsedkey);
            $has = ($data instanceof ttl_wrapper && !$data->has_expired());
        } else if (!$this->store_supports_key_awareness()) {
            // The store doesn't support key awareness, get the data and check it manually... puke.
            // Either no TTL is set of the store supports its handling natively.
            $data = $store->get($parsedkey);
            $has = ($data !== false);
        } else {
            // The store supports key awareness, this is easy!
            // Either no TTL is set of the store supports its handling natively.
            /* @var store|key_aware_cache_interface $store */
            $has = $store->has($parsedkey);
        }
        if (!$has && $tryloadifpossible) {
            $result = null;
            if ($this->get_loader() !== false) {
                $result = $this->get_loader()->get($parsedkey);
            } else if ($this->get_datasource() !== null) {
                $result = $this->get_datasource()->load_for_cache($key);
            }
            $has = ($result !== null);
            if ($has) {
                $this->set($key, $result);
            }
        }
        return $has;
    }

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
    public function has_all(array $keys) {
        $this->check_tracked_user();
        if (($this->has_a_ttl() && !$this->store_supports_native_ttl()) || !$this->store_supports_key_awareness()) {
            foreach ($keys as $key) {
                if (!$this->has($key)) {
                    return false;
                }
            }
            return true;
        }
        // The cache must be key aware and if support native ttl if it a ttl is set.
        /* @var store|key_aware_cache_interface $store */
        $store = $this->get_store();
        return $store->has_all(array_map([$this, 'parse_key'], $keys));
    }

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
    public function has_any(array $keys) {
        if (($this->has_a_ttl() && !$this->store_supports_native_ttl()) || !$this->store_supports_key_awareness()) {
            foreach ($keys as $key) {
                if ($this->has($key)) {
                    return true;
                }
            }
            return false;
        }
        /* @var store|key_aware_cache_interface $store */
        $store = $this->get_store();
        return $store->has_any(array_map([$this, 'parse_key'], $keys));
    }

    /**
     * The session loader never uses static acceleration.
     * Instead it stores things in the static $session variable. Shared between all session loaders.
     *
     * @return bool
     */
    protected function use_static_acceleration() {
        return false;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(session_cache::class, \cache_session::class);
