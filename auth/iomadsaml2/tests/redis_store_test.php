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
 * Testcase class for auth/iomadsaml2 Redis store.
 *
 * @package    auth_iomadsaml2
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use auth_iomadsaml2\redis_store;

/**
 * Testcase class for auth/iomadsaml2 Redis store.
 *
 * @package    auth_iomadsaml2
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_iomadsaml2_redis_store_testcase extends advanced_testcase {

    /**
     * @var null|\Redis
     */
    protected $redis;

    public function setUp(): void {
        if (!$this->is_redis_available()) {
            $this->markTestSkipped('Redis was not available - skipping test');
        }

        $this->redis = new \Redis();
        $this->redis->connect(AUTH_SAML2_REDIS_STORE_TEST_SERVER);
        $this->redis->setOption(\Redis::OPT_PREFIX, 'simpleSAMLphp.testdbname.');
        $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
    }

    public function tearDown(): void {
        unset($this->redis);
    }

    public function test_set_with_expire() {
        $now = time();
        $expectedttl = 60;
        $expire = $now + $expectedttl;
        $redisstore = new redis_store($this->redis);
        $redisstore->set('session', '12345&$%8', (object) ['k' => 'v', 'k2' => 'v2'], $expire);

        $ttl = $this->redis->ttl('session.12345&$%8');
        $this->assertEquals($expectedttl, $ttl, '', 5);
    }

    public function test_set_no_expire() {
        // Redis returns -1 for keys that have no TTL.
        $expectedttl = -1;
        $redisstore = new redis_store($this->redis);
        $redisstore->set('session', 'g987654321', (object) ['k' => 'v', 'k2' => 'v2']);

        $ttl = $this->redis->ttl('session.g987654321');
        $this->assertEquals($expectedttl, $ttl);
    }

    public function test_get_key_exists() {
        $redisstore = new redis_store($this->redis);

        $value = (object) ['k' => 'v', 'k2' => 'v2'];
        $redisstore->set('session', 'g98765', $value);

        $this->assertEquals($value, $redisstore->get('session', 'g98765'));
    }

    public function test_get_key_not_exists() {
        $redisstore = new redis_store($this->redis);

        $this->assertNull($redisstore->get('session', 'nonexistentkey'));
    }

    public function test_delete() {
        $redisstore = new redis_store($this->redis);

        $redisstore->set('session', '12345-09', 'value');
        $this->assertEquals('value', $redisstore->get('session', '12345-09'));

        $redisstore->delete('session', '12345-09');
        $this->assertNull($redisstore->get('session', '12345-09'));
    }

    public function test_delete_key_not_exists() {
        $redisstore = new redis_store($this->redis);
        $redisstore->delete('session', 'nonexistentkey');
    }

    public function test_bootstrap_redis() {
        global $CFG;

        $this->resetAfterTest(true);
        $CFG->auth_iomadsaml2_redis_server = AUTH_SAML2_REDIS_STORE_TEST_SERVER;
        $CFG->dbname = 'testdbname';

        $value = (object) ['k' => 'v', 'k2' => 'v2'];
        $redistore = new redis_store();
        $redistore->set('session', 'key', $value);

        $this->assertEquals($value, $redistore->get('session', 'key'));
    }

    /**
     * Helper method to determine whether a Redis server is available to run these tests.
     * If AUTH_SAML2_REDIS_STORE_TEST_SERVER is not set most of these tests will be skipped.
     *
     * @uses AUTH_SAML2_REDIS_STORE_TEST_SERVER
     * @return bool
     */
    protected function is_redis_available() {
        return defined('AUTH_SAML2_REDIS_STORE_TEST_SERVER');
    }
}
