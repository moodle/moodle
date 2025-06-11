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
 * Unit tests for \local_redislock\lock\redis_lock_factory.
 *
 * @package   local_redislock
 * @author    Sam Chaffee
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_redislock;

use core\lock\lock_config;
use local_redislock\api\shared_redis_connection;

/**
 * PHPUnit testcase class for \local_redislock\lock\redis_lock_factory.
 *
 * @package   local_redislock
 * @author    Sam Chaffee
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class redis_lock_factory_test extends \advanced_testcase {

    public function setUp(): void {
        global $CFG;
        $this->resetAfterTest();
        if (empty($CFG->local_redislock_redis_server)) {
            $CFG->local_redislock_redis_server = '127.0.0.1';
        }
        $CFG->lock_factory = '\\local_redislock\\lock\\redis_lock_factory';
    }

    /**
     * @throws coding_exception
     */
    protected function tearDown(): void {
        shared_redis_connection::get_instance()->close();
        while (!empty(shared_redis_connection::get_instance()->get_factory_count())) {
            shared_redis_connection::get_instance()->remove_factory();
        }
    }

    /**
     * Tests acquiring locks using the Redis lock factory.
     *
     * @throws coding_exception
     */
    public function test_acquire_lock() {
        if (!$this->is_redis_available()) {
            $this->markTestSkipped('Redis server not available');
        }

        /** @var local_redislock\lock\redis_lock_factory $redislockfactory */
        $redislockfactory = lock_config::get_lock_factory('core_cron');
        $lock1 = $redislockfactory->get_lock('test', 2);
        $this->assertNotEmpty($lock1);
        $factoryclass = get_class($redislockfactory);
        switch ($factoryclass) {
            case 'local_redislock\lock\redis_lock_factory':
                $newttl = $redislockfactory->get_ttl($lock1);
                break;
            case 'core\lock\timing_wrapper_lock_factory':
                $newttl = $redislockfactory->get_real_factory()->get_ttl($lock1);
                break;
            default:
                $newttl = 1;
        }
        $this->assertEquals(-1, $newttl);

        $lock2 = $redislockfactory->get_lock('test', 1);
        $this->assertEmpty($lock2);

        $this->assertTrue($lock1->release());

        $lock3 = $redislockfactory->get_lock('another_test', 2, 1);
        $this->assertNotEmpty($lock3);
        $factoryclass = get_class($redislockfactory);
        switch ($factoryclass) {
            case 'local_redislock\lock\redis_lock_factory':
                $newttl = $redislockfactory->get_ttl($lock3);
                break;
            case 'core\lock\timing_wrapper_lock_factory':
                $newttl = $redislockfactory->get_real_factory()->get_ttl($lock3);
                break;
            default:
                $newttl = 1;
        }
        $this->assertEquals(-1, $newttl);

        // Not using TTL anymore so this should fail to acquire the lock.
        $lock4 = $redislockfactory->get_lock('another_test', 2);
        $this->assertEmpty($lock4);

        $this->assertTrue($lock3->release());

        // Now try some interesting keys.
        $key1 = "\\A\\key_with!odd:Chars$^\\A newline\n\\1\\And unicode ☀↑!";
        $key2 = "\\A\\key_with!odd:Chars$^\\A newline\n\\2\\And unicode ☀↑!";
        $lock5 = $redislockfactory->get_lock($key1, 2);
        $this->assertNotEmpty($lock5);
        $factoryclass = get_class($redislockfactory);
        switch ($factoryclass) {
            case 'local_redislock\lock\redis_lock_factory':
                $newttl = $redislockfactory->get_ttl($lock5);
                break;
            case 'core\lock\timing_wrapper_lock_factory':
                $newttl = $redislockfactory->get_real_factory()->get_ttl($lock5);
                break;
            default:
                $newttl = 1;
        }
        $this->assertEquals(-1, $newttl);

        // This key should also acquire.
        $lock6 = $redislockfactory->get_lock($key2, 2);
        $this->assertNotEmpty($lock6);
        $factoryclass = get_class($redislockfactory);
        switch ($factoryclass) {
            case 'local_redislock\lock\redis_lock_factory':
                $newttl = $redislockfactory->get_ttl($lock6);
                break;
            case 'core\lock\timing_wrapper_lock_factory':
                $newttl = $redislockfactory->get_real_factory()->get_ttl($lock6);
                break;
            default:
                $newttl = 1;
        }
        $this->assertEquals(-1, $newttl);

        // But this should not (already held).
        $lock7 = $redislockfactory->get_lock($key1, 2);
        $this->assertEmpty($lock7);

        $this->assertTrue($lock5->release());
        $this->assertTrue($lock6->release());

        // Now get lock 2 again to be sure we had released.
        // This key should also acquire.
        $lock8 = $redislockfactory->get_lock($key2, 2);
        $this->assertNotEmpty($lock8);
        $factoryclass = get_class($redislockfactory);
        switch ($factoryclass) {
            case 'local_redislock\lock\redis_lock_factory':
                $newttl = $redislockfactory->get_ttl($lock8);
                break;
            case 'core\lock\timing_wrapper_lock_factory':
                $newttl = $redislockfactory->get_real_factory()->get_ttl($lock8);
                break;
            default:
                $newttl = 1;
        }
        $this->assertEquals(-1, $newttl);

        $this->assertTrue($lock8->release());
    }

    /**
     * Tests extending a lock's TTL using Redis lock factory.
     *
     * @throws coding_exception
     */
    public function test_lock_extendttl() {
        if (!$this->is_redis_available()) {
            $this->markTestSkipped('Redis server not available');
        }

        /** @var local_redislock\lock\redis_lock_factory $redislockfactory */
        $redislockfactory = lock_config::get_lock_factory('conduit_cron');
        $lock1 = $redislockfactory->get_lock('test', 10, 200);
        $this->assertNotEmpty($lock1);
        $this->assertFalse($lock1->extend(10000));

        $this->assertDebuggingCalledCount(2,
            ['The function extend() is deprecated, please do not use it anymore.',
            'The function extend_lock() is deprecated, please do not use it anymore.']);
        $factoryclass = get_class($redislockfactory);
        switch ($factoryclass) {
            case 'local_redislock\lock\redis_lock_factory':
                $newttl = $redislockfactory->get_ttl($lock1);
                break;
            case 'core\lock\timing_wrapper_lock_factory':
                $newttl = $redislockfactory->get_real_factory()->get_ttl($lock1);
                break;
            default:
                $newttl = 1;
        }
        $this->assertEquals(-1, $newttl);

        $lock1->release();
    }

    /**
     * Tests auto_release method of the Redis lock factory.
     *
     * @throws coding_exception
     */
    public function test_lock_autorelease() {
        if (!$this->is_redis_available()) {
            $this->markTestSkipped('Redis server not available');
        }

        /** @var local_redislock\lock\redis_lock_factory $redislockfactory */
        $redislockfactory = lock_config::get_lock_factory('conduit_cron');
        $lock1 = $redislockfactory->get_lock('test', 10, 200);
        $this->assertNotEmpty($lock1);

        $lock2 = $redislockfactory->get_lock('another_test', 10, 200);
        $this->assertNotEmpty($lock2);

        // Class core\lock\lock has a __destruct method that throws a coding exception if the lock wasn't released.
        // The test fails when that happens. Simulate the auto-release being called by the shutdown manager.
        $factoryclass = get_class($redislockfactory);
        switch ($factoryclass) {
            case 'local_redislock\lock\redis_lock_factory':
                $redislockfactory->auto_release();
                break;
            case 'core\lock\timing_wrapper_lock_factory':
                $redislockfactory->get_real_factory()->auto_release();
                break;
            default:
                $redislockfactory->auto_release();
        }
    }

    /**
     * Tests that timeout on acquiring lock works with Redis lock factory.
     *
     * @throws coding_exception
     */
    public function test_lock_timeout() {
        $mockbuilder = $this->getMockBuilder('Redis')->onlyMethods(array('setnx'))->disableOriginalConstructor();
        $redis = $mockbuilder->getMock();

        $redislockfactory = new \local_redislock\lock\redis_lock_factory('cron', $redis);

        $redis->expects($this->atLeastOnce())->method('setnx')->will($this->returnValue(false));

        $starttime = time();
        $timedoutlock = $redislockfactory->get_lock('block_conduit', 3);
        $endtime = time();

        $this->assertEmpty($timedoutlock);
        $this->assertGreaterThanOrEqual(3, $endtime - $starttime);
    }

    /**
     * Tests that there are no retries or sleeping when the timeout is zero.
     *
     * @throws coding_exception
     */
    public function test_lock_zero_timeout() {
        $redis   = $this->getMockBuilder('Redis')->onlyMethods(array('setnx'))->disableOriginalConstructor()->getMock();
        $redis->expects($this->once())->method('setnx')->will($this->returnValue(false));

        $factory = new \local_redislock\lock\redis_lock_factory('cron', $redis);

        $start = microtime(true);
        $this->assertFalse($factory->get_lock('block_conduit', 0));
        $this->assertLessThan(.5, microtime(true) - $start);
    }

    /**
     * Tests shared connection.
     *
     * @throws coding_exception
     */
    public function test_shared_connection() {
        if (!$this->is_redis_available()) {
            $this->markTestSkipped('Redis server not available');
        }

        /** @var local_redislock\lock\redis_lock_factory $redislockfactory1 */
        $redislockfactory1 = lock_config::get_lock_factory('conduit_cron');
        $lock1 = $redislockfactory1->get_lock('shared_conn_test1', 10, 200);
        $this->assertNotEmpty($lock1);
        $redis1 = shared_redis_connection::get_instance()->get_redis();
        $this->assertNotNull($redis1);
        $lock1->release(); // All locks should be released.

        /** @var local_redislock\lock\redis_lock_factory $redislockfactory2 */
        $redislockfactory2 = lock_config::get_lock_factory('cron');
        $lock2 = $redislockfactory2->get_lock('shared_conn_test2', 10, 200);
        $this->assertNotEmpty($lock2);

        // Simulating auto releases.
        $factoryclass = get_class($redislockfactory1);
        // This should not close redis.
        switch ($factoryclass) {
            case 'local_redislock\lock\redis_lock_factory':
                $redislockfactory1->auto_release();
                break;
            case 'core\lock\timing_wrapper_lock_factory':
                $redislockfactory1->get_real_factory()->auto_release();
                break;
            default:
                $redislockfactory1->auto_release();
        }

        $redis2 = shared_redis_connection::get_instance()->get_redis();
        $this->assertSame($redis1, $redis2);
        $this->assertTrue($redis2->isConnected());

        // Last auto-release.
        $factoryclass = get_class($redislockfactory2);
        // This SHOULD close redis.
        switch ($factoryclass) {
            case 'local_redislock\lock\redis_lock_factory':
                $redislockfactory2->auto_release();
                break;
            case 'core\lock\timing_wrapper_lock_factory':
                $redislockfactory2->get_real_factory()->auto_release();
                break;
            default:
                $redislockfactory2->auto_release();
        }

        // Connection should be auto closed when Moodle shuts down (All auto-releases have run).
        $redis3 = shared_redis_connection::get_instance()->get_redis();
        $this->assertNull($redis3);
    }

    /**
     * Helper method to determine whether a Redis server is available to run these tests.
     * If LOCAL_REDISLOCK_REDIS_LOCK_TEST is not true most of these tests will be skipped.
     *
     * @uses LOCAL_REDISLOCK_REDIS_LOCK_TEST
     * @return bool
     */
    protected function is_redis_available() {
        return defined('LOCAL_REDISLOCK_REDIS_LOCK_TEST') && LOCAL_REDISLOCK_REDIS_LOCK_TEST;
    }
}
