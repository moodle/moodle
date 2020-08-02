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
 * PHPunit tests for the cache API and in particular the core_cache\administration_helper
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_cache_administration_helper_testcase extends advanced_testcase {

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

        $storesummaries = core_cache\administration_helper::get_store_instance_summaries();
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

        // Find the number of mappings to sessionstore.
        $mappingcount = count(array_filter($config->get_definitions(), function($element) {
            return $element['mode'] === cache_store::MODE_APPLICATION;
        }));
        $this->assertEquals($mappingcount, $summary['mappings']);

        $definitionsummaries = core_cache\administration_helper::get_definition_summaries();
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

        $pluginsummaries = core_cache\administration_helper::get_store_plugin_summaries();
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

        $locksummaries = core_cache\administration_helper::get_lock_summaries();
        $this->assertInternalType('array', $locksummaries);
        $this->assertTrue(count($locksummaries) > 0);

        $mappings = core_cache\administration_helper::get_default_mode_stores();
        $this->assertInternalType('array', $mappings);
        $this->assertCount(3, $mappings);
        $this->assertArrayHasKey(cache_store::MODE_APPLICATION, $mappings);
        $this->assertInternalType('array', $mappings[cache_store::MODE_APPLICATION]);
        $this->assertContains('summariesstore', $mappings[cache_store::MODE_APPLICATION]);

        $potentials = core_cache\administration_helper::get_definition_store_options('core', 'eventinvalidation');
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
        $form = cache_factory::get_administration_display_helper()->get_add_store_form('file');
        $this->assertInstanceOf('moodleform', $form);

        try {
            $form = cache_factory::get_administration_display_helper()->get_add_store_form('somethingstupid');
            $this->fail('You should not be able to create an add form for a store plugin that does not exist.');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e, 'Needs to be: ' .get_class($e)." ::: ".$e->getMessage());
        }
    }

    /**
     * Test instantiating a form to edit a store instance.
     */
    public function test_get_edit_store_form() {
        // Always instantiate a new core display helper here.
        $administrationhelper = new core_cache\local\administration_display_helper;
        $config = cache_config_writer::instance();
        $this->assertTrue($config->add_store_instance('test_get_edit_store_form', 'file'));

        $form = $administrationhelper->get_edit_store_form('file', 'test_get_edit_store_form');
        $this->assertInstanceOf('moodleform', $form);

        try {
            $form = $administrationhelper->get_edit_store_form('somethingstupid', 'moron');
            $this->fail('You should not be able to create an edit form for a store plugin that does not exist.');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $form = $administrationhelper->get_edit_store_form('file', 'blisters');
            $this->fail('You should not be able to create an edit form for a store plugin that does not exist.');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    /**
     * Test the hash_key functionality.
     */
    public function test_hash_key() {
        $this->resetAfterTest();
        set_debugging(DEBUG_ALL);

        // First with simplekeys
        $instance = cache_config_testing::instance(true);
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
    }
}
