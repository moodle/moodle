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

use cache_config_testing;
use cache_config_writer;
use cache_factory;
use cache_store;

defined('MOODLE_INTERNAL') || die();

// Include the necessary evils.
global $CFG;
require_once($CFG->dirroot.'/cache/locallib.php');
require_once($CFG->dirroot.'/cache/tests/fixtures/lib.php');

/**
 * PHPunit tests for the cache API and in particular the cache config writer.
 *
 * @package    core_cache
 * @category   test
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config_writer_test extends \advanced_testcase {

    /**
     * Set things back to the default before each test.
     */
    public function setUp(): void {
        parent::setUp();
        cache_factory::reset();
        cache_config_testing::create_default_configuration();
    }

    /**
     * Final task is to reset the cache system
     */
    public static function tearDownAfterClass(): void {
        parent::tearDownAfterClass();
        cache_factory::reset();
    }

    /**
     * Test getting an instance. Pretty basic.
     */
    public function test_instance() {
        $config = cache_config_writer::instance();
        $this->assertInstanceOf('cache_config_writer', $config);
    }

    /**
     * Test the default configuration.
     */
    public function test_default_configuration() {
        $config = cache_config_writer::instance();

        // First check stores.
        $stores = $config->get_all_stores();
        $hasapplication = false;
        $hassession = false;
        $hasrequest = false;
        foreach ($stores as $store) {
            // Check the required keys.
            $this->assertArrayHasKey('name', $store);
            $this->assertArrayHasKey('plugin', $store);
            $this->assertArrayHasKey('modes', $store);
            $this->assertArrayHasKey('default', $store);
            // Check the mode, we need at least one default store of each mode.
            if (!empty($store['default'])) {
                if ($store['modes'] & cache_store::MODE_APPLICATION) {
                    $hasapplication = true;
                }
                if ($store['modes'] & cache_store::MODE_SESSION) {
                    $hassession = true;
                }
                if ($store['modes'] & cache_store::MODE_REQUEST) {
                    $hasrequest = true;
                }
            }
        }
        $this->assertTrue($hasapplication, 'There is no default application cache store.');
        $this->assertTrue($hassession, 'There is no default session cache store.');
        $this->assertTrue($hasrequest, 'There is no default request cache store.');

        // Next check the definitions.
        $definitions = $config->get_definitions();
        $eventinvalidation = false;
        foreach ($definitions as $definition) {
            // Check the required keys.
            $this->assertArrayHasKey('mode', $definition);
            $this->assertArrayHasKey('component', $definition);
            $this->assertArrayHasKey('area', $definition);
            if ($definition['component'] === 'core' && $definition['area'] === 'eventinvalidation') {
                $eventinvalidation = true;
            }
        }
        $this->assertTrue($eventinvalidation, 'Missing the event invalidation definition.');

        // Next mode mappings
        $mappings = $config->get_mode_mappings();
        $hasapplication = false;
        $hassession = false;
        $hasrequest = false;
        foreach ($mappings as $mode) {
            // Check the required keys.
            $this->assertArrayHasKey('mode', $mode);
            $this->assertArrayHasKey('store', $mode);

            if ($mode['mode'] === cache_store::MODE_APPLICATION) {
                $hasapplication = true;
            }
            if ($mode['mode'] === cache_store::MODE_SESSION) {
                $hassession = true;
            }
            if ($mode['mode'] === cache_store::MODE_REQUEST) {
                $hasrequest = true;
            }
        }
        $this->assertTrue($hasapplication, 'There is no mapping for the application mode.');
        $this->assertTrue($hassession, 'There is no mapping for the session mode.');
        $this->assertTrue($hasrequest, 'There is no mapping for the request mode.');

        // Finally check config locks
        $locks = $config->get_locks();
        foreach ($locks as $lock) {
            $this->assertArrayHasKey('name', $lock);
            $this->assertArrayHasKey('type', $lock);
            $this->assertArrayHasKey('default', $lock);
        }
        // There has to be at least the default lock.
        $this->assertTrue(count($locks) > 0);
    }

    /**
     * Test updating the definitions.
     */
    public function test_update_definitions() {
        $config = cache_config_writer::instance();
        // Remove the definition.
        $config->phpunit_remove_definition('core/string');
        $definitions = $config->get_definitions();
        // Check it is gone.
        $this->assertFalse(array_key_exists('core/string', $definitions));
        // Update definitions. This should re-add it.
        cache_config_writer::update_definitions();
        $definitions = $config->get_definitions();
        // Check it is back again.
        $this->assertTrue(array_key_exists('core/string', $definitions));
    }

    /**
     * Test adding/editing/deleting store instances.
     */
    public function test_add_edit_delete_plugin_instance() {
        $config = cache_config_writer::instance();
        $this->assertArrayNotHasKey('addplugintest', $config->get_all_stores());
        $this->assertArrayNotHasKey('addplugintestwlock', $config->get_all_stores());
        // Add a default file instance.
        $config->add_store_instance('addplugintest', 'file');

        cache_factory::reset();
        $config = cache_config_writer::instance();
        $this->assertArrayHasKey('addplugintest', $config->get_all_stores());

        // Add a store with a lock described.
        $config->add_store_instance('addplugintestwlock', 'file', array('lock' => 'default_file_lock'));
        $this->assertArrayHasKey('addplugintestwlock', $config->get_all_stores());

        $config->delete_store_instance('addplugintest');
        $this->assertArrayNotHasKey('addplugintest', $config->get_all_stores());
        $this->assertArrayHasKey('addplugintestwlock', $config->get_all_stores());

        $config->delete_store_instance('addplugintestwlock');
        $this->assertArrayNotHasKey('addplugintest', $config->get_all_stores());
        $this->assertArrayNotHasKey('addplugintestwlock', $config->get_all_stores());

        // Add a default file instance.
        $config->add_store_instance('storeconfigtest', 'file', array('test' => 'a', 'one' => 'two'));
        $stores = $config->get_all_stores();
        $this->assertArrayHasKey('storeconfigtest', $stores);
        $this->assertArrayHasKey('configuration', $stores['storeconfigtest']);
        $this->assertArrayHasKey('test', $stores['storeconfigtest']['configuration']);
        $this->assertArrayHasKey('one', $stores['storeconfigtest']['configuration']);
        $this->assertEquals('a', $stores['storeconfigtest']['configuration']['test']);
        $this->assertEquals('two', $stores['storeconfigtest']['configuration']['one']);

        $config->edit_store_instance('storeconfigtest', 'file', array('test' => 'b', 'one' => 'three'));
        $stores = $config->get_all_stores();
        $this->assertArrayHasKey('storeconfigtest', $stores);
        $this->assertArrayHasKey('configuration', $stores['storeconfigtest']);
        $this->assertArrayHasKey('test', $stores['storeconfigtest']['configuration']);
        $this->assertArrayHasKey('one', $stores['storeconfigtest']['configuration']);
        $this->assertEquals('b', $stores['storeconfigtest']['configuration']['test']);
        $this->assertEquals('three', $stores['storeconfigtest']['configuration']['one']);

        $config->delete_store_instance('storeconfigtest');

        try {
            $config->delete_store_instance('default_application');
            $this->fail('Default store deleted. This should not be possible!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('cache_exception', $e);
        }

        try {
            $config->delete_store_instance('some_crazy_store');
            $this->fail('You should not be able to delete a store that does not exist.');
        } catch (\Exception $e) {
            $this->assertInstanceOf('cache_exception', $e);
        }

        try {
            // Try with a plugin that does not exist.
            $config->add_store_instance('storeconfigtest', 'shallowfail', array('test' => 'a', 'one' => 'two'));
            $this->fail('You should not be able to add an instance of a store that does not exist.');
        } catch (\Exception $e) {
            $this->assertInstanceOf('cache_exception', $e);
        }
    }

    /**
     * Test setting some mode mappings.
     */
    public function test_set_mode_mappings() {
        $config = cache_config_writer::instance();
        $this->assertTrue($config->add_store_instance('setmodetest', 'file'));
        $this->assertTrue($config->set_mode_mappings(array(
            cache_store::MODE_APPLICATION => array('setmodetest', 'default_application'),
            cache_store::MODE_SESSION => array('default_session'),
            cache_store::MODE_REQUEST => array('default_request'),
        )));
        $mappings = $config->get_mode_mappings();
        $setmodetestfound = false;
        foreach ($mappings as $mapping) {
            if ($mapping['store'] == 'setmodetest' && $mapping['mode'] == cache_store::MODE_APPLICATION) {
                $setmodetestfound = true;
            }
        }
        $this->assertTrue($setmodetestfound, 'Set mapping did not work as expected.');
    }

    /**
     * Test setting some definition mappings.
     */
    public function test_set_definition_mappings() {
        $config = cache_config_testing::instance(true);
        $config->phpunit_add_definition('phpunit/testdefinition', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'testdefinition'
        ));

        $config = cache_config_writer::instance();
        $this->assertTrue($config->add_store_instance('setdefinitiontest', 'file'));
        $this->assertIsArray($config->get_definition_by_id('phpunit/testdefinition'));
        $config->set_definition_mappings('phpunit/testdefinition', array('setdefinitiontest', 'default_application'));

        try {
            $config->set_definition_mappings('phpunit/testdefinition', array('something that does not exist'));
            $this->fail('You should not be able to set a mapping for a store that does not exist.');
        } catch (\Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $config->set_definition_mappings('something/crazy', array('setdefinitiontest'));
            $this->fail('You should not be able to set a mapping for a definition that does not exist.');
        } catch (\Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }
}
