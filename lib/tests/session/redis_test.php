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

namespace core\session;

use core\tests\session\mock_handler;
use Redis;
use RedisException;

/**
 * Unit tests for classes/session/redis.php.
 *
 * NOTE: in order to execute this test you need to set up
 *       Redis server and add configuration a constant
 *       to config.php or phpunit.xml configuration file:
 *
 * define('TEST_SESSION_REDIS_HOST', '127.0.0.1');
 *
 * @package   core
 * @covers    \core\session\redis
 * @author    Russell Smith <mr-russ@smith2001.net>
 * @copyright 2016 Russell Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @runClassInSeparateProcess
 * @covers \core\session\redis
 */
final class redis_test extends \advanced_testcase {
    /** @var string $keyprefix This key prefix used when testing Redis */
    protected string $keyprefix = '';
    /** @var ?Redis $redis The current testing redis connection */
    protected ?Redis $redis = null;
    /** @var bool $encrypted Is the current testing redis connection encrypted*/
    protected bool $encrypted = false;
    /** @var int $acquiretimeout how long we wait for session lock in seconds when testing Redis */
    protected int $acquiretimeout = 1;
    /** @var int $lockexpire how long to wait in seconds before expiring the lock when testing Redis */
    protected int $lockexpire = 70;

    #[\Override]
    public function setUp(): void {
        global $CFG;
        parent::setUp();

        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension not loaded.');
        }
        if (!defined('TEST_SESSION_REDIS_HOST')) {
            $this->markTestSkipped('Session test server not set. define: TEST_SESSION_REDIS_HOST');
        }
        $version = phpversion('Redis');
        if (!$version) {
            $this->markTestSkipped('Redis extension version missing');
        } else if (version_compare($version, \core\session\redis::REDIS_MIN_EXTENSION_VERSION) <= 0) {
            $this->markTestSkipped('Redis extension version must be at least ' . \core\session\redis::REDIS_MIN_EXTENSION_VERSION .
                ': now running "' . $version . '"');
        }

        $this->resetAfterTest();

        $this->keyprefix = 'phpunit'.rand(1, 100000);

        if (strpos(TEST_SESSION_REDIS_HOST, ':')) {
            list($server, $port) = explode(':', TEST_SESSION_REDIS_HOST);
        } else {
            $server = TEST_SESSION_REDIS_HOST;
            $port = 6379;
        }
        $CFG->session_redis_host = $server;
        $CFG->session_redis_port = $port;

        $opts = [];
        if (defined('TEST_SESSION_REDIS_ENCRYPT') && TEST_SESSION_REDIS_ENCRYPT) {
            $this->encrypted = true;
            $sslopts = $CFG->session_redis_encrypt = ['verify_peer' => false, 'verify_peer_name' => false];
            $opts['stream'] = $sslopts;
        }
        $CFG->session_redis_prefix = $this->keyprefix;

        // Set a very short lock timeout to ensure tests run quickly.  We are running single threaded,
        // so unless we lock and expect it to be there, we will always see a lock.
        $CFG->session_redis_acquire_lock_timeout = $this->acquiretimeout;
        $CFG->session_redis_lock_expire = $this->lockexpire;

