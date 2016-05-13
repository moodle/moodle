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
 * Support library for the cache PHPUnit tests.
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

require_once($CFG->dirroot.'/cache/locallib.php');

/**
 * Override the default cache configuration for our own maniacal purposes.
 *
 * This class was originally named cache_config_phpunittest but was renamed in 2.9
 * because it is used for both unit tests and acceptance tests.
 *
 * @since 2.9
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_config_testing extends cache_config_writer {

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
        global $CFG;
        // HACK ALERT.
        // We probably need to come up with a better way to create the default stores, or at least ensure 100% that the
        // default store plugins are protected from deletion.
        $writer = new self;
        $writer->configstores = self::get_default_stores();
        $writer->configdefinitions = self::locate_definitions();
        $defaultapplication = 'default_application';

        $appdefine = defined('TEST_CACHE_USING_APPLICATION_STORE') ? TEST_CACHE_USING_APPLICATION_STORE : false;
        if ($appdefine !== false && preg_match('/^[a-zA-Z][a-zA-Z0-9_]+$/', $appdefine)) {
            $expectedstore = $appdefine;
            $file = $CFG->dirroot.'/cache/stores/'.$appdefine.'/lib.php';
            $class = 'cachestore_'.$appdefine;
            if (file_exists($file)) {
                require_once($file);
            }
            if (class_exists($class) && $class::ready_to_be_used_for_testing()) {
                /* @var cache_store $class */
                $writer->configstores['test_application'] = array(
                    'use_test_store' => true,
                    'name' => 'test_application',
                    'plugin' => $expectedstore,
                    'alt' => $writer->configstores[$defaultapplication],
                    'modes' => $class::get_supported_modes(),
                    'features' => $class::get_supported_features()
                );
                $defaultapplication = 'test_application';
            }
        }

        $writer->configmodemappings = array(
            array(
                'mode' => cache_store::MODE_APPLICATION,
                'store' => $defaultapplication,
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
     * Returns the expected path to the configuration file.
     *
     * We override this function to add handling for $CFG->altcacheconfigpath.
     * We want to support it so that people can run unit tests against alternative cache setups.
     * However we don't want to ever make changes to the file at $CFG->altcacheconfigpath so we
     * always use dataroot and copy the alt file there as required.
     *
     * @throws cache_exception
     * @return string The absolute path
     */
    protected static function get_config_file_path() {
        global $CFG;
        // We always use this path.
        $configpath = $CFG->dataroot.'/muc/config.php';

        if (!empty($CFG->altcacheconfigpath)) {

            // No need to check we are within a test here, this is the cache config class that gets used
            // only when one of those is true.
            if  (!defined('TEST_CACHE_USING_ALT_CACHE_CONFIG_PATH') || !TEST_CACHE_USING_ALT_CACHE_CONFIG_PATH) {
                // TEST_CACHE_USING_ALT_CACHE_CONFIG_PATH has not being defined or is false, we want to use the default.
                return $configpath;
            }

            $path = $CFG->altcacheconfigpath;
            if (is_dir($path) && is_writable($path)) {
                // Its a writable directory, thats fine. Convert it to a file.
                $path = $CFG->altcacheconfigpath.'/cacheconfig.php';
            }
            if (is_readable($path)) {
                $directory = dirname($configpath);
                if ($directory !== $CFG->dataroot && !file_exists($directory)) {
                    $result = make_writable_directory($directory, false);
                    if (!$result) {
                        throw new cache_exception('ex_configcannotsave', 'cache', '', null, 'Cannot create config directory. Check the permissions on your moodledata directory.');
                    }
                }
                // We don't care that this fails but we should let the developer know.
                if (!is_readable($configpath) && !@copy($path, $configpath)) {
                    debugging('Failed to copy alt cache config file to required location');
                }
            }
        }

        // We always use the dataroot location.
        return $configpath;
    }

    /**
     * Adds a definition to the stack
     * @param string $area
     * @param array $properties
     * @param bool $addmapping By default this method adds a definition and a mapping for that definition. You can
     *    however set this to false if you only want it to add the definition and not the mapping.
     */
    public function phpunit_add_definition($area, array $properties, $addmapping = true) {
        if (!array_key_exists('overrideclass', $properties)) {
            switch ($properties['mode']) {
                case cache_store::MODE_APPLICATION:
                    $properties['overrideclass'] = 'cache_phpunit_application';
                    break;
                case cache_store::MODE_SESSION:
                    $properties['overrideclass'] = 'cache_phpunit_session';
                    break;
                case cache_store::MODE_REQUEST:
                    $properties['overrideclass'] = 'cache_phpunit_request';
                    break;
            }
        }
        $this->configdefinitions[$area] = $properties;
        if ($addmapping) {
            switch ($properties['mode']) {
                case cache_store::MODE_APPLICATION:
                    $this->phpunit_add_definition_mapping($area, 'default_application', 0);
                    break;
                case cache_store::MODE_SESSION:
                    $this->phpunit_add_definition_mapping($area, 'default_session', 0);
                    break;
                case cache_store::MODE_REQUEST:
                    $this->phpunit_add_definition_mapping($area, 'default_request', 0);
                    break;
            }
        }
    }

    /**
     * Removes a definition.
     * @param string $name
     */
    public function phpunit_remove_definition($name) {
        unset($this->configdefinitions[$name]);
    }

    /**
     * Removes the configured stores so that there are none available.
     */
    public function phpunit_remove_stores() {
        $this->configstores = array();
    }

    /**
     * Forcefully adds a file store.
     *
     * @param string $name
     */
    public function phpunit_add_file_store($name) {
        $this->configstores[$name] = array(
            'name' => $name,
            'plugin' => 'file',
            'configuration' => array(
                'path' => ''
            ),
            'features' => 6,
            'modes' => 3,
            'mappingsonly' => false,
            'class' => 'cachestore_file',
            'default' => false,
            'lock' => 'cachelock_file_default'
        );
    }

    /**
     * Forcefully adds a session store.
     *
     * @param string $name
     */
    public function phpunit_add_session_store($name) {
        $this->configstores[$name] = array(
            'name' => $name,
            'plugin' => 'session',
            'configuration' => array(),
            'features' => 14,
            'modes' => 2,
            'default' => true,
            'class' => 'cachestore_session',
            'lock' => 'cachelock_file_default',
        );
    }

    /**
     * Forcefully injects a definition => store mapping.
     *
     * This function does no validation, you should only be calling if it you know
     * exactly what to expect.
     *
     * @param string $definition
     * @param string $store
     * @param int $sort
     */
    public function phpunit_add_definition_mapping($definition, $store, $sort) {
        $this->configdefinitionmappings[] = array(
            'store' => $store,
            'definition' => $definition,
            'sort' => (int)$sort
        );
    }

    /**
     * Overrides the default site identifier used by the Cache API so that we can be sure of what it is.
     *
     * @return string
     */
    public function get_site_identifier() {
        global $CFG;
        return $CFG->wwwroot.'phpunit';
    }
}

/**
 * This is a deprecated class. It has been renamed to cache_config_testing.
 *
 * This was deprecated in Moodle 2.9 but will be removed at the next major release
 * as it is only used during testing and its highly unlikely anyone has used this.
 *
 * @deprecated since 2.9
 * @copyright  2014 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_config_phpunittest extends cache_config_testing {
    // We can't do anything here to warn the user.
    // The cache can be utilised before sessions have even been started.
}

/**
 * Dummy object for testing cacheable object interface and interaction
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_phpunit_dummy_object extends stdClass implements cacheable_object {
    /**
     * Test property 1
     * @var string
     */
    public $property1;
    /**
     * Test property 1
     * @var string
     */
    public $property2;
    /**
     * Constructor
     * @param string $property1
     * @param string $property2
     */
    public function __construct($property1, $property2) {
        $this->property1 = $property1;
        $this->property2 = $property2;
    }
    /**
     * Prepares this object for caching
     * @return array
     */
    public function prepare_to_cache() {
        return array($this->property1.'_ptc', $this->property2.'_ptc');
    }
    /**
     * Returns this object from the cache
     * @param array $data
     * @return cache_phpunit_dummy_object
     */
    public static function wake_from_cache($data) {
        return new cache_phpunit_dummy_object(array_shift($data).'_wfc', array_shift($data).'_wfc');
    }
}

