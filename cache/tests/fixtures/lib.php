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

/**
 * Override the default cache configuration for our own maniacle purposes.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_config_phpunittest extends cache_config_writer {

    /**
     * Adds a definition to the stack
     * @param string $area
     * @param array $properties
     */
    public function phpunit_add_definition($area, array $properties) {
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
    public function phpunit_get_directly_from_staticaccelerationarray($key) {
        $key = $this->parse_key($key);
        return $this->get_from_persist_cache($key);
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
}