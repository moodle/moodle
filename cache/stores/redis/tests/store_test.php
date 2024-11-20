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

namespace cachestore_redis;

use core_cache\definition;
use core_cache\store;
use cachestore_redis;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../../tests/fixtures/stores.php');
require_once(__DIR__.'/../lib.php');

/**
 * Redis cache test.
 *
 * If you wish to use these unit tests all you need to do is add the following definition to
 * your config.php file.
 *
 * define('TEST_CACHESTORE_REDIS_TESTSERVERS', '127.0.0.1');
 *
 * @package   cachestore_redis
 * @covers    \cachestore_redis
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class store_test extends \cachestore_tests {
    /**
     * @var cachestore_redis
     */
    protected $store;

    /**
     * Returns the MongoDB class name
     *
     * @return string
     */
    protected function get_class_name() {
        return 'cachestore_redis';
    }

    public function setUp(): void {
        if (!cachestore_redis::are_requirements_met() || !defined('TEST_CACHESTORE_REDIS_TESTSERVERS')) {
            $this->markTestSkipped('Could not test cachestore_redis. Requirements are not met.');
        }
        parent::setUp();
    }
    protected function tearDown(): void {
        parent::tearDown();

        if ($this->store instanceof cachestore_redis) {
            $this->store->purge();
        }
    }

    /**
     * Creates the required cachestore for the tests to run against Redis.
     *
     * @param array $extraconfig Extra configuration options for Redis instance, if any
     * @param bool $ttl True to use a cache definition with TTL enabled
     * @return cachestore_redis
     */
    protected function create_cachestore_redis(array $extraconfig = [], bool $ttl = false): cachestore_redis {
        if ($ttl) {
            /** @var definition $definition */
            $definition = definition::load('core/wibble', [
                'mode' => 1,
                'simplekeys' => true,
                'simpledata' => true,
                'ttl' => 10,
                'component' => 'core',
                'area' => 'wibble',
                'selectedsharingoption' => 2,
                'userinputsharingkey' => '',
                'sharingoptions' => 15,
            ]);
        } else {
            /** @var definition $definition */
            $definition = definition::load_adhoc(store::MODE_APPLICATION, 'cachestore_redis', 'phpunit_test');
        }
        $configuration = array_merge(cachestore_redis::unit_test_configuration(), $extraconfig);
        $store = new cachestore_redis('Test', $configuration);
        $store->initialise($definition);

        $this->store = $store;

        if (!$store) {
            $this->markTestSkipped();
        }

        return $store;
    }

    public function test_has(): void {
        $store = $this->create_cachestore_redis();

        $this->assertTrue($store->set('foo', 'bar'));
        $this->assertTrue($store->has('foo'));
        $this->assertFalse($store->has('bat'));
    }

    public function test_has_any(): void {
        $store = $this->create_cachestore_redis();

        $this->assertTrue($store->set('foo', 'bar'));
        $this->assertTrue($store->has_any(array('bat', 'foo')));
        $this->assertFalse($store->has_any(array('bat', 'baz')));
    }

    public function test_has_all(): void {
        $store = $this->create_cachestore_redis();

        $this->assertTrue($store->set('foo', 'bar'));
        $this->assertTrue($store->set('bat', 'baz'));
        $this->assertTrue($store->has_all(array('foo', 'bat')));
        $this->assertFalse($store->has_all(array('foo', 'bat', 'this')));
    }

    public function test_lock(): void {
        $store = $this->create_cachestore_redis();

        $this->assertTrue($store->acquire_lock('lock', '123'));
        $this->assertTrue($store->check_lock_state('lock', '123'));
        $this->assertFalse($store->check_lock_state('lock', '321'));
        $this->assertNull($store->check_lock_state('notalock', '123'));
        $this->assertFalse($store->release_lock('lock', '321'));
        $this->assertTrue($store->release_lock('lock', '123'));
    }

    /**
     * Checks the timeout features of locking.
     */
    public function test_lock_timeouts(): void {
        $store = $this->create_cachestore_redis(['lockwait' => 2, 'locktimeout' => 4]);

        // User 123 acquires lock.
        $this->assertTrue($store->acquire_lock('lock', '123'));
        $this->assertTrue($store->check_lock_state('lock', '123'));

        // User 456 tries to acquire lock - should fail after about 2 seconds.
        $before = microtime(true);
        $this->assertFalse($store->acquire_lock('lock', '456'));
        $after = microtime(true);
        $this->assertEqualsWithDelta(2, $after - $before, 0.5);

        // Wait another 2 seconds and then it should be able to get the lock because of timeout.
        sleep(2);
        $this->assertTrue($store->acquire_lock('lock', '456'));
        $this->assertTrue($store->check_lock_state('lock', '456'));

        // The first user doesn't have the lock any more.
        $this->assertFalse($store->check_lock_state('lock', '123'));

        // Releasing the lock from the first user does nothing.
        $this->assertFalse($store->release_lock('lock', '123'));
        $this->assertTrue($store->check_lock_state('lock', '456'));

        $this->assertTrue($store->release_lock('lock', '456'));
    }

    /**
     * Tests the shutdown function that is supposed to free any remaining locks.
     */
    public function test_lock_shutdown(): void {
        $store = $this->create_cachestore_redis();
        try {
            $this->assertTrue($store->acquire_lock('a', '123'));
            $this->assertTrue($store->acquire_lock('b', '123'));
            $this->assertTrue($store->acquire_lock('c', '123'));
            $this->assertTrue($store->check_lock_state('a', '123'));
            $this->assertTrue($store->check_lock_state('b', '123'));
            $this->assertTrue($store->check_lock_state('c', '123'));
        } finally {
            $store->shutdown_release_locks();
            $this->assertDebuggingCalledCount(3);
        }
        $this->assertNull($store->check_lock_state('a', '123'));
        $this->assertNull($store->check_lock_state('b', '123'));
        $this->assertNull($store->check_lock_state('c', '123'));
    }

    /**
     * Tests the get_last_io_bytes function when not using compression (just returns unknown).
     */
    public function test_get_last_io_bytes(): void {
        $store = $this->create_cachestore_redis();

        $store->set('foo', [1, 2, 3, 4]);
        $this->assertEquals(store::IO_BYTES_NOT_SUPPORTED, $store->get_last_io_bytes());
        $store->get('foo');
        $this->assertEquals(store::IO_BYTES_NOT_SUPPORTED, $store->get_last_io_bytes());
    }

    /**
     * Tests the get_last_io_bytes byte count when using compression.
     */
    public function test_get_last_io_bytes_compressed(): void {
        $store = $this->create_cachestore_redis(['compressor' => cachestore_redis::COMPRESSOR_PHP_GZIP]);

        $alphabet = 'abcdefghijklmnopqrstuvwxyz';

        $store->set('small', $alphabet);
        $store->set('large', str_repeat($alphabet, 10));

        $store->get('small');
        // Interesting 'compression'.
        $this->assertEquals(54, $store->get_last_io_bytes());
        $store->get('large');
        // This one is actually smaller than uncompressed value!
        $this->assertEquals(57, $store->get_last_io_bytes());
        $store->get_many(['small', 'large']);
        $this->assertEquals(111, $store->get_last_io_bytes());

        $store->set('small', str_repeat($alphabet, 2));
        $this->assertEquals(56, $store->get_last_io_bytes());
        $store->set_many([
                ['key' => 'small', 'value' => $alphabet],
                ['key' => 'large', 'value' => str_repeat($alphabet, 10)]
        ]);
        $this->assertEquals(111, $store->get_last_io_bytes());
    }

    /**
     * Data provider for whether cache uses TTL or not.
     *
     * @return array Array with true and false options
     */
    public static function ttl_or_not(): array {
        return [
            [false],
            [true]
        ];
    }

    /**
     * Tests the delete_many function.
     *
     * The behaviour is different with TTL enabled so we need to test with that kind of definition
     * as well as a 'normal' one.
     *
     * @param bool $ttl True to test using a TTL definition
     * @dataProvider ttl_or_not
     */
    public function test_delete_many(bool $ttl): void {
        $store = $this->create_cachestore_redis([], $ttl);

        // Check it works to delete selected items.
        $store->set('foo', 'frog');
        $store->set('bar', 'amphibian');
        $store->set('hmm', 'undead');
        $this->store->delete_many(['foo', 'bar']);
        $this->assertFalse($store->get('foo'));
        $this->assertFalse($store->get('bar'));
        $this->assertEquals('undead', $store->get('hmm'));

        // If called with no keys it should do nothing.
        $store->delete_many([]);
        $this->assertEquals('undead', $store->get('hmm'));
    }

}
