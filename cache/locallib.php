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
 * The supplementary cache API.
 *
 * This file is part of Moodle's cache API, affectionately called MUC.
 * It contains elements of the API that are not required in order to use caching.
 * Things in here are more in line with administration and management of the cache setup and configuration.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Cache configuration writer.
 *
 * This class should only be used when you need to write to the config, all read operations exist within the cache_config.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_config_writer extends cache_config {

    /**
     * Switch that gets set to true when ever a cache_config_writer instance is saving the cache configuration file.
     * If this is set to true when save is next called we must avoid the trying to save and instead return the
     * generated config so that is may be used instead of the file.
     * @var bool
     */
    protected static $creatingconfig = false;

    /**
     * Returns an instance of the configuration writer.
     *
     * @return cache_config_writer
     */
    public static function instance() {
        $factory = cache_factory::instance();
        return $factory->create_config_instance(true);
    }

    /**
     * Saves the current configuration.
     *
     * Exceptions within this function are tolerated but must be of type cache_exception.
     * They are caught during initialisation and written to the error log. This is required in order to avoid
     * infinite loop situations caused by the cache throwing exceptions during its initialisation.
     */
    protected function config_save() {
        global $CFG;
        $cachefile = static::get_config_file_path();
        $directory = dirname($cachefile);
        if ($directory !== $CFG->dataroot && !file_exists($directory)) {
            $result = make_writable_directory($directory, false);
            if (!$result) {
                throw new cache_exception('ex_configcannotsave', 'cache', '', null, 'Cannot create config directory. Check the permissions on your moodledata directory.');
            }
        }
        if (!file_exists($directory) || !is_writable($directory)) {
            throw new cache_exception('ex_configcannotsave', 'cache', '', null, 'Config directory is not writable. Check the permissions on the moodledata/muc directory.');
        }

        // Prepare a configuration array to store.
        $configuration = $this->generate_configuration_array();

        // Prepare the file content.
        $content = "<?php defined('MOODLE_INTERNAL') || die();\n \$configuration = ".var_export($configuration, true).";";

        // We need to create a temporary cache lock instance for use here. Remember we are generating the config file
        // it doesn't exist and thus we can't use the normal API for this (it'll just try to use config).
        $lockconf = reset($this->configlocks);
        if ($lockconf === false) {
            debugging('Your cache configuration file is out of date and needs to be refreshed.', DEBUG_DEVELOPER);
            // Use the default
            $lockconf = array(
                'name' => 'cachelock_file_default',
                'type' => 'cachelock_file',
                'dir' => 'filelocks',
                'default' => true
            );
        }
        $factory = cache_factory::instance();
        $locking = $factory->create_lock_instance($lockconf);
        if ($locking->lock('configwrite', 'config', true)) {
            // Its safe to use w mode here because we have already acquired the lock.
            $handle = fopen($cachefile, 'w');
            fwrite($handle, $content);
            fflush($handle);
            fclose($handle);
            $locking->unlock('configwrite', 'config');
            @chmod($cachefile, $CFG->filepermissions);
            // Tell PHP to recompile the script.
            core_component::invalidate_opcode_php_cache($cachefile);
        } else {
            throw new cache_exception('ex_configcannotsave', 'cache', '', null, 'Unable to open the cache config file.');
        }
    }

    /**
     * Generates a configuration array suitable to be written to the config file.
     * @return array
     */
    protected function generate_configuration_array() {
        $configuration = array();
        $configuration['siteidentifier'] = $this->siteidentifier;
        $configuration['stores'] = $this->configstores;
        $configuration['modemappings'] = $this->configmodemappings;
        $configuration['definitions'] = $this->configdefinitions;
        $configuration['definitionmappings'] = $this->configdefinitionmappings;
        $configuration['locks'] = $this->configlocks;
        return $configuration;
    }

    /**
     * Adds a plugin instance.
     *
     * This function also calls save so you should redirect immediately, or at least very shortly after
     * calling this method.
     *
     * @param string $name The name for the instance (must be unique)
     * @param string $plugin The name of the plugin.
     * @param array $configuration The configuration data for the plugin instance.
     * @return bool
     * @throws cache_exception
     */
    public function add_store_instance($name, $plugin, array $configuration = array()) {
        if (array_key_exists($name, $this->configstores)) {
            throw new cache_exception('Duplicate name specificed for cache plugin instance. You must provide a unique name.');
        }
        $class = 'cachestore_'.$plugin;
        if (!class_exists($class)) {
            $plugins = core_component::get_plugin_list_with_file('cachestore', 'lib.php');
            if (!array_key_exists($plugin, $plugins)) {
                throw new cache_exception('Invalid plugin name specified. The plugin does not exist or is not valid.');
            }
            $file = $plugins[$plugin];
            if (file_exists($file)) {
                require_once($file);
            }
            if (!class_exists($class)) {
                throw new cache_exception('Invalid cache plugin specified. The plugin does not contain the required class.');
            }
        }
        $reflection = new ReflectionClass($class);
        if (!$reflection->isSubclassOf('cache_store')) {
            throw new cache_exception('Invalid cache plugin specified. The plugin does not extend the required class.');
        }
        if (!$class::are_requirements_met()) {
            throw new cache_exception('Unable to add new cache plugin instance. The requested plugin type is not supported.');
        }
        $this->configstores[$name] = array(
            'name' => $name,
            'plugin' => $plugin,
            'configuration' => $configuration,
            'features' => $class::get_supported_features($configuration),
            'modes' => $class::get_supported_modes($configuration),
            'mappingsonly' => !empty($configuration['mappingsonly']),
            'class' => $class,
            'default' => false
        );
        if (array_key_exists('lock', $configuration)) {
            $this->configstores[$name]['lock'] = $configuration['lock'];
            unset($this->configstores[$name]['configuration']['lock']);
        }
        // Call instance_created()
        $store = new $class($name, $this->configstores[$name]['configuration']);
        $store->instance_created();

        $this->config_save();
        return true;
    }

    /**
     * Adds a new lock instance to the config file.
     *
     * @param string $name The name the user gave the instance. PARAM_ALHPANUMEXT
     * @param string $plugin The plugin we are creating an instance of.
     * @param string $configuration Configuration data from the config instance.
     * @throws cache_exception
     */
    public function add_lock_instance($name, $plugin, $configuration = array()) {
        if (array_key_exists($name, $this->configlocks)) {
            throw new cache_exception('Duplicate name specificed for cache lock instance. You must provide a unique name.');
        }
        $class = 'cachelock_'.$plugin;
        if (!class_exists($class)) {
            $plugins = core_component::get_plugin_list_with_file('cachelock', 'lib.php');
            if (!array_key_exists($plugin, $plugins)) {
                throw new cache_exception('Invalid lock name specified. The plugin does not exist or is not valid.');
            }
            $file = $plugins[$plugin];
            if (file_exists($file)) {
                require_once($file);
            }
            if (!class_exists($class)) {
                throw new cache_exception('Invalid lock plugin specified. The plugin does not contain the required class.');
            }
        }
        $reflection = new ReflectionClass($class);
        if (!$reflection->implementsInterface('cache_lock_interface')) {
            throw new cache_exception('Invalid lock plugin specified. The plugin does not implement the required interface.');
        }
        $this->configlocks[$name] = array_merge($configuration, array(
            'name' => $name,
            'type' => 'cachelock_'.$plugin,
            'default' => false
        ));
        $this->config_save();
    }

    /**
     * Deletes a lock instance given its name.
     *
     * @param string $name The name of the plugin, PARAM_ALPHANUMEXT.
     * @return bool
     * @throws cache_exception
     */
    public function delete_lock_instance($name) {
        if (!array_key_exists($name, $this->configlocks)) {
            throw new cache_exception('The requested store does not exist.');
        }
        if ($this->configlocks[$name]['default']) {
            throw new cache_exception('You can not delete the default lock.');
        }
        foreach ($this->configstores as $store) {
            if (isset($store['lock']) && $store['lock'] === $name) {
                throw new cache_exception('You cannot delete a cache lock that is being used by a store.');
            }
        }
        unset($this->configlocks[$name]);
        $this->config_save();
        return true;
    }

    /**
     * Sets the mode mappings.
     *
     * These determine the default caches for the different modes.
     * This function also calls save so you should redirect immediately, or at least very shortly after
     * calling this method.
     *
     * @param array $modemappings
     * @return bool
     * @throws cache_exception
     */
    public function set_mode_mappings(array $modemappings) {
        $mappings = array(
            cache_store::MODE_APPLICATION => array(),
            cache_store::MODE_SESSION => array(),
            cache_store::MODE_REQUEST => array(),
        );
        foreach ($modemappings as $mode => $stores) {
            if (!array_key_exists($mode, $mappings)) {
                throw new cache_exception('The cache mode for the new mapping does not exist');
            }
            $sort = 0;
            foreach ($stores as $store) {
                if (!array_key_exists($store, $this->configstores)) {
                    throw new cache_exception('The instance name for the new mapping does not exist');
                }
                if (array_key_exists($store, $mappings[$mode])) {
                    throw new cache_exception('This cache mapping already exists');
                }
                $mappings[$mode][] = array(
                    'store' => $store,
                    'mode' => $mode,
                    'sort' => $sort++
                );
            }
        }
        $this->configmodemappings = array_merge(
            $mappings[cache_store::MODE_APPLICATION],
            $mappings[cache_store::MODE_SESSION],
            $mappings[cache_store::MODE_REQUEST]
        );

        $this->config_save();
        return true;
    }

    /**
     * Edits a give plugin instance.
     *
     * The plugin instance is determined by its name, hence you cannot rename plugins.
     * This function also calls save so you should redirect immediately, or at least very shortly after
     * calling this method.
     *
     * @param string $name
     * @param string $plugin
     * @param array $configuration
     * @return bool
     * @throws cache_exception
     */
    public function edit_store_instance($name, $plugin, $configuration) {
        if (!array_key_exists($name, $this->configstores)) {
            throw new cache_exception('The requested instance does not exist.');
        }
        $plugins = core_component::get_plugin_list_with_file('cachestore', 'lib.php');
        if (!array_key_exists($plugin, $plugins)) {
            throw new cache_exception('Invalid plugin name specified. The plugin either does not exist or is not valid.');
        }
        $class = 'cachestore_'.$plugin;
        $file = $plugins[$plugin];
        if (!class_exists($class)) {
            if (file_exists($file)) {
                require_once($file);
            }
            if (!class_exists($class)) {
                throw new cache_exception('Invalid cache plugin specified. The plugin does not contain the required class.'.$class);
            }
        }
        $this->configstores[$name] = array(
            'name' => $name,
            'plugin' => $plugin,
            'configuration' => $configuration,
            'features' => $class::get_supported_features($configuration),
            'modes' => $class::get_supported_modes($configuration),
            'mappingsonly' => !empty($configuration['mappingsonly']),
            'class' => $class,
            'default' => $this->configstores[$name]['default'] // Can't change the default.
        );
        if (array_key_exists('lock', $configuration)) {
            $this->configstores[$name]['lock'] = $configuration['lock'];
            unset($this->configstores[$name]['configuration']['lock']);
        }
        $this->config_save();
        return true;
    }

    /**
     * Deletes a store instance.
     *
     * This function also calls save so you should redirect immediately, or at least very shortly after
     * calling this method.
     *
     * @param string $name The name of the instance to delete.
     * @return bool
     * @throws cache_exception
     */
    public function delete_store_instance($name) {
        if (!array_key_exists($name, $this->configstores)) {
            throw new cache_exception('The requested store does not exist.');
        }
        if ($this->configstores[$name]['default']) {
            throw new cache_exception('The can not delete the default stores.');
        }
        foreach ($this->configmodemappings as $mapping) {
            if ($mapping['store'] === $name) {
                throw new cache_exception('You cannot delete a cache store that has mode mappings.');
            }
        }
        foreach ($this->configdefinitionmappings as $mapping) {
            if ($mapping['store'] === $name) {
                throw new cache_exception('You cannot delete a cache store that has definition mappings.');
            }
        }

        // Call instance_deleted()
        $class = 'cachestore_'.$this->configstores[$name]['plugin'];
        $store = new $class($name, $this->configstores[$name]['configuration']);
        $store->instance_deleted();

        unset($this->configstores[$name]);
        $this->config_save();
        return true;
    }

    /**
     * Creates the default configuration and saves it.
     *
     * This function calls config_save, however it is safe to continue using it afterwards as this function should only ever
     * be called when there is no configuration file already.
     *
     * @param bool $forcesave If set to true then we will forcefully save the default configuration file.
     * @return true|array Returns true if the default configuration was successfully created.
     *     Returns a configuration array if it could not be saved. This is a bad situation. Check your error logs.
     */
    public static function create_default_configuration($forcesave = false) {
        // HACK ALERT.
        // We probably need to come up with a better way to create the default stores, or at least ensure 100% that the
        // default store plugins are protected from deletion.
        $writer = new self;
        $writer->configstores = self::get_default_stores();
        $writer->configdefinitions = self::locate_definitions();
        $writer->configmodemappings = array(
            array(
                'mode' => cache_store::MODE_APPLICATION,
                'store' => 'default_application',
                'sort' => -1
            ),
            array(
                'mode' => cache_store::MODE_SESSION,
                'store' => 'default_session',
                'sort' => -1
            ),
            array(
                'mode' => cache_store::MODE_REQUEST,
                'store' => 'default_request',
                'sort' => -1
            )
        );
        $writer->configlocks = array(
            'default_file_lock' => array(
                'name' => 'cachelock_file_default',
                'type' => 'cachelock_file',
                'dir' => 'filelocks',
                'default' => true
            )
        );

        $factory = cache_factory::instance();
        // We expect the cache to be initialising presently. If its not then something has gone wrong and likely
        // we are now in a loop.
        if (!$forcesave && $factory->get_state() !== cache_factory::STATE_INITIALISING) {
            return $writer->generate_configuration_array();
        }
        $factory->set_state(cache_factory::STATE_SAVING);
        $writer->config_save();
        return true;
    }

    /**
     * Returns an array of default stores for use.
     *
     * @return array
     */
    protected static function get_default_stores() {
        global $CFG;

        require_once($CFG->dirroot.'/cache/stores/file/lib.php');
        require_once($CFG->dirroot.'/cache/stores/session/lib.php');
        require_once($CFG->dirroot.'/cache/stores/static/lib.php');

        return array(
            'default_application' => array(
                'name' => 'default_application',
                'plugin' => 'file',
                'configuration' => array(),
                'features' => cachestore_file::get_supported_features(),
                'modes' => cachestore_file::get_supported_modes(),
                'default' => true,
            ),
            'default_session' => array(
                'name' => 'default_session',
                'plugin' => 'session',
                'configuration' => array(),
                'features' => cachestore_session::get_supported_features(),
                'modes' => cachestore_session::get_supported_modes(),
                'default' => true,
            ),
            'default_request' => array(
                'name' => 'default_request',
                'plugin' => 'static',
                'configuration' => array(),
                'features' => cachestore_static::get_supported_features(),
                'modes' => cachestore_static::get_supported_modes(),
                'default' => true,
            )
        );
    }

    /**
     * Updates the default stores within the MUC config file.
     */
    public static function update_default_config_stores() {
        $factory = cache_factory::instance();
        $factory->updating_started();
        $config = $factory->create_config_instance(true);
        $config->configstores = array_merge($config->configstores, self::get_default_stores());
        $config->config_save();
        $factory->updating_finished();
    }

    /**
     * Updates the definition in the configuration from those found in the cache files.
     *
     * Calls config_save further down, you should redirect immediately or asap after calling this method.
     *
     * @param bool $coreonly If set to true only core definitions will be updated.
     */
    public static function update_definitions($coreonly = false) {
        $factory = cache_factory::instance();
        $factory->updating_started();
        $config = $factory->create_config_instance(true);
        $config->write_definitions_to_cache(self::locate_definitions($coreonly));
        $factory->updating_finished();
    }

    /**
     * Locates all of the definition files.
     *
     * @param bool $coreonly If set to true only core definitions will be updated.
     * @return array
     */
    protected static function locate_definitions($coreonly = false) {
        global $CFG;

        $files = array();
        if (file_exists($CFG->dirroot.'/lib/db/caches.php')) {
            $files['core'] = $CFG->dirroot.'/lib/db/caches.php';
        }

        if (!$coreonly) {
            $plugintypes = core_component::get_plugin_types();
            foreach ($plugintypes as $type => $location) {
                $plugins = core_component::get_plugin_list_with_file($type, 'db/caches.php');
                foreach ($plugins as $plugin => $filepath) {
                    $component = clean_param($type.'_'.$plugin, PARAM_COMPONENT); // Standardised plugin name.
                    $files[$component] = $filepath;
                }
            }
        }

        $definitions = array();
        foreach ($files as $component => $file) {
            $filedefs = self::load_caches_file($file);
            foreach ($filedefs as $area => $definition) {
                $area = clean_param($area, PARAM_AREA);
                $id = $component.'/'.$area;
                $definition['component'] = $component;
                $definition['area'] = $area;
                if (array_key_exists($id, $definitions)) {
                    debugging('Error: duplicate cache definition found with id: '.$id, DEBUG_DEVELOPER);
                    continue;
                }
                $definitions[$id] = $definition;
            }
        }

        return $definitions;
    }

    /**
     * Writes the updated definitions for the config file.
     * @param array $definitions
     */
    private function write_definitions_to_cache(array $definitions) {

        // Preserve the selected sharing option when updating the definitions.
        // This is set by the user and should never come from caches.php.
        foreach ($definitions as $key => $definition) {
            unset($definitions[$key]['selectedsharingoption']);
            unset($definitions[$key]['userinputsharingkey']);
            if (isset($this->configdefinitions[$key]) && isset($this->configdefinitions[$key]['selectedsharingoption'])) {
                $definitions[$key]['selectedsharingoption'] = $this->configdefinitions[$key]['selectedsharingoption'];
            }
            if (isset($this->configdefinitions[$key]) && isset($this->configdefinitions[$key]['userinputsharingkey'])) {
                $definitions[$key]['userinputsharingkey'] = $this->configdefinitions[$key]['userinputsharingkey'];
            }
        }

        $this->configdefinitions = $definitions;
        foreach ($this->configdefinitionmappings as $key => $mapping) {
            if (!array_key_exists($mapping['definition'], $definitions)) {
                unset($this->configdefinitionmappings[$key]);
            }
        }
        $this->config_save();
    }

    /**
     * Loads the caches file if it exists.
     * @param string $file Absolute path to the file.
     * @return array
     */
    private static function load_caches_file($file) {
        if (!file_exists($file)) {
            return array();
        }
        $definitions = array();
        include($file);
        return $definitions;
    }

    /**
     * Sets the mappings for a given definition.
     *
     * @param string $definition
     * @param array $mappings
     * @throws coding_exception
     */
    public function set_definition_mappings($definition, $mappings) {
        if (!array_key_exists($definition, $this->configdefinitions)) {
            throw new coding_exception('Invalid definition name passed when updating mappings.');
        }
        foreach ($mappings as $store) {
            if (!array_key_exists($store, $this->configstores)) {
                throw new coding_exception('Invalid store name passed when updating definition mappings.');
            }
        }
        foreach ($this->configdefinitionmappings as $key => $mapping) {
            if ($mapping['definition'] == $definition) {
                unset($this->configdefinitionmappings[$key]);
            }
        }
        $sort = count($mappings);
        foreach ($mappings as $store) {
            $this->configdefinitionmappings[] = array(
                'store' => $store,
                'definition' => $definition,
                'sort' => $sort
            );
            $sort--;
        }

        $this->config_save();
    }

    /**
     * Update the site identifier stored by the cache API.
     *
     * @param string $siteidentifier
     * @return string The new site identifier.
     */
    public function update_site_identifier($siteidentifier) {
        $this->siteidentifier = md5((string)$siteidentifier);
        $this->config_save();
        return $this->siteidentifier;
    }

    /**
     * Sets the selected sharing options and key for a definition.
     *
     * @param string $definition The name of the definition to set for.
     * @param int $sharingoption The sharing option to set.
     * @param string|null $userinputsharingkey The user input key or null.
     * @throws coding_exception
     */
    public function set_definition_sharing($definition, $sharingoption, $userinputsharingkey = null) {
        if (!array_key_exists($definition, $this->configdefinitions)) {
            throw new coding_exception('Invalid definition name passed when updating sharing options.');
        }
        if (!($this->configdefinitions[$definition]['sharingoptions'] & $sharingoption)) {
            throw new coding_exception('Invalid sharing option passed when updating definition.');
        }
        $this->configdefinitions[$definition]['selectedsharingoption'] = (int)$sharingoption;
        if (!empty($userinputsharingkey)) {
            $this->configdefinitions[$definition]['userinputsharingkey'] = (string)$userinputsharingkey;
        }
        $this->config_save();
    }

}

