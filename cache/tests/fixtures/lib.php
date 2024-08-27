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
 * @package    core_cache
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_cache\store;

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
        $writer = new self();
        $writer->configstores = self::get_default_stores();
        $writer->configdefinitions = self::locate_definitions();
        $defaultapplication = 'default_application';

        $appdefine = defined('TEST_CACHE_USING_APPLICATION_STORE') ? TEST_CACHE_USING_APPLICATION_STORE : false;
        if ($appdefine !== false && preg_match('/^[a-zA-Z][a-zA-Z0-9_]+$/', $appdefine)) {
            $expectedstore = $appdefine;
            $file = $CFG->dirroot . '/cache/stores/' . $appdefine . '/lib.php';
            $class = 'cachestore_' . $appdefine;
            if (file_exists($file)) {
                require_once($file);
            }
            if (class_exists($class) && $class::ready_to_be_used_for_testing()) {
                /* @var store $class */
                $writer->configstores['test_application'] = [
                    'name' => 'test_application',
                    'plugin' => $expectedstore,
                    'modes' => $class::get_supported_modes(),
                    'features' => $class::get_supported_features(),
                    'configuration' => $class::unit_test_configuration(),
                ];

                $defaultapplication = 'test_application';
            }
        }

        $writer->configmodemappings = [
            [
                'mode' => store::MODE_APPLICATION,
                'store' => $defaultapplication,
                'sort' => -1,
            ],
            [
                'mode' => store::MODE_SESSION,
                'store' => 'default_session',
                'sort' => -1,
            ],
            [
                'mode' => store::MODE_REQUEST,
                'store' => 'default_request',
                'sort' => -1,
            ],
        ];
        $writer->configlocks = [
            'default_file_lock' => [
                'name' => 'cachelock_file_default',
                'type' => 'cachelock_file',
                'dir' => 'filelocks',
                'default' => true,
            ],
        ];

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
        $configpath = $CFG->dataroot . '/muc/config.php';

        if (!empty($CFG->altcacheconfigpath)) {
            // No need to check we are within a test here, this is the cache config class that gets used
            // only when one of those is true.
            if (!defined('TEST_CACHE_USING_ALT_CACHE_CONFIG_PATH') || !TEST_CACHE_USING_ALT_CACHE_CONFIG_PATH) {
                // TEST_CACHE_USING_ALT_CACHE_CONFIG_PATH has not being defined or is false, we want to use the default.
                return $configpath;
            }

            $path = $CFG->altcacheconfigpath;
            if (is_dir($path) && is_writable($path)) {
                // Its a writable directory, thats fine. Convert it to a file.
                $path = $CFG->altcacheconfigpath . '/cacheconfig.php';
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
                case store::MODE_APPLICATION:
                    $properties['overrideclass'] = 'cache_phpunit_application';
                    break;
                case store::MODE_SESSION:
                    $properties['overrideclass'] = 'cache_phpunit_session';
                    break;
                case store::MODE_REQUEST:
                    $properties['overrideclass'] = 'cache_phpunit_request';
                    break;
            }
        }
        $this->configdefinitions[$area] = $properties;
        if ($addmapping) {
            switch ($properties['mode']) {
                case store::MODE_APPLICATION:
                    $this->phpunit_add_definition_mapping($area, 'default_application', 0);
                    break;
                case store::MODE_SESSION:
                    $this->phpunit_add_definition_mapping($area, 'default_session', 0);
                    break;
                case store::MODE_REQUEST:
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
        $this->configstores = [];
    }

    /**
     * Forcefully adds a file store.
     *
     * You can turn off native TTL support if you want a way to test TTL wrapper objects.
     *
     * @param string $name
     * @param bool $nativettl If false, uses fixture that turns off native TTL support
     */
    public function phpunit_add_file_store(string $name, bool $nativettl = true): void {
        if (!$nativettl) {
            require_once(__DIR__ . '/cachestore_file_with_ttl_wrappers.php');
        }
        $this->configstores[$name] = [
            'name' => $name,
            'plugin' => 'file',
            'configuration' => [
                'path' => '',
            ],
            'features' => 6,
            'modes' => 3,
            'mappingsonly' => false,
            'class' => $nativettl ? 'cachestore_file' : 'cachestore_file_with_ttl_wrappers',
            'default' => false,
            'lock' => 'cachelock_file_default',
        ];
    }

    /**
     * Hacks the in-memory configuration for a store.
     *
     * @param string $store Name of store to edit e.g. 'default_application'
     * @param array $configchanges List of config changes
     */
    public function phpunit_edit_store_config(string $store, array $configchanges): void {
        foreach ($configchanges as $name => $value) {
            $this->configstores[$store]['configuration'][$name] = $value;
        }
    }

    /**
     * Forcefully adds a session store.
     *
     * @param string $name
     */
    public function phpunit_add_session_store($name) {
        $this->configstores[$name] = [
            'name' => $name,
            'plugin' => 'session',
            'configuration' => [],
            'features' => 14,
            'modes' => 2,
            'default' => true,
            'class' => 'cachestore_session',
            'lock' => 'cachelock_file_default',
        ];
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
        $this->configdefinitionmappings[] = [
            'store' => $store,
            'definition' => $definition,
            'sort' => (int)$sort,
        ];
    }

    /**
     * Overrides the default site identifier used by the Cache API so that we can be sure of what it is.
     *
     * @return string
     */
    public function get_site_identifier() {
        global $CFG;
        return $CFG->wwwroot . 'phpunit';
    }

    /**
     * Checks if the configuration file exists.
     *
     * @return bool True if it exists
     */
    public static function config_file_exists() {
        // Allow for late static binding by using static.
        $configfilepath = static::get_config_file_path();

        // Invalidate opcode php cache, so we get correct status of file.
        core_component::invalidate_opcode_php_cache($configfilepath);
        return file_exists($configfilepath);
    }
}


