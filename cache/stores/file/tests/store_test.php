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

namespace cachestore_file;

use core_cache\definition;
use core_cache\store;
use cachestore_file;

defined('MOODLE_INTERNAL') || die();

// Include the necessary evils.
global $CFG;
require_once($CFG->dirroot.'/cache/tests/fixtures/stores.php');
require_once($CFG->dirroot.'/cache/stores/file/lib.php');

/**
 * File unit test class.
 *
 * @package    cachestore_file
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \cachestore_file
 */
class store_test extends \cachestore_tests {
    /**
     * Returns the file class name
     * @return string
     */
    protected function get_class_name() {
        return 'cachestore_file';
    }

    /**
     * Testing cachestore_file::get with prescan enabled and with
     * deleting the cache between the prescan and the call to get.
     *
     * The deleting of cache simulates some other process purging
     * the cache.
     */
    public function test_cache_get_with_prescan_and_purge(): void {
        global $CFG;

        $definition = definition::load_adhoc(store::MODE_REQUEST, 'cachestore_file', 'phpunit_test');
        $name = 'File test';

        $path = make_cache_directory('cachestore_file_test');
        $cache = new cachestore_file($name, array('path' => $path, 'prescan' => true));
        $cache->initialise($definition);

        $cache->set('testing', 'value');

        $path  = make_cache_directory('cachestore_file_test');
        $cache = new cachestore_file($name, array('path' => $path, 'prescan' => true));
        $cache->initialise($definition);

        // Let's pretend that some other process purged caches.
        remove_dir($CFG->cachedir.'/cachestore_file_test', true);
        make_cache_directory('cachestore_file_test');

        $cache->get('testing');
    }

    /**
     * Tests the get_last_read byte count.
     */
    public function test_get_last_io_bytes(): void {
        $definition = definition::load_adhoc(store::MODE_REQUEST, 'cachestore_file', 'phpunit_test');
        $store = new \cachestore_file('Test');
        $store->initialise($definition);

        $store->set('foo', 'bar');
        $store->set('frog', 'ribbit');
        $store->get('foo');
        // It's not 3 bytes, because the data is stored serialized.
        $this->assertEquals(10, $store->get_last_io_bytes());
        $store->get('frog');
        $this->assertEquals(13, $store->get_last_io_bytes());
        $store->get_many(['foo', 'frog']);
        $this->assertEquals(23, $store->get_last_io_bytes());

        $store->set('foo', 'goo');
        $this->assertEquals(10, $store->get_last_io_bytes());
        $store->set_many([
                ['key' => 'foo', 'value' => 'bar'],
                ['key' => 'frog', 'value' => 'jump']
        ]);
        $this->assertEquals(21, $store->get_last_io_bytes());
    }

    public function test_lock(): void {
        $store = new \cachestore_file('Test');

        $this->assertTrue($store->acquire_lock('lock', '123'));
        $this->assertTrue($store->check_lock_state('lock', '123'));
        $this->assertFalse($store->check_lock_state('lock', '321'));
        $this->assertNull($store->check_lock_state('notalock', '123'));
        $this->assertFalse($store->release_lock('lock', '321'));
        $this->assertTrue($store->release_lock('lock', '123'));
    }
}
