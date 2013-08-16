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
     * Prepare to run tests.
     */
    public function setUp() {
        if (defined('TEST_CACHESTORE_MEMCACHE_TESTSERVERS')) {
            set_config('testservers', TEST_CACHESTORE_MEMCACHE_TESTSERVERS, 'cachestore_memcache');
            $this->resetAfterTest();
        }
        parent::setUp();
    }
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
        $definition = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, 'cachestore_memcache', 'phpunit_test');
        $instance = cachestore_memcache::initialise_test_instance($definition);

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
        foreach ($keys as $key) {
            $this->assertTrue($instance->set($key, $key), "Failed to set key `$key`");
        }
        foreach ($keys as $key) {
            $this->assertEquals($key, $instance->get($key), "Failed to get key `$key`");
        }
        $values = $instance->get_many($keys);
        foreach ($values as $key => $value) {
            $this->assertEquals($key, $value);
        }
    }
}
