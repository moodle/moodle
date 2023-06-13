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
 * Cache loaders
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
 * The main cache class.
 *
 * This class if the first class that any end developer will interact with.
 * In order to create an instance of a cache that they can work with they must call one of the static make methods belonging
 * to this class.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache implements cache_loader {

    /**
     * @var int Constant for cache entries that do not have a version number
     */
    const VERSION_NONE = -1;

    /**
     * We need a timestamp to use within the cache API.
     * This stamp needs to be used for all ttl and time based operations to ensure that we don't end up with
     * timing issues.
     * @var int
     */
    protected static $now;

    /**
     * A purge token used to distinguish between multiple cache purges in the same second.
     * This is in the format <microtime>-<random string>.
     *
     * @var string
     */
    protected static $purgetoken;

    /**
     * The definition used when loading this cache if there was one.
     * @var cache_definition
     */
    private $definition = false;

    /**
     * The cache store that this loader will make use of.
     * @var cache_store
     */
    private $store;

    /**
     * The next cache loader in the chain if there is one.
     * If a cache request misses for the store belonging to this loader then the loader
     * stored here will be checked next.
     * If there is a loader here then $datasource must be false.
     * @var cache_loader|false
     */
    private $loader = false;

    /**
     * The data source to use if we need to load data (because if doesn't exist in the cache store).
     * If there is a data source here then $loader above must be false.
     * @var cache_data_source|false
     */
    private $datasource = false;

    /**
     * Used to quickly check if the store supports key awareness.
     * This is set when the cache is initialised and is used to speed up processing.
     * @var bool
     */
    private $supportskeyawareness = null;

    /**
     * Used to quickly check if the store supports ttl natively.
     * This is set when the cache is initialised and is used to speed up processing.
     * @var bool
     */
    private $supportsnativettl = null;

    /**
     * Gets set to true if the cache is going to be using a static array for acceleration.
     * The array statically caches items used during the lifetime of the request. This greatly speeds up interaction
     * with the cache in areas where it will be repetitively hit for the same information such as with strings.
     * There are several other variables to control how this static acceleration array works.
     * @var bool
     */
    private $staticacceleration = false;

    /**
     * The static acceleration array.
     * Items will be stored in this cache as they were provided. This ensure there is no unnecessary processing taking place.
     * @var array
     */
    private $staticaccelerationarray = array();

    /**
     * The number of items in the static acceleration array. Avoids count calls like you wouldn't believe.
     * @var int
     */
    private $staticaccelerationcount = 0;

    /**
     * An array containing just the keys being used in the static acceleration array.
     * This seems redundant perhaps but is used when managing the size of the static acceleration array.
     * Items are added to the end of the array and the when we need to reduce the size of the cache we use the
     * key that is first on this array.
     * @var array
     */
    private $staticaccelerationkeys = array();

    /**
     * The maximum size of the static acceleration array.
     *
     * If set to false there is no max size.
     * Caches that make use of static acceleration should seriously consider setting this to something reasonably small, but
     * still large enough to offset repetitive calls.
     *
     * @var int|false
     */
    private $staticaccelerationsize = false;

    /**
     * Gets set to true during initialisation if the definition is making use of a ttl.
     * Used to speed up processing.
     * @var bool
     */
    private $hasattl = false;

    /**
     * Gets set to the class name of the store during initialisation. This is used several times in the cache class internally
     * and having it here helps speed up processing.
     * @var strubg
     */
    protected $storetype = 'unknown';

    /**
     * Gets set to true if we want to collect performance information about the cache API.
     * @var bool
     */
    protected $perfdebug = false;

    /**
     * Determines if this loader is a sub loader, not the top of the chain.
     * @var bool
     */
    protected $subloader = false;

    /**
     * Creates a new cache instance for a pre-defined definition.
     *
     * @param string $component The component for the definition
     * @param string $area The area for the definition
     * @param array $identifiers Any additional identifiers that should be provided to the definition.
     * @param string $unused Used to be datasourceaggregate but that was removed and this is now unused.
     * @return cache_application|cache_session|cache_store
     */
    public static function make($component, $area, array $identifiers = array(), $unused = null) {
        $factory = cache_factory::instance();
        return $factory->create_cache_from_definition($component, $area, $identifiers);
    }

    /**
     * Creates a new cache instance based upon the given params.
     *
     * @param int $mode One of cache_store::MODE_*
     * @param string $component The component this cache relates to.
     * @param string $area The area this cache relates to.
     * @param array $identifiers Any additional identifiers that should be provided to the definition.
     * @param array $options An array of options, available options are:
     *   - simplekeys : Set to true if the keys you will use are a-zA-Z0-9_
     *   - simpledata : Set to true if the type of the data you are going to store is scalar, or an array of scalar vars
     *   - staticacceleration : If set to true the cache will hold onto data passing through it.
     *   - staticaccelerationsize : The max size for the static acceleration array.
     * @return cache_application|cache_session|cache_store
     */
    public static function make_from_params($mode, $component, $area, array $identifiers = array(), array $options = array()) {
        $factory = cache_factory::instance();
        return $factory->create_cache_from_params($mode, $component, $area, $identifiers, $options);
    }

    /**
     * Constructs a new cache instance.
     *
     * You should not call this method from your code, instead you should use the cache::make methods.
     *
     * This method is public so that the cache_factory is able to instantiate cache instances.
     * Ideally we would make this method protected and expose its construction to the factory method internally somehow.
     * The factory class is responsible for this in order to centralise the storage of instances once created. This way if needed
     * we can force a reset of the cache API (used during unit testing).
     *
     * @param cache_definition $definition The definition for the cache instance.
     * @param cache_store $store The store that cache should use.
     * @param cache_loader|cache_data_source $loader The next loader in the chain or the data source if there is one and there
     *      are no other cache_loaders in the chain.
     */
    public function __construct(cache_definition $definition, cache_store $store, $loader = null) {
        global $CFG;
        $this->definition = $definition;
        $this->store = $store;
        $this->storetype = get_class($store);
        $this->perfdebug = (!empty($CFG->perfdebug) and $CFG->perfdebug > 7);
        if ($loader instanceof cache_loader) {
            $this->set_loader($loader);
        } else if ($loader instanceof cache_data_source) {
            $this->set_data_source($loader);
        }
        $this->definition->generate_definition_hash();
        $this->staticacceleration = $this->definition->use_static_acceleration();
        if ($this->staticacceleration) {
            $this->staticaccelerationsize = $this->definition->get_static_acceleration_size();
        }
        $this->hasattl = ($this->definition->get_ttl() > 0);
    }

    /**
     * Set the loader for this cache.
     *
     * @param   cache_loader $loader
     */
    protected function set_loader(cache_loader $loader): void {
        $this->loader = $loader;

        // Mark the loader as a sub (chained) loader.
        $this->loader->set_is_sub_loader(true);
    }

    /**
     * Set the data source for this cache.
     *
     * @param   cache_data_source $datasource
     */
    protected function set_data_source(cache_data_source $datasource): void {
        $this->datasource = $datasource;
    }

    /**
     * Used to inform the loader of its state as a sub loader, or as the top of the chain.
     *
     * This is important as it ensures that we do not have more than one loader keeping static acceleration data.
     * Subloaders need to be "pure" loaders in the sense that they are used to store and retrieve information from stores or the
     * next loader/data source in the chain.
     * Nothing fancy, nothing flash.
     *
     * @param bool $setting
     */
    protected function set_is_sub_loader($setting = true) {
        if ($setting) {
            $this->subloader = true;
            // Subloaders should not keep static acceleration data.
            $this->staticacceleration = false;
            $this->staticaccelerationsize = false;
        } else {
            $this->subloader = true;
            $this->staticacceleration = $this->definition->use_static_acceleration();
            if ($this->staticacceleration) {
                $this->staticaccelerationsize = $this->definition->get_static_acceleration_size();
            }
        }
    }

    /**
     * Alters the identifiers that have been provided to the definition.
     *
     * This is an advanced method and should not be used unless really needed.
     * It allows the developer to slightly alter the definition without having to re-establish the cache.
     * It will cause more processing as the definition will need to clear and reprepare some of its properties.
     *
     * @param array $identifiers
     */
    public function set_identifiers(array $identifiers) {
        if ($this->definition->set_identifiers($identifiers)) {
            // As static acceleration uses input keys and not parsed keys
            // it much be cleared when the identifier set is changed.
            $this->staticaccelerationarray = array();
            if ($this->staticaccelerationsize !== false) {
                $this->staticaccelerationkeys = array();
                $this->staticaccelerationcount = 0;
            }
        }
    }

    /**
     * Process any outstanding invalidation events for the cache we are registering,
     *
     * Identifiers and event invalidation are not compatible with each other at this time.
     * As a result the cache does not need to consider identifiers when working out what to invalidate.
     */
    protected function handle_invalidation_events() {
        if (!$this->definition->has_invalidation_events()) {
            return;
        }

        // Each cache stores the current 'lastinvalidation' value within the cache itself.
        $lastinvalidation = $this->get('lastinvalidation');
        if ($lastinvalidation === false) {
            // There is currently no  value for the lastinvalidation token, therefore the token is not set, and there
            // can be nothing to invalidate.
            // Set the lastinvalidation value to the current purge token and return early.
            $this->set('lastinvalidation', self::get_purge_token());
            return;
        } else if ($lastinvalidation == self::get_purge_token()) {
            // The current purge request has already been fully handled by this cache.
            return;
        }

        /*
         * Now that the whole cache check is complete, we check the meaning of any specific cache invalidation events.
         * These are stored in the core/eventinvalidation cache as an multi-dimensinoal array in the form:
         *  [
         *      eventname => [
         *          keyname => purgetoken,
         *      ]
         *  ]
         *
         * The 'keyname' value is used to delete a specific key in the cache.
         * If the keyname is set to the special value 'purged', then the whole cache is purged instead.
         *
         * The 'purgetoken' is the token that this key was last purged.
         * a) If the purgetoken matches the last invalidation, then the key/cache is not purged.
         * b) If the purgetoken is newer than the last invalidation, then the key/cache is not purged.
         * c) If the purge token is older than the last invalidation, or it has a different token component, then the
         *    cache is purged.
         *
         * Option b should not happen under normal operation, but may happen in race condition whereby a long-running
         * request's cache is cleared in another process during that request, and prior to that long-running request
         * creating the cache. In such a condition, it would be incorrect to clear that cache.
         */
        $cache = self::make('core', 'eventinvalidation');
        $events = $cache->get_many($this->definition->get_invalidation_events());
        $todelete = array();
        $purgeall = false;

        // Iterate the returned data for the events.
        foreach ($events as $event => $keys) {
            if ($keys === false) {
                // No data to be invalidated yet.
                continue;
            }

            // Look at each key and check the timestamp.
            foreach ($keys as $key => $purgetoken) {
                // If the timestamp of the event is more than or equal to the last invalidation (happened between the last
                // invalidation and now), then we need to invaliate the key.
                if (self::compare_purge_tokens($purgetoken, $lastinvalidation) > 0) {
                    if ($key === 'purged') {
                        $purgeall = true;
                        break;
                    } else {
                        $todelete[] = $key;
                    }
                }
            }
        }
        if ($purgeall) {
            $this->purge();
        } else if (!empty($todelete)) {
            $todelete = array_unique($todelete);
            $this->delete_many($todelete);
        }
        // Set the time of the last invalidation.
        if ($purgeall || !empty($todelete)) {
            $this->set('lastinvalidation', self::get_purge_token(true));
        }
    }

    /**
     * Retrieves the value for the given key from the cache.
     *
     * @param string|int $key The key for the data being requested.
     *      It can be any structure although using a scalar string or int is recommended in the interests of performance.
     *      In advanced cases an array may be useful such as in situations requiring the multi-key functionality.
     * @param int $strictness One of IGNORE_MISSING | MUST_EXIST
     * @return mixed|false The data from the cache or false if the key did not exist within the cache.
     * @throws coding_exception
     */
    public function get($key, $strictness = IGNORE_MISSING) {
        return $this->get_implementation($key, self::VERSION_NONE, $strictness);
    }

    /**
     * Retrieves the value and actual version for the given key, with at least the required version.
     *
     * If there is no value for the key, or there is a value but it doesn't have the required
     * version, then this function will return null (or throw an exception if you set strictness
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
     * @throws \coding_exception If you call get_versioned on a non-versioned cache key
     */
    public function get_versioned($key, int $requiredversion, int $strictness = IGNORE_MISSING, &$actualversion = null) {
        return $this->get_implementation($key, $requiredversion, $strictness, $actualversion);
    }

    /**
     * Checks returned data to see if it matches the specified version number.
     *
     * For versioned data, this returns the version_wrapper object (or false). For other
     * data, it returns the actual data (or false).
     *
     * @param mixed $result Result data
     * @param int $requiredversion Required version number or VERSION_NONE if there must be no version
     * @return bool True if version is current, false if not (or if result is false)
     * @throws \coding_exception If unexpected type of data (versioned vs non-versioned) is found
     */
    protected static function check_version($result, int $requiredversion): bool {
        if ($requiredversion === self::VERSION_NONE) {
            if ($result instanceof \core_cache\version_wrapper) {
                throw new \coding_exception('Unexpectedly found versioned cache entry');
            } else {
                // No version checks, so version is always correct.
                return true;
            }
        } else {
            // If there's no result, obviously it doesn't meet the required version.
            if (!cache_helper::result_found($result)) {
                return false;
            }
            if (!($result instanceof \core_cache\version_wrapper)) {
                throw new \coding_exception('Unexpectedly found non-versioned cache entry');
            }
            // If the result doesn't match the required version tag, return false.
            if ($result->version < $requiredversion) {
                return false;
            }
            // The version meets the requirement.
            return true;
        }
    }

    /**
     * Retrieves the value for the given key from the cache.
     *
     * @param string|int $key The key for the data being requested.
     *      It can be any structure although using a scalar string or int is recommended in the interests of performance.
     *      In advanced cases an array may be useful such as in situations requiring the multi-key functionality.
     * @param int $requiredversion Minimum required version of the data or cache::VERSION_NONE
     * @param int $strictness One of IGNORE_MISSING | MUST_EXIST
     * @param mixed $actualversion If specified, will be set to the actual version number retrieved
     * @return mixed|false The data from the cache or false if the key did not exist within the cache.
     * @throws coding_exception
     */
    protected function get_implementation($key, int $requiredversion, int $strictness, &$actualversion = null) {
        // 1. Get it from the static acceleration array if we can (only when it is enabled and it has already been requested/set).
        $usesstaticacceleration = $this->use_static_acceleration();

        if ($usesstaticacceleration) {
            $result = $this->static_acceleration_get($key);
            if (cache_helper::result_found($result) && self::check_version($result, $requiredversion)) {
                if ($requiredversion === self::VERSION_NONE) {
                    return $result;
                } else {
                    $actualversion = $result->version;
                    return $result->data;
                }
            }
        }

        // 2. Parse the key.
        $parsedkey = $this->parse_key($key);

        // 3. Get it from the store. Obviously wasn't in the static acceleration array.
        $result = $this->store->get($parsedkey);
        if (cache_helper::result_found($result)) {
            // Check the result has at least the required version.
            try {
                $validversion = self::check_version($result, $requiredversion);
            } catch (\coding_exception $e) {
                // In certain circumstances this could happen before users are taken to the upgrade
                // screen when upgrading from an earlier Moodle version that didn't use a versioned
                // cache for this item, so redirect instead of showing error if that's the case.
                redirect_if_major_upgrade_required();

                // If we still get an exception because there is incorrect data in the cache (not
                // versioned when it ought to be), delete it so this exception goes away next time.
                // The exception should only happen if there is a code bug (which is why we still
                // throw it) but there are unusual scenarios in development where it might happen
                // and that would be annoying if it doesn't fix itself.
                $this->store->delete($parsedkey);
                throw $e;
            }

            if (!$validversion) {
                // If the result was too old, don't use it.
                $result = false;

                // Also delete it immediately. This improves performance in the
                // case when the cache item is large and there may be multiple clients simultaneously
                // requesting it - they won't all have to do a megabyte of IO just in order to find
                // that it's out of date.
                $this->store->delete($parsedkey);
            }
        }
        if (cache_helper::result_found($result)) {
            // Look to see if there's a TTL wrapper. It might be inside a version wrapper.
            if ($requiredversion !== self::VERSION_NONE) {
                $ttlconsider = $result->data;
            } else {
                $ttlconsider = $result;
            }
            if ($ttlconsider instanceof cache_ttl_wrapper) {
                if ($ttlconsider->has_expired()) {
                    $this->store->delete($parsedkey);
                    $result = false;
                } else if ($requiredversion === self::VERSION_NONE) {
                    // Use the data inside the TTL wrapper as the result.
                    $result = $ttlconsider->data;
                } else {
                    // Put the data from the TTL wrapper directly inside the version wrapper.
                    $result->data = $ttlconsider->data;
                }
            }
            if ($usesstaticacceleration) {
                $this->static_acceleration_set($key, $result);
            }
            // Remove version wrapper if necessary.
            if ($requiredversion !== self::VERSION_NONE) {
                $actualversion = $result->version;
                $result = $result->data;
            }
            if ($result instanceof cache_cached_object) {
                $result = $result->restore_object();
            }
        }

        // 4. Load if from the loader/datasource if we don't already have it.
        $setaftervalidation = false;
        if (!cache_helper::result_found($result)) {
            if ($this->perfdebug) {
                cache_helper::record_cache_miss($this->store, $this->definition);
            }
            if ($this->loader !== false) {
                // We must pass the original (unparsed) key to the next loader in the chain.
                // The next loader will parse the key as it sees fit. It may be parsed differently
                // depending upon the capabilities of the store associated with the loader.
                if ($requiredversion === self::VERSION_NONE) {
                    $result = $this->loader->get($key);
                } else {
                    $result = $this->loader->get_versioned($key, $requiredversion, IGNORE_MISSING, $actualversion);
                }
            } else if ($this->datasource !== false) {
                if ($requiredversion === self::VERSION_NONE) {
                    $result = $this->datasource->load_for_cache($key);
                } else {
                    if (!$this->datasource instanceof cache_data_source_versionable) {
                        throw new \coding_exception('Data source is not versionable');
                    }
                    $result = $this->datasource->load_for_cache_versioned($key, $requiredversion, $actualversion);
                    if ($result && $actualversion < $requiredversion) {
                        throw new \coding_exception('Data source returned outdated version');
                    }
                }
            }
            $setaftervalidation = (cache_helper::result_found($result));
        } else if ($this->perfdebug) {
            $readbytes = $this->store->get_last_io_bytes();
            cache_helper::record_cache_hit($this->store, $this->definition, 1, $readbytes);
        }
        // 5. Validate strictness.
        if ($strictness === MUST_EXIST && !cache_helper::result_found($result)) {
            throw new coding_exception('Requested key did not exist in any cache stores and could not be loaded.');
        }
        // 6. Set it to the store if we got it from the loader/datasource. Only set to this direct
        // store; parent method will have set it to all stores if needed.
        if ($setaftervalidation) {
            if ($requiredversion === self::VERSION_NONE) {
                $this->set_implementation($key, self::VERSION_NONE, $result, false);
            } else {
                $this->set_implementation($key, $actualversion, $result, false);
            }
        }
        // 7. Make sure we don't pass back anything that could be a reference.
        //    We don't want people modifying the data in the cache.
        if (!$this->store->supports_dereferencing_objects() && !is_scalar($result)) {
            // If data is an object it will be a reference.
            // If data is an array if may contain references.
            // We want to break references so that the cache cannot be modified outside of itself.
            // Call the function to unreference it (in the best way possible).
            $result = $this->unref($result);
        }
        return $result;
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

        $keysparsed = array();
        $parsedkeys = array();
        $resultpersist = array();
        $resultstore = array();
        $keystofind = array();
        $readbytes = cache_store::IO_BYTES_NOT_SUPPORTED;

        // First up check the persist cache for each key.
        $isusingpersist = $this->use_static_acceleration();
        foreach ($keys as $key) {
            $pkey = $this->parse_key($key);
            if (is_array($pkey)) {
                $pkey = $pkey['key'];
            }
            $keysparsed[$key] = $pkey;
            $parsedkeys[$pkey] = $key;
            $keystofind[$pkey] = $key;
            if ($isusingpersist) {
                $value = $this->static_acceleration_get($key);
                if ($value !== false) {
                    $resultpersist[$pkey] = $value;
                    unset($keystofind[$pkey]);
                }
            }
        }

        // Next assuming we didn't find all of the keys in the persist cache try loading them from the store.
        if (count($keystofind)) {
            $resultstore = $this->store->get_many(array_keys($keystofind));
            if ($this->perfdebug) {
                $readbytes = $this->store->get_last_io_bytes();
            }
            // Process each item in the result to "unwrap" it.
            foreach ($resultstore as $key => $value) {
                if ($value instanceof cache_ttl_wrapper) {
                    if ($value->has_expired()) {
                        $value = false;
                    } else {
                        $value = $value->data;
                    }
                }
                if ($value !== false && $this->use_static_acceleration()) {
                    $this->static_acceleration_set($keystofind[$key], $value);
                }
                if ($value instanceof cache_cached_object) {
                    $value = $value->restore_object();
                }
                $resultstore[$key] = $value;
            }
        }

        // Merge the result from the persis cache with the results from the store load.
        $result = $resultpersist + $resultstore;
        unset($resultpersist);
        unset($resultstore);

        // Next we need to find any missing values and load them from the loader/datasource next in the chain.
        $usingloader = ($this->loader !== false);
        $usingsource = (!$usingloader && ($this->datasource !== false));
        if ($usingloader || $usingsource) {
            $missingkeys = array();
            foreach ($result as $key => $value) {
                if ($value === false) {
                    $missingkeys[] = $parsedkeys[$key];
                }
            }
            if (!empty($missingkeys)) {
                if ($usingloader) {
                    $resultmissing = $this->loader->get_many($missingkeys);
                } else {
                    $resultmissing = $this->datasource->load_many_for_cache($missingkeys);
                }
                foreach ($resultmissing as $key => $value) {
                    $result[$keysparsed[$key]] = $value;
                    if ($value !== false) {
                        $this->set($key, $value);
                    }
                }
                unset($resultmissing);
            }
            unset($missingkeys);
        }

        // Create an array with the original keys and the found values. This will be what we return.
        $fullresult = array();
        foreach ($result as $key => $value) {
            if (!is_scalar($value)) {
                // If data is an object it will be a reference.
                // If data is an array if may contain references.
                // We want to break references so that the cache cannot be modified outside of itself.
                // Call the function to unreference it (in the best way possible).
                $value = $this->unref($value);
            }
            $fullresult[$parsedkeys[$key]] = $value;
        }
        unset($result);

        // Final step is to check strictness.
        if ($strictness === MUST_EXIST) {
            foreach ($keys as $key) {
                if (!array_key_exists($key, $fullresult)) {
                    throw new coding_exception('Not all the requested keys existed within the cache stores.');
                }
            }
        }

        if ($this->perfdebug) {
            $hits = 0;
            $misses = 0;
            foreach ($fullresult as $value) {
                if ($value === false) {
                    $misses++;
                } else {
                    $hits++;
                }
            }
            cache_helper::record_cache_hit($this->store, $this->definition, $hits, $readbytes);
            cache_helper::record_cache_miss($this->store, $this->definition, $misses);
        }

        // Return the result. Phew!
        return $fullresult;
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
        return $this->set_implementation($key, self::VERSION_NONE, $data);
    }

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
    public function set_versioned($key, int $version, $data): bool {
        return $this->set_implementation($key, $version, $data);
    }

    /**
     * Sets the value for the given key, optionally with a version tag.
     *
     * @param string|int $key The key for the data being set.
     * @param int $version Version number for the data or cache::VERSION_NONE if none
     * @param mixed $data The data to set against the key.
     * @param bool $setparents If true, sets all parent loaders, otherwise only this one
     * @return bool True on success, false otherwise.
     */
    protected function set_implementation($key, int $version, $data, bool $setparents = true): bool {
        if ($this->loader !== false && $setparents) {
            // We have a loader available set it there as well.
            // We have to let the loader do its own parsing of data as it may be unique.
            if ($version === self::VERSION_NONE) {
                $this->loader->set($key, $data);
            } else {
                $this->loader->set_versioned($key, $version, $data);
            }
        }
        $usestaticacceleration = $this->use_static_acceleration();

        if (is_object($data) && $data instanceof cacheable_object) {
            $data = new cache_cached_object($data);
        } else if (!$this->store->supports_dereferencing_objects() && !is_scalar($data)) {
            // If data is an object it will be a reference.
            // If data is an array if may contain references.
            // We want to break references so that the cache cannot be modified outside of itself.
            // Call the function to unreference it (in the best way possible).
            $data = $this->unref($data);
        }

        if ($usestaticacceleration) {
            // Static acceleration cache should include the cache version wrapper, but not TTL.
            if ($version === self::VERSION_NONE) {
                $this->static_acceleration_set($key, $data);
            } else {
                $this->static_acceleration_set($key, new \core_cache\version_wrapper($data, $version));
            }
        }

        if ($this->has_a_ttl() && !$this->store_supports_native_ttl()) {
            $data = new cache_ttl_wrapper($data, $this->definition->get_ttl());
        }
        $parsedkey = $this->parse_key($key);

        if ($version !== self::VERSION_NONE) {
            $data = new \core_cache\version_wrapper($data, $version);
        }

        $success = $this->store->set($parsedkey, $data);
        if ($this->perfdebug) {
            cache_helper::record_cache_set($this->store, $this->definition, 1,
                    $this->store->get_last_io_bytes());
        }
        return $success;
    }

    /**
     * Removes references where required.
     *
     * @param stdClass|array $data
     * @return mixed What ever was put in but without any references.
     */
    protected function unref($data) {
        if ($this->definition->uses_simple_data()) {
            return $data;
        }
        // Check if it requires serialisation in order to produce a reference free copy.
        if ($this->requires_serialisation($data)) {
            // Damn, its going to have to be serialise.
            $data = serialize($data);
            // We unserialise immediately so that we don't have to do it every time on get.
            $data = unserialize($data);
        } else if (!is_scalar($data)) {
            // Its safe to clone, lets do it, its going to beat the pants of serialisation.
            $data = $this->deep_clone($data);
        }
        return $data;
    }

    /**
     * Checks to see if a var requires serialisation.
     *
     * @param mixed $value The value to check.
     * @param int $depth Used to ensure we don't enter an endless loop (think recursion).
     * @return bool Returns true if the value is going to require serialisation in order to ensure a reference free copy
     *      or false if its safe to clone.
     */
    protected function requires_serialisation($value, $depth = 1) {
        if (is_scalar($value)) {
            return false;
        } else if (is_array($value) || $value instanceof stdClass || $value instanceof Traversable) {
            if ($depth > 5) {
                // Skrew it, mega-deep object, developer you suck, we're just going to serialise.
                return true;
            }
            foreach ($value as $key => $subvalue) {
                if ($this->requires_serialisation($subvalue, $depth++)) {
                    return true;
                }
            }
        }
        // Its not scalar, array, or stdClass so we'll need to serialise.
        return true;
    }

    /**
     * Creates a reference free clone of the given value.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function deep_clone($value) {
        if (is_object($value)) {
            // Objects must be cloned to begin with.
            $value = clone $value;
        }
        if (is_array($value) || is_object($value)) {
            foreach ($value as $key => $subvalue) {
                $value[$key] = $this->deep_clone($subvalue);
            }
        }
        return $value;
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
        if ($this->loader !== false) {
            // We have a loader available set it there as well.
            // We have to let the loader do its own parsing of data as it may be unique.
            $this->loader->set_many($keyvaluearray);
        }
        $data = array();
        $simulatettl = $this->has_a_ttl() && !$this->store_supports_native_ttl();
        $usestaticaccelerationarray = $this->use_static_acceleration();
        $needsdereferencing = !$this->store->supports_dereferencing_objects();
        foreach ($keyvaluearray as $key => $value) {
            if (is_object($value) && $value instanceof cacheable_object) {
                $value = new cache_cached_object($value);
            } else if ($needsdereferencing && !is_scalar($value)) {
                // If data is an object it will be a reference.
                // If data is an array if may contain references.
                // We want to break references so that the cache cannot be modified outside of itself.
                // Call the function to unreference it (in the best way possible).
                $value = $this->unref($value);
            }
            if ($usestaticaccelerationarray) {
                $this->static_acceleration_set($key, $value);
            }
            if ($simulatettl) {
                $value = new cache_ttl_wrapper($value, $this->definition->get_ttl());
            }
            $data[$key] = array(
                'key' => $this->parse_key($key),
                'value' => $value
            );
        }
        $successfullyset = $this->store->set_many($data);
        if ($this->perfdebug && $successfullyset) {
            cache_helper::record_cache_set($this->store, $this->definition, $successfullyset,
                    $this->store->get_last_io_bytes());
        }
        return $successfullyset;
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
        if ($this->static_acceleration_has($key)) {
            // Hoorah, that was easy. It exists in the static acceleration array so we definitely have it.
            return true;
        }
        $parsedkey = $this->parse_key($key);

        if ($this->has_a_ttl() && !$this->store_supports_native_ttl()) {
            // The data has a TTL and the store doesn't support it natively.
            // We must fetch the data and expect a ttl wrapper.
            $data = $this->store->get($parsedkey);
            $has = ($data instanceof cache_ttl_wrapper && !$data->has_expired());
        } else if (!$this->store_supports_key_awareness()) {
            // The store doesn't support key awareness, get the data and check it manually... puke.
            // Either no TTL is set of the store supports its handling natively.
            $data = $this->store->get($parsedkey);
            $has = ($data !== false);
        } else {
            // The store supports key awareness, this is easy!
            // Either no TTL is set of the store supports its handling natively.
            $has = $this->store->has($parsedkey);
        }
        if (!$has && $tryloadifpossible) {
            if ($this->loader !== false) {
                $result = $this->loader->get($parsedkey);
            } else if ($this->datasource !== null) {
                $result = $this->datasource->load_for_cache($key);
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
        if (($this->has_a_ttl() && !$this->store_supports_native_ttl()) || !$this->store_supports_key_awareness()) {
            foreach ($keys as $key) {
                if (!$this->has($key)) {
                    return false;
                }
            }
            return true;
        }
        $parsedkeys = array_map(array($this, 'parse_key'), $keys);
        return $this->store->has_all($parsedkeys);
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

        if ($this->use_static_acceleration()) {
            foreach ($keys as $id => $key) {
                if ($this->static_acceleration_has($key)) {
                    return true;
                }
            }
        }
        $parsedkeys = array_map(array($this, 'parse_key'), $keys);
        return $this->store->has_any($parsedkeys);
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
        $this->static_acceleration_delete($key);
        if ($recurse && $this->loader !== false) {
            // Delete from the bottom of the stack first.
            $this->loader->delete($key, $recurse);
        }
        $parsedkey = $this->parse_key($key);
        return $this->store->delete($parsedkey);
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
        if ($this->use_static_acceleration()) {
            foreach ($keys as $key) {
                $this->static_acceleration_delete($key);
            }
        }
        if ($recurse && $this->loader !== false) {
            // Delete from the bottom of the stack first.
            $this->loader->delete_many($keys, $recurse);
        }
        $parsedkeys = array_map(array($this, 'parse_key'), $keys);
        return $this->store->delete_many($parsedkeys);
    }

    /**
     * Purges the cache store, and loader if there is one.
     *
     * @return bool True on success, false otherwise
     */
    public function purge() {
        // 1. Purge the static acceleration array.
        $this->static_acceleration_purge();
        // 2. Purge the store.
        $this->store->purge();
        // 3. Optionally pruge any stacked loaders.
        if ($this->loader) {
            $this->loader->purge();
        }
        return true;
    }

    /**
     * Parses the key turning it into a string (or array is required) suitable to be passed to the cache store.
     *
     * @param string|int $key As passed to get|set|delete etc.
     * @return string|array String unless the store supports multi-identifiers in which case an array if returned.
     */
    protected function parse_key($key) {
        // First up if the store supports multiple keys we'll go with that.
        if ($this->store->supports_multiple_identifiers()) {
            $result = $this->definition->generate_multi_key_parts();
            $result['key'] = $key;
            return $result;
        }
        // If not we need to generate a hash and to for that we use the cache_helper.
        return cache_helper::hash_key($key, $this->definition);
    }

    /**
     * Returns true if the cache is making use of a ttl.
     * @return bool
     */
    protected function has_a_ttl() {
        return $this->hasattl;
    }

    /**
     * Returns true if the cache store supports native ttl.
     * @return bool
     */
    protected function store_supports_native_ttl() {
        if ($this->supportsnativettl === null) {
            $this->supportsnativettl = ($this->store->supports_native_ttl());
        }
        return $this->supportsnativettl;
    }

    /**
     * Returns the cache definition.
     *
     * @return cache_definition
     */
    protected function get_definition() {
        return $this->definition;
    }

    /**
     * Returns the cache store
     *
     * @return cache_store
     */
    protected function get_store() {
        return $this->store;
    }

    /**
     * Returns the loader associated with this instance.
     *
     * @since Moodle 2.4.4
     * @return cache|false
     */
    protected function get_loader() {
        return $this->loader;
    }

    /**
     * Returns the data source associated with this cache.
     *
     * @since Moodle 2.4.4
     * @return cache_data_source|false
     */
    protected function get_datasource() {
        return $this->datasource;
    }

    /**
     * Returns true if the store supports key awareness.
     *
     * @return bool
     */
    protected function store_supports_key_awareness() {
        if ($this->supportskeyawareness === null) {
            $this->supportskeyawareness = ($this->store instanceof cache_is_key_aware);
        }
        return $this->supportskeyawareness;
    }

    /**
     * Returns true if the store natively supports locking.
     *
     * @return bool
     */
    protected function store_supports_native_locking() {
        if ($this->nativelocking === null) {
            $this->nativelocking = ($this->store instanceof cache_is_lockable);
        }
        return $this->nativelocking;
    }

    /**
     * @deprecated since 2.6
     * @see cache::use_static_acceleration()
     */
    protected function is_using_persist_cache() {
        throw new coding_exception('cache::is_using_persist_cache() can not be used anymore.' .
            ' Please use cache::use_static_acceleration() instead.');
    }

    /**
     * Returns true if this cache is making use of the static acceleration array.
     *
     * @return bool
     */
    protected function use_static_acceleration() {
        return $this->staticacceleration;
    }

    /**
     * @see cache::static_acceleration_has
     * @deprecated since 2.6
     */
    protected function is_in_persist_cache() {
        throw new coding_exception('cache::is_in_persist_cache() can not be used anymore.' .
            ' Please use cache::static_acceleration_has() instead.');
    }

    /**
     * Returns true if the requested key exists within the static acceleration array.
     *
     * @param string $key The parsed key
     * @return bool
     */
    protected function static_acceleration_has($key) {
        // This could be written as a single line, however it has been split because the ttl check is faster than the instanceof
        // and has_expired calls.
        if (!$this->staticacceleration || !isset($this->staticaccelerationarray[$key])) {
            return false;
        }
        return true;
    }

    /**
     * @deprecated since 2.6
     * @see cache::static_acceleration_get
     */
    protected function get_from_persist_cache() {
        throw new coding_exception('cache::get_from_persist_cache() can not be used anymore.' .
            ' Please use cache::static_acceleration_get() instead.');
    }

    /**
     * Returns the item from the static acceleration array if it exists there.
     *
     * @param string $key The parsed key
     * @return mixed|false Dereferenced data from the static acceleration array or false if it wasn't there.
     */
    protected function static_acceleration_get($key) {
        if (!$this->staticacceleration || !isset($this->staticaccelerationarray[$key])) {
            $result = false;
        } else {
            $data = $this->staticaccelerationarray[$key]['data'];

            if ($data instanceof cache_cached_object) {
                $result = $data->restore_object();
            } else if ($this->staticaccelerationarray[$key]['serialized']) {
                $result = unserialize($data);
            } else {
                $result = $data;
            }
        }
        if (cache_helper::result_found($result)) {
            if ($this->perfdebug) {
                cache_helper::record_cache_hit(cache_store::STATIC_ACCEL, $this->definition);
            }
            if ($this->staticaccelerationsize > 1 && $this->staticaccelerationcount > 1) {
                // Check to see if this is the last item on the static acceleration keys array.
                if (end($this->staticaccelerationkeys) !== $key) {
                    // It isn't the last item.
                    // Move the item to the end of the array so that it is last to be removed.
                    unset($this->staticaccelerationkeys[$key]);
                    $this->staticaccelerationkeys[$key] = $key;
                }
            }
            return $result;
        } else {
            if ($this->perfdebug) {
                cache_helper::record_cache_miss(cache_store::STATIC_ACCEL, $this->definition);
            }
            return false;
        }
    }

    /**
     * @deprecated since 2.6
     * @see cache::static_acceleration_set
     */
    protected function set_in_persist_cache() {
        throw new coding_exception('cache::set_in_persist_cache() can not be used anymore.' .
            ' Please use cache::static_acceleration_set() instead.');
    }

    /**
     * Sets a key value pair into the static acceleration array.
     *
     * @param string $key The parsed key
     * @param mixed $data
     * @return bool
     */
    protected function static_acceleration_set($key, $data) {
        if ($this->staticaccelerationsize !== false && isset($this->staticaccelerationkeys[$key])) {
            $this->staticaccelerationcount--;
            unset($this->staticaccelerationkeys[$key]);
        }

        // We serialize anything that's not;
        // 1. A known scalar safe value.
        // 2. A definition that says it's simpledata.  We trust it that it doesn't contain dangerous references.
        // 3. An object that handles dereferencing by itself.
        if (is_scalar($data) || $this->definition->uses_simple_data()
                || $data instanceof cache_cached_object) {
            $this->staticaccelerationarray[$key]['data'] = $data;
            $this->staticaccelerationarray[$key]['serialized'] = false;
        } else {
            $this->staticaccelerationarray[$key]['data'] = serialize($data);
            $this->staticaccelerationarray[$key]['serialized'] = true;
        }
        if ($this->staticaccelerationsize !== false) {
            $this->staticaccelerationcount++;
            $this->staticaccelerationkeys[$key] = $key;
            if ($this->staticaccelerationcount > $this->staticaccelerationsize) {
                $dropkey = array_shift($this->staticaccelerationkeys);
                unset($this->staticaccelerationarray[$dropkey]);
                $this->staticaccelerationcount--;
            }
        }
        return true;
    }

    /**
     * @deprecated since 2.6
     * @see cache::static_acceleration_delete()
     */
    protected function delete_from_persist_cache() {
        throw new coding_exception('cache::delete_from_persist_cache() can not be used anymore.' .
            ' Please use cache::static_acceleration_delete() instead.');
    }

    /**
     * Deletes an item from the static acceleration array.
     *
     * @param string|int $key As given to get|set|delete
     * @return bool True on success, false otherwise.
     */
    protected function static_acceleration_delete($key) {
        unset($this->staticaccelerationarray[$key]);
        if ($this->staticaccelerationsize !== false && isset($this->staticaccelerationkeys[$key])) {
            unset($this->staticaccelerationkeys[$key]);
            $this->staticaccelerationcount--;
        }
        return true;
    }

    /**
     * Purge the static acceleration cache.
     */
    protected function static_acceleration_purge() {
        $this->staticaccelerationarray = array();
        if ($this->staticaccelerationsize !== false) {
            $this->staticaccelerationkeys = array();
            $this->staticaccelerationcount = 0;
        }
    }

    /**
     * Returns the timestamp from the first request for the time from the cache API.
     *
     * This stamp needs to be used for all ttl and time based operations to ensure that we don't end up with
     * timing issues.
     *
     * @param   bool    $float Whether to use floating precision accuracy.
     * @return  int|float
     */
    public static function now($float = false) {
        if (self::$now === null) {
            self::$now = microtime(true);
        }

        if ($float) {
            return self::$now;
        } else {
            return (int) self::$now;
        }
    }

    /**
     * Get a 'purge' token used for cache invalidation handling.
     *
     * Note: This function is intended for use from within the Cache API only and not by plugins, or cache stores.
     *
     * @param   bool    $reset  Whether to reset the token and generate a new one.
     * @return  string
     */
    public static function get_purge_token($reset = false) {
        if (self::$purgetoken === null || $reset) {
            self::$now = null;
            self::$purgetoken = self::now(true) . '-' . uniqid('', true);
        }

        return self::$purgetoken;
    }

    /**
     * Compare a pair of purge tokens.
     *
     * If the two tokens are identical, then the return value is 0.
     * If the time component of token A is newer than token B, then a positive value is returned.
     * If the time component of token B is newer than token A, then a negative value is returned.
     *
     * Note: This function is intended for use from within the Cache API only and not by plugins, or cache stores.
     *
     * @param   string  $tokena
     * @param   string  $tokenb
     * @return  int
     */
    public static function compare_purge_tokens($tokena, $tokenb) {
        if ($tokena === $tokenb) {
            // There is an exact match.
            return 0;
        }

        // The token for when the cache was last invalidated.
        list($atime) = explode('-', "{$tokena}-", 2);

        // The token for this cache.
        list($btime) = explode('-', "{$tokenb}-", 2);

        if ($atime >= $btime) {
            // Token A is newer.
            return 1;
        } else {
            // Token A is older.
            return -1;
        }
    }

    /**
     * Subclasses may support purging cache of all data belonging to the
     * current user.
     */
    public function purge_current_user() {
    }
}

