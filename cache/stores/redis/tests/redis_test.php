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
 * Redis cache test.
 *
 * If you wish to use these unit tests all you need to do is add the following definition to
 * your config.php file.
 *
 * define('TEST_CACHESTORE_REDIS_TESTSERVERS', '127.0.0.1');
 *
 * @package   cachestore_redis
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../../tests/fixtures/stores.php');
require_once(__DIR__.'/../lib.php');

/**
 * Redis cache test.
 *
 * @package   cachestore_redis
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_redis_test extends cachestore_tests {
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

    public function setUp() {
        if (!cachestore_redis::are_requirements_met() || !defined('TEST_CACHESTORE_REDIS_TESTSERVERS')) {
            $this->markTestSkipped('Could not test cachestore_redis. Requirements are not met.');
        }
        parent::setUp();
    }
    protected function tearDown() {
        parent::tearDown();

        if ($this->store instanceof cachestore_redis) {
            $this->store->purge();
        }
    }

    /**
     * Creates the required cachestore for the tests to run against Redis.
     *
     * @return cachestore_redis
     */
    protected function create_cachestore_redis() {
        /** @var cache_definition $definition */
        $definition = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, 'cachestore_redis', 'phpunit_test');
        $store = new cachestore_redis('Test', cachestore_redis::unit_test_configuration());
        $store->initialise($definition);

        $this->store = $store;

        if (!$store) {
            $this->markTestSkipped();
        }

        return $store;
    }

    public function test_has() {
        $store = $this->create_cachestore_redis();

        $this->assertTrue($store->set('foo', 'bar'));
        $this->assertTrue($store->has('foo'));
        $this->assertFalse($store->has('bat'));
    }

    public function test_has_any() {
        $store = $this->create_cachestore_redis();

        $this->assertTrue($store->set('foo', 'bar'));
        $this->assertTrue($store->has_any(array('bat', 'foo')));
        $this->assertFalse($store->has_any(array('bat', 'baz')));
    }

    public function test_has_all() {
        $store = $this->create_cachestore_redis();

        $this->assertTrue($store->set('foo', 'bar'));
        $this->assertTrue($store->set('bat', 'baz'));
        $this->assertTrue($store->has_all(array('foo', 'bat')));
        $this->assertFalse($store->has_all(array('foo', 'bat', 'this')));
    }

    public function test_lock() {
        $store = $this->create_cachestore_redis();

        $this->assertTrue($store->acquire_lock('lock', '123'));
        $this->assertTrue($store->check_lock_state('lock', '123'));
        $this->assertFalse($store->check_lock_state('lock', '321'));
        $this->assertNull($store->check_lock_state('notalock', '123'));
        $this->assertFalse($store->release_lock('lock', '321'));
        $this->assertTrue($store->release_lock('lock', '123'));
    }
}