        $this->redis = new Redis();
        $this->redis->connect($server, $port, 1, null, 1, 0, $opts);
        if (!$this->redis->ping()) {
            $this->markTestSkipped("Redis ping failed");
        }
    }

    public function tearDown(): void {
        if (!extension_loaded('redis') || !defined('TEST_SESSION_REDIS_HOST')) {
            return;
        }

        $list = $this->redis->keys($this->keyprefix.'*');
        foreach ($list as $keyname) {
            $this->redis->del($keyname);
        }
        $this->redis->close();
        parent::tearDown();
    }

    public function test_normal_session_read_only(): void {
        $sess = new \core\session\redis();
        $sess->set_requires_write_lock(false);
        $sess->init();
        $this->assertSame('', $sess->read('sess1'));
        $this->assertTrue($sess->close());
    }

    public function test_normal_session_start_stop_works(): void {
        $sess = new \core\session\redis();
        $sess->init();
        $sess->set_requires_write_lock(true);
        $this->assertTrue($sess->open('Not used', 'Not used'));
        $this->assertSame('', $sess->read('sess1'));
        $this->assertTrue($sess->write('sess1', 'DATA'));
        $this->assertTrue($sess->close());

        // Read the session again to ensure locking did what it should.
        $this->assertTrue($sess->open('Not used', 'Not used'));
        $this->assertSame('DATA', $sess->read('sess1'));
        $this->assertTrue($sess->write('sess1', 'DATA-new'));
        $this->assertTrue($sess->close());
        $this->assert_session_no_locks();
    }

    public function test_compression_read_and_write_works(): void {
        global $CFG;

        $CFG->session_redis_compressor = \core\session\redis::COMPRESSION_GZIP;

        $sess = new \core\session\redis();
        $sess->init();
        $this->assertTrue($sess->write('sess1', 'DATA'));
        $this->assertSame('DATA', $sess->read('sess1'));
        $this->assertTrue($sess->close());

        if (extension_loaded('zstd')) {
            $CFG->session_redis_compressor = \core\session\redis::COMPRESSION_ZSTD;

            $sess = new \core\session\redis();
            $sess->init();
            $this->assertTrue($sess->write('sess2', 'DATA'));
            $this->assertSame('DATA', $sess->read('sess2'));
            $this->assertTrue($sess->close());
        }

        $CFG->session_redis_compressor = \core\session\redis::COMPRESSION_NONE;
    }

    public function test_session_blocks_with_existing_session(): void {
        $sess = new \core\session\redis();
        $sess->init();
        $sess->set_requires_write_lock(true);
        $this->assertTrue($sess->open('Not used', 'Not used'));
        $this->assertSame('', $sess->read('sess1'));
        $this->assertTrue($sess->write('sess1', 'DATA'));
        $this->assertTrue($sess->close());

        // Sessions are not locked until they have been saved once.
        $this->assertTrue($sess->open('Not used', 'Not used'));
        $this->assertSame('DATA', $sess->read('sess1'));

        $sessblocked = new \core\session\redis();
        $sessblocked->init();
        $sessblocked->set_requires_write_lock(true);
        $this->assertTrue($sessblocked->open('Not used', 'Not used'));

        // Trap the error log and send it to stdOut so we can expect output at the right times.
        $errorlog = tempnam(sys_get_temp_dir(), "rediserrorlog");
        $this->iniSet('error_log', $errorlog);
        try {
            $sessblocked->read('sess1');
            $this->fail('Session lock must fail to be obtained.');
        } catch (\core\session\exception $e) {
            $this->assertStringContainsString("Unable to obtain lock for session id session_se", $e->getMessage());
            $this->assertStringContainsString('within 1 sec.', $e->getMessage());
            $this->assertStringContainsString('session lock timeout (1 min 10 secs) ', $e->getMessage());
            $this->assertStringContainsString('Cannot obtain session lock for sid: session_sess1', file_get_contents($errorlog));
        }

        $this->assertTrue($sessblocked->close());
        $this->assertTrue($sess->write('sess1', 'DATA-new'));
        $this->assertTrue($sess->close());
        $this->assert_session_no_locks();
    }

    public function test_session_is_destroyed_when_it_does_not_exist(): void {
        $sess = new \core\session\redis();
        $sess->init();
        $sess->set_requires_write_lock(true);
        $this->assertTrue($sess->open('Not used', 'Not used'));
        $this->assertTrue($sess->destroy('sess-destroy'));
        $this->assert_session_no_locks();
    }

    public function test_session_is_destroyed_when_we_have_it_open(): void {
        $sess = new \core\session\redis();
        $sess->init();
        $sess->set_requires_write_lock(true);
        $this->assertTrue($sess->open('Not used', 'Not used'));
        $this->assertSame('', $sess->read('sess-destroy'));
        $this->assertTrue($sess->destroy('sess-destroy'));
        $this->assertTrue($sess->close());
        $this->assert_session_no_locks();
    }

    public function test_multiple_sessions_do_not_interfere_with_each_other(): void {
        $sess1 = new \core\session\redis();
        $sess1->set_requires_write_lock(true);
        $sess1->init();
        $sess2 = new \core\session\redis();
        $sess2->set_requires_write_lock(true);
        $sess2->init();

        // Initialize session 1.
        $this->assertTrue($sess1->open('Not used', 'Not used'));
        $this->assertSame('', $sess1->read('sess1'));
        $this->assertTrue($sess1->write('sess1', 'DATA'));
        $this->assertTrue($sess1->close());

        // Initialize session 2.
        $this->assertTrue($sess2->open('Not used', 'Not used'));
        $this->assertSame('', $sess2->read('sess2'));
        $this->assertTrue($sess2->write('sess2', 'DATA2'));
        $this->assertTrue($sess2->close());

        // Open and read session 1 and 2.
        $this->assertTrue($sess1->open('Not used', 'Not used'));
        $this->assertSame('DATA', $sess1->read('sess1'));
        $this->assertTrue($sess2->open('Not used', 'Not used'));
        $this->assertSame('DATA2', $sess2->read('sess2'));

        // Write both sessions.
        $this->assertTrue($sess1->write('sess1', 'DATAX'));
        $this->assertTrue($sess2->write('sess2', 'DATA2X'));

        // Read both sessions.
        $this->assertTrue($sess1->open('Not used', 'Not used'));
        $this->assertTrue($sess2->open('Not used', 'Not used'));
        $this->assertEquals('DATAX', $sess1->read('sess1'));
        $this->assertEquals('DATA2X', $sess2->read('sess2'));

        // Close both sessions
        $this->assertTrue($sess1->close());
        $this->assertTrue($sess2->close());

        // Read the session again to ensure locking did what it should.
        $this->assert_session_no_locks();
    }

    public function test_multiple_sessions_work_with_a_single_instance(): void {
        $sess = new \core\session\redis();
        $sess->init();
        $sess->set_requires_write_lock(true);

        // Initialize session 1.
        $this->assertTrue($sess->open('Not used', 'Not used'));
        $this->assertSame('', $sess->read('sess1'));
        $this->assertTrue($sess->write('sess1', 'DATA'));
        $this->assertSame('', $sess->read('sess2'));
        $this->assertTrue($sess->write('sess2', 'DATA2'));
        $this->assertSame('DATA', $sess->read('sess1'));
        $this->assertSame('DATA2', $sess->read('sess2'));
        $this->assertTrue($sess->destroy('sess2'));

        $this->assertTrue($sess->close());
        $this->assert_session_no_locks();

        $this->assertTrue($sess->close());
    }

    public function test_session_exists_returns_valid_values(): void {
        $sess = new \core\session\redis();
        $sess->init();
        $sess->set_requires_write_lock(true);

        $this->assertTrue($sess->open('Not used', 'Not used'));
        $this->assertSame('', $sess->read('sess1'));

        $this->assertFalse($sess->session_exists('sess1'), 'Session must not exist yet, it has not been saved');
        $this->assertTrue($sess->write('sess1', 'DATA'));
        $this->assertTrue($sess->session_exists('sess1'), 'Session must exist now.');
        $this->assertTrue($sess->destroy('sess1'));
        $this->assertFalse($sess->session_exists('sess1'), 'Session should be destroyed.');
    }

    public function test_destroy_removes_the_session_from_redis(): void {
        global $DB;

        $sess = new \core\session\redis();
        $sess->init();

        $mockhandler = new mock_handler();

        $this->assertTrue($sess->open('Not used', 'Not used'));
        $this->assertTrue($sess->write('sess1', 'DATA'));
        $this->assertTrue($sess->write('sess2', 'DATA'));
        $this->assertTrue($sess->write('sess3', 'DATA'));

        $sessiondata = new \stdClass();
        $sessiondata->userid = 2;
        $sessiondata->timecreated = time();
        $sessiondata->timemodified = time();

        $sessiondata->sid = 'sess1';
        $mockhandler->add_test_session($sessiondata);
        $sessiondata->sid = 'sess2';
        $mockhandler->add_test_session($sessiondata);
        $sessiondata->sid = 'sess3';
        $mockhandler->add_test_session($sessiondata);

        $this->assertNotEquals('', $sess->read('sess1'));
        $sess->destroy('sess1');
        $this->assertEquals('', $sess->read('sess1'));

        $this->assertEmpty($this->redis->keys($this->keyprefix.'sess1.lock'));

        $sess->destroy_all();

        $mockhandler = new mock_handler();
        $this->assertEquals(
            3,
            $mockhandler->count_sessions(),
            'Moodle handles session database, plugin must not change it.',
        );
        $this->assert_session_no_locks();
        $this->assertEmpty($this->redis->keys($this->keyprefix.'*'), 'There should be no session data left.');
    }

    public function test_exception_when_connection_attempts_exceeded(): void {
        global $CFG;

        $CFG->session_redis_port = 111111;
        $actual = '';

        $sess = new \core\session\redis();
        try {
            $sess->init();
        } catch (RedisException $e) {
            $actual = $e->getMessage();
        }

        // The Redis session test config allows the user to put the port number inside the host. e.g. 127.0.0.1:6380.
        // Therefore, to get the host, we need to explode it.
        list($host, ) = explode(':', TEST_SESSION_REDIS_HOST);

        $expected = "Failed to connect (try 3 out of 3) to Redis at $host:111111";
        $this->assertDebuggingCalledCount(3);
        $this->assertStringContainsString($expected, $actual);
    }

    /**
     * Assert that we don't have any session locks in Redis.
     */
    protected function assert_session_no_locks(): void {
        $this->assertEmpty($this->redis->keys($this->keyprefix.'*.lock'));
    }

    public function test_session_redis_encrypt(): void {
        global $CFG;

        $CFG->session_redis_encrypt = ['verify_peer' => false, 'verify_peer_name' => false];

        $sess = new \core\session\redis();

        $prop = new \ReflectionProperty(\core\session\redis::class, 'sslopts');

        $this->assertEquals($CFG->session_redis_encrypt, $prop->getValue($sess));
    }

    /**
     * Test the get maxlifetime method.
     */
    public function test_get_maxlifetime(): void {
        global $CFG;

        // Set the timeout to something known for the test.
        set_config('sessiontimeout', 100);

        // Generate a test user.
        $user = $this->getDataGenerator()->create_user();

        // Create a new redis session object.
        $session = new \core\session\redis();
        $session->init();

        // The get_maxlifetime is private, so we need to use reflection to access it.
        $method = new \ReflectionMethod(\core\session\redis::class, 'get_maxlifetime');

        // Test guest timeout, which should be longer.
        $result = $method->invoke($session, $CFG->siteguest);
        $this->assertEquals(500, $result);

        // Test first access timeout.
        $result = $method->invoke($session, 0, true);
        $this->assertEquals(180, $result);

        // Test with a real user.
        $result = $method->invoke($session, $user->id);
        $this->assertEquals(180, $result);

    }

    /**
     * Test the add session method.
     */
    public function test_add_session(): void {

        // Set the timeout to something known for the test.
        set_config('sessiontimeout', 100);

        // Generate a test user.
        $user = $this->getDataGenerator()->create_user();

        // Create a new redis session object.
        $session = new \core\session\redis();
        $session->init();

        // Create two sessions for the user.
        session_id('id1');
        $session1data = $session->add_session($user->id);
        session_id('id2');
        $session2data = $session->add_session($user->id);

        $session1 = $session->get_session_by_sid('id1');
        $session2 = $session->get_session_by_sid('id2');

        // Assert that the sessions were created and have expected data.
        $this->assertEqualsCanonicalizing((array)$session1data, (array)$session1);
        $this->assertEqualsCanonicalizing((array)$session2data, (array)$session2);

        // Check that the session hash has a ttl set.
        $this->assertGreaterThan(-1, $this->redis->ttl($this->keyprefix . 'session_id1'));

        // Check that the session ttl is less or equal to what we set it.
        $this->assertLessThanOrEqual(180, $this->redis->ttl($this->keyprefix . 'session_id1'));

    }

    /**
     * Test writing session data.
     */
    public function test_write(): void {
        // Set the timeout to something known for the test.
        set_config('sessiontimeout', 100);

        // Generate a test user.
        $user = $this->getDataGenerator()->create_user();

        // Create a new redis session object.
        $session = new \core\session\redis();
        $session->init();

        // Create two sessions for the user.
        session_id('id1');
        $session->add_session($user->id);
        session_id('id2');
        $session->add_session($user->id);

        $testdata = 'some test data';

        // Write some data to the store.
        $result = $session->write('id2', $testdata);

        // Check that the write was successful.
        $this->assertTrue($result);

        // Check that the data was written to the store.
        $getdata = $this->redis->hget($this->keyprefix . 'session_id2', 'sessdata');
        $this->assertStringContainsString($testdata, $getdata);
    }

    /**
     * Test reading session data.
     */
    public function test_read(): void {
        // Set the timeout to something known for the test.
        set_config('sessiontimeout', 100);

        // Generate a test user.
        $user = $this->getDataGenerator()->create_user();

        // Create a new redis session object.
        $session = new \core\session\redis();
        $session->init();

        // Create two sessions for the user.
        session_id('id1');
        $session->add_session($user->id);
        session_id('id2');
        $session->add_session($user->id);

        $testdata = 'some test data';

        // Write some session data to the store.
        $session->write('id2', $testdata);

        // Read the session data.
        $result = $session->read('id2');

        // Check that the read was successful.
        $this->assertEquals($result, $testdata);

    }

    /**
     * Test updating a session.
     */
    public function test_update_session(): void {
        // Set the timeout to something known for the test.
        set_config('sessiontimeout', 100);

        // Generate a test user.
        $user = $this->getDataGenerator()->create_user();

        // Create a new redis session object.
        $session = new \core\session\redis();
        $session->init();

        // Create two sessions for the user.
        session_id('id1');
        $session->add_session($user->id);
        session_id('id2');
        $sessiondata = $session->add_session($user->id);

        // Update the session data.
        $sessiondata->lastip = '8.8.8.8';
        $session->update_session($sessiondata);

        // Check the value was updated.
        $updatedsession = $session->get_session_by_sid('id2');
        $this->assertEquals('8.8.8.8', $updatedsession->lastip);

        // Test session update when userid is not set, should not error.
        unset($sessiondata->userid);
        $session->update_session($sessiondata);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test destroying a session by auth plugin.
     */
    public function test_destroy_by_auth_plugin(): void {
        // Create test users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user(['auth' => 'db']);

        // Create a new redis session object.
        $session = new \core\session\redis();
        $session->init();

        // Create  sessions for the users.
        session_id('id1');
        $session1data = $session->add_session($user1->id);
        session_id('id2');
        $session2data = $session->add_session($user2->id);

        $session1 = $session->get_session_by_sid('id1');
        $session2 = $session->get_session_by_sid('id2');

        // Assert that the sessions were created and have expected data.
        $this->assertEqualsCanonicalizing((array) $session1data, (array) $session1);
        $this->assertEqualsCanonicalizing((array) $session2data, (array) $session2);

        // Destroy the session by auth plugin.
        $session->destroy_by_auth_plugin('manual');

        // Check that the session was destroyed.
        $this->assertFalse($session->session_exists('id1'));

        // Check the session with db auth plugin was not destroyed.
        $this->assertTrue($session->session_exists('id2'));
    }
}