/**
 * An application cache.
 *
 * This class is used for application caches returned by the cache::make methods.
 * On top of the standard functionality it also allows locking to be required and or manually operated.
 *
 * This cache class should never be interacted with directly. Instead you should always use the cache::make methods.
 * It is technically possible to call those methods through this class however there is no guarantee that you will get an
 * instance of this class back again.
 *
 * @internal don't use me directly.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_application extends cache implements cache_loader_with_locking {

    /**
     * Lock identifier.
     * This is used to ensure the lock belongs to the cache instance + definition + user.
     * @var string
     */
    protected $lockidentifier;

    /**
     * Gets set to true if the cache's primary store natively supports locking.
     * If it does then we use that, otherwise we need to instantiate a second store to use for locking.
     * @var cache_store
     */
    protected $nativelocking = null;

    /**
     * Gets set to true if the cache is going to be using locking.
     * This isn't a requirement, it doesn't need to use locking (most won't) and this bool is used to quickly check things.
     * If required then locking will be forced for the get|set|delete operation.
     * @var bool
     */
    protected $requirelocking = false;

    /**
     * Gets set to true if the cache must use read locking (get|has).
     * @var bool
     */
    protected $requirelockingread = false;

    /**
     * Gets set to true if the cache must use write locking (set|delete)
     * @var bool
     */
    protected $requirelockingwrite = false;

    /**
     * Gets set to a cache_store to use for locking if the caches primary store doesn't support locking natively.
     * @var cache_lock_interface
     */
    protected $cachelockinstance;

    /**
     * Overrides the cache construct method.
     *
     * You should not call this method from your code, instead you should use the cache::make methods.
     *
     * @param cache_definition $definition
     * @param cache_store $store
     * @param cache_loader|cache_data_source $loader
     */
    public function __construct(cache_definition $definition, cache_store $store, $loader = null) {
        parent::__construct($definition, $store, $loader);
        $this->nativelocking = $this->store_supports_native_locking();
        if ($definition->require_locking()) {
            $this->requirelocking = true;
            $this->requirelockingread = $definition->require_locking_read();
            $this->requirelockingwrite = $definition->require_locking_write();
        }

        $this->handle_invalidation_events();
    }

    /**
     * Returns the identifier to use
     *
     * @staticvar int $instances Counts the number of instances. Used as part of the lock identifier.
     * @return string
     */
    public function get_identifier() {
        static $instances = 0;
        if ($this->lockidentifier === null) {
            $this->lockidentifier = md5(
                $this->get_definition()->generate_definition_hash() .
                sesskey() .
                $instances++ .
                'cache_application'
            );
        }
        return $this->lockidentifier;
    }

    /**
     * Fixes the instance up after a clone.
     */
    public function __clone() {
        // Force a new idenfitier.
        $this->lockidentifier = null;
    }

    /**
     * Acquires a lock on the given key.
     *
     * This is done automatically if the definition requires it.
     * It is recommended to use a definition if you want to have locking although it is possible to do locking without having
     * it required by the definition.
     * The problem with such an approach is that you cannot ensure that code will consistently use locking. You will need to
     * rely on the integrators review skills.
     *
     * @param string|int $key The key as given to get|set|delete
     * @return bool Returns true if the lock could be acquired, false otherwise.
     */
    public function acquire_lock($key) {
        $key = $this->parse_key($key);
        if ($this->nativelocking) {
            return $this->get_store()->acquire_lock($key, $this->get_identifier());
        } else {
            $this->ensure_cachelock_available();
            return $this->cachelockinstance->lock($key, $this->get_identifier());
        }
    }

    /**
     * Checks if this cache has a lock on the given key.
     *
     * @param string|int $key The key as given to get|set|delete
     * @return bool|null Returns true if there is a lock and this cache has it, null if no one has a lock on that key, false if
     *      someone else has the lock.
     */
    public function check_lock_state($key) {
        $key = $this->parse_key($key);
        if ($this->nativelocking) {
            return $this->get_store()->check_lock_state($key, $this->get_identifier());
        } else {
            $this->ensure_cachelock_available();
            return $this->cachelockinstance->check_state($key, $this->get_identifier());
        }
    }

    /**
     * Releases the lock this cache has on the given key
     *
     * @param string|int $key
     * @return bool True if the operation succeeded, false otherwise.
     */
    public function release_lock($key) {
        $key = $this->parse_key($key);
        if ($this->nativelocking) {
            return $this->get_store()->release_lock($key, $this->get_identifier());
        } else {
            $this->ensure_cachelock_available();
            return $this->cachelockinstance->unlock($key, $this->get_identifier());
        }
    }

    /**
     * Ensure that the dedicated lock store is ready to go.
     *
     * This should only happen if the cache store doesn't natively support it.
     */
    protected function ensure_cachelock_available() {
        if ($this->cachelockinstance === null) {
            $this->cachelockinstance = cache_helper::get_cachelock_for_store($this->get_store());
        }
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
     * @param int $version Version number
     * @param mixed $data The data to set against the key.
     * @param bool $setparents If true, sets all parent loaders, otherwise only this one
     * @return bool True on success, false otherwise.
     */
    protected function set_implementation($key, int $version, $data, bool $setparents = true): bool {
        if ($this->requirelockingwrite && !$this->acquire_lock($key)) {
            return false;
        }
        $result = parent::set_implementation($key, $version, $data, $setparents);
        if ($this->requirelockingwrite && !$this->release_lock($key)) {
            debugging('Failed to release cache lock on set operation... this should not happen.', DEBUG_DEVELOPER);
        }
        return $result;
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
        if ($this->requirelockingwrite) {
            $locks = array();
            foreach ($keyvaluearray as $id => $pair) {
                $key = $pair['key'];
                if ($this->acquire_lock($key)) {
                    $locks[] = $key;
                } else {
                    unset($keyvaluearray[$id]);
                }
            }
        }
        $result = parent::set_many($keyvaluearray);
        if ($this->requirelockingwrite) {
            foreach ($locks as $key) {
                if ($this->release_lock($key)) {
                    debugging('Failed to release cache lock on set_many operation... this should not happen.', DEBUG_DEVELOPER);
                }
            }
        }
        return $result;
    }

    /**
     * Retrieves the value for the given key from the cache.
     *
     * @param string|int $key The key for the data being requested.
     * @param int $requiredversion Minimum required version of the data or cache::VERSION_NONE
     * @param int $strictness One of IGNORE_MISSING | MUST_EXIST
     * @param mixed &$actualversion If specified, will be set to the actual version number retrieved
     * @return mixed|false The data from the cache or false if the key did not exist within the cache.
     */
    protected function get_implementation($key, int $requiredversion, int $strictness, &$actualversion = null) {
        if ($this->requirelockingread && $this->check_lock_state($key) === false) {
            // Read locking required and someone else has the read lock.
            return false;
        }
        return parent::get_implementation($key, $requiredversion, $strictness, $actualversion);
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
     * @param int $strictness One of IGNORE_MISSING or MUST_EXIST.
     * @return array An array of key value pairs for the items that could be retrieved from the cache.
     *      If MUST_EXIST was used and not all keys existed within the cache then an exception will be thrown.
     *      Otherwise any key that did not exist will have a data value of false within the results.
     * @throws coding_exception
     */
    public function get_many(array $keys, $strictness = IGNORE_MISSING) {
        if ($this->requirelockingread) {
            foreach ($keys as $id => $key) {
                $lock =$this->acquire_lock($key);
                if (!$lock) {
                    if ($strictness === MUST_EXIST) {
                        throw new coding_exception('Could not acquire read locks for all of the items being requested.');
                    } else {
                        // Can't return this as we couldn't get a read lock.
                        unset($keys[$id]);
                    }
                }

            }
        }
        return parent::get_many($keys, $strictness);
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
        if ($this->requirelockingwrite && !$this->acquire_lock($key)) {
            return false;
        }
        $result = parent::delete($key, $recurse);
        if ($this->requirelockingwrite && !$this->release_lock($key)) {
            debugging('Failed to release cache lock on delete operation... this should not happen.', DEBUG_DEVELOPER);
        }
        return $result;
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
        if ($this->requirelockingwrite) {
            $locks = array();
            foreach ($keys as $id => $key) {
                if ($this->acquire_lock($key)) {
                    $locks[] = $key;
                } else {
                    unset($keys[$id]);
                }
            }
        }
        $result = parent::delete_many($keys, $recurse);
        if ($this->requirelockingwrite) {
            foreach ($locks as $key) {
                if ($this->release_lock($key)) {
                    debugging('Failed to release cache lock on delete_many operation... this should not happen.', DEBUG_DEVELOPER);
                }
            }
        }
        return $result;
    }
}

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
 *       implement cache_is_searchable. This ensures stores used for the cache can be
 *       targetted for garbage collection of session data.
 *
 * This cache class should never be interacted with directly. Instead you should always use the cache::make methods.
 * It is technically possible to call those methods through this class however there is no guarantee that you will get an
 * instance of this class back again.
 *
 * @todo we should support locking in the session as well. Should be pretty simple to set up.
 *
 * @internal don't use me directly.
 * @method cache_store|cache_is_searchable get_store() Returns the cache store which must implement both cache_is_searchable.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_session extends cache {
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
     * @param cache_definition $definition
     * @param cache_store $store
     * @param cache_loader|cache_data_source $loader
     */
    public function __construct(cache_definition $definition, cache_store $store, $loader = null) {
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
        return 'u'.$this->currentuserid.'_'.$this->sessionid;
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
            return $key.$prefix;
        }
        return $prefix.'_'.parent::parse_key($key);
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
        if (is_object($data) && $data instanceof cacheable_object) {
            $data = new cache_cached_object($data);
        } else if (!$this->get_store()->supports_dereferencing_objects() && !is_scalar($data)) {
            // If data is an object it will be a reference.
            // If data is an array if may contain references.
            // We want to break references so that the cache cannot be modified outside of itself.
            // Call the function to unreference it (in the best way possible).
            $data = $this->unref($data);
        }
        // We dont' support native TTL here as we consolidate data for sessions.
        if ($this->has_a_ttl() && !$this->store_supports_native_ttl()) {
            $data = new cache_ttl_wrapper($data, $this->get_definition()->get_ttl());
        }
        $success = $this->get_store()->set($this->parse_key($key), $data);
        if ($this->perfdebug) {
            cache_helper::record_cache_set($this->get_store(), $this->get_definition(), 1,
                    $this->get_store()->get_last_io_bytes());
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
        $parsedkeys = array();
        $keymap = array();
        foreach ($keys as $key) {
            $parsedkey = $this->parse_key($key);
            $parsedkeys[$key] = $parsedkey;
            $keymap[$parsedkey] = $key;
        }
        $result = $this->get_store()->get_many($parsedkeys);
        if ($this->perfdebug) {
            $readbytes = $this->get_store()->get_last_io_bytes();
        }
        $return = array();
        $missingkeys = array();
        $hasmissingkeys = false;
        foreach ($result as $parsedkey => $value) {
            $key = $keymap[$parsedkey];
            if ($value instanceof cache_ttl_wrapper) {
                /* @var cache_ttl_wrapper $value */
                if ($value->has_expired()) {
                    $this->delete($keymap[$parsedkey]);
                    $value = false;
                } else {
                    $value = $value->data;
                }
            }
            if ($value instanceof cache_cached_object) {
                /* @var cache_cached_object $value */
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
            cache_helper::record_cache_hit($this->get_store(), $this->get_definition(), $hits, $readbytes);
            cache_helper::record_cache_miss($this->get_store(), $this->get_definition(), $misses);
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
        $parsedkeys = array_map(array($this, 'parse_key'), $keys);
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
        $data = array();
        $definitionid = $this->get_definition()->get_ttl();
        $simulatettl = $this->has_a_ttl() && !$this->store_supports_native_ttl();
        foreach ($keyvaluearray as $key => $value) {
            if (is_object($value) && $value instanceof cacheable_object) {
                $value = new cache_cached_object($value);
            } else if (!$this->get_store()->supports_dereferencing_objects() && !is_scalar($value)) {
                // If data is an object it will be a reference.
                // If data is an array if may contain references.
                // We want to break references so that the cache cannot be modified outside of itself.
                // Call the function to unreference it (in the best way possible).
                $value = $this->unref($value);
            }
            if ($simulatettl) {
                $value = new cache_ttl_wrapper($value, $definitionid);
            }
            $data[$key] = array(
                'key' => $this->parse_key($key),
                'value' => $value
            );
        }
        $successfullyset = $this->get_store()->set_many($data);
        if ($this->perfdebug && $successfullyset) {
            cache_helper::record_cache_set($this->get_store(), $this->get_definition(), $successfullyset,
                    $this->get_store()->get_last_io_bytes());
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
            $has = ($data instanceof cache_ttl_wrapper && !$data->has_expired());
        } else if (!$this->store_supports_key_awareness()) {
            // The store doesn't support key awareness, get the data and check it manually... puke.
            // Either no TTL is set of the store supports its handling natively.
            $data = $store->get($parsedkey);
            $has = ($data !== false);
        } else {
            // The store supports key awareness, this is easy!
            // Either no TTL is set of the store supports its handling natively.
            /* @var cache_store|cache_is_key_aware $store */
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
        /* @var cache_store|cache_is_key_aware $store */
        $store = $this->get_store();
        return $store->has_all(array_map(array($this, 'parse_key'), $keys));
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
        /* @var cache_store|cache_is_key_aware $store */
        $store = $this->get_store();
        return $store->has_any(array_map(array($this, 'parse_key'), $keys));
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

/**
 * An request cache.
 *
 * This class is used for request caches returned by the cache::make methods.
 *
 * This cache class should never be interacted with directly. Instead you should always use the cache::make methods.
 * It is technically possible to call those methods through this class however there is no guarantee that you will get an
 * instance of this class back again.
 *
 * @internal don't use me directly.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_request extends cache {
    // This comment appeases code pre-checker ;) !
}
