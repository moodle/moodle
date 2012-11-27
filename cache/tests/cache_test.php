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
 * PHPunit tests for the cache API
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
 * PHPunit tests for the cache API
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_phpunit_tests extends advanced_testcase {

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
     * Tests cache configuration
     */
    public function test_cache_config() {
        $instance = cache_config::instance();
        $this->assertInstanceOf('cache_config_phpunittest', $instance);

        $this->assertTrue(cache_config_phpunittest::config_file_exists());

        $stores = $instance->get_all_stores();
        $this->assertCount(3, $stores);
        foreach ($stores as $name => $store) {
            // Check its an array.
            $this->assertInternalType('array', $store);
            // Check the name is the key.
            $this->assertEquals($name, $store['name']);
            // Check that it has been declared default.
            $this->assertTrue($store['default']);
            // Required attributes = name + plugin + configuration + modes + features.
            $this->assertArrayHasKey('name', $store);
            $this->assertArrayHasKey('plugin', $store);
            $this->assertArrayHasKey('configuration', $store);
            $this->assertArrayHasKey('modes', $store);
            $this->assertArrayHasKey('features', $store);
        }

        $modemappings = $instance->get_mode_mappings();
        $this->assertCount(3, $modemappings);
        $modes = array(
            cache_store::MODE_APPLICATION => false,
            cache_store::MODE_SESSION => false,
            cache_store::MODE_REQUEST => false,
        );
        foreach ($modemappings as $mapping) {
            // We expect 3 properties.
            $this->assertCount(3, $mapping);
            // Required attributes = mode + store.
            $this->assertArrayHasKey('mode', $mapping);
            $this->assertArrayHasKey('store', $mapping);
            // Record the mode.
            $modes[$mapping['mode']] = true;
        }

        // Must have the default 3 modes and no more.
        $this->assertCount(3, $mapping);
        foreach ($modes as $mode) {
            $this->assertTrue($mode);
        }

        $definitions = $instance->get_definitions();
        // The event invalidation definition is required for the cache API and must be there.
        $this->assertArrayHasKey('core/eventinvalidation', $definitions);

        $definitionmappings = $instance->get_definition_mappings();
        foreach ($definitionmappings as $mapping) {
            // Required attributes = definition + store.
            $this->assertArrayHasKey('definition', $mapping);
            $this->assertArrayHasKey('store', $mapping);
        }
    }

    /**
     * Tests the default application cache
     */
    public function test_default_application_cache() {
        $cache = cache::make_from_params(cache_store::MODE_APPLICATION, 'phpunit', 'applicationtest');
        $this->assertInstanceOf('cache_application', $cache);
        $this->run_on_cache($cache);
    }

    /**
     * Tests the default session cache
     */
    public function test_default_session_cache() {
        $cache = cache::make_from_params(cache_store::MODE_SESSION, 'phpunit', 'applicationtest');
        $this->assertInstanceOf('cache_session', $cache);
        $this->run_on_cache($cache);
    }

    /**
     * Tests the default request cache
     */
    public function test_default_request_cache() {
        $cache = cache::make_from_params(cache_store::MODE_REQUEST, 'phpunit', 'applicationtest');
        $this->assertInstanceOf('cache_request', $cache);
        $this->run_on_cache($cache);
    }

    /**
     * Tests using a cache system when there are no stores available (who knows what the admin did to achieve this).
     */
    public function test_on_cache_without_store() {
        $instance = cache_config_phpunittest::instance(true);
        $instance->phpunit_add_definition('phpunit/nostoretest1', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'nostoretest1',
        ));
        $instance->phpunit_add_definition('phpunit/nostoretest2', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'nostoretest2',
            'persistent' => true
        ));
        $instance->phpunit_remove_stores();

        $cache = cache::make('phpunit', 'nostoretest1');
        $this->run_on_cache($cache);

        $cache = cache::make('phpunit', 'nostoretest2');
        $this->run_on_cache($cache);
    }

    /**
     * Runs a standard series of access and use tests on a cache instance.
     *
     * This function is great because we can use it to ensure all of the loaders perform exactly the same way.
     *
     * @param cache_loader $cache
     */
    protected function run_on_cache(cache_loader $cache) {
        $key = 'testkey';
        $datascalar = 'test data';
        $dataarray = array('test' => 'data', 'part' => 'two');
        $dataobject = (object)$dataarray;

        $this->assertTrue($cache->purge());

        // Check all read methods.
        $this->assertFalse($cache->get($key));
        $this->assertFalse($cache->has($key));
        $result = $cache->get_many(array($key));
        $this->assertCount(1, $result);
        $this->assertFalse(reset($result));
        $this->assertFalse($cache->has_any(array($key)));
        $this->assertFalse($cache->has_all(array($key)));

        // Set the data.
        $this->assertTrue($cache->set($key, $datascalar));
        // Setting it more than once should be permitted.
        $this->assertTrue($cache->set($key, $datascalar));

        // Recheck the read methods.
        $this->assertEquals($datascalar, $cache->get($key));
        $this->assertTrue($cache->has($key));
        $result = $cache->get_many(array($key));
        $this->assertCount(1, $result);
        $this->assertEquals($datascalar, reset($result));
        $this->assertTrue($cache->has_any(array($key)));
        $this->assertTrue($cache->has_all(array($key)));

        // Delete it.
        $this->assertTrue($cache->delete($key));

        // Check its gone.
        $this->assertFalse($cache->get($key));
        $this->assertFalse($cache->has($key));

        // Test arrays.
        $this->assertTrue($cache->set($key, $dataarray));
        $this->assertEquals($dataarray, $cache->get($key));

        // Test objects.
        $this->assertTrue($cache->set($key, $dataobject));
        $this->assertEquals($dataobject, $cache->get($key));

        $specobject = new cache_phpunit_dummy_object('red', 'blue');
        $this->assertTrue($cache->set($key, $specobject));
        $result = $cache->get($key);
        $this->assertInstanceOf('cache_phpunit_dummy_object', $result);
        $this->assertEquals('red_ptc_wfc', $result->property1);
        $this->assertEquals('blue_ptc_wfc', $result->property2);

        // Test set many.
        $cache->set_many(array('key1' => 'data1', 'key2' => 'data2'));
        $this->assertEquals('data1', $cache->get('key1'));
        $this->assertEquals('data2', $cache->get('key2'));
        $this->assertTrue($cache->delete('key1'));
        $this->assertTrue($cache->delete('key2'));

        // Test delete many.
        $this->assertTrue($cache->set('key1', 'data1'));
        $this->assertTrue($cache->set('key2', 'data2'));

        $this->assertEquals('data1', $cache->get('key1'));
        $this->assertEquals('data2', $cache->get('key2'));

        $this->assertEquals(2, $cache->delete_many(array('key1', 'key2')));

        $this->assertFalse($cache->get('key1'));
        $this->assertFalse($cache->get('key2'));

        // Quick reference test.
        $obj = new stdClass;
        $obj->key = 'value';
        $ref =& $obj;
        $this->assertTrue($cache->set('obj', $obj));

        $obj->key = 'eulav';
        $var = $cache->get('obj');
        $this->assertInstanceOf('stdClass', $var);
        $this->assertEquals('value', $var->key);

        $ref->key = 'eulav';
        $var = $cache->get('obj');
        $this->assertInstanceOf('stdClass', $var);
        $this->assertEquals('value', $var->key);

        $this->assertTrue($cache->delete('obj'));

        // Deep reference test.
        $obj1 = new stdClass;
        $obj1->key = 'value';
        $obj2 = new stdClass;
        $obj2->key = 'test';
        $obj3 = new stdClass;
        $obj3->key = 'pork';
        $obj1->subobj =& $obj2;
        $obj2->subobj =& $obj3;
        $this->assertTrue($cache->set('obj', $obj1));

        $obj1->key = 'eulav';
        $obj2->key = 'tset';
        $obj3->key = 'krop';
        $var = $cache->get('obj');
        $this->assertInstanceOf('stdClass', $var);
        $this->assertEquals('value', $var->key);
        $this->assertInstanceOf('stdClass', $var->subobj);
        $this->assertEquals('test', $var->subobj->key);
        $this->assertInstanceOf('stdClass', $var->subobj->subobj);
        $this->assertEquals('pork', $var->subobj->subobj->key);
        $this->assertTrue($cache->delete('obj'));

        // Death reference test... basicaly we don't want this to die.
        $obj = new stdClass;
        $obj->key = 'value';
        $obj->self =& $obj;
        $this->assertTrue($cache->set('obj', $obj));
        $var = $cache->get('obj');
        $this->assertInstanceOf('stdClass', $var);
        $this->assertEquals('value', $var->key);

        // Reference test after retrieve.
        $obj = new stdClass;
        $obj->key = 'value';
        $this->assertTrue($cache->set('obj', $obj));

        $var1 = $cache->get('obj');
        $this->assertInstanceOf('stdClass', $var1);
        $this->assertEquals('value', $var1->key);
        $var1->key = 'eulav';
        $this->assertEquals('eulav', $var1->key);

        $var2 = $cache->get('obj');
        $this->assertInstanceOf('stdClass', $var2);
        $this->assertEquals('value', $var2->key);

        $this->assertTrue($cache->delete('obj'));
    }

    /**
     * Tests a definition using a data loader
     */
    public function test_definition_data_loader() {
        $instance = cache_config_phpunittest::instance(true);
        $instance->phpunit_add_definition('phpunit/datasourcetest', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'datasourcetest',
            'datasource' => 'cache_phpunit_dummy_datasource',
            'datasourcefile' => 'cache/tests/fixtures/lib.php'
        ));

        $cache = cache::make('phpunit', 'datasourcetest');
        $this->assertInstanceOf('cache_application', $cache);

        // Purge it to be sure.
        $this->assertTrue($cache->purge());
        // It won't be there yet.
        $this->assertFalse($cache->has('Test'));

        // It should load it ;).
        $this->assertTrue($cache->has('Test', true));

        // Purge it to be sure.
        $this->assertTrue($cache->purge());
        $this->assertEquals('Test has no value really.', $cache->get('Test'));
    }

    /**
     * Tests a definition using an overridden loader
     */
    public function test_definition_overridden_loader() {
        $instance = cache_config_phpunittest::instance(true);
        $instance->phpunit_add_definition('phpunit/overridetest', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'overridetest',
            'overrideclass' => 'cache_phpunit_dummy_overrideclass',
            'overrideclassfile' => 'cache/tests/fixtures/lib.php'
        ));
        $cache = cache::make('phpunit', 'overridetest');
        $this->assertInstanceOf('cache_phpunit_dummy_overrideclass', $cache);
        $this->assertInstanceOf('cache_application', $cache);
        // Purge it to be sure.
        $this->assertTrue($cache->purge());
        // It won't be there yet.
        $this->assertFalse($cache->has('Test'));
        // Add it.
        $this->assertTrue($cache->set('Test', 'Test has no value really.'));
        // Check its there.
        $this->assertEquals('Test has no value really.', $cache->get('Test'));
    }

    /**
     * Test a very basic definition.
     */
    public function test_definition() {
        $instance = cache_config_phpunittest::instance();
        $instance->phpunit_add_definition('phpunit/test', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'test',
        ));
        $cache = cache::make('phpunit', 'test');

        $this->assertTrue($cache->set('testkey1', 'test data 1'));
        $this->assertEquals('test data 1', $cache->get('testkey1'));
        $this->assertTrue($cache->set('testkey2', 'test data 2'));
        $this->assertEquals('test data 2', $cache->get('testkey2'));
    }

    /**
     * Test a definition using the simple keys.
     */
    public function test_definition_simplekeys() {
        $instance = cache_config_phpunittest::instance();
        $instance->phpunit_add_definition('phpunit/simplekeytest', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'simplekeytest',
            'simplekeys' => true
        ));
        $cache = cache::make('phpunit', 'simplekeytest');

        $this->assertTrue($cache->set('testkey1', 'test data 1'));
        $this->assertEquals('test data 1', $cache->get('testkey1'));
        $this->assertTrue($cache->set('testkey2', 'test data 2'));
        $this->assertEquals('test data 2', $cache->get('testkey2'));

        $cache->purge();

        $this->assertTrue($cache->set('1', 'test data 1'));
        $this->assertEquals('test data 1', $cache->get('1'));
        $this->assertTrue($cache->set('2', 'test data 2'));
        $this->assertEquals('test data 2', $cache->get('2'));
    }

    public function test_definition_ttl() {
        $instance = cache_config_phpunittest::instance(true);
        $instance->phpunit_add_definition('phpunit/ttltest', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'ttltest',
            'ttl' => -10
        ));
        $cache = cache::make('phpunit', 'ttltest');
        $this->assertInstanceOf('cache_application', $cache);

        // Purge it to be sure.
        $this->assertTrue($cache->purge());
        // It won't be there yet.
        $this->assertFalse($cache->has('Test'));
        // Set it now.
        $this->assertTrue($cache->set('Test', 'Test'));
        // Check its not there.
        $this->assertFalse($cache->has('Test'));
        // Double check by trying to get it.
        $this->assertFalse($cache->get('Test'));
    }

    /**
     * Tests manual locking operations on an application cache
     */
    public function test_application_manual_locking() {
        $instance = cache_config_phpunittest::instance();
        $instance->phpunit_add_definition('phpunit/lockingtest', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'lockingtest'
        ));
        $cache1 = cache::make('phpunit', 'lockingtest');
        $cache2 = clone($cache1);

        $this->assertTrue($cache1->set('testkey', 'test data'));
        $this->assertTrue($cache2->set('testkey', 'test data'));

        $this->assertTrue($cache1->acquire_lock('testkey'));
        $this->assertFalse($cache2->acquire_lock('testkey'));

        $this->assertTrue($cache1->check_lock_state('testkey'));
        $this->assertFalse($cache2->check_lock_state('testkey'));

        $this->assertTrue($cache1->release_lock('testkey'));
        $this->assertFalse($cache2->release_lock('testkey'));

        $this->assertTrue($cache1->set('testkey', 'test data'));
        $this->assertTrue($cache2->set('testkey', 'test data'));
    }

    /**
     * Tests application cache event invalidation
     */
    public function test_application_event_invalidation() {
        $instance = cache_config_phpunittest::instance();
        $instance->phpunit_add_definition('phpunit/eventinvalidationtest', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'eventinvalidationtest',
            'invalidationevents' => array(
                'crazyevent'
            )
        ));
        $cache = cache::make('phpunit', 'eventinvalidationtest');

        $this->assertTrue($cache->set('testkey1', 'test data 1'));
        $this->assertEquals('test data 1', $cache->get('testkey1'));
        $this->assertTrue($cache->set('testkey2', 'test data 2'));
        $this->assertEquals('test data 2', $cache->get('testkey2'));

        // Test invalidating a single entry.
        cache_helper::invalidate_by_event('crazyevent', array('testkey1'));

        $this->assertFalse($cache->get('testkey1'));
        $this->assertEquals('test data 2', $cache->get('testkey2'));

        $this->assertTrue($cache->set('testkey1', 'test data 1'));

        // Test invalidating both entries.
        cache_helper::invalidate_by_event('crazyevent', array('testkey1', 'testkey2'));

        $this->assertFalse($cache->get('testkey1'));
        $this->assertFalse($cache->get('testkey2'));
    }

    /**
     * Tests application cache definition invalidation
     */
    public function test_application_definition_invalidation() {
        $instance = cache_config_phpunittest::instance();
        $instance->phpunit_add_definition('phpunit/definitioninvalidation', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'definitioninvalidation'
        ));
        $cache = cache::make('phpunit', 'definitioninvalidation');
        $this->assertTrue($cache->set('testkey1', 'test data 1'));
        $this->assertEquals('test data 1', $cache->get('testkey1'));
        $this->assertTrue($cache->set('testkey2', 'test data 2'));
        $this->assertEquals('test data 2', $cache->get('testkey2'));

        cache_helper::invalidate_by_definition('phpunit', 'definitioninvalidation', array(), 'testkey1');

        $this->assertFalse($cache->get('testkey1'));
        $this->assertEquals('test data 2', $cache->get('testkey2'));

        $this->assertTrue($cache->set('testkey1', 'test data 1'));

        cache_helper::invalidate_by_definition('phpunit', 'definitioninvalidation', array(), array('testkey1'));

        $this->assertFalse($cache->get('testkey1'));
        $this->assertEquals('test data 2', $cache->get('testkey2'));

        $this->assertTrue($cache->set('testkey1', 'test data 1'));

        cache_helper::invalidate_by_definition('phpunit', 'definitioninvalidation', array(), array('testkey1', 'testkey2'));

        $this->assertFalse($cache->get('testkey1'));
        $this->assertFalse($cache->get('testkey2'));
    }

    /**
     * Tests application cache event invalidation over a distributed setup.
     */
    public function test_distributed_application_event_invalidation() {
        global $CFG;
        // This is going to be an intense wee test.
        // We need to add data the to cache, invalidate it by event, manually force it back without MUC knowing to simulate a
        // disconnected/distributed setup (think load balanced server using local cache), instantiate the cache again and finally
        // check that it is not picked up.
        $instance = cache_config_phpunittest::instance();
        $instance->phpunit_add_definition('phpunit/eventinvalidationtest', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'eventinvalidationtest',
            'invalidationevents' => array(
                'crazyevent'
            )
        ));
        $cache = cache::make('phpunit', 'eventinvalidationtest');
        $this->assertTrue($cache->set('testkey1', 'test data 1'));
        $this->assertEquals('test data 1', $cache->get('testkey1'));

        cache_helper::invalidate_by_event('crazyevent', array('testkey1'));

        $this->assertFalse($cache->get('testkey1'));

        // OK data added, data invalidated, and invalidation time has been set.
        // Now we need to manually add back the data and adjust the invalidation time.
        $timefile = $CFG->dataroot.'/cache/cachestore_file/default_application/phpunit_eventinvalidationtest/a65/a65b1dc524cf6e03c1795197c84d5231eb229b86.cache';
        $timecont = serialize(cache::now() - 60); // Back 60sec in the past to force it to re-invalidate.
        make_writable_directory(dirname($timefile));
        file_put_contents($timefile, $timecont);
        $this->assertTrue(file_exists($timefile));

        $datafile = $CFG->dataroot.'/cache/cachestore_file/default_application/phpunit_eventinvalidationtest/626/626e9c7a45febd98f064c2b383de8d9d4ebbde7b.cache';
        $datacont = serialize("test data 1");
        make_writable_directory(dirname($datafile));
        file_put_contents($datafile, $datacont);
        $this->assertTrue(file_exists($datafile));

        // Test 1: Rebuild without the event and test its there.
        cache_factory::reset();
        $instance = cache_config_phpunittest::instance();
        $instance->phpunit_add_definition('phpunit/eventinvalidationtest', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'eventinvalidationtest',
        ));
        $cache = cache::make('phpunit', 'eventinvalidationtest');
        $this->assertEquals('test data 1', $cache->get('testkey1'));

        // Test 2: Rebuild and test the invalidation of the event via the invalidation cache.
        cache_factory::reset();
        $instance = cache_config_phpunittest::instance();
        $instance->phpunit_add_definition('phpunit/eventinvalidationtest', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'eventinvalidationtest',
            'invalidationevents' => array(
                'crazyevent'
            )
        ));
        $cache = cache::make('phpunit', 'eventinvalidationtest');
        $this->assertFalse($cache->get('testkey1'));
    }

    /**
     * Tests application cache event purge
     */
    public function test_application_event_purge() {
        $instance = cache_config_phpunittest::instance();
        $instance->phpunit_add_definition('phpunit/eventpurgetest', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'eventpurgetest',
            'invalidationevents' => array(
                'crazyevent'
            )
        ));
        $cache = cache::make('phpunit', 'eventpurgetest');

        $this->assertTrue($cache->set('testkey1', 'test data 1'));
        $this->assertEquals('test data 1', $cache->get('testkey1'));
        $this->assertTrue($cache->set('testkey2', 'test data 2'));
        $this->assertEquals('test data 2', $cache->get('testkey2'));

        // Purge the event.
        cache_helper::purge_by_event('crazyevent');

        // Check things have been removed.
        $this->assertFalse($cache->get('testkey1'));
        $this->assertFalse($cache->get('testkey2'));
    }

    /**
     * Tests application cache definition purge
     */
    public function test_application_definition_purge() {
        $instance = cache_config_phpunittest::instance();
        $instance->phpunit_add_definition('phpunit/definitionpurgetest', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'definitionpurgetest',
            'invalidationevents' => array(
                'crazyevent'
            )
        ));
        $cache = cache::make('phpunit', 'definitionpurgetest');

        $this->assertTrue($cache->set('testkey1', 'test data 1'));
        $this->assertEquals('test data 1', $cache->get('testkey1'));
        $this->assertTrue($cache->set('testkey2', 'test data 2'));
        $this->assertEquals('test data 2', $cache->get('testkey2'));

        // Purge the event.
        cache_helper::purge_by_definition('phpunit', 'definitionpurgetest');

        // Check things have been removed.
        $this->assertFalse($cache->get('testkey1'));
        $this->assertFalse($cache->get('testkey2'));
    }

    /**
     * Test the use of an alt path.
     * If we can generate a config instance we are done :)
     */
    public function test_alt_cache_path() {
        global $CFG;
        $this->resetAfterTest();
        $CFG->altcacheconfigpath = $CFG->dataroot.'/cache/altcacheconfigpath';
        $instance = cache_config_phpunittest::instance();
        $this->assertInstanceOf('cache_config', $instance);
    }

    /**
     * Test disabling the cache stores.
     */
    public function test_disable_stores() {
        $instance = cache_config_phpunittest::instance();
        $instance->phpunit_add_definition('phpunit/disabletest', array(
            'mode' => cache_store::MODE_APPLICATION,
            'component' => 'phpunit',
            'area' => 'disabletest'
        ));
        $cache = cache::make('phpunit', 'disabletest');
        $this->assertInstanceOf('cache_phpunit_application', $cache);
        $this->assertEquals('cachestore_file', $cache->phpunit_get_store_class());

        $this->assertFalse($cache->get('test'));
        $this->assertTrue($cache->set('test', 'test'));
        $this->assertEquals('test', $cache->get('test'));

        cache_factory::disable_stores();

        $cache = cache::make('phpunit', 'disabletest');
        $this->assertInstanceOf('cache_phpunit_application', $cache);
        $this->assertEquals('cachestore_dummy', $cache->phpunit_get_store_class());

        $this->assertFalse($cache->get('test'));
        $this->assertTrue($cache->set('test', 'test'));
        $this->assertEquals('test', $cache->get('test'));
    }

    /**
     * Test disabling the cache.
     */
    public function test_disable() {
        global $CFG;

        $configfile = $CFG->dataroot.'/muc/config.php';

        // That's right, we're deleting the config file.
        $this->assertTrue(@unlink($configfile));

        // Disable the cache
        cache_phpunit_factory::phpunit_disable();

        // Check we get the expected disabled factory.
        $factory = cache_factory::instance();
        $this->assertInstanceOf('cache_factory_disabled', $factory);

        // Check we get the expected disabled config.
        $config = $factory->create_config_instance();
        $this->assertInstanceOf('cache_config_disabled', $config);

        // Check we get the expected disabled caches.
        $cache = cache::make('phpunit', 'disable');
        $this->assertInstanceOf('cache_disabled', $cache);

        $cache = cache::make_from_params(cache_store::MODE_APPLICATION, 'phpunit', 'disable');
        $this->assertInstanceOf('cache_disabled', $cache);

        $this->assertFalse(file_exists($configfile));

        $this->assertFalse($cache->get('test'));
        $this->assertFalse($cache->set('test', 'test'));
        $this->assertFalse($cache->delete('test'));
        $this->assertTrue($cache->purge());

        cache_factory::reset();

        $factory = cache_factory::instance(true);
        $config = $factory->create_config_instance();
        $this->assertEquals('cache_config_phpunittest', get_class($config));
    }
}