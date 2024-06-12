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

/**
 * TTL support test for Redis cache.
 *
 * If you wish to use these unit tests all you need to do is add the following definition to
 * your config.php file.
 *
 * define('TEST_CACHESTORE_REDIS_TESTSERVERS', '127.0.0.1');
 *
 * @package cachestore_redis
 * @copyright 2021 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \cachestore_redis
 */
final class ttl_test extends \advanced_testcase {
    /** @var \cachestore_redis|null Cache store  */
    protected $store = null;

    public function setUp(): void {
        // Make sure cachestore_redis is available.
        require_once(__DIR__ . '/../lib.php');
        if (!\cachestore_redis::are_requirements_met() || !defined('TEST_CACHESTORE_REDIS_TESTSERVERS')) {
            $this->markTestSkipped('Could not test cachestore_redis. Requirements are not met.');
        }

        // Set up a Redis store with a fake definition that has TTL set to 10 seconds.
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
        $this->store = new \cachestore_redis('Test', \cachestore_redis::unit_test_configuration());
        $this->store->initialise($definition);

        parent::setUp();
    }

    protected function tearDown(): void {
        parent::tearDown();

        if ($this->store instanceof \cachestore_redis) {
            $this->store->purge();
        }
    }

    /**
     * Test calling set_many with an empty array
     *
     * Trivial test to ensure we don't trigger an ArgumentCountError when calling zAdd with invalid parameters
     */
    public function test_set_many_empty(): void {
        $this->assertEquals(0, $this->store->set_many([]));
    }

    /**
     * Tests expiring data.
     */
    public function test_expire_ttl(): void {
        $this->resetAfterTest();

        // Set some data at time 100.
        \cachestore_redis::set_phpunit_time(100);
        $this->store->set('a', 1);
        $this->store->set('b', 2);
        $this->store->set_many([['key' => 'c', 'value' => 3], ['key' => 'd', 'value' => 4],
                ['key' => 'e', 'value' => 5], ['key' => 'f', 'value' => 6],
                ['key' => 'g', 'value' => 7], ['key' => 'h', 'value' => 8]]);

        // Set some other data at time 110, including some of the existing values. Whether the
        // value changes or not, its TTL should update.
        \cachestore_redis::set_phpunit_time(110);
        $this->store->set('b', 2);
        $this->store->set_many([['key' => 'c', 'value' => 99], ['key' => 'd', 'value' => 4]]);

        // Check all the data is still set.
        $this->assertEqualsCanonicalizing(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'],
                $this->store->find_all());

        // Delete some data (to check deletion doesn't confuse expiry).
        $this->store->delete('f');
        $this->store->delete_many(['g', 'h']);

        // Set time to 115 and expire data.
        \cachestore_redis::set_phpunit_time(115);
        $info = $this->store->expire_ttl();

        // We are expecting keys a and e to be deleted.
        $this->assertEquals(2, $info['keys']);
        $this->assertEquals(1, $info['batches']);

        // Check the keys are as expected.
        $this->assertEqualsCanonicalizing(['b', 'c', 'd'], $this->store->find_all());

        // Might as well check the values of the surviving keys.
        $this->assertEquals(2, $this->store->get('b'));
        $this->assertEquals(99, $this->store->get('c'));
        $this->assertEquals(4, $this->store->get('d'));
    }
}
