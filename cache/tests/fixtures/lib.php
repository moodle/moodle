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
        $this->configdefinitions[$area] = $properties;
    }

    /**
     * Removes the configured stores so that there are none available.
     */
    public function phpunit_remove_stores() {
        $this->configstores = array();
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
 * Dummy overridden cache loader class that we can use to test overriding loader functionality.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_phpunit_dummy_overrideclass extends cache_application {
    // Satisfying the code pre-checker is just part of my day job.
}