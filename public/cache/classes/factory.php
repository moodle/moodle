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
use cache_config_testing;
use cache_phpunit_factory;

/**
 * The cache factory class.
 *
 * This factory class is important because it stores instances of objects used by the cache API and returns them upon requests.
 * This allows us to both reuse objects saving on overhead, and gives us an easy place to "reset" the cache API in situations that
 * we need such as unit testing.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_cache
 */
class factory {
    /** The cache has not been initialised yet. */
    const STATE_UNINITIALISED = 0;
    /** The cache is in the process of initialising itself. */
    const STATE_INITIALISING = 1;
    /** The cache is in the process of saving its configuration file. */
    const STATE_SAVING = 2;
    /** The cache is ready to use. */
    const STATE_READY = 3;
    /** The cache is currently updating itself */
    const STATE_UPDATING = 4;
    /** The cache encountered an error while initialising. */
    const STATE_ERROR_INITIALISING = 9;
    /** The cache has been disabled. */
    const STATE_DISABLED = 10;
    /** The cache stores have been disabled */
    const STATE_STORES_DISABLED = 11;

    /**
     * An instance of the factory class created upon the first request.
     * @var factory
     */
    protected static $instance;

    /**
     * An array containing caches created for definitions
     * @var array
     */
    protected $cachesfromdefinitions = [];

    /**
     * Array of caches created by parameters, ad-hoc definitions will have been used.
     * @var array
     */
    protected $cachesfromparams = [];

    /**
     * An array of stores organised by definitions.
     * @var array
     */
    protected $definitionstores = [];

    /**
     * An array of instantiated stores.
     * @var array
     */
    protected $stores = [];

    /**
     * An array of configuration instances
     * @var array
     */
    protected $configs = [];

    /**
     * An array of initialised definitions
     * @var array
     */
    protected $definitions = [];

    /**
     * An array of lock plugins.
     * @var array
     */
    protected $lockplugins = [];

    /**
     * The current state of the cache API.
     * @var int
     */
    protected $state = 0;

    /**
     * The current cache display helper.
     * @var core_cache\local\administration_display_helper
     */
    protected static $displayhelper = null;

