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
 * This file contains the cache factory class.
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
 * The cache factory class.
 *
 * This factory class is important because it stores instances of objects used by the cache API and returns them upon requests.
 * This allows us to both reuse objects saving on overhead, and gives us an easy place to "reset" the cache API in situations that
 * we need such as unit testing.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_factory {

    /**
     * An instance of the cache_factory class created upon the first request.
     * @var cache_factory
     */
    protected static $instance;

    /**
     * An array containing caches created for definitions
     * @var array
     */
    protected $cachesfromdefinitions = array();

    /**
     * Array of caches created by parameters, ad-hoc definitions will have been used.
     * @var array
     */
    protected $cachesfromparams = array();

    /**
     * An array of instantiated stores.
     * @var array
     */
    protected $stores = array();

    /**
     * An array of configuration instances
     * @var array
     */
    protected $configs = array();

    /**
     * An array of initialised definitions
     * @var array
     */
    protected $definitions = array();

    /**
     * An array of lock plugins.
     * @var array
     */
    protected $lockplugins = null;

    /**
     * Returns an instance of the cache_factor method.
     *
     * @param bool $forcereload If set to true a new cache_factory instance will be created and used.
     * @return cache_factory
     */
    public static function instance($forcereload = false) {
        if ($forcereload || self::$instance === null) {
            self::$instance = new cache_factory();
        }
        return self::$instance;
    }

    /**
     * Protected constructor, please use the static instance method.
     */
    protected function __construct() {
        // Nothing to do here.
    }

    /**
     * Resets the arrays containing instantiated caches, stores, and config instances.
     */
    public static function reset() {
        $factory = self::instance();
        $factory->cachesfromdefinitions = array();
        $factory->cachesfromparams = array();
        $factory->stores = array();
        $factory->configs = array();
        $factory->definitions = array();
        $factory->lockplugins = null; // MUST be null in order to force its regeneration.
    }

    /**
     * Creates a cache object given the parameters for a definition.
     *
     * If a cache has already been created for the given definition then that cache instance will be returned.
     *
     * @param string $component
     * @param string $area
     * @param array $identifiers
     * @param string $aggregate
     * @return cache_application|cache_session|cache_request
     */
    public function create_cache_from_definition($component, $area, array $identifiers = array(), $aggregate = null) {
        $definitionname = $component.'/'.$area;
        if (array_key_exists($definitionname, $this->cachesfromdefinitions)) {
            $cache = $this->cachesfromdefinitions[$definitionname];
            $cache->set_identifiers($identifiers);
            return $cache;
        }
        $definition = $this->create_definition($component, $area, $aggregate);
        $definition->set_identifiers($identifiers);
        $cache = $this->create_cache($definition, $identifiers);
        if ($definition->should_be_persistent()) {
            $this->cachesfromdefinitions[$definitionname] = $cache;
        }
        return $cache;
    }

    /**
     * Creates an ad-hoc cache from the given param.
     *
     * If a cache has already been created using the same params then that cache instance will be returned.
     *
     * @param int $mode
     * @param string $component
     * @param string $area
     * @param array $identifiers
     * @param bool $persistent
     * @return cache_application|cache_session|cache_request
     */
    public function create_cache_from_params($mode, $component, $area, array $identifiers = array(), $persistent = false) {
        $key = "{$mode}_{$component}_{$area}";
        if (array_key_exists($key, $this->cachesfromparams)) {
            return $this->cachesfromparams[$key];
        }
        // Get the class. Note this is a late static binding so we need to use get_called_class.
        $definition = cache_definition::load_adhoc($mode, $component, $area, null, $persistent);
        $definition->set_identifiers($identifiers);
        $cache = $this->create_cache($definition, $identifiers);
        if ($definition->should_be_persistent()) {
            $cache->persist = true;
            $cache->persistcache = array();
            $this->cachesfromparams[$key] = $cache;
        }
        return $cache;
    }

    /**
     * Common public method to create a cache instance given a definition.
     *
     * This is used by the static make methods.
     *
     * @param cache_definition $definition
     * @return cache_application|cache_session|cache_store
     * @throws coding_exception
     */
    public function create_cache(cache_definition $definition) {
        $class = $definition->get_cache_class();
        $stores = cache_helper::get_cache_stores($definition);
        if (count($stores) === 0) {
            // Hmm no stores, better provide a dummy store to mimick functionality. The dev will be none the wiser.
            $stores[] = $this->create_dummy_store($definition);
        }
        $loader = null;
        if ($definition->has_data_source()) {
            $loader = $definition->get_data_source();
        }
        while (($store = array_pop($stores)) !== null) {
            $loader = new $class($definition, $store, $loader);
        }
        return $loader;
    }

    /**
     * Creates a store instance given its name and configuration.
     *
     * If the store has already been instantiated then the original objetc will be returned. (reused)
     *
     * @param string $name The name of the store (must be unique remember)
     * @param array $details
     * @param cache_definition $definition The definition to instantiate it for.
     * @return boolean
     */
    public function create_store_from_config($name, array $details, cache_definition $definition) {
        if (!array_key_exists($name, $this->stores)) {
            // Properties: name, plugin, configuration, class.
            $class = $details['class'];
            $store = new $class($details['name'], $details['configuration']);
            $this->stores[$name] = $store;
        }
        $store = $this->stores[$name];
        if (!$store->is_ready() || !$store->is_supported_mode($definition->get_mode())) {
            return false;
        }
        $store = clone($this->stores[$name]);
        $store->initialise($definition);
        return $store;
    }

    /**
     * Creates a cache config instance with the ability to write if required.
     *
     * @param bool $writer If set to true an instance that can update the configuration will be returned.
     * @return cache_config|cache_config_writer
     */
    public function create_config_instance($writer = false) {
        global $CFG;

        // Check if we need to create a config file with defaults.
        $needtocreate = !cache_config::config_file_exists();

        // The class to use.
        $class = 'cache_config';
        if ($writer || $needtocreate) {
            require_once($CFG->dirroot.'/cache/locallib.php');
            $class .= '_writer';
        }

        // Check if this is a PHPUnit test and redirect to the phpunit config classes if it is.
        if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
            require_once($CFG->dirroot.'/cache/locallib.php');
            require_once($CFG->dirroot.'/cache/tests/fixtures/lib.php');
            // We have just a single class for PHP unit tests. We don't care enough about its
            // performance to do otherwise and having a single method allows us to inject things into it
            // while testing.
            $class = 'cache_config_phpunittest';
        }

        if ($needtocreate) {
            // Create the default configuration.
            $class::create_default_configuration();
        }

        if (!array_key_exists($class, $this->configs)) {
            // Create a new instance and call it to load it.
            $this->configs[$class] = new $class;
            $this->configs[$class]->load();
        }

        // Return the instance.
        return $this->configs[$class];
    }

    /**
     * Creates a definition instance or returns the existing one if it has already been created.
     * @param string $component
     * @param string $area
     * @param string $aggregate
     * @return cache_definition
     */
    public function create_definition($component, $area, $aggregate = null) {
        $id = $component.'/'.$area;
        if ($aggregate) {
            $id .= '::'.$aggregate;
        }
        if (!array_key_exists($id, $this->definitions)) {
            $instance = $this->create_config_instance();
            $definition = $instance->get_definition_by_id($id);
            if (!$definition) {
                $this->reset();
                $instance = $this->create_config_instance(true);
                $instance->update_definitions();
                $definition = $instance->get_definition_by_id($id);
                if (!$definition) {
                    throw new coding_exception('The requested cache definition does not exist.'. $id, $id);
                } else {
                    debugging('Cache definitions reparsed causing cache reset in order to locate definition.
                        You should bump the version number to ensure definitions are reprocessed.', DEBUG_DEVELOPER);
                }
            }
            $this->definitions[$id] = cache_definition::load($id, $definition, $aggregate);
        }
        return $this->definitions[$id];
    }

    /**
     * Creates a dummy store object for use when a loader has no potential stores to use.
     *
     * @param cache_definition $definition
     * @return cachestore_dummy
     */
    protected function create_dummy_store(cache_definition $definition) {
        global $CFG;
        require_once($CFG->dirroot.'/cache/classes/dummystore.php');
        $store = new cachestore_dummy();
        $store->initialise($definition);
        return $store;
    }

    /**
     * Returns a lock instance ready for use.
     *
     * @param array $config
     * @return cache_lock_interface
     */
    public function create_lock_instance(array $config) {
        if (!array_key_exists('name', $config) || !array_key_exists('type', $config)) {
            throw new coding_exception('Invalid cache lock instance provided');
        }
        $name = $config['name'];
        $type = $config['type'];
        unset($config['name']);
        unset($config['type']);

        if ($this->lockplugins === null) {
            $this->lockplugins = get_plugin_list_with_class('cachelock', '', 'lib.php');
        }
        if (!array_key_exists($type, $this->lockplugins)) {
            throw new coding_exception('Invalid cache lock type.');
        }
        $class = $this->lockplugins[$type];
        return new $class($name, $config);
    }
}