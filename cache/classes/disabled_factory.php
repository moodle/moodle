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
 * The cache factory class used when the Cache has been disabled.
 *
 * @package core_cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_factory_disabled extends cache_factory {
    /** @var array Array of temporary caches in use. */
    protected static $tempcaches = [];

    /**
     * Returns an instance of the cache_factor method.
     *
     * @param bool $forcereload Unused.
     * @return cache_factory
     * @throws coding_exception
     */
    public static function instance($forcereload = false) {
        throw new coding_exception('You must not call to this cache factory within your code.');
    }

    /**
     * Creates a definition instance or returns the existing one if it has already been created.
     *
     * @param string $component
     * @param string $area
     * @param string $unused Used to be datasourceaggregate but that was removed and this is now unused.
     * @return cache_definition
     */
    public function create_definition($component, $area, $unused = null) {
        $definition = parent::create_definition($component, $area);
        if ($definition->has_data_source()) {
            return $definition;
        }

        return cache_definition::load_adhoc(cache_store::MODE_REQUEST, $component, $area);
    }

    /**
     * Common public method to create a cache instance given a definition.
     *
     * @param cache_definition $definition
     * @return cache_application|cache_session|cache_store
     * @throws coding_exception
     */
    public function create_cache(cache_definition $definition) {
        $loader = null;
        if ($definition->has_data_source()) {
            $loader = $definition->get_data_source();
        }
        return new cache_disabled($definition, $this->create_dummy_store($definition), $loader);
    }

    /**
     * Creates a cache object given the parameters for a definition.
     *
     * @param string $component
     * @param string $area
     * @param array $identifiers
     * @param string $unused Used to be datasourceaggregate but that was removed and this is now unused.
     * @return cache_application|cache_session|request_cache
     */
    public function create_cache_from_definition($component, $area, array $identifiers = array(), $unused = null) {
        // Temporary in-memory caches are sometimes allowed when caching is disabled.
        if (\core_cache\allow_temporary_caches::is_allowed() && !$identifiers) {
            $key = $component . '/' . $area;
            if (array_key_exists($key, self::$tempcaches)) {
                $cache = self::$tempcaches[$key];
            } else {
                $definition = $this->create_definition($component, $area);
                // The cachestore_static class returns true to all three 'SUPPORTS_' checks so it
                // can be used with all definitions.
                $store = new cachestore_static('TEMP:' . $component . '/' . $area);
                $store->initialise($definition);
                // We need to use a cache loader wrapper rather than directly returning the store,
                // or it wouldn't have support for versioning. The cache_application class is used
                // (rather than cache_request which might make more sense logically) because it
                // includes support for locking, which might be necessary for some caches.
                $cache = new cache_application($definition, $store);
                self::$tempcaches[$key] = $cache;
            }
            return $cache;
        }

        // Regular cache definitions are cached inside create_definition().  This is not the case for disabledlib.php
        // definitions as they use load_adhoc().  They are built as a new object on each call.
        // We do not need to clone the definition because we know it's new.
        $definition = $this->create_definition($component, $area);
        $definition->set_identifiers($identifiers);
        $cache = $this->create_cache($definition);
        return $cache;
    }

    /**
     * Removes all temporary caches.
     *
     * Don't call this directly - used by {@see \core_cache\allow_temporary_caches}.
     */
    public static function clear_temporary_caches(): void {
        self::$tempcaches = [];
    }

    /**
     * Creates an ad-hoc cache from the given param.
     *
     * @param int $mode
     * @param string $component
     * @param string $area
     * @param array $identifiers
     * @param array $options An array of options, available options are:
     *   - simplekeys : Set to true if the keys you will use are a-zA-Z0-9_
     *   - simpledata : Set to true if the type of the data you are going to store is scalar, or an array of scalar vars
     *   - staticacceleration : If set to true the cache will hold onto all data passing through it.
     *   - staticaccelerationsize : Sets the max size of the static acceleration array.
     * @return cache_application|cache_session|request_cache
     */
    public function create_cache_from_params($mode, $component, $area, array $identifiers = array(), array $options = array()) {
        // Regular cache definitions are cached inside create_definition().  This is not the case for disabledlib.php
        // definitions as they use load_adhoc().  They are built as a new object on each call.
        // We do not need to clone the definition because we know it's new.
        $definition = cache_definition::load_adhoc($mode, $component, $area, $options);
        $definition->set_identifiers($identifiers);
        $cache = $this->create_cache($definition);
        return $cache;
    }

    /**
     * Creates a store instance given its name and configuration.
     *
     * @param string $name Unused.
     * @param array $details Unused.
     * @param cache_definition $definition
     * @return boolean|cache_store
     */
    public function create_store_from_config($name, array $details, cache_definition $definition) {
        return $this->create_dummy_store($definition);
    }

    /**
     * Creates a cache config instance with the ability to write if required.
     *
     * @param bool $writer Unused.
     * @return cache_config_disabled|config_writer
     */
    public function create_config_instance($writer = false) {
        // We are always going to use the cache_config_disabled class for all regular request.
        // However if the code has requested the writer then likely something is changing and
        // we're going to need to interact with the config.php file.
        // In this case we will still use the cache_config_writer.
        $class = 'cache_config_disabled';
        if ($writer) {
            // If the writer was requested then something is changing.
            $class = 'cache_config_writer';
        }
        if (!array_key_exists($class, $this->configs)) {
            self::set_state(self::STATE_INITIALISING);
            if ($class === 'cache_config_disabled') {
                $configuration = $class::create_default_configuration();
                $this->configs[$class] = new $class;
            } else {
                $configuration = false;
                // If we need a writer, we should get the classname from the generic factory.
                // This is so alternative classes can be used if a different writer is required.
                $this->configs[$class] = parent::get_disabled_writer();
            }
            $this->configs[$class]->load($configuration);
        }
        self::set_state(self::STATE_READY);

        // Return the instance.
        return $this->configs[$class];
    }

    /**
     * Returns true if the cache API has been disabled.
     *
     * @return bool
     */
    public function is_disabled() {
        return true;
    }
}