/**
 * Dummy data source object for testing data source interface and implementation
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_phpunit_dummy_datasource implements cache_data_source {
    /**
     * Returns an instance of this object for use with the cache.
     *
     * @param cache_definition $definition
     * @return cache_phpunit_dummy_datasource
     */
    public static function get_instance_for_cache(cache_definition $definition) {
        return new cache_phpunit_dummy_datasource();
    }

    /**
     * Loads a key for the cache.
     *
     * @param string $key
     * @return string
     */
    public function load_for_cache($key) {
        return $key.' has no value really.';
    }

    /**
     * Loads many keys for the cache
     *
     * @param array $keys
     * @return array
     */
    public function load_many_for_cache(array $keys) {
        $return = array();
        foreach ($keys as $key) {
            $return[$key] = $key.' has no value really.';
        }
        return $return;
    }
}

/**
 * PHPUnit application cache loader.
 *
 * Used to expose things we could not otherwise see within an application cache.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_phpunit_application extends cache_application {

    /**
     * Returns the class of the store immediately associated with this cache.
     * @return string
     */
    public function phpunit_get_store_class() {
        return get_class($this->get_store());
    }

    /**
     * Returns all the interfaces the cache store implements.
     * @return array
     */
    public function phpunit_get_store_implements() {
        return class_implements($this->get_store());
    }

    /**
     * Returns the given key directly from the static acceleration array.
     *
     * @param string $key
     * @return false|mixed
     */
    public function phpunit_static_acceleration_get($key) {
        return $this->static_acceleration_get($key);
    }
}

