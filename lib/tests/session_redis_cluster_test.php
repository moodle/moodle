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

namespace core;

use core\session\redis as redis_session;
use RedisClusterException;

/**
 * Unit tests for Redis cluster in the core/session/redis.php.
 *
 * NOTE: in order to execute this test you need to set up
 *       Redis cluster server and add configuration a constant
 *       to config.php or phpunit.xml configuration file:
 *
 * define('TEST_SESSION_REDIS_HOSTCLUSTER', '127.0.0.1:7000,127.0.0.1:7001,127.0.0.1:7002');
 * define('TEST_SESSION_REDIS_AUTHCLUSTER', 'foobared');
 *
 * define('TEST_SESSION_REDIS_ENCRYPTCLUSTER', ['verify_peer' => false, 'verify_peer_name' => false]);
 * OR
 * define('TEST_SESSION_REDIS_ENCRYPTCLUSTER', ['cafile' => '/cafile/dir/ca.crt']);
 *
 * @package   core
 * @copyright 2024 Meirza <meirza.arson@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass    \core\session\redis
 */
class session_redis_cluster_test extends \advanced_testcase {

    /**
     * Set up the test environment.
     */
    public function setUp(): void {
        global $CFG;

        if (!\cache_helper::is_cluster_available()) {
            $this->markTestSkipped('Could not test core_session with cluster, class RedisCluster is not available.');
        } else if (!defined('TEST_SESSION_REDIS_HOSTCLUSTER')) {
            $this->markTestSkipped('Could not test session_redis_cluster_test with cluster, missing configuration. ' .
                                  "Example: define('TEST_SESSION_REDIS_HOSTCLUSTER', " .
                                  "'localhost:7000,localhost:7001,localhost:7002');");
        }
        $this->resetAfterTest();
        $CFG->session_redis_host = TEST_SESSION_REDIS_HOSTCLUSTER;
        if (defined('TEST_SESSION_REDIS_ENCRYPTCLUSTER') && TEST_SESSION_REDIS_ENCRYPTCLUSTER) {
            $CFG->session_redis_encrypt = TEST_SESSION_REDIS_ENCRYPTCLUSTER;
        }
        if (defined('TEST_SESSION_REDIS_AUTHCLUSTER') && TEST_SESSION_REDIS_AUTHCLUSTER) {
            $CFG->session_redis_auth = TEST_SESSION_REDIS_AUTHCLUSTER;
        }
    }

    /**
     * Tests compression for session read and write operations.
     *
     * It covers the behavior of session read and write operations under different compression configurations.
     *
     * @runInSeparateProcess
     * @covers ::read
     * @covers ::write
     */
    public function test_read_and_write(): void {
        $rediscluster = new redis_session();
        $rediscluster->init();
        $this->assertTrue($rediscluster->write('sess1', 'DATA'));
        $this->assertSame('DATA', $rediscluster->read('sess1'));
        $this->assertTrue($rediscluster->close());
    }

    /**
     * Tests the behavior when connection attempts to Redis cluster are exceeded.
     *
     * It sets up the environment to simulate multiple failed connection attempts and
     * checks if the expected exception message is received.
     *
     * @runInSeparateProcess
     * @covers ::init
     */
    public function test_exception_when_connection_attempts_exceeded(): void {
        global $CFG;

        $CFG->session_redis_host = '127.0.0.1:1111111,127.0.0.1:1111112,127.0.0.1:1111113';
        $actual = '';

        $rediscluster = new redis_session();
        try {
            $rediscluster->init();
        } catch (RedisClusterException $e) {
            $actual = $e->getMessage();
        }

        $expected = "Failed to connect (try 5 out of 5) to Redis at";
        $this->assertDebuggingCalledCount(5);
        $this->assertStringContainsString($expected, $actual);
    }
}
