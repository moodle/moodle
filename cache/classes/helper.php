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

use core\lang_string;
use core\exception\coding_exception;
use DirectoryIterator;

/**
 * The cache helper class.
 *
 * The cache helper class provides common functionality to the cache API and is useful to developers within to interact with
 * the cache API in a general way.
 *
 * @package    core_cache
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /**
     * Statistics gathered by the cache API during its operation will be used here.
     * @static
     * @var array
     */
    protected static $stats = [];

    /**
     * The instance of the cache helper.
     * @var self
     */
    protected static $instance;

    /**
     * The site identifier used by the cache.
     * Set the first time get_site_identifier is called.
     * @var string
     */
    protected static $siteidentifier = null;

    /**
     * Returns true if the cache API can be initialised before Moodle has finished initialising itself.
     *
     * This check is essential when trying to cache the likes of configuration information. It checks to make sure that the cache
     * configuration file has been created which allows use to set up caching when ever is required.
     *
     * @return bool
     */
    public static function ready_for_early_init() {
        return config::config_file_exists();
    }

    /**
     * Returns an instance of the helper.
     *
     * This is designed for internal use only and acts as a static store.
     * @staticvar null $instance
     * @return self
     */
    protected static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructs an instance of the helper class. Again for internal use only.
     */
    protected function __construct() {
        // Nothing to do here, just making sure you can't get an instance of this.
    }

    /**
     * Used as a data store for initialised definitions.
     * @var array
     */
    protected $definitions = [];

    /**
     * Used as a data store for initialised cache stores
     * We use this because we want to avoid establishing multiple instances of a single store.
     * @var array
     */
    protected $stores = [];

    /**
     * Returns the class for use as a cache loader for the given mode.
     *
     * @param int $mode One of store::MODE_
     * @return string
     * @throws coding_exception
     */
    public static function get_class_for_mode($mode) {
        switch ($mode) {
            case store::MODE_APPLICATION:
                return application_cache::class;
            case store::MODE_REQUEST:
                return request_cache::class;
            case store::MODE_SESSION:
                return session_cache::class;
        }
        throw new coding_exception('Unknown cache mode passed. Must be one of store::MODE_*');
    }

    /**
     * Returns the cache stores to be used with the given definition.
     * @param definition $definition
     * @return array
     */
    public static function get_cache_stores(definition $definition) {
        $instance = config::instance();
        $stores = $instance->get_stores_for_definition($definition);
        $stores = self::initialise_cachestore_instances($stores, $definition);
        return $stores;
    }

    /**
     * Internal function for initialising an array of stores against a given cache definition.
     *
     * @param array $stores
     * @param definition $definition
     * @return store[]
     */
    protected static function initialise_cachestore_instances(array $stores, definition $definition) {
        $return = [];
        $factory = factory::instance();
        foreach ($stores as $name => $details) {
            $store = $factory->create_store_from_config($name, $details, $definition);
            if ($store !== false) {
                $return[] = $store;
            }
        }
        return $return;
    }

    /**
     * Returns a locakable_cache_interface instance suitable for use with the store.
     *
     * @param store $store
     * @return lockable_cache_interface
     */
    public static function get_cachelock_for_store(store $store) {
        $instance = config::instance();
        $lockconf = $instance->get_lock_for_store($store->my_name());
        $factory = factory::instance();
        return $factory->create_lock_instance($lockconf);
    }

    /**
     * Returns an array of plugins without using core methods.
     *
     * This function explicitly does NOT use core functions as it will in some circumstances be called before Moodle has
     * finished initialising. This happens when loading configuration for instance.
     *
     * @return array
     */
    public static function early_get_cache_plugins() {
        global $CFG;
        $result = [];
        $ignored = ['CVS', '_vti_cnf', 'simpletest', 'db', 'yui', 'tests'];
        $fulldir = $CFG->dirroot . '/cache/stores';
        $items = new DirectoryIterator($fulldir);
        foreach ($items as $item) {
            if ($item->isDot() or !$item->isDir()) {
                continue;
            }
            $pluginname = $item->getFilename();
            if (in_array($pluginname, $ignored)) {
                continue;
            }
            if (!is_valid_plugin_name($pluginname)) {
                // Better ignore plugins with problematic names here.
                continue;
            }
            $result[$pluginname] = $fulldir . '/' . $pluginname;
            unset($item);
        }
        unset($items);
        return $result;
    }

    /**
     * Invalidates a given set of keys from a given definition.
     *
     * @todo Invalidating by definition should also add to the event cache so that sessions can be invalidated (when required).
     *
     * @param string $component
     * @param string $area
     * @param array $identifiers
     * @param array|string|int $keys
     * @return boolean
     * @throws coding_exception
     */
    public static function invalidate_by_definition($component, $area, array $identifiers = [], $keys = []) {
        $cache = cache::make($component, $area, $identifiers);
        if (is_array($keys)) {
            $cache->delete_many($keys);
        } else if (is_scalar($keys)) {
            $cache->delete($keys);
        } else {
            throw new coding_exception('helper::invalidate_by_definition only accepts $keys as array, or scalar.');
        }
        return true;
    }

    /**
     * Invalidates a given set of keys by means of an event.
     *
     * Events cannot determine what identifiers might need to be cleared. Event based purge and invalidation
     * are only supported on caches without identifiers.
     *
     * @param string $event
     * @param array $keys
     */
    public static function invalidate_by_event($event, array $keys) {
        $instance = config::instance();
        $invalidationeventset = false;
        $factory = factory::instance();
        $inuse = $factory->get_caches_in_use();
        $purgetoken = null;
        foreach ($instance->get_definitions() as $name => $definitionarr) {
            $definition = definition::load($name, $definitionarr);
            if ($definition->invalidates_on_event($event)) {
                // First up check if there is a cache loader for this definition already.
                // If there is we need to invalidate the keys from there.
                $definitionkey = $definition->get_component() . '/' . $definition->get_area();
                if (isset($inuse[$definitionkey])) {
                    $inuse[$definitionkey]->delete_many($keys);
                }

                // We should only log events for application and session caches.
                // Request caches shouldn't have events as all data is lost at the end of the request.
                // Events should only be logged once of course and likely several definitions are watching so we
                // track its logging with $invalidationeventset.
                $logevent = ($invalidationeventset === false && $definition->get_mode() !== store::MODE_REQUEST);

                if ($logevent) {
                    // Get the event invalidation cache.
                    $cache = cache::make('core', 'eventinvalidation');
                    // Get any existing invalidated keys for this cache.
                    $data = $cache->get($event);
                    if ($data === false) {
                        // There are none.
                        $data = [];
                    }
                    // Add our keys to them with the current cache timestamp.
                    if (null === $purgetoken) {
                        $purgetoken = cache::get_purge_token(true);
                    }
                    foreach ($keys as $key) {
                        $data[$key] = $purgetoken;
                    }
                    // Set that data back to the cache.
                    $cache->set($event, $data);
                    // This only needs to occur once.
                    $invalidationeventset = true;
                }
            }
        }
    }

    /**
     * Purges the cache for a specific definition.
     *
     * @param string $component
     * @param string $area
     * @param array $identifiers
     * @return bool
     */
    public static function purge_by_definition($component, $area, array $identifiers = []) {
        // Create the cache.
        $cache = cache::make($component, $area, $identifiers);
        // Initialise, in case of a store.
        if ($cache instanceof store) {
            $factory = factory::instance();
            $definition = $factory->create_definition($component, $area, null);
            $cacheddefinition = clone $definition;
            $cacheddefinition->set_identifiers($identifiers);
            $cache->initialise($cacheddefinition);
        }
        // Purge baby, purge.
        $cache->purge();
        return true;
    }

    /**
     * Purges a cache of all information on a given event.
     *
     * Events cannot determine what identifiers might need to be cleared. Event based purge and invalidation
     * are only supported on caches without identifiers.
     *
     * @param string $event
     */
    public static function purge_by_event($event) {
        $instance = config::instance();
        $invalidationeventset = false;
        $factory = factory::instance();
        $inuse = $factory->get_caches_in_use();
        $purgetoken = null;
        foreach ($instance->get_definitions() as $name => $definitionarr) {
            $definition = definition::load($name, $definitionarr);
            if ($definition->invalidates_on_event($event)) {
                // First up check if there is a cache loader for this definition already.
                // If there is we need to invalidate the keys from there.
                $definitionkey = $definition->get_component() . '/' . $definition->get_area();
                if (isset($inuse[$definitionkey])) {
                    $inuse[$definitionkey]->purge();
                } else {
                    cache::make($definition->get_component(), $definition->get_area())->purge();
                }

                // We should only log events for application and session caches.
                // Request caches shouldn't have events as all data is lost at the end of the request.
                // Events should only be logged once of course and likely several definitions are watching so we
                // track its logging with $invalidationeventset.
                $logevent = ($invalidationeventset === false && $definition->get_mode() !== store::MODE_REQUEST);

                // We need to flag the event in the "Event invalidation" cache if it hasn't already happened.
                if ($logevent && $invalidationeventset === false) {
                    // Get the event invalidation cache.
                    $cache = cache::make('core', 'eventinvalidation');
                    // Create a key to invalidate all.
                    if (null === $purgetoken) {
                        $purgetoken = cache::get_purge_token(true);
                    }
                    $data = [
                        'purged' => $purgetoken,
                    ];
                    // Set that data back to the cache.
                    $cache->set($event, $data);
                    // This only needs to occur once.
                    $invalidationeventset = true;
                }
            }
        }
    }

    /**
     * Ensure that the stats array is ready to collect information for the given store and definition.
     * @param string $store
     * @param string $storeclass
     * @param string $definition A string that identifies the definition.
     * @param int $mode One of store::MODE_*. Since 2.9.
     */
    protected static function ensure_ready_for_stats($store, $storeclass, $definition, $mode = store::MODE_APPLICATION) {
        // This function is performance-sensitive, so exit as quickly as possible
        // if we do not need to do anything.
        if (isset(self::$stats[$definition]['stores'][$store])) {
            return;
        }

        if (!array_key_exists($definition, self::$stats)) {
            self::$stats[$definition] = [
                'mode' => $mode,
                'stores' => [
                    $store => [
                        'class' => $storeclass,
                        'hits' => 0,
                        'misses' => 0,
                        'sets' => 0,
                        'iobytes' => store::IO_BYTES_NOT_SUPPORTED,
                        'locks' => 0,
                    ],
                ],
            ];
        } else if (!array_key_exists($store, self::$stats[$definition]['stores'])) {
            self::$stats[$definition]['stores'][$store] = [
                'class' => $storeclass,
                'hits' => 0,
                'misses' => 0,
                'sets' => 0,
                'iobytes' => store::IO_BYTES_NOT_SUPPORTED,
                'locks' => 0,
            ];
        }
    }

    /**
     * Returns a string to describe the definition.
     *
     * This method supports the definition as a string due to legacy requirements.
     * It is backwards compatible when a string is passed but is not accurate.
     *
     * @since 2.9
     * @param definition|string $definition
     * @return string
     */
    protected static function get_definition_stat_id_and_mode($definition) {
        if (!($definition instanceof definition)) {
            // All core calls to this method have been updated, this is the legacy state.
            // We'll use application as the default as that is the most common, really this is not accurate of course but
            // at this point we can only guess and as it only affects calls to cache stat outside of core (of which there should
            // be none) I think that is fine.
            debugging('Please update you cache stat calls to pass the definition rather than just its ID.', DEBUG_DEVELOPER);
            return [(string)$definition, store::MODE_APPLICATION];
        }
        return [$definition->get_id(), $definition->get_mode()];
    }

    /**
     * Record a cache hit in the stats for the given store and definition.
     *
     * In Moodle 2.9 the $definition argument changed from accepting only a string to accepting a string or a
     * definition instance. It is preferable to pass a cache definition instance.
     *
     * In Moodle 3.9 the first argument changed to also accept a store.
     *
     * @internal
     * @param string|store $store
     * @param definition $definition You used to be able to pass a string here, however that is deprecated please pass the
     *      actual definition object now.
     * @param int $hits The number of hits to record (by default 1)
     * @param int $readbytes Number of bytes read from the cache or store::IO_BYTES_NOT_SUPPORTED
     */
    public static function record_cache_hit(
        $store,
        $definition,
        int $hits = 1,
        int $readbytes = store::IO_BYTES_NOT_SUPPORTED,
    ): void {
        $storeclass = '';
        if ($store instanceof store) {
            $storeclass = get_class($store);
            $store = $store->my_name();
        }
        [$definitionstr, $mode] = self::get_definition_stat_id_and_mode($definition);
        self::ensure_ready_for_stats($store, $storeclass, $definitionstr, $mode);
        self::$stats[$definitionstr]['stores'][$store]['hits'] += $hits;
        if ($readbytes !== store::IO_BYTES_NOT_SUPPORTED) {
            if (self::$stats[$definitionstr]['stores'][$store]['iobytes'] === store::IO_BYTES_NOT_SUPPORTED) {
                self::$stats[$definitionstr]['stores'][$store]['iobytes'] = $readbytes;
            } else {
                self::$stats[$definitionstr]['stores'][$store]['iobytes'] += $readbytes;
            }
        }
    }

    /**
     * Record a cache miss in the stats for the given store and definition.
     *
     * In Moodle 2.9 the $definition argument changed from accepting only a string to accepting a string or a
     * definition instance. It is preferable to pass a cache definition instance.
     *
     * In Moodle 3.9 the first argument changed to also accept a store.
     *
     * @internal
     * @param string|store $store
     * @param definition $definition You used to be able to pass a string here, however that is deprecated please pass the
     *      actual definition object now.
     * @param int $misses The number of misses to record (by default 1)
     */
    public static function record_cache_miss($store, $definition, $misses = 1) {
        $storeclass = '';
        if ($store instanceof store) {
            $storeclass = get_class($store);
            $store = $store->my_name();
        }
        [$definitionstr, $mode] = self::get_definition_stat_id_and_mode($definition);
        self::ensure_ready_for_stats($store, $storeclass, $definitionstr, $mode);
        self::$stats[$definitionstr]['stores'][$store]['misses'] += $misses;
    }

    /**
     * Record a cache set in the stats for the given store and definition.
     *
     * In Moodle 2.9 the $definition argument changed from accepting only a string to accepting a string or a
     * definition instance. It is preferable to pass a cache definition instance.
     *
     * In Moodle 3.9 the first argument changed to also accept a store.
     *
     * @internal
     * @param string|store $store
     * @param definition $definition You used to be able to pass a string here, however that is deprecated please pass the
     *      actual definition object now.
     * @param int $sets The number of sets to record (by default 1)
     * @param int $writebytes Number of bytes written to the cache or store::IO_BYTES_NOT_SUPPORTED
     */
    public static function record_cache_set(
        $store,
        $definition,
        int $sets = 1,
        int $writebytes = store::IO_BYTES_NOT_SUPPORTED
    ) {
        $storeclass = '';
        if ($store instanceof store) {
            $storeclass = get_class($store);
            $store = $store->my_name();
        }
        [$definitionstr, $mode] = self::get_definition_stat_id_and_mode($definition);
        self::ensure_ready_for_stats($store, $storeclass, $definitionstr, $mode);
        self::$stats[$definitionstr]['stores'][$store]['sets'] += $sets;
        if ($writebytes !== store::IO_BYTES_NOT_SUPPORTED) {
            if (self::$stats[$definitionstr]['stores'][$store]['iobytes'] === store::IO_BYTES_NOT_SUPPORTED) {
                self::$stats[$definitionstr]['stores'][$store]['iobytes'] = $writebytes;
            } else {
                self::$stats[$definitionstr]['stores'][$store]['iobytes'] += $writebytes;
            }
        }
    }

    /**
     * Return the stats collected so far.
     * @return array
     */
    public static function get_stats() {
        return self::$stats;
    }

    /**
     * Purge all of the cache stores of all of their data.
     *
     * Think twice before calling this method. It will purge **ALL** caches regardless of whether they have been used recently or
     * anything. This will involve full setup of the cache + the purge operation. On a site using caching heavily this WILL be
     * painful.
     *
     * @param bool $usewriter If set to true the config_writer class is used. This class is special as it avoids
     *      it is still usable when caches have been disabled.
     *      Please use this option only if you really must. It's purpose is to allow the cache to be purged when it would be
     *      otherwise impossible.
     */
    public static function purge_all($usewriter = false) {
        $factory = factory::instance();
        $config = $factory->create_config_instance($usewriter);
        foreach ($config->get_all_stores() as $store) {
            self::purge_store($store['name'], $config);
        }
        foreach ($factory->get_adhoc_caches_in_use() as $cache) {
            $cache->purge();
        }
    }

    /**
     * Purges a store given its name.
     *
     * @param string $storename
     * @param config|null $config
     * @return bool
     */
    public static function purge_store($storename, ?config $config = null) {
        if ($config === null) {
            $config = config::instance();
        }

        $stores = $config->get_all_stores();
        if (!array_key_exists($storename, $stores)) {
            // The store does not exist.
            return false;
        }

        $store = $stores[$storename];
        $class = $store['class'];


        // We check are_requirements_met although we expect is_ready is going to check as well.
        if (!$class::are_requirements_met()) {
            return false;
        }
        // Found the store: is it ready?
        /* @var store $instance */
        $instance = new $class($store['name'], $store['configuration']);
        if (!$instance->is_ready()) {
            unset($instance);
            return false;
        }
        foreach ($config->get_definitions_by_store($storename) as $id => $definition) {
            $definition = definition::load($id, $definition);
            $definitioninstance = clone($instance);
            $definitioninstance->initialise($definition);
            $definitioninstance->purge();
            unset($definitioninstance);
        }

        return true;
    }

    /**
     * Purges all of the stores used by a definition.
     *
     * Unlike helper::purge_by_definition this purges all of the data from the stores not
     * just the data relating to the definition.
     * This function is useful when you must purge a definition that requires setup but you don't
     * want to set it up.
     *
     * @param string $component
     * @param string $area
     */
    public static function purge_stores_used_by_definition($component, $area) {
        $factory = factory::instance();
        $config = $factory->create_config_instance();
        $definition = $factory->create_definition($component, $area);
        $stores = $config->get_stores_for_definition($definition);
        foreach ($stores as $store) {
            self::purge_store($store['name']);
        }
    }

    /**
     * Returns the translated name of the definition.
     *
     * @param definition $definition
     * @return lang_string
     */
    public static function get_definition_name($definition) {
        if ($definition instanceof definition) {
            return $definition->get_name();
        }
        $identifier = 'cachedef_' . clean_param($definition['area'], PARAM_STRINGID);
        $component = $definition['component'];
        if ($component === 'core') {
            $component = 'cache';
        }
        return new lang_string($identifier, $component);
    }

    /**
     * Hashes a descriptive key to make it shorter and still unique.
     * @param string|int $key
     * @param definition $definition
     * @return string
     */
    public static function hash_key($key, definition $definition) {
        if ($definition->uses_simple_keys()) {
            if (debugging() && preg_match('#[^a-zA-Z0-9_]#', $key ?? '')) {
                throw new coding_exception(
                    'Cache definition ' . $definition->get_id() . ' requires simple keys. Invalid key provided.',
                    $key,
                );
            }
            // We put the key first so that we can be sure the start of the key changes.
            return (string)$key . '-' . $definition->generate_single_key_prefix();
        }
        $key = $definition->generate_single_key_prefix() . '-' . $key;
        return sha1($key);
    }

    /**
     * Finds all definitions and updates them within the cache config file.
     *
     * @param bool $coreonly If set to true only core definitions will be updated.
     */
    public static function update_definitions($coreonly = false) {
        // First update definitions
        config_writer::update_definitions($coreonly);
        // Second reset anything we have already initialised to ensure we're all up to date.
        factory::reset();
    }

    /**
     * Update the site identifier stored by the cache API.
     *
     * @param string $siteidentifier
     * @return string The new site identifier.
     */
    public static function update_site_identifier($siteidentifier) {
        $factory = factory::instance();
        $factory->updating_started();
        $config = $factory->create_config_instance(true);
        $siteidentifier = $config->update_site_identifier($siteidentifier);
        $factory->updating_finished();
        factory::reset();
        return $siteidentifier;
    }

    /**
     * Returns the site identifier.
     *
     * @return string
     */
    public static function get_site_identifier() {
        global $CFG;
        if (!is_null(self::$siteidentifier)) {
            return self::$siteidentifier;
        }
        // If site identifier hasn't been collected yet attempt to get it from the cache config.
        $factory = factory::instance();
        // If the factory is initialising then we don't want to try to get it from the config or we risk
        // causing the cache to enter an infinite initialisation loop.
        if (!$factory->is_initialising()) {
            $config = $factory->create_config_instance();
            self::$siteidentifier = $config->get_site_identifier();
        }
        if (is_null(self::$siteidentifier)) {
            // If the site identifier is still null then config isn't aware of it yet.
            // We'll see if the CFG is loaded, and if not we will just use unknown.
            // It's very important here that we don't use get_config. We don't want an endless cache loop!
            if (!empty($CFG->siteidentifier)) {
                self::$siteidentifier = self::update_site_identifier($CFG->siteidentifier);
            } else {
                // It's not being recorded in MUC's config and the config data hasn't been loaded yet.
                // Likely we are initialising.
                return 'unknown';
            }
        }
        return self::$siteidentifier;
    }

    /**
     * Returns the site version.
     *
     * @return string
     */
    public static function get_site_version() {
        global $CFG;
        return (string)$CFG->version;
    }

    /**
     * Runs cron routines for MUC.
     */
    public static function cron() {
        self::clean_old_session_data(true);
    }

    /**
     * Cleans old session data from cache stores used for session based definitions.
     *
     * @param bool $output If set to true output will be given.
     */
    public static function clean_old_session_data($output = false) {
        global $CFG;
        if ($output) {
            mtrace('Cleaning up stale session data from cache stores.');
        }
        $factory = factory::instance();
        $config = $factory->create_config_instance();
        $definitions = $config->get_definitions();
        $purgetime = time() - $CFG->sessiontimeout;
        foreach ($definitions as $definitionarray) {
            // We are only interested in session caches.
            if (!($definitionarray['mode'] & store::MODE_SESSION)) {
                continue;
            }
            $definition = $factory->create_definition($definitionarray['component'], $definitionarray['area']);
            $stores = $config->get_stores_for_definition($definition);
            // Turn them into store instances.
            $stores = self::initialise_cachestore_instances($stores, $definition);
            // Initialise all of the stores used for that definition.
            foreach ($stores as $store) {
                // If the store doesn't support searching we can skip it.
                if (!($store instanceof searchable_cache_interface)) {
                    debugging('Cache stores used for session definitions should ideally be searchable.', DEBUG_DEVELOPER);
                    continue;
                }
                // Get all of the last access keys.
                $keys = $store->find_by_prefix(session_cache::LASTACCESS);
                $todelete = [];
                foreach ($store->get_many($keys) as $key => $value) {
                    $expiresvalue = 0;
                    if ($value instanceof ttl_wrapper) {
                        $expiresvalue = $value->data;
                    } else if ($value instanceof cached_object) {
                        $expiresvalue = $value->restore_object();
                    } else {
                        $expiresvalue = $value;
                    }
                    $expires = (int) $expiresvalue;

                    if ($expires > 0 && $expires < $purgetime) {
                        $prefix = substr($key, strlen(session_cache::LASTACCESS));
                        $foundbyprefix = $store->find_by_prefix($prefix);
                        $todelete = array_merge($todelete, [$key], $foundbyprefix);
                    }
                }
                if ($todelete) {
                    $outcome = (int) $store->delete_many($todelete);
                    if ($output) {
                        $strdef = s($definition->get_id());
                        $strstore = s($store->my_name());
                        mtrace("- Removed {$outcome} old {$strdef} sessions from the '{$strstore}' cache store.");
                    }
                }
            }
        }
    }

    /**
     * Returns an array of stores that would meet the requirements for every definition.
     *
     * These stores would be 100% suitable to map as defaults for cache modes.
     *
     * @return array[] An array of stores, keys are the store names.
     */
    public static function get_stores_suitable_for_mode_default() {
        $factory = factory::instance();
        $config = $factory->create_config_instance();
        $requirements = 0;
        foreach ($config->get_definitions() as $definition) {
            $definition = definition::load($definition['component'] . '/' . $definition['area'], $definition);
            $requirements = $requirements | $definition->get_requirements_bin();
        }
        $stores = [];
        foreach ($config->get_all_stores() as $name => $store) {
            if (!empty($store['features']) && ($store['features'] & $requirements)) {
                $stores[$name] = $store;
            }
        }
        return $stores;
    }

    /**
     * Returns stores suitable for use with a given definition.
     *
     * @param definition $definition
     * @return store[]
     */
    public static function get_stores_suitable_for_definition(definition $definition) {
        $factory = factory::instance();
        $stores = [];
        if ($factory->is_initialising() || $factory->stores_disabled()) {
            // No suitable stores here.
            return $stores;
        } else {
            $stores = self::get_cache_stores($definition);
            // If mappingsonly is set, having 0 stores is ok.
            if ((count($stores) === 0) && (!$definition->is_for_mappings_only())) {
                // No suitable stores we found for the definition. We need to come up with a sensible default.
                // If this has happened we can be sure that the user has mapped custom stores to either the
                // mode of the definition. The first alternative to try is the system default for the mode.
                // e.g. the default file store instance for application definitions.
                $config = $factory->create_config_instance();
                foreach ($config->get_stores($definition->get_mode()) as $name => $details) {
                    if (!empty($details['default'])) {
                        $stores[] = $factory->create_store_from_config($name, $details, $definition);
                        break;
                    }
                }
            }
        }
        return $stores;
    }

    /**
     * Returns an array of warnings from the cache API.
     *
     * The warning returned here are for things like conflicting store instance configurations etc.
     * These get shown on the admin notifications page for example.
     *
     * @param array|null $stores An array of stores to get warnings for, or null for all.
     * @return string[]
     */
    public static function warnings(?array $stores = null) {
        if ($stores === null) {
            $stores = administration_helper::get_store_instance_summaries();
        }
        $warnings = [];
        foreach ($stores as $store) {
            if (!empty($store['warnings'])) {
                $warnings = array_merge($warnings, $store['warnings']);
            }
        }
        return $warnings;
    }

    /**
     * A helper to determine whether a result was found.
     *
     * This has been deemed required after people have been confused by the fact that [] == false.
     *
     * @param mixed $value
     * @return bool
     */
    public static function result_found($value): bool {
        return $value !== false;
    }

    /**
     * Checks whether the cluster mode is available in PHP.
     *
     * @return bool Return true if the PHP supports redis cluster, otherwise false.
     */
    public static function is_cluster_available(): bool {
        return class_exists('RedisCluster');
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(helper::class, \cache_helper::class);
