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
 * Memcache unit tests.
 *
 * If you wish to use these unit tests all you need to do is add the following definition to
 * your config.php file.
 *
 * define('TEST_CACHESTORE_MEMCACHE_TESTSERVERS', '127.0.0.1:11211');
 *
 * @package    cachestore_memcache
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include the necessary evils.
global $CFG;
require_once($CFG->dirroot.'/cache/tests/fixtures/stores.php');
require_once($CFG->dirroot.'/cache/stores/memcache/lib.php');

/**
 * Memcache unit test class.
 *
 * @package    cachestore_memcache
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_memcache_test extends cachestore_tests {
    /**
     * Returns the memcache class name
     * @return string
     */
    protected function get_class_name() {
        return 'cachestore_memcache';
    }

    /**
     * Tests the valid keys to ensure they work.
     */
    public function test_valid_keys() {
        $this->resetAfterTest(true);

        $definition = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, 'cachestore_memcache', 'phpunit_test');
        $instance = cachestore_memcache::initialise_unit_test_instance($definition);

        if (!$instance) { // Something prevented memcache store to be inited (extension, TEST_CACHESTORE_MEMCACHE_TESTSERVERS...).
            $this->markTestSkipped();
        }

        $keys = array(
            // Alphanumeric.
            'abc', 'ABC', '123', 'aB1', '1aB',
            // Hyphens.
            'a-1', '1-a', '-a1', 'a1-',
            // Underscores.
            'a_1', '1_a', '_a1', 'a1_'
        );

        // Set some keys.
        foreach ($keys as $key) {
            $this->assertTrue($instance->set($key, $key), "Failed to set key `$key`");
        }

        // Get some keys.
        foreach ($keys as $key) {
            $this->assertEquals($key, $instance->get($key), "Failed to get key `$key`");
        }

        // Try get many.
        $values = $instance->get_many($keys);
        foreach ($values as $key => $value) {
            $this->assertEquals($key, $value);
        }

        // Reset a key.
        $this->assertTrue($instance->set($keys[0], 'New'), "Failed to reset key `$key`");
        $this->assertEquals('New', $instance->get($keys[0]), "Failed to get reset key `$key`");

        // Delete and check that we can't retrieve.
        foreach ($keys as $key) {
            $this->assertTrue($instance->delete($key), "Failed to delete key `$key`");
            $this->assertFalse($instance->get($key), "Retrieved deleted key `$key`");
        }

        // Try set many, and check that count is correct.
        $many = array();
        foreach ($keys as $key) {
            $many[] = array('key' => $key, 'value' => $key);
        }
        $returncount = $instance->set_many($many);
        $this->assertEquals(count($many), $returncount, 'Set many count didn\'t match');

        // Check keys retrieved with get_many.
        $values = $instance->get_many($keys);
        foreach ($keys as $key) {
            $this->assertTrue(isset($values[$key]), "Failed to get_many key `$key`");
            $this->assertEquals($key, $values[$key], "Failed to match get_many key `$key`");
        }

        // Delete many, make sure count matches.
        $returncount = $instance->delete_many($keys);
        $this->assertEquals(count($many), $returncount, 'Delete many count didn\'t match');

        // Check that each key was deleted.
        foreach ($keys as $key) {
            $this->assertFalse($instance->get($key), "Retrieved many deleted key `$key`");
        }

        // Set the keys again.
        $returncount = $instance->set_many($many);
        $this->assertEquals(count($many), $returncount, 'Set many count didn\'t match');

        // Purge.
        $this->assertTrue($instance->purge(), 'Failure to purge');

        // Delete and check that we can't retrieve.
        foreach ($keys as $key) {
            $this->assertFalse($instance->get($key), "Retrieved purged key `$key`");
        }
    }

    /**
     * Tests the clustering feature.
     */
    public function test_clustered() {
        $this->resetAfterTest(true);

        if (!defined('TEST_CACHESTORE_MEMCACHE_TESTSERVERS')) {
            $this->markTestSkipped();
        }

        $testservers = explode("\n", trim(TEST_CACHESTORE_MEMCACHE_TESTSERVERS));

        if (count($testservers) < 2) {
            $this->markTestSkipped();
        }

        // User the first server as our primary.
        set_config('testservers', $testservers[0], 'cachestore_memcache');
        set_config('testsetservers', TEST_CACHESTORE_MEMCACHE_TESTSERVERS, 'cachestore_memcache');
        set_config('testclustered', true, 'cachestore_memcache');

        // First and instance that we can use to test the second server.
        $definition = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, 'cachestore_memcache', 'phpunit_test');
        $instance = cachestore_memcache::initialise_test_instance($definition);

        if (!$instance) {
            $this->markTestSkipped();
        }

        // Now we are going to setup a connection to each independent server.
        set_config('testclustered', false, 'cachestore_memcache');
        set_config('testsetservers', '', 'cachestore_memcache');
        $checkinstances = array();
        foreach ($testservers as $testserver) {
            set_config('testservers', $testserver, 'cachestore_memcache');
            $checkinstance = cachestore_memcache::initialise_test_instance($definition);
            if (!$checkinstance) {
                $this->markTestSkipped();
            }
            $checkinstances[] = $checkinstance;
        }

        $keys = array(
            // Alphanumeric.
            'abc', 'ABC', '123', 'aB1', '1aB',
            // Hyphens.
            'a-1', '1-a', '-a1', 'a1-',
            // Underscores.
            'a_1', '1_a', '_a1', 'a1_'
        );

        // Set each key.
        foreach ($keys as $key) {
            $this->assertTrue($instance->set($key, $key), "Failed to set key `$key`");
        }

        // Check each key.
        foreach ($keys as $key) {
            $this->assertEquals($key, $instance->get($key), "Failed to get key `$key`");
            foreach ($checkinstances as $id => $checkinstance) {
                $this->assertEquals($key, $checkinstance->get($key), "Failed to get key `$key` from server $id");
            }
        }

        // Reset a key.
        $this->assertTrue($instance->set($keys[0], 'New'), "Failed to reset key `$key`");
        $this->assertEquals('New', $instance->get($keys[0]), "Failed to get reset key `$key`");
        foreach ($checkinstances as $id => $checkinstance) {
            $this->assertEquals('New', $checkinstance->get($keys[0]), "Failed to get reset key `$key` from server $id");
        }

        // Delete and check that we can't retrieve.
        foreach ($keys as $key) {
            $this->assertTrue($instance->delete($key), "Failed to delete key `$key`");
            $this->assertFalse($instance->get($key), "Retrieved deleted key `$key`");
            foreach ($checkinstances as $id => $checkinstance) {
                $this->assertFalse($checkinstance->get($key), "Retrieved deleted key `$key` from server $id");
            }
        }

        // Try set many, and check that count is correct.
        $many = array();
        foreach ($keys as $key) {
            $many[] = array('key' => $key, 'value' => $key);
        }
        $returncount = $instance->set_many($many);
        $this->assertEquals(count($many), $returncount, 'Set many count didn\'t match');

        // Check keys retrieved with get_many.
        $values = $instance->get_many($keys);
        foreach ($keys as $key) {
            $this->assertTrue(isset($values[$key]), "Failed to get_many key `$key`");
            $this->assertEquals($key, $values[$key], "Failed to match get_many key `$key`");
        }
        foreach ($checkinstances as $id => $checkinstance) {
            $values = $checkinstance->get_many($keys);
            foreach ($keys as $key) {
                $this->assertTrue(isset($values[$key]), "Failed to get_many key `$key` from server $id");
                $this->assertEquals($key, $values[$key], "Failed to get_many key `$key` from server $id");
            }
        }

        // Delete many, make sure count matches.
        $returncount = $instance->delete_many($keys);
        $this->assertEquals(count($many), $returncount, 'Delete many count didn\'t match');

        // Check that each key was deleted.
        foreach ($keys as $key) {
            $this->assertFalse($instance->get($key), "Retrieved many deleted key `$key`");
            foreach ($checkinstances as $id => $checkinstance) {
                $this->assertFalse($checkinstance->get($key), "Retrieved many deleted key `$key` from server $id");
            }
        }

        // Set the keys again.
        $returncount = $instance->set_many($many);
        $this->assertEquals(count($many), $returncount, 'Set many count didn\'t match');

        // Purge.
        $this->assertTrue($instance->purge(), 'Failure to purge');

        // Delete and check that we can't retrieve.
        foreach ($keys as $key) {
            $this->assertFalse($instance->get($key), "Retrieved purged key `$key`");
            foreach ($checkinstances as $id => $checkinstance) {
                $this->assertFalse($checkinstance->get($key), "Retrieved purged key `$key` from server 2");
            }
        }
    }

    /**
     * Test our checks for encoding.
     */
    public function test_require_encoding() {
        $this->assertTrue(cachestore_memcache::require_encoding('dev'));
        $this->assertTrue(cachestore_memcache::require_encoding('1.0'));
        $this->assertTrue(cachestore_memcache::require_encoding('1.0.0'));
        $this->assertTrue(cachestore_memcache::require_encoding('2.0'));
        $this->assertTrue(cachestore_memcache::require_encoding('2.0.8'));
        $this->assertTrue(cachestore_memcache::require_encoding('2.2.8'));
        $this->assertTrue(cachestore_memcache::require_encoding('3.0'));
        $this->assertTrue(cachestore_memcache::require_encoding('3.0-dev'));
        $this->assertTrue(cachestore_memcache::require_encoding('3.0.0'));
        $this->assertTrue(cachestore_memcache::require_encoding('3.0.1'));
        $this->assertTrue(cachestore_memcache::require_encoding('3.0.2-dev'));
        $this->assertTrue(cachestore_memcache::require_encoding('3.0.2'));
        $this->assertTrue(cachestore_memcache::require_encoding('3.0.3-dev'));
        $this->assertFalse(cachestore_memcache::require_encoding('3.0.3'));
        $this->assertFalse(cachestore_memcache::require_encoding('3.0.4'));
        $this->assertFalse(cachestore_memcache::require_encoding('3.0.4-dev'));
        $this->assertFalse(cachestore_memcache::require_encoding('3.0.8'));
        $this->assertFalse(cachestore_memcache::require_encoding('3.1.0'));
        $this->assertFalse(cachestore_memcache::require_encoding('3.1.2'));

    }
}