/**
 * Dummy object for testing cacheable object interface and interaction
 *
 * Wake from cache needs specific testing at times to ensure that during multiple
 * cache get() requests it's possible to verify that it's getting woken each time.
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
     * Test property time for verifying wake is run at each get() call.
     * @var float
     */
    public $propertytime;
    /**
     * Constructor
     * @param string $property1
     * @param string $property2
     */
    public function __construct($property1, $property2, $propertytime = null) {
        $this->property1 = $property1;
        $this->property2 = $property2;
        $this->propertytime = $propertytime === null ? microtime(true) : $propertytime;
    }
    /**
     * Prepares this object for caching
     * @return array
     */
    public function prepare_to_cache() {
        return [$this->property1 . '_ptc', $this->property2 . '_ptc', $this->propertytime];
    }
    /**
     * Returns this object from the cache
     * @param array $data
     * @return cache_phpunit_dummy_object
     */
    public static function wake_from_cache($data) {
        $time = null;
        if (!is_null($data[2])) {
            // Windows 32bit microtime() resolution is 15ms, we ensure the time has moved forward.
            do {
                $time = microtime(true);
            } while ($time == $data[2]);
        }
        return new cache_phpunit_dummy_object(array_shift($data) . '_wfc', array_shift($data) . '_wfc', $time);
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
        return $key . ' has no value really.';
    }

    /**
     * Loads many keys for the cache
     *
     * @param array $keys
     * @return array
     */
    public function load_many_for_cache(array $keys) {
        $return = [];
        foreach ($keys as $key) {
            $return[$key] = $key . ' has no value really.';
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
    #[\Override]
    public function get_store() {
        return parent::get_store();
    }

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

    /**
     * Purges only the static acceleration while leaving the rest of the store in tack.
     *
     * Used for behaving like you have loaded 2 pages, and reset static while the backing store
     * still contains all the same data.
     *
     */
    public function phpunit_static_acceleration_purge() {
        $this->static_acceleration_purge();
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
    /** @var Static member used for emulating the behaviour of session_id() during the tests. */
    protected static $sessionidmockup = 'phpunitmockupsessionid';

    #[\Override]
    public function get_store() {
        return parent::get_store();
    }

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
     * Provide access to the {@link cache_session::get_key_prefix()} method.
     *
     * @return string
     */
    public function phpunit_get_key_prefix() {
        return $this->get_key_prefix();
    }

    /**
     * Allows to inject the session identifier.
     *
     * @param string $sessionid
     */
    public static function phpunit_mockup_session_id($sessionid) {
        static::$sessionidmockup = $sessionid;
    }

    /**
     * Override the parent behaviour so that it does not need the actual session_id() call.
     */
    protected function set_session_id() {
        $this->sessionid = static::$sessionidmockup;
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
    #[\Override]
    public function get_store() {
        return parent::get_store();
    }

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
}

/**
 * Cache PHPUnit specific Cache helper.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_phpunit_cache extends cache {
    /**
     * Make the changes which simulate a new request within the cache.
     * This essentially resets currently held static values in the class, and increments the current timestamp.
     */
    public static function simulate_new_request() {
        self::$now += 0.1;
        self::$purgetoken = null;
    }
}