    /**
     * Returns an instance of the factory class.
     *
     * @param bool $forcereload If set to true a new factory instance will be created and used.
     * @return factory
     */
    public static function instance($forcereload = false) {
        global $CFG;
        if ($forcereload || self::$instance === null) {
            // Initialise a new factory to facilitate our needs.
            if (defined('CACHE_DISABLE_ALL') && CACHE_DISABLE_ALL !== false) {
                // The cache has been disabled. Load disabledlib and start using the factory designed to handle this
                // situation. It will use disabled alternatives where available.
                self::$instance = new disabled_factory();
            } else if ((defined('PHPUNIT_TEST') && PHPUNIT_TEST) || defined('BEHAT_SITE_RUNNING')) {
                // We're using the test factory.
                require_once($CFG->dirroot . '/cache/tests/fixtures/lib.php');
                self::$instance = new cache_phpunit_factory();
                if (defined('CACHE_DISABLE_STORES') && CACHE_DISABLE_STORES !== false) {
                    // The cache stores have been disabled.
                    self::$instance->set_state(self::STATE_STORES_DISABLED);
                }
            } else if (!empty($CFG->alternative_cache_factory_class)) {
                $factoryclass = $CFG->alternative_cache_factory_class;
                self::$instance = new $factoryclass();
            } else {
                // We're using the regular factory.
                self::$instance = new factory();
                if (defined('CACHE_DISABLE_STORES') && CACHE_DISABLE_STORES !== false) {
                    // The cache stores have been disabled.
                    self::$instance->set_state(self::STATE_STORES_DISABLED);
                }
            }
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
        $factory->reset_cache_instances();
        $factory->configs = [];
        $factory->definitions = [];
        $factory->definitionstores = [];
        $factory->lockplugins = []; // MUST be null in order to force its regeneration.
        // Reset the state to uninitialised.
        $factory->state = self::STATE_UNINITIALISED;
    }

    /**
     * Resets the stores, clearing the array of created stores.
     *
     * Cache objects still held onto by the code that initialised them will remain as is
     * however all future requests for a cache/store will lead to a new instance being re-initialised.
     */
    public function reset_cache_instances() {
        $this->cachesfromdefinitions = [];
        $this->cachesfromparams = [];
        $this->stores = [];
    }

    /**
     * Creates a cache object given the parameters for a definition.
     *
     * If a cache has already been created for the given definition then that cache instance will be returned.
     *
     * @param string $component
     * @param string $area
     * @param array $identifiers
     * @param string $unused Used to be data source aggregate however that was removed and this is now unused.
     * @return application_cache|session_cache|request_cache
     */
    public function create_cache_from_definition($component, $area, array $identifiers = [], $unused = null) {
        $identifierstring = empty($identifiers) ? '' : '/' . http_build_query($identifiers, '', '&amp;');
        $definitionname = $component . '/' . $area . $identifierstring;
        if (isset($this->cachesfromdefinitions[$definitionname])) {
            $cache = $this->cachesfromdefinitions[$definitionname];
            return $cache;
        }
        $definition = $this->create_definition($component, $area);
        // Identifiers are cached as part of the cache creation, so we store a cloned version of the cache.
        $cacheddefinition = clone($definition);
        $cacheddefinition->set_identifiers($identifiers);
        $cache = $this->create_cache($cacheddefinition);

        // Loaders are always held onto to speed up subsequent requests.
        $this->cachesfromdefinitions[$definitionname] = $cache;
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
     * @param array $options An array of options, available options are:
     *   - simplekeys : Set to true if the keys you will use are a-zA-Z0-9_
     *   - simpledata : Set to true if the type of the data you are going to store is scalar, or an array of scalar vars
     *   - staticacceleration : If set to true the cache will hold onto data passing through it.
     *   - staticaccelerationsize : The maximum number of items to hold onto for acceleration purposes.
     * @return application_cache|session_cache|request_cache
     */
    public function create_cache_from_params($mode, $component, $area, array $identifiers = [], array $options = []) {
        $identifierstring = empty($identifiers) ? '' : '_' . http_build_query($identifiers, '', '&amp;');
        $key = "{$mode}_{$component}_{$area}{$identifierstring}";
        if (isset($this->cachesfromparams[$key])) {
            return $this->cachesfromparams[$key];
        }
        // Regular cache definitions are cached inside create_definition().  This is not the case for Adhoc definitions
        // using load_adhoc().  They are built as a new object on each call.
        // We do not need to clone the definition because we know it's new.
        $definition = definition::load_adhoc($mode, $component, $area, $options);
        $definition->set_identifiers($identifiers);
        $cache = $this->create_cache($definition);
        $this->cachesfromparams[$key] = $cache;
        return $cache;
    }

    /**
     * Common public method to create a cache instance given a definition.
     *
     * This is used by the static make methods.
     *
     * @param definition $definition
     * @return application_cache|session_cache|store
     * @throws coding_exception
     */
    public function create_cache(definition $definition) {
        $class = $definition->get_cache_class();
        $stores = helper::get_stores_suitable_for_definition($definition);
        foreach ($stores as $key => $store) {
            if (!$store::are_requirements_met()) {
                unset($stores[$key]);
            }
        }
        if (count($stores) === 0) {
            // Hmm still no stores, better provide a dummy store to mimic functionality. The dev will be none the wiser.
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
     * If the store has already been instantiated then the original object will be returned. (reused)
     *
     * @param string $name The name of the store (must be unique remember)
     * @param array $details
     * @param definition $definition The definition to instantiate it for.
     * @return boolean|store
     */
    public function create_store_from_config($name, array $details, definition $definition) {
        if (!array_key_exists($name, $this->stores)) {
            // Properties: name, plugin, configuration, class.
            $class = $details['class'];
            if (!$class::are_requirements_met()) {
                return false;
            }
            $store = new $class($details['name'], $details['configuration']);
            $this->stores[$name] = $store;
        }
        /* @var store $store */
        $store = $this->stores[$name];
        // We check are_requirements_met although we expect is_ready is going to check as well.
        if (!$store::are_requirements_met() || !$store->is_ready() || !$store->is_supported_mode($definition->get_mode())) {
            return false;
        }
        // We always create a clone of the original store.
        // If we were to clone a store that had already been initialised with a definition then
        // we'd run into a myriad of issues.
        // We use a method of the store to create a clone rather than just creating it ourselves
        // so that if any store out there doesn't handle cloning they can override this method in
        // order to address the issues.
        $store = $this->stores[$name]->create_clone($details);
        $store->initialise($definition);
        $definitionid = $definition->get_id();
        if (!isset($this->definitionstores[$definitionid])) {
            $this->definitionstores[$definitionid] = [];
        }
        $this->definitionstores[$definitionid][] = $store;
        return $store;
    }

    /**
     * Returns an array of cache stores that have been initialised for use in definitions.
     * @param definition $definition
     * @return array
     */
    public function get_store_instances_in_use(definition $definition) {
        $id = $definition->get_id();
        if (!isset($this->definitionstores[$id])) {
            return [];
        }
        return $this->definitionstores[$id];
    }

    /**
     * Returns the cache instances that have been used within this request.
     * @since Moodle 2.6
     * @return array
     */
    public function get_caches_in_use() {
        return $this->cachesfromdefinitions;
    }

    /**
     * Gets all adhoc caches that have been used within this request.
     *
     * @return store[] Caches currently in use
     */
    public function get_adhoc_caches_in_use() {
        return $this->cachesfromparams;
    }

    /**
     * Creates a cache config instance with the ability to write if required.
     *
     * @param bool $writer If set to true an instance that can update the configuration will be returned.
     * @return config|config_writer
     */
    public function create_config_instance($writer = false) {
        global $CFG;

        // The class to use.
        $class = config::class;
        // Are we running tests of some form?
        $testing = (defined('PHPUNIT_TEST') && PHPUNIT_TEST) || defined('BEHAT_SITE_RUNNING');

        // Check if this is a PHPUnit test and redirect to the phpunit config classes if it is.
        if ($testing) {
            require_once($CFG->dirroot . '/cache/tests/fixtures/lib.php');
            // We have just a single class for PHP unit tests. We don't care enough about its
            // performance to do otherwise and having a single method allows us to inject things into it
            // while testing.
            $class = cache_config_testing::class;
        }

        // Check if we need to create a config file with defaults.
        $needtocreate = !$class::config_file_exists();

        if ($writer || $needtocreate) {
            if (!$testing) {
                $class .= '_writer';
            }
        }

        $error = false;
        if ($needtocreate) {
            // Create the default configuration.
            // Update the state, we are now initialising the cache.
            self::set_state(self::STATE_INITIALISING);
            /** @var config_writer $class */
            $configuration = $class::create_default_configuration();
            if ($configuration !== true) {
                // Failed to create the default configuration. Disable the cache stores and update the state.
                self::set_state(self::STATE_ERROR_INITIALISING);
                $this->configs[$class] = new $class();
                $this->configs[$class]->load($configuration);
                $error = true;
            }
        }

        if (!array_key_exists($class, $this->configs)) {
            // Create a new instance and call it to load it.
            $this->configs[$class] = new $class();
            $this->configs[$class]->load();
        }

        if (!$error) {
            // The cache is now ready to use. Update the state.
            self::set_state(self::STATE_READY);
        }

        // Return the instance.
        return $this->configs[$class];
    }

    /**
     * Creates a definition instance or returns the existing one if it has already been created.
     * @param string $component
     * @param string $area
     * @param string $unused This used to be data source aggregate - however that functionality has been removed and
     *        this argument is now unused.
     * @return definition
     * @throws coding_exception If the definition cannot be found.
     */
    public function create_definition($component, $area, $unused = null) {
        $id = $component . '/' . $area;
        if (!isset($this->definitions[$id])) {
            // This is the first time this definition has been requested.
            if ($this->is_initialising()) {
                // We're initialising the cache right now. Don't try to create another config instance.
                // We'll just use an ad-hoc cache for the time being.
                $definition = definition::load_adhoc(store::MODE_REQUEST, $component, $area);
            } else {
                // Load all the known definitions and find the desired one.
                $instance = $this->create_config_instance();
                $definition = $instance->get_definition_by_id($id);
                if (!$definition) {
                    // Oh-oh the definition doesn't exist.
                    // There are several things that could be going on here.
                    // We may be installing/upgrading a site and have hit a definition that hasn't been used before.
                    // Of the developer may be trying to use a newly created definition.
                    if ($this->is_updating()) {
                        // The cache is presently initialising and the requested cache definition has not been found.
                        // This means that the cache initialisation has requested something from a cache (I had recursive nightmares about this).
                        // To serve this purpose and avoid errors we are going to make use of an ad-hoc cache rather than
                        // search for the definition which would possibly cause an infitite loop trying to initialise the cache.
                        $definition = definition::load_adhoc(store::MODE_REQUEST, $component, $area);
                    } else {
                        // Either a typo of the developer has just created the definition and is using it for the first time.
                        $this->reset();
                        $instance = $this->create_config_instance(true);
                        $instance->update_definitions();
                        $definition = $instance->get_definition_by_id($id);
                        if (!$definition) {
                            throw new coding_exception('The requested cache definition does not exist.' . $id, $id);
                        }
                        if (!$this->is_disabled()) {
                            debugging('Cache definitions reparsed causing cache reset in order to locate definition.
                                You should bump the version number to ensure definitions are reprocessed.', DEBUG_DEVELOPER);
                        }
                        $definition = definition::load($id, $definition);
                    }
                } else {
                    $definition = definition::load($id, $definition);
                }
            }
            $this->definitions[$id] = $definition;
        }
        return $this->definitions[$id];
    }

    /**
     * Creates a dummy store object for use when a loader has no potential stores to use.
     *
     * @param definition $definition
     * @return dummy_cachestore
     */
    protected function create_dummy_store(definition $definition) {
        $store = new dummy_cachestore();
        $store->initialise($definition);
        return $store;
    }

    /**
     * Returns a lock instance ready for use.
     *
     * @param array $config
     * @return lockable_cache_interface
     */
    public function create_lock_instance(array $config) {
        global $CFG;
        if (!array_key_exists('name', $config) || !array_key_exists('type', $config)) {
            throw new coding_exception('Invalid cache lock instance provided');
        }
        $name = $config['name'];
        $type = $config['type'];
        unset($config['name']);
        unset($config['type']);

        if (!isset($this->lockplugins[$type])) {
            $pluginname = substr($type, 10);
            $file = $CFG->dirroot . "/cache/locks/{$pluginname}/lib.php";
            if (file_exists($file) && is_readable($file)) {
                require_once($file);
            }
            if (!class_exists($type)) {
                throw new coding_exception('Invalid lock plugin requested.');
            }
            $this->lockplugins[$type] = $type;
        }
        if (!array_key_exists($type, $this->lockplugins)) {
            throw new coding_exception('Invalid cache lock type.');
        }
        $class = $this->lockplugins[$type];
        return new $class($name, $config);
    }

    /**
     * Returns the current state of the cache API.
     *
     * @return int
     */
    public function get_state() {
        return $this->state;
    }

    /**
     * Updates the state fo the cache API.
     *
     * @param int $state
     * @return bool
     */
    public function set_state($state) {
        if ($state <= $this->state) {
            return false;
        }
        $this->state = $state;
        return true;
    }

    /**
     * Informs the factory that the cache is currently updating itself.
     *
     * This forces the state to upgrading and can only be called once the cache is ready to use.
     * Calling it ensure we don't try to reinstantite things when requesting cache definitions that don't exist yet.
     */
    public function updating_started() {
        if ($this->state !== self::STATE_READY) {
            return false;
        }
        $this->state = self::STATE_UPDATING;
        return true;
    }

    /**
     * Informs the factory that the upgrading has finished.
     *
     * This forces the state back to ready.
     */
    public function updating_finished() {
        $this->state = self::STATE_READY;
    }

    /**
     * Returns true if the cache API has been disabled.
     *
     * @return bool
     */
    public function is_disabled() {
        return $this->state === self::STATE_DISABLED;
    }

    /**
     * Returns true if the cache is currently initialising itself.
     *
     * This includes both initialisation and saving the cache config file as part of that initialisation.
     *
     * @return bool
     */
    public function is_initialising() {
        return $this->state === self::STATE_INITIALISING || $this->state === self::STATE_SAVING;
    }

    /**
     * Returns true if the cache is currently updating itself.
     *
     * @return bool
     */
    public function is_updating() {
        return $this->state === self::STATE_UPDATING;
    }

    /**
     * Disables as much of the cache API as possible.
     *
     * All of the magic associated with the disabled cache is wrapped into this function.
     * In switching out the factory for the disabled factory it gains full control over the initialisation of objects
     * and can use all of the disabled alternatives.
     * Simple!
     *
     * This function has been marked as protected so that it cannot be abused through the public API presently.
     * Perhaps in the future we will allow this, however as per the build up to the first release containing
     * MUC it was decided that this was just to risky and abusable.
     */
    protected static function disable() {
        self::$instance = new disabled_factory();
    }

    /**
     * Returns true if the cache stores have been disabled.
     *
     * @return bool
     */
    public function stores_disabled() {
        return $this->state === self::STATE_STORES_DISABLED || $this->is_disabled();
    }

    /**
     * Disables cache stores.
     *
     * The cache API will continue to function however none of the actual stores will be used.
     * Instead the dummy store will be provided for all cache requests.
     * This is useful in situations where you cannot be sure any stores are working.
     *
     * In order to re-enable the cache you must call the cache factories static reset method:
     * <code>
     * // Disable the cache factory.
     * factory::disable_stores();
     * // Re-enable the cache factory by resetting it.
     * factory::reset();
     * </code>
     */
    public static function disable_stores() {
        // First reset to clear any static acceleration array.
        $factory = self::instance();
        $factory->reset_cache_instances();
        $factory->set_state(self::STATE_STORES_DISABLED);
    }

    /**
     * Returns an instance of the current display_helper.
     *
     * @return administration_helper
     */
    public static function get_administration_display_helper(): administration_helper {
        if (is_null(self::$displayhelper)) {
            self::$displayhelper = new \core_cache\local\administration_display_helper();
        }
        return self::$displayhelper;
    }

    /**
     * Gets the config_writer to use when caching is disabled.
     * This should only be called from disabled_factory.
     *
     * @return config_writer
     */
    public static function get_disabled_writer(): config_writer {
        global $CFG;

        // Figure out if we are in a recursive loop using late static binding.
        // This happens when get_disabled_writer is not overridden. We just want the default.
        $loop = false;
        if (!empty($CFG->alternative_cache_factory_class)) {
            $loop = get_called_class() === $CFG->alternative_cache_factory_class;
        }

        if (!$loop && !empty($CFG->alternative_cache_factory_class)) {
            // Get the class to use from the alternative factory.
            $factoryinstance = new $CFG->alternative_cache_factory_class();
            return $factoryinstance::get_disabled_writer();
        } else {
            // We got here from disabled_factory.
            // We should use the default writer here.
            // Make sure we have a default config if needed.
            if (!config::config_file_exists()) {
                config_writer::create_default_configuration(true);
            }

            return new config_writer();
        }
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(factory::class, \cache_factory::class);