/**
 * A cache helper for administration tasks
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class cache_administration_helper extends cache_helper {

    /**
     * Returns an array containing all of the information about stores a renderer needs.
     * @return array
     */
    public static function get_store_instance_summaries() {
        $return = array();
        $default = array();
        $instance = cache_config::instance();
        $stores = $instance->get_all_stores();
        $locks = $instance->get_locks();
        foreach ($stores as $name => $details) {
            $class = $details['class'];
            $store = new $class($details['name'], $details['configuration']);
            $lock = (isset($details['lock'])) ? $locks[$details['lock']] : $instance->get_default_lock();
            $record = array(
                'name' => $name,
                'plugin' => $details['plugin'],
                'default' => $details['default'],
                'isready' => $store->is_ready(),
                'requirementsmet' => $class::are_requirements_met(),
                'mappings' => 0,
                'lock' => $lock,
                'modes' => array(
                    cache_store::MODE_APPLICATION =>
                        ($store->get_supported_modes($return) & cache_store::MODE_APPLICATION) == cache_store::MODE_APPLICATION,
                    cache_store::MODE_SESSION =>
                        ($store->get_supported_modes($return) & cache_store::MODE_SESSION) == cache_store::MODE_SESSION,
                    cache_store::MODE_REQUEST =>
                        ($store->get_supported_modes($return) & cache_store::MODE_REQUEST) == cache_store::MODE_REQUEST,
                ),
                'supports' => array(
                    'multipleidentifiers' => $store->supports_multiple_identifiers(),
                    'dataguarantee' => $store->supports_data_guarantee(),
                    'nativettl' => $store->supports_native_ttl(),
                    'nativelocking' => ($store instanceof cache_is_lockable),
                    'keyawareness' => ($store instanceof cache_is_key_aware),
                    'searchable' => ($store instanceof cache_is_searchable)
                ),
                'warnings' => $store->get_warnings()
            );
            if (empty($details['default'])) {
                $return[$name] = $record;
            } else {
                $default[$name] = $record;
            }
        }

        ksort($return);
        ksort($default);
        $return = $return + $default;

        foreach ($instance->get_definition_mappings() as $mapping) {
            if (!array_key_exists($mapping['store'], $return)) {
                continue;
            }
            $return[$mapping['store']]['mappings']++;
        }

        return $return;
    }

    /**
     * Returns an array of information about plugins, everything a renderer needs.
     * @return array
     */
    public static function get_store_plugin_summaries() {
        $return = array();
        $plugins = core_component::get_plugin_list_with_file('cachestore', 'lib.php', true);
        foreach ($plugins as $plugin => $path) {
            $class = 'cachestore_'.$plugin;
            $return[$plugin] = array(
                'name' => get_string('pluginname', 'cachestore_'.$plugin),
                'requirementsmet' => $class::are_requirements_met(),
                'instances' => 0,
                'modes' => array(
                    cache_store::MODE_APPLICATION => ($class::get_supported_modes() & cache_store::MODE_APPLICATION),
                    cache_store::MODE_SESSION => ($class::get_supported_modes() & cache_store::MODE_SESSION),
                    cache_store::MODE_REQUEST => ($class::get_supported_modes() & cache_store::MODE_REQUEST),
                ),
                'supports' => array(
                    'multipleidentifiers' => ($class::get_supported_features() & cache_store::SUPPORTS_MULTIPLE_IDENTIFIERS),
                    'dataguarantee' => ($class::get_supported_features() & cache_store::SUPPORTS_DATA_GUARANTEE),
                    'nativettl' => ($class::get_supported_features() & cache_store::SUPPORTS_NATIVE_TTL),
                    'nativelocking' => (in_array('cache_is_lockable', class_implements($class))),
                    'keyawareness' => (array_key_exists('cache_is_key_aware', class_implements($class))),
                ),
                'canaddinstance' => ($class::can_add_instance() && $class::are_requirements_met())
            );
        }

        $instance = cache_config::instance();
        $stores = $instance->get_all_stores();
        foreach ($stores as $store) {
            $plugin = $store['plugin'];
            if (array_key_exists($plugin, $return)) {
                $return[$plugin]['instances']++;
            }
        }

        return $return;
    }

    /**
     * Returns an array about the definitions. All the information a renderer needs.
     * @return array
     */
    public static function get_definition_summaries() {
        $factory = cache_factory::instance();
        $config = $factory->create_config_instance();
        $storenames = array();
        foreach ($config->get_all_stores() as $key => $store) {
            if (!empty($store['default'])) {
                $storenames[$key] = new lang_string('store_'.$key, 'cache');
            } else {
                $storenames[$store['name']] = $store['name'];
            }
        }
        /* @var cache_definition[] $definitions */
        $definitions = array();
        foreach ($config->get_definitions() as $key => $definition) {
            $definitions[$key] = cache_definition::load($definition['component'].'/'.$definition['area'], $definition);
        }
        foreach ($definitions as $id => $definition) {
            $mappings = array();
            foreach (cache_helper::get_stores_suitable_for_definition($definition) as $store) {
                $mappings[] = $storenames[$store->my_name()];
            }
            $return[$id] = array(
                'id' => $id,
                'name' => $definition->get_name(),
                'mode' => $definition->get_mode(),
                'component' => $definition->get_component(),
                'area' => $definition->get_area(),
                'mappings' => $mappings,
                'canuselocalstore' => $definition->can_use_localstore(),
                'sharingoptions' => self::get_definition_sharing_options($definition->get_sharing_options(), false),
                'selectedsharingoption' => self::get_definition_sharing_options($definition->get_selected_sharing_option(), true),
                'userinputsharingkey' => $definition->get_user_input_sharing_key()
            );
        }
        return $return;
    }

    /**
     * Given a sharing option hash this function returns an array of strings that can be used to describe it.
     *
     * @param int $sharingoption The sharing option hash to get strings for.
     * @param bool $isselectedoptions Set to true if the strings will be used to view the selected options.
     * @return array An array of lang_string's.
     */
    public static function get_definition_sharing_options($sharingoption, $isselectedoptions = true) {
        $options = array();
        $prefix = ($isselectedoptions) ? 'sharingselected' : 'sharing';
        if ($sharingoption & cache_definition::SHARING_ALL) {
            $options[cache_definition::SHARING_ALL] = new lang_string($prefix.'_all', 'cache');
        }
        if ($sharingoption & cache_definition::SHARING_SITEID) {
            $options[cache_definition::SHARING_SITEID] = new lang_string($prefix.'_siteid', 'cache');
        }
        if ($sharingoption & cache_definition::SHARING_VERSION) {
            $options[cache_definition::SHARING_VERSION] = new lang_string($prefix.'_version', 'cache');
        }
        if ($sharingoption & cache_definition::SHARING_INPUT) {
            $options[cache_definition::SHARING_INPUT] = new lang_string($prefix.'_input', 'cache');
        }
        return $options;
    }

    /**
     * Returns all of the actions that can be performed on a definition.
     * @param context $context
     * @return array
     */
    public static function get_definition_actions(context $context, array $definition) {
        if (has_capability('moodle/site:config', $context)) {
            $actions = array();
            // Edit mappings.
            $actions[] = array(
                'text' => get_string('editmappings', 'cache'),
                'url' => new moodle_url('/cache/admin.php', array('action' => 'editdefinitionmapping', 'sesskey' => sesskey()))
            );
            // Edit sharing.
            if (count($definition['sharingoptions']) > 1) {
                $actions[] = array(
                    'text' => get_string('editsharing', 'cache'),
                    'url' => new moodle_url('/cache/admin.php', array('action' => 'editdefinitionsharing', 'sesskey' => sesskey()))
                );
            }
            // Purge.
            $actions[] = array(
                'text' => get_string('purge', 'cache'),
                'url' => new moodle_url('/cache/admin.php', array('action' => 'purgedefinition', 'sesskey' => sesskey()))
            );
            return $actions;
        }
        return array();
    }

    /**
     * Returns all of the actions that can be performed on a store.
     *
     * @param string $name The name of the store
     * @param array $storedetails
     * @return array
     */
    public static function get_store_instance_actions($name, array $storedetails) {
        $actions = array();
        if (has_capability('moodle/site:config', context_system::instance())) {
            $baseurl = new moodle_url('/cache/admin.php', array('store' => $name, 'sesskey' => sesskey()));
            if (empty($storedetails['default'])) {
                $actions[] = array(
                    'text' => get_string('editstore', 'cache'),
                    'url' => new moodle_url($baseurl, array('action' => 'editstore', 'plugin' => $storedetails['plugin']))
                );
                $actions[] = array(
                    'text' => get_string('deletestore', 'cache'),
                    'url' => new moodle_url($baseurl, array('action' => 'deletestore'))
                );
            }
            $actions[] = array(
                'text' => get_string('purge', 'cache'),
                'url' => new moodle_url($baseurl, array('action' => 'purgestore'))
            );
        }
        return $actions;
    }


    /**
     * Returns all of the actions that can be performed on a plugin.
     *
     * @param string $name The name of the plugin
     * @param array $plugindetails
     * @return array
     */
    public static function get_store_plugin_actions($name, array $plugindetails) {
        $actions = array();
        if (has_capability('moodle/site:config', context_system::instance())) {
            if (!empty($plugindetails['canaddinstance'])) {
                $url = new moodle_url('/cache/admin.php', array('action' => 'addstore', 'plugin' => $name, 'sesskey' => sesskey()));
                $actions[] = array(
                    'text' => get_string('addinstance', 'cache'),
                    'url' => $url
                );
            }
        }
        return $actions;
    }

    /**
     * Returns a form that can be used to add a store instance.
     *
     * @param string $plugin The plugin to add an instance of
     * @return cachestore_addinstance_form
     * @throws coding_exception
     */
    public static function get_add_store_form($plugin) {
        global $CFG; // Needed for includes.
        $plugins = core_component::get_plugin_list('cachestore');
        if (!array_key_exists($plugin, $plugins)) {
            throw new coding_exception('Invalid cache plugin used when trying to create an edit form.');
        }
        $plugindir = $plugins[$plugin];
        $class = 'cachestore_addinstance_form';
        if (file_exists($plugindir.'/addinstanceform.php')) {
            require_once($plugindir.'/addinstanceform.php');
            if (class_exists('cachestore_'.$plugin.'_addinstance_form')) {
                $class = 'cachestore_'.$plugin.'_addinstance_form';
                if (!array_key_exists('cachestore_addinstance_form', class_parents($class))) {
                    throw new coding_exception('Cache plugin add instance forms must extend cachestore_addinstance_form');
                }
            }
        }

        $locks = self::get_possible_locks_for_stores($plugindir, $plugin);

        $url = new moodle_url('/cache/admin.php', array('action' => 'addstore'));
        return new $class($url, array('plugin' => $plugin, 'store' => null, 'locks' => $locks));
    }

    /**
     * Returns a form that can be used to edit a store instance.
     *
     * @param string $plugin
     * @param string $store
     * @return cachestore_addinstance_form
     * @throws coding_exception
     */
    public static function get_edit_store_form($plugin, $store) {
        global $CFG; // Needed for includes.
        $plugins = core_component::get_plugin_list('cachestore');
        if (!array_key_exists($plugin, $plugins)) {
            throw new coding_exception('Invalid cache plugin used when trying to create an edit form.');
        }
        $factory = cache_factory::instance();
        $config = $factory->create_config_instance();
        $stores = $config->get_all_stores();
        if (!array_key_exists($store, $stores)) {
            throw new coding_exception('Invalid store name given when trying to create an edit form.');
        }
        $plugindir = $plugins[$plugin];
        $class = 'cachestore_addinstance_form';
        if (file_exists($plugindir.'/addinstanceform.php')) {
            require_once($plugindir.'/addinstanceform.php');
            if (class_exists('cachestore_'.$plugin.'_addinstance_form')) {
                $class = 'cachestore_'.$plugin.'_addinstance_form';
                if (!array_key_exists('cachestore_addinstance_form', class_parents($class))) {
                    throw new coding_exception('Cache plugin add instance forms must extend cachestore_addinstance_form');
                }
            }
        }

        $locks = self::get_possible_locks_for_stores($plugindir, $plugin);

        $url = new moodle_url('/cache/admin.php', array('action' => 'editstore', 'plugin' => $plugin, 'store' => $store));
        $editform = new $class($url, array('plugin' => $plugin, 'store' => $store, 'locks' => $locks));
        if (isset($stores[$store]['lock'])) {
            $editform->set_data(array('lock' => $stores[$store]['lock']));
        }
        // See if the cachestore is going to want to load data for the form.
        // If it has a customised add instance form then it is going to want to.
        $storeclass = 'cachestore_'.$plugin;
        $storedata = $stores[$store];
        if (array_key_exists('configuration', $storedata) && array_key_exists('cache_is_configurable', class_implements($storeclass))) {
            $storeclass::config_set_edit_form_data($editform, $storedata['configuration']);
        }
        return $editform;
    }

    /**
     * Returns an array of suitable lock instances for use with this plugin, or false if the plugin handles locking itself.
     *
     * @param string $plugindir
     * @param string $plugin
     * @return array|false
     */
    protected static function get_possible_locks_for_stores($plugindir, $plugin) {
        global $CFG; // Needed for includes.
        $supportsnativelocking = false;
        if (file_exists($plugindir.'/lib.php')) {
            require_once($plugindir.'/lib.php');
            $pluginclass = 'cachestore_'.$plugin;
            if (class_exists($pluginclass)) {
                $supportsnativelocking = array_key_exists('cache_is_lockable', class_implements($pluginclass));
            }
        }

        if (!$supportsnativelocking) {
            $config = cache_config::instance();
            $locks = array();
            foreach ($config->get_locks() as $lock => $conf) {
                if (!empty($conf['default'])) {
                    $name = get_string($lock, 'cache');
                } else {
                    $name = $lock;
                }
                $locks[$lock] = $name;
            }
        } else {
            $locks = false;
        }

        return $locks;
    }

    /**
     * Processes the results of the add/edit instance form data for a plugin returning an array of config information suitable to
     * store in configuration.
     *
     * @param stdClass $data The mform data.
     * @return array
     * @throws coding_exception
     */
    public static function get_store_configuration_from_data(stdClass $data) {
        global $CFG;
        $file = $CFG->dirroot.'/cache/stores/'.$data->plugin.'/lib.php';
        if (!file_exists($file)) {
            throw new coding_exception('Invalid cache plugin provided. '.$file);
        }
        require_once($file);
        $class = 'cachestore_'.$data->plugin;
        if (!class_exists($class)) {
            throw new coding_exception('Invalid cache plugin provided.');
        }
        if (array_key_exists('cache_is_configurable', class_implements($class))) {
            return $class::config_get_configuration_array($data);
        }
        return array();
    }

    /**
     * Get an array of stores that are suitable to be used for a given definition.
     *
     * @param string $component
     * @param string $area
     * @return array Array containing 3 elements
     *      1. An array of currently used stores
     *      2. An array of suitable stores
     *      3. An array of default stores
     */
    public static function get_definition_store_options($component, $area) {
        $factory = cache_factory::instance();
        $definition = $factory->create_definition($component, $area);
        $config = cache_config::instance();
        $currentstores = $config->get_stores_for_definition($definition);
        $possiblestores = $config->get_stores($definition->get_mode(), $definition->get_requirements_bin());

        $defaults = array();
        foreach ($currentstores as $key => $store) {
            if (!empty($store['default'])) {
                $defaults[] = $key;
                unset($currentstores[$key]);
            }
        }
        foreach ($possiblestores as $key => $store) {
            if ($store['default']) {
                unset($possiblestores[$key]);
                $possiblestores[$key] = $store;
            }
        }
        return array($currentstores, $possiblestores, $defaults);
    }

    /**
     * Get the default stores for all modes.
     *
     * @return array An array containing sub-arrays, one for each mode.
     */
    public static function get_default_mode_stores() {
        global $OUTPUT;
        $instance = cache_config::instance();
        $adequatestores = cache_helper::get_stores_suitable_for_mode_default();
        $icon = new pix_icon('i/warning', new lang_string('inadequatestoreformapping', 'cache'));
        $storenames = array();
        foreach ($instance->get_all_stores() as $key => $store) {
            if (!empty($store['default'])) {
                $storenames[$key] = new lang_string('store_'.$key, 'cache');
            }
        }
        $modemappings = array(
            cache_store::MODE_APPLICATION => array(),
            cache_store::MODE_SESSION => array(),
            cache_store::MODE_REQUEST => array(),
        );
        foreach ($instance->get_mode_mappings() as $mapping) {
            $mode = $mapping['mode'];
            if (!array_key_exists($mode, $modemappings)) {
                debugging('Unknown mode in cache store mode mappings', DEBUG_DEVELOPER);
                continue;
            }
            if (array_key_exists($mapping['store'], $storenames)) {
                $modemappings[$mode][$mapping['store']] = $storenames[$mapping['store']];
            } else {
                $modemappings[$mode][$mapping['store']] = $mapping['store'];
            }
            if (!array_key_exists($mapping['store'], $adequatestores)) {
                $modemappings[$mode][$mapping['store']] = $modemappings[$mode][$mapping['store']].' '.$OUTPUT->render($icon);
            }
        }
        return $modemappings;
    }

    /**
     * Returns an array summarising the locks available in the system
     */
    public static function get_lock_summaries() {
        $locks = array();
        $instance = cache_config::instance();
        $stores = $instance->get_all_stores();
        foreach ($instance->get_locks() as $lock) {
            $default = !empty($lock['default']);
            if ($default) {
                $name = new lang_string($lock['name'], 'cache');
            } else {
                $name = $lock['name'];
            }
            $uses = 0;
            foreach ($stores as $store) {
                if (!empty($store['lock']) && $store['lock'] === $lock['name']) {
                    $uses++;
                }
            }
            $lockdata = array(
                'name' => $name,
                'default' => $default,
                'uses' => $uses,
                'type' => get_string('pluginname', $lock['type'])
            );
            $locks[$lock['name']] = $lockdata;
        }
        return $locks;
    }

    /**
     * Returns an array of lock plugins for which we can add an instance.
     *
     * Suitable for use within an mform select element.
     *
     * @return array
     */
    public static function get_addable_lock_options() {
        $plugins = core_component::get_plugin_list_with_class('cachelock', '', 'lib.php');
        $options = array();
        $len = strlen('cachelock_');
        foreach ($plugins as $plugin => $class) {
            $method = "$class::can_add_instance";
            if (is_callable($method) && !call_user_func($method)) {
                // Can't add an instance of this plugin.
                continue;
            }
            $options[substr($plugin, $len)] = get_string('pluginname', $plugin);
        }
        return $options;
    }

    /**
     * Gets the form to use when adding a lock instance.
     *
     * @param string $plugin
     * @param array $lockplugin
     * @return cache_lock_form
     * @throws coding_exception
     */
    public static function get_add_lock_form($plugin, array $lockplugin = null) {
        global $CFG; // Needed for includes.
        $plugins = core_component::get_plugin_list('cachelock');
        if (!array_key_exists($plugin, $plugins)) {
            throw new coding_exception('Invalid cache lock plugin requested when trying to create a form.');
        }
        $plugindir = $plugins[$plugin];
        $class = 'cache_lock_form';
        if (file_exists($plugindir.'/addinstanceform.php') && in_array('cache_is_configurable', class_implements($class))) {
            require_once($plugindir.'/addinstanceform.php');
            if (class_exists('cachelock_'.$plugin.'_addinstance_form')) {
                $class = 'cachelock_'.$plugin.'_addinstance_form';
                if (!array_key_exists('cache_lock_form', class_parents($class))) {
                    throw new coding_exception('Cache lock plugin add instance forms must extend cache_lock_form');
                }
            }
        }
        return new $class(null, array('lock' => $plugin));
    }

    /**
     * Gets configuration data from a new lock instance form.
     *
     * @param string $plugin
     * @param stdClass $data
     * @return array
     * @throws coding_exception
     */
    public static function get_lock_configuration_from_data($plugin, $data) {
        global $CFG;
        $file = $CFG->dirroot.'/cache/locks/'.$plugin.'/lib.php';
        if (!file_exists($file)) {
            throw new coding_exception('Invalid cache plugin provided. '.$file);
        }
        require_once($file);
        $class = 'cachelock_'.$plugin;
        if (!class_exists($class)) {
            throw new coding_exception('Invalid cache plugin provided.');
        }
        if (array_key_exists('cache_is_configurable', class_implements($class))) {
            return $class::config_get_configuration_array($data);
        }
        return array();
    }
}
