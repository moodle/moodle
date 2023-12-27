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
 */
class session_redis_test extends \advanced_testcase {

    /** @var $keyprefix This key prefix used when testing Redis */
    protected $keyprefix = null;
    /** @var $redis The current testing redis connection */
    protected $redis = null;
    /** @var bool $encrypted Is the current testing redis connection encrypted*/
    protected $encrypted = false;
    /** @var int $acquiretimeout how long we wait for session lock in seconds when testing Redis */
    protected $acquiretimeout = 1;
    /** @var int $lockexpire how long to wait in seconds before expiring the lock when testing Redis */
    protected $lockexpire = 70;


    public function setUp(): void {
        global $CFG;

        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension not loaded.');
        }
        if (!defined('TEST_SESSION_REDIS_HOST')) {
            $this->markTestSkipped('Session test server not set. define: TEST_SESSION_REDIS_HOST');
        }
        $version = phpversion('Redis');
        if (!$version) {
            $this->markTestSkipped('Redis extension version missing');
        } else if (version_compare($version, '2.0') <= 0) {
            $this->markTestSkipped('Redis extension version must be at least 2.0: now running "' . $version . '"');
        }

        $this->resetAfterTest();

        $this->keyprefix = 'phpunit'.rand(1, 100000);

        $CFG->session_redis_host = TEST_SESSION_REDIS_HOST;
        if (strpos(TEST_SESSION_REDIS_HOST, ':')) {
            list($server, $port) = explode(':', TEST_SESSION_REDIS_HOST);
        } else {
            $server = TEST_SESSION_REDIS_HOST;
            $port = 6379;
        }

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
    }

    public function test_normal_session_read_only() {
        $sess = new \core\session\redis();
        $sess->set_requires_write_lock(false);
        $sess->init();
        $this->assertSame('', $sess->read('sess1'));
        $this->assertTrue($sess->close());
    }

    public function test_normal_session_start_stop_works() {
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
        $this->assertSessionNoLocks();
    }

    public function test_compression_read_and_write_works() {
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

    public function test_session_blocks_with_existing_session() {
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
            $this->assertStringContainsString("Unable to obtain lock for session id sess1", $e->getMessage());
            $this->assertStringContainsString('within 1 sec.', $e->getMessage());
            $this->assertStringContainsString('session lock timeout (1 min 10 secs) ', $e->getMessage());
            $this->assertStringContainsString('Cannot obtain session lock for sid: sess1', file_get_contents($errorlog));
        }

        $this->assertTrue($sessblocked->close());
        $this->assertTrue($sess->write('sess1', 'DATA-new'));
        $this->assertTrue($sess->close());
        $this->assertSessionNoLocks();
    }

    public function test_session_is_destroyed_when_it_does_not_exist() {
        $sess = new \core\session\redis();
        $sess->init();
        $sess->set_requires_write_lock(true);
        $this->assertTrue($sess->open('Not used', 'Not used'));
        $this->assertTrue($sess->destroy('sess-destroy'));
        $this->assertSessionNoLocks();
    }

    public function test_session_is_destroyed_when_we_have_it_open() {
        $sess = new \core\session\redis();
        $sess->init();
        $sess->set_requires_write_lock(true);
        $this->assertTrue($sess->open('Not used', 'Not used'));
        $this->assertSame('', $sess->read('sess-destroy'));
        $this->assertTrue($sess->destroy('sess-destroy'));
        $this->assertTrue($sess->close());
        $this->assertSessionNoLocks();
    }

    public function test_multiple_sessions_do_not_interfere_with_each_other() {
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
        $this->assertSessionNoLocks();
    }

    public function test_multiple_sessions_work_with_a_single_instance() {
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
        $this->assertSessionNoLocks();

        $this->assertTrue($sess->close());
    }

    public function test_session_exists_returns_valid_values() {
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

    public function test_kill_sessions_removes_the_session_from_redis() {
        global $DB;

        $sess = new \core\session\redis();
        $sess->init();

        $this->assertTrue($sess->open('Not used', 'Not used'));
        $this->assertTrue($sess->write('sess1', 'DATA'));
        $this->assertTrue($sess->write('sess2', 'DATA'));
        $this->assertTrue($sess->write('sess3', 'DATA'));

        $sessiondata = new \stdClass();
        $sessiondata->userid = 2;
        $sessiondata->timecreated = time();
        $sessiondata->timemodified = time();

        $sessiondata->sid = 'sess1';
        $DB->insert_record('sessions', $sessiondata);
        $sessiondata->sid = 'sess2';
        $DB->insert_record('sessions', $sessiondata);
        $sessiondata->sid = 'sess3';
        $DB->insert_record('sessions', $sessiondata);

        $this->assertNotEquals('', $sess->read('sess1'));
        $sess->kill_session('sess1');
        $this->assertEquals('', $sess->read('sess1'));

        $this->assertEmpty($this->redis->keys($this->keyprefix.'sess1.lock'));

        $sess->kill_all_sessions();

        $this->assertEquals(3, $DB->count_records('sessions'), 'Moodle handles session database, plugin must not change it.');
        $this->assertSessionNoLocks();
        $this->assertEmpty($this->redis->keys($this->keyprefix.'*'), 'There should be no session data left.');
    }

    public function test_exception_when_connection_attempts_exceeded() {
        global $CFG;

        $CFG->session_redis_port = 111111;
        $actual = '';

        $sess = new \core\session\redis();
        try {
            $sess->init();
        } catch (RedisException $e) {
            $actual = $e->getMessage();
        }

        $host = TEST_SESSION_REDIS_HOST;
        if ($this->encrypted) {
            $host = "tls://$host";
        }
        $expected = "Failed to connect (try 5 out of 5) to Redis at $host:111111";
        $this->assertDebuggingCalledCount(5);
        $this->assertStringContainsString($expected, $actual);
    }

    /**
     * Assert that we don't have any session locks in Redis.
     */
    protected function assertSessionNoLocks() {
        $this->assertEmpty($this->redis->keys($this->keyprefix.'*.lock'));
    }

    public function test_session_redis_encrypt() {
        global $CFG;

        $CFG->session_redis_encrypt = ['verify_peer' => false, 'verify_peer_name' => false];

        $sess = new \core\session\redis();

        $prop = new \ReflectionProperty(\core\session\redis::class, 'host');
        $this->assertEquals('tls://' . TEST_SESSION_REDIS_HOST, $prop->getValue($sess)[0]);
    }
}
