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
 * Cache configuration reader
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
 * Cache configuration reader.
 *
 * This class is used to interact with the cache's configuration.
 * The configuration is stored in the Moodle data directory.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_config {

    /**
     * The configured stores
     * @var array
     */
    protected $configstores = array();

    /**
     * The configured mode mappings
     * @var array
     */
    protected $configmodemappings = array();

    /**
     * The configured definitions as picked up from cache.php files
     * @var array
     */
    protected $configdefinitions = array();

    /**
     * The definition mappings that have been configured.
     * @var array
     */
    protected $configdefinitionmappings = array();

    /**
     * An array of configured cache lock instances.
     * @var array
     */
    protected $configlocks = array();

    /**
     * The site identifier used when the cache config was last saved.
     * @var string
     */
    protected $siteidentifier = null;

    /**
     * Please use cache_config::instance to get an instance of the cache config that is ready to be used.
     */
    public function __construct() {
        // Nothing to do here but look pretty.
    }

    /**
     * Gets an instance of the cache_configuration class.
     *
     * @return cache_config
     */
    public static function instance() {
        $factory = cache_factory::instance();
        return $factory->create_config_instance();
    }

    /**
     * Checks if the configuration file exists.
     *
     * @return bool True if it exists
     */
    public static function config_file_exists() {
        // Allow for late static binding by using static.
        return file_exists(static::get_config_file_path());
    }

    /**
     * Returns the expected path to the configuration file.
     *
     * @return string The absolute path
     */
    protected static function get_config_file_path() {
        global $CFG;
        if (!empty($CFG->altcacheconfigpath)) {
            $path = $CFG->altcacheconfigpath;
            if (is_dir($path) && is_writable($path)) {
                // Its a writable directory, thats fine.
                return $path.'/cacheconfig.php';
            } else if (is_writable(dirname($path)) && (!file_exists($path) || is_writable($path))) {
                // Its a file, either it doesn't exist and the directory is writable or the file exists and is writable.
                return $path;
            }
        }
        // Return the default location within dataroot.
        return $CFG->dataroot.'/muc/config.php';
    }

    /**
     * Loads the configuration file and parses its contents into the expected structure.
     *
     * @param array|false $configuration Can be used to force a configuration. Should only be used when truly required.
     * @return boolean
     */
    public function load($configuration = false) {
        global $CFG;

        if ($configuration === false) {
            $configuration = $this->include_configuration();
        }

        $this->configstores = array();
        $this->configdefinitions = array();
        $this->configlocks = array();
        $this->configmodemappings = array();
        $this->configdefinitionmappings = array();
        $this->configlockmappings = array();

        $siteidentifier = 'unknown';
        if (array_key_exists('siteidentifier', $configuration)) {
            $siteidentifier = $configuration['siteidentifier'];
        }
        $this->siteidentifier = $siteidentifier;

        // Filter the lock instances.
        $defaultlock = null;
        foreach ($configuration['locks'] as $conf) {
            if (!is_array($conf)) {
                // Something is very wrong here.
                continue;
            }
            if (!array_key_exists('name', $conf)) {
                // Not a valid definition configuration.
                continue;
            }
            $name = $conf['name'];
            if (array_key_exists($name, $this->configlocks)) {
                debugging('Duplicate cache lock detected. This should never happen.', DEBUG_DEVELOPER);
                continue;
            }
            $conf['default'] = (!empty($conf['default']));
            if ($defaultlock === null || $conf['default']) {
                $defaultlock = $name;
            }
            $this->configlocks[$name] = $conf;
        }

        // Filter the stores.
        $availableplugins = cache_helper::early_get_cache_plugins();
        foreach ($configuration['stores'] as $store) {
            if (!is_array($store) || !array_key_exists('name', $store) || !array_key_exists('plugin', $store)) {
                // Not a valid instance configuration.
                debugging('Invalid cache store in config. Missing name or plugin.', DEBUG_DEVELOPER);
                continue;
            }
            $plugin = $store['plugin'];
            $class = 'cachestore_'.$plugin;
            $exists = array_key_exists($plugin, $availableplugins);
            if (!$exists) {
                // Not a valid plugin, or has been uninstalled, just skip it an carry on.
                debugging('Invalid cache store in config. Not an available plugin.', DEBUG_DEVELOPER);
                continue;
            }
            $file = $CFG->dirroot.'/cache/stores/'.$plugin.'/lib.php';
            if (!class_exists($class) && file_exists($file)) {
                require_once($file);
            }
            if (!class_exists($class)) {
                continue;
            }
            if (!array_key_exists('cache_store', class_parents($class))) {
                continue;
            }
            if (!array_key_exists('configuration', $store) || !is_array($store['configuration'])) {
                $store['configuration'] = array();
            }
            $store['class'] = $class;
            $store['default'] = !empty($store['default']);
            if (!array_key_exists('lock', $store) || !array_key_exists($store['lock'], $this->configlocks)) {
                $store['lock'] = $defaultlock;
            }

            $this->configstores[$store['name']] = $store;
        }

        // Filter the definitions.
        foreach ($configuration['definitions'] as $id => $conf) {
            if (!is_array($conf)) {
                // Something is very wrong here.
                continue;
            }
            if (!array_key_exists('mode', $conf) || !array_key_exists('component', $conf) || !array_key_exists('area', $conf)) {
                // Not a valid definition configuration.
                continue;
            }
            if (array_key_exists($id, $this->configdefinitions)) {
                debugging('Duplicate cache definition detected. This should never happen.', DEBUG_DEVELOPER);
                continue;
            }
            $conf['mode'] = (int)$conf['mode'];
            if ($conf['mode'] < cache_store::MODE_APPLICATION || $conf['mode'] > cache_store::MODE_REQUEST) {
                // Invalid cache mode used for the definition.
                continue;
            }
            if ($conf['mode'] === cache_store::MODE_SESSION || $conf['mode'] === cache_store::MODE_REQUEST) {
                // We force this for session and request caches.
                // They are only allowed to use the default as we don't want people changing them.
                $conf['sharingoptions'] = cache_definition::SHARING_DEFAULT;
                $conf['selectedsharingoption'] = cache_definition::SHARING_DEFAULT;
                $conf['userinputsharingkey'] = '';
            } else {
                // Default the sharing option as it was added for 2.5.
                // This can be removed sometime after 2.5 is the minimum version someone can upgrade from.
                if (!isset($conf['sharingoptions'])) {
                    $conf['sharingoptions'] = cache_definition::SHARING_DEFAULTOPTIONS;
                }
                // Default the selected sharing option as it was added for 2.5.
                // This can be removed sometime after 2.5 is the minimum version someone can upgrade from.
                if (!isset($conf['selectedsharingoption'])) {
                    $conf['selectedsharingoption'] = cache_definition::SHARING_DEFAULT;
                }
                // Default the user input sharing key as it was added for 2.5.
                // This can be removed sometime after 2.5 is the minimum version someone can upgrade from.
                if (!isset($conf['userinputsharingkey'])) {
                    $conf['userinputsharingkey'] = '';
                }
            }
            $this->configdefinitions[$id] = $conf;
        }

        // Filter the mode mappings.
        foreach ($configuration['modemappings'] as $mapping) {
            if (!is_array($mapping) || !array_key_exists('mode', $mapping) || !array_key_exists('store', $mapping)) {
                // Not a valid mapping configuration.
                debugging('A cache mode mapping entry is invalid.', DEBUG_DEVELOPER);
                continue;
            }
            if (!array_key_exists($mapping['store'], $this->configstores)) {
                // Mapped array instance doesn't exist.
                debugging('A cache mode mapping exists for a mode or store that does not exist.', DEBUG_DEVELOPER);
                continue;
            }
            $mapping['mode'] = (int)$mapping['mode'];
            if ($mapping['mode'] < 0 || $mapping['mode'] > 4) {
                // Invalid cache type used for the mapping.
                continue;
            }
            if (!array_key_exists('sort', $mapping)) {
                $mapping['sort'] = 0;
            }
            $this->configmodemappings[] = $mapping;
        }

        // Filter the definition mappings.
        foreach ($configuration['definitionmappings'] as $mapping) {
            if (!is_array($mapping) || !array_key_exists('definition', $mapping) || !array_key_exists('store', $mapping)) {
                // Not a valid mapping configuration.
                continue;
            }
            if (!array_key_exists($mapping['store'], $this->configstores)) {
                // Mapped array instance doesn't exist.
                continue;
            }
            if (!array_key_exists($mapping['definition'], $this->configdefinitions)) {
                // Mapped array instance doesn't exist.
                continue;
            }
            if (!array_key_exists('sort', $mapping)) {
                $mapping['sort'] = 0;
            }
            $this->configdefinitionmappings[] = $mapping;
        }

        usort($this->configmodemappings, array($this, 'sort_mappings'));
        usort($this->configdefinitionmappings, array($this, 'sort_mappings'));

        return true;
    }

    /**
     * Returns the site identifier used by the cache API.
     * @return string
     */
    public function get_site_identifier() {
        return $this->siteidentifier;
    }

    /**
     * Includes the configuration file and makes sure it contains the expected bits.
     *
     * You need to ensure that the config file exists before this is called.
     *
     * @return array
     * @throws cache_exception
     */
    protected function include_configuration() {
        $configuration = array();
        // We need to allow for late static bindings to allow for class path mudling happending for unit tests.
        $cachefile = static::get_config_file_path();

        if (!file_exists($cachefile)) {
            throw new cache_exception('Default cache config could not be found. It should have already been created by now.');
        }

        if (!include($cachefile)) {
            throw new cache_exception('Unable to load the cache configuration file');
        }

        if (!is_array($configuration)) {
            throw new cache_exception('Invalid cache configuration file');
        }
        if (!array_key_exists('stores', $configuration) || !is_array($configuration['stores'])) {
            $configuration['stores'] = array();
        }
        if (!array_key_exists('modemappings', $configuration) || !is_array($configuration['modemappings'])) {
            $configuration['modemappings'] = array();
        }
        if (!array_key_exists('definitions', $configuration) || !is_array($configuration['definitions'])) {
            $configuration['definitions'] = array();
        }
        if (!array_key_exists('definitionmappings', $configuration) || !is_array($configuration['definitionmappings'])) {
            $configuration['definitionmappings'] = array();
        }
        if (!array_key_exists('locks', $configuration) || !is_array($configuration['locks'])) {
            $configuration['locks'] = array();
        }

        return $configuration;
    }

    /**
     * Used to sort cache config arrays based upon a sort key.
     *
     * Highest number at the top.
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function sort_mappings(array $a, array $b) {
        if ($a['sort'] == $b['sort']) {
            return 0;
        }
        return ($a['sort'] < $b['sort']) ? 1 : -1;
    }

    /**
     * Gets a definition from the config given its name.
     *
     * @param string $id
     * @return bool
     */
    public function get_definition_by_id($id) {
        if (array_key_exists($id, $this->configdefinitions)) {
            return $this->configdefinitions[$id];
        }
        return false;
    }

    /**
     * Returns all the known definitions.
     *
     * @return array
     */
    public function get_definitions() {
        return $this->configdefinitions;
    }

    /**
     * Returns the definitions mapped into the given store name.
     *
     * @param string $storename
     * @return array Associative array of definitions, id=>definition
     */
    public function get_definitions_by_store($storename) {
        $definitions = array();

        // This function was accidentally made static at some stage in the past.
        // It was converted to an instance method but to be backwards compatible
        // we must step around this in code.
        if (!isset($this)) {
            $config = cache_config::instance();
        } else {
            $config = $this;
        }

        $stores = $config->get_all_stores();
        if (!array_key_exists($storename, $stores)) {
            // The store does not exist.
            return false;
        }

        $defmappings = $config->get_definition_mappings();
        // Create an associative array for the definition mappings.
        $thedefmappings = array();
        foreach ($defmappings as $defmapping) {
            $thedefmappings[$defmapping['definition']] = $defmapping;
        }

        // Search for matches in default mappings.
        $defs = $config->get_definitions();
        foreach($config->get_mode_mappings() as $modemapping) {
            if ($modemapping['store'] !== $storename) {
                continue;
            }
            foreach($defs as $id => $definition) {
                if ($definition['mode'] !== $modemapping['mode']) {
                    continue;
                }
                // Exclude custom definitions mapping: they will be managed few lines below.
                if (array_key_exists($id, $thedefmappings)) {
                    continue;
                }
                $definitions[$id] = $definition;
            }
        }

        // Search for matches in the custom definitions mapping
        foreach ($defmappings as $defmapping) {
            if ($defmapping['store'] !== $storename) {
                continue;
            }
            $definition = $config->get_definition_by_id($defmapping['definition']);
            if ($definition) {
                $definitions[$defmapping['definition']] = $definition;
            }
        }

        return $definitions;
    }

    /**
     * Returns all of the stores that are suitable for the given mode and requirements.
     *
     * @param int $mode One of cache_store::MODE_*
     * @param int $requirements The requirements of the cache as a binary flag
     * @return array An array of suitable stores.
     */
    public function get_stores($mode, $requirements = 0) {
        $stores = array();
        foreach ($this->configstores as $name => $store) {
            // If the mode is supported and all of the requirements are provided features.
            if (($store['modes'] & $mode) && ($store['features'] & $requirements) === $requirements) {
                $stores[$name] = $store;
            }
        }
        return $stores;
    }

    /**
     * Gets all of the stores that are to be used for the given definition.
     *
     * @param cache_definition $definition
     * @return array
     */
    public function get_stores_for_definition(cache_definition $definition) {
        // Check if MUC has been disabled.
        $factory = cache_factory::instance();
        if ($factory->stores_disabled()) {
            // Yip its been disabled.
            // To facilitate this we are going to always return an empty array of stores to use.
            // This will force all cache instances to use the cachestore_dummy.
            // MUC will still be used essentially so that code using it will still continue to function but because no cache stores
            // are being used interaction with MUC will be purely based around a static var.
            return array();
        }

        $availablestores = $this->get_stores($definition->get_mode(), $definition->get_requirements_bin());
        $stores = array();
        $id = $definition->get_id();

        // Now get any mappings and give them priority.
        foreach ($this->configdefinitionmappings as $mapping) {
            if ($mapping['definition'] !== $id) {
                continue;
            }
            $storename = $mapping['store'];
            if (!array_key_exists($storename, $availablestores)) {
                continue;
            }
            if (array_key_exists($storename, $stores)) {
                $store = $stores[$storename];
                unset($stores[$storename]);
                $stores[$storename] = $store;
            } else {
                $stores[$storename] = $availablestores[$storename];
            }
        }

        if (empty($stores) && !$definition->is_for_mappings_only()) {
            $mode = $definition->get_mode();
            // Load the default stores.
            foreach ($this->configmodemappings as $mapping) {
                if ($mapping['mode'] === $mode && array_key_exists($mapping['store'], $availablestores)) {
                    $store = $availablestores[$mapping['store']];
                    if (empty($store['mappingsonly'])) {
                        $stores[$mapping['store']] = $store;
                    }
                }
            }
        }

        return $stores;
    }

    /**
     * Returns all of the configured stores
     * @return array
     */
    public function get_all_stores() {
        return $this->configstores;
    }

    /**
     * Returns all of the configured mode mappings
     * @return array
     */
    public function get_mode_mappings() {
        return $this->configmodemappings;
    }

    /**
     * Returns all of the known definition mappings.
     * @return array
     */
    public function get_definition_mappings() {
        return $this->configdefinitionmappings;
    }

    /**
     * Returns an array of the configured locks.
     * @return array Array of name => config
     */
    public function get_locks() {
        return $this->configlocks;
    }

    /**
     * Returns the lock store configuration to use with a given store.
     * @param string $storename
     * @return array
     * @throws cache_exception
     */
    public function get_lock_for_store($storename) {
        if (array_key_exists($storename, $this->configstores)) {
            if (array_key_exists($this->configstores[$storename]['lock'], $this->configlocks)) {
                $lock = $this->configstores[$storename]['lock'];
                return $this->configlocks[$lock];
            }
        }
        return $this->get_default_lock();
    }

    /**
     * Gets the default lock instance.
     *
     * @return array
     * @throws cache_exception
     */
    public function get_default_lock() {
        foreach ($this->configlocks as $lockconf) {
            if (!empty($lockconf['default'])) {
                return $lockconf;
            }
        }
        throw new cache_exception('ex_nodefaultlock');
    }
}