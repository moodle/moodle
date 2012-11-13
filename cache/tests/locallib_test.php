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
 * PHPunit tests for the cache API and in particular things in locallib.php
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

// Include the necessary evils.
global $CFG;
require_once($CFG->dirroot.'/cache/locallib.php');
require_once($CFG->dirroot.'/cache/tests/fixtures/lib.php');

/**
 * PHPunit tests for the cache API and in particular the cache config writer.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_config_writer_phpunit_tests extends advanced_testcase {

    /**
     * Set things back to the default before each test.
     */
    public function setUp() {
        parent::setUp();
        cache_factory::reset();
        cache_config_phpunittest::create_default_configuration();
    }

    /**
     * Final task is to reset the cache system
     */
    public static function tearDownAfterClass() {
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
        $earlydefinitions = $config->get_definitions();
        unset($config);
        cache_factory::reset();
        cache_config_writer::update_definitions();

        $config = cache_config_writer::instance();
        $latedefinitions = $config->get_definitions();

        $this->assertSame($latedefinitions, $earlydefinitions);
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
        } catch (Exception $e) {
            $this->assertInstanceOf('cache_exception', $e);
        }

        try {
            $config->delete_store_instance('some_crazy_store');
            $this->fail('You should not be able to delete a store that does not exist.');
        } catch (Exception $e) {
            $this->assertInstanceOf('cache_exception', $e);
        }

        try {
            // Try with a plugin that does not exist.
            $config->add_store_instance('storeconfigtest', 'shallowfail', array('test' => 'a', 'one' => 'two'));
            $this->fail('You should not be able to add an instance of a store that does not exist.');
        } catch (Exception $e) {
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
        $config = cache_config_phpunittest::instance(true);
        $config->phpunit_add_definition('phpunit/testdefinition', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'testdefinition'
        ));

        $config = cache_config_writer::instance();
        $this->assertTrue($config->add_store_instance('setdefinitiontest', 'file'));
        $this->assertInternalType('array', $config->get_definition_by_id('phpunit/testdefinition'));
        $config->set_definition_mappings('phpunit/testdefinition', array('setdefinitiontest', 'default_application'));

        try {
            $config->set_definition_mappings('phpunit/testdefinition', array('something that does not exist'));
            $this->fail('You should not be able to set a mapping for a store that does not exist.');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $config->set_definition_mappings('something/crazy', array('setdefinitiontest'));
            $this->fail('You should not be able to set a mapping for a definition that does not exist.');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }
}

/**
 * PHPunit tests for the cache API and in particular the cache_administration_helper
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_administration_helper_phpunit_tests extends advanced_testcase {

    /**
     * Set things back to the default before each test.
     */
    public function setUp() {
        parent::setUp();
        cache_factory::reset();
        cache_config_phpunittest::create_default_configuration();
    }

    /**
     * Final task is to reset the cache system
     */
    public static function tearDownAfterClass() {
        parent::tearDownAfterClass();
        cache_factory::reset();
    }

    /**
     * Test the numerous summaries the helper can produce.
     */
    public function test_get_summaries() {
        // First the preparation.
        $config = cache_config_writer::instance();
        $this->assertTrue($config->add_store_instance('summariesstore', 'file'));
        $config->set_definition_mappings('core/eventinvalidation', array('summariesstore'));
        $this->assertTrue($config->set_mode_mappings(array(
            cache_store::MODE_APPLICATION => array('summariesstore'),
            cache_store::MODE_SESSION => array('default_session'),
            cache_store::MODE_REQUEST => array('default_request'),
        )));

        $storesummaries = cache_administration_helper::get_store_instance_summaries();
        $this->assertInternalType('array', $storesummaries);
        $this->assertArrayHasKey('summariesstore', $storesummaries);
        $summary = $storesummaries['summariesstore'];
        // Check the keys
        $this->assertArrayHasKey('name', $summary);
        $this->assertArrayHasKey('plugin', $summary);
        $this->assertArrayHasKey('default', $summary);
        $this->assertArrayHasKey('isready', $summary);
        $this->assertArrayHasKey('requirementsmet', $summary);
        $this->assertArrayHasKey('mappings', $summary);
        $this->assertArrayHasKey('modes', $summary);
        $this->assertArrayHasKey('supports', $summary);
        // Check the important/known values
        $this->assertEquals('summariesstore', $summary['name']);
        $this->assertEquals('file', $summary['plugin']);
        $this->assertEquals(0, $summary['default']);
        $this->assertEquals(1, $summary['isready']);
        $this->assertEquals(1, $summary['requirementsmet']);
        $this->assertEquals(1, $summary['mappings']);

        $definitionsummaries = cache_administration_helper::get_definition_summaries();
        $this->assertInternalType('array', $definitionsummaries);
        $this->assertArrayHasKey('core/eventinvalidation', $definitionsummaries);
        $summary = $definitionsummaries['core/eventinvalidation'];
        // Check the keys
        $this->assertArrayHasKey('id', $summary);
        $this->assertArrayHasKey('name', $summary);
        $this->assertArrayHasKey('mode', $summary);
        $this->assertArrayHasKey('component', $summary);
        $this->assertArrayHasKey('area', $summary);
        $this->assertArrayHasKey('mappings', $summary);
        // Check the important/known values
        $this->assertEquals('core/eventinvalidation', $summary['id']);
        $this->assertInstanceOf('lang_string', $summary['name']);
        $this->assertEquals(cache_store::MODE_APPLICATION, $summary['mode']);
        $this->assertEquals('core', $summary['component']);
        $this->assertEquals('eventinvalidation', $summary['area']);
        $this->assertInternalType('array', $summary['mappings']);
        $this->assertContains('summariesstore', $summary['mappings']);

        $pluginsummaries = cache_administration_helper::get_store_plugin_summaries();
        $this->assertInternalType('array', $pluginsummaries);
        $this->assertArrayHasKey('file', $pluginsummaries);
        $summary = $pluginsummaries['file'];
        // Check the keys
        $this->assertArrayHasKey('name', $summary);
        $this->assertArrayHasKey('requirementsmet', $summary);
        $this->assertArrayHasKey('instances', $summary);
        $this->assertArrayHasKey('modes', $summary);
        $this->assertArrayHasKey('supports', $summary);
        $this->assertArrayHasKey('canaddinstance', $summary);

        $locksummaries = cache_administration_helper::get_lock_summaries();
        $this->assertInternalType('array', $locksummaries);
        $this->assertTrue(count($locksummaries) > 0);

        $mappings = cache_administration_helper::get_default_mode_stores();
        $this->assertInternalType('array', $mappings);
        $this->assertCount(3, $mappings);
        $this->assertArrayHasKey(cache_store::MODE_APPLICATION, $mappings);
        $this->assertInternalType('array', $mappings[cache_store::MODE_APPLICATION]);
        $this->assertContains('summariesstore', $mappings[cache_store::MODE_APPLICATION]);

        $potentials = cache_administration_helper::get_definition_store_options('core', 'eventinvalidation');
        $this->assertInternalType('array', $potentials); // Currently used, suitable, default
        $this->assertCount(3, $potentials);
        $this->assertArrayHasKey('summariesstore', $potentials[0]);
        $this->assertArrayHasKey('summariesstore', $potentials[1]);
        $this->assertArrayHasKey('default_application', $potentials[1]);
    }

    /**
     * Test instantiating an add store form.
     */
    public function test_get_add_store_form() {
        $form = cache_administration_helper::get_add_store_form('file');
        $this->assertInstanceOf('moodleform', $form);

        try {
            $form = cache_administration_helper::get_add_store_form('somethingstupid');
            $this->fail('You should not be able to create an add form for a store plugin that does not exist.');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e, 'Needs to be: ' .get_class($e)." ::: ".$e->getMessage());
        }
    }

    /**
     * Test instantiating a form to edit a store instance.
     */
    public function test_get_edit_store_form() {
        $config = cache_config_writer::instance();
        $this->assertTrue($config->add_store_instance('summariesstore', 'file'));

        $form = cache_administration_helper::get_edit_store_form('file', 'summariesstore');
        $this->assertInstanceOf('moodleform', $form);

        try {
            $form = cache_administration_helper::get_edit_store_form('somethingstupid', 'moron');
            $this->fail('You should not be able to create an edit form for a store plugin that does not exist.');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $form = cache_administration_helper::get_edit_store_form('file', 'blisters');
            $this->fail('You should not be able to create an edit form for a store plugin that does not exist.');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    /**
     * Test the hash_key functionality.
     */
    public function test_hash_key() {
        global $CFG;

        $currentdebugging = $CFG->debug;

        $CFG->debug = E_ALL;

        // First with simplekeys
        $instance = cache_config_phpunittest::instance(true);
        $instance->phpunit_add_definition('phpunit/hashtest', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'hashtest',
            'simplekeys' => true
        ));
        $factory = cache_factory::instance();
        $definition = $factory->create_definition('phpunit', 'hashtest');

        $result = cache_helper::hash_key('test', $definition);
        $this->assertEquals('test-'.$definition->generate_single_key_prefix(), $result);

        try {
            cache_helper::hash_key('test/test', $definition);
            $this->fail('Invalid key was allowed, you should see this.');
        } catch (coding_exception $e) {
            $this->assertEquals('test/test', $e->debuginfo);
        }

        // Second without simple keys
        $instance->phpunit_add_definition('phpunit/hashtest2', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'hashtest2',
            'simplekeys' => false
        ));
        $definition = $factory->create_definition('phpunit', 'hashtest2');

        $result = cache_helper::hash_key('test', $definition);
        $this->assertEquals(sha1($definition->generate_single_key_prefix().'-test'), $result);

        $result = cache_helper::hash_key('test/test', $definition);
        $this->assertEquals(sha1($definition->generate_single_key_prefix().'-test/test'), $result);

        $CFG->debug = $currentdebugging;
    }
}