/**
 * PHPUnit session cache loader.
 *
 * Used to expose things we could not otherwise see within an session cache.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_phpunit_session extends cache_session {

    /**
     * Returns the class of the store immediately associated with this cache.
     * @return string
     */
    public function phpunit_get_store_class() {
        return get_class($this->get_store());
    }

    /**
     * Returns all the interfaces the cache store implements.
     * @return array
     */
    public function phpunit_get_store_implements() {
        return class_implements($this->get_store());
    }
}

/**
 * PHPUnit request cache loader.
 *
 * Used to expose things we could not otherwise see within an request cache.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_phpunit_request extends cache_request {

    /**
     * Returns the class of the store immediately associated with this cache.
     * @return string
     */
    public function phpunit_get_store_class() {
        return get_class($this->get_store());
    }

    /**
     * Returns all the interfaces the cache store implements.
     * @return array
     */
    public function phpunit_get_store_implements() {
        return class_implements($this->get_store());
    }
}

/**
 * Dummy overridden cache loader class that we can use to test overriding loader functionality.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_phpunit_dummy_overrideclass extends cache_application {
    // Satisfying the code pre-checker is just part of my day job.
}

/**
 * Cache PHPUnit specific factory.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_phpunit_factory extends cache_factory {
    /**
     * Exposes the cache_factory's disable method.
     *
     * Perhaps one day that method will be made public, for the time being it is protected.
     */
    public static function phpunit_disable() {
        parent::disable();
    }

    /**
     * Creates a store instance given its name and configuration.
     *
     * If the store has already been instantiated then the original object will be returned. (reused)
     *
     * @param string $name The name of the store (must be unique remember)
     * @param array $details
     * @param cache_definition $definition The definition to instantiate it for.
     * @return boolean|cache_store
     */
    public function create_store_from_config($name, array $details, cache_definition $definition) {

        if (isset($details['use_test_store'])) {
            // name, plugin, alt
            $class = 'cachestore_'.$details['plugin'];
            $method = 'initialise_unit_test_instance';
            if (class_exists($class) && method_exists($class, $method)) {
                $instance = $class::$method($definition);

                if ($instance) {
                    return $instance;
                }
            }
            $details = $details['alt'];
            $name = $details['name'];
        }

        return parent::create_store_from_config($name, $details, $definition);
    }
}