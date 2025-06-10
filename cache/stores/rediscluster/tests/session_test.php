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

defined('MOODLE_INTERNAL') || die();

/**
 * RedisCluster session tests.
 *
 * NOTE: in order to execute this test you need to set up
 *       RedisCluster and add a configuration constant
 *       to config.php or phpunit.xml configuration file:
 *
 * define('CACHESTORE_REDISCLUSTER_TEST_SERVER', '127.0.0.1:6379');
 *
 * These tests are derived from the ones for the core redis_session handler.
 *
 * @package   cachestore_rediscluster
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @runClassInSeparateProcess
 */

class cachestore_rediscluster_session_testcase extends advanced_testcase {

    protected $sesshandler = null;

    public function setUp(): void {
        global $CFG;

        $this->resetAfterTest();

        require_once("{$CFG->dirroot}/cache/stores/rediscluster/lib.php");

        if (!\cachestore_rediscluster::are_requirements_met() || !\cachestore_rediscluster::ready_to_be_used_for_testing()) {
            $this->markTestSkipped();
        }

        // Set a very short lock timeout to ensure tests run quickly.  We are
        // running single threaded, so unless we lock and expect it to be
        // there, we will always see a lock.
        $CFG->session_rediscluster = [
            'server' => CACHESTORE_REDISCLUSTER_TEST_SERVER,
            'prefix' => 'phpunit'.rand(1, 100000),
            'acquire_lock_timeout' => 1,
            'lock_expire' => 70,
        ];

        $this->sesshandler = new \cachestore_rediscluster\session();
        $this->sesshandler->init();

        if (!$this->sesshandler) {
            $this->markTestSkipped();
        }
    }

    public function tearDown(): void {
        if ($this->sesshandler) {
            $this->sesshandler->cleanup_test_instance();
        }
    }

    public function test_normal_session_start_stop_works() {
        $sess = new \cachestore_rediscluster\session();
        $sess->init();
        $this->assertTrue($sess->handler_open('Not used', 'Not used'));
        $this->assertSame('', $sess->handler_read('sess1'));
        $this->assertTrue($sess->handler_write('sess1', 'DATA'));
        $this->assertTrue($sess->handler_close());

        // Read the session again to ensure locking did what it should.
        $this->assertTrue($sess->handler_open('Not used', 'Not used'));
        $this->assertSame('DATA', $sess->handler_read('sess1'));
        $this->assertTrue($sess->handler_write('sess1', 'DATA-new'));
        $this->assertTrue($sess->handler_close());
        $this->assert_session_no_locks();
    }

    public function test_session_blocks_with_existing_session() {
        $sess = new \cachestore_rediscluster\session();
        $sess->init();
        $sess->set_requires_write_lock(true);
        $this->assertTrue($sess->handler_open('Not used', 'Not used'));
        $this->assertSame('', $sess->handler_read('sess1'));
        $this->assertTrue($sess->handler_write('sess1', 'DATA'));
        $this->assertTrue($sess->handler_close());

        // Sessions are not locked until they have been saved once.
        $this->assertTrue($sess->handler_open('Not used', 'Not used'));
        $this->assertSame('DATA', $sess->handler_read('sess1'));

        $sessblocked = new \cachestore_rediscluster\session();
        $sessblocked->init();
        $sessblocked->set_requires_write_lock(true);
        $this->assertTrue($sessblocked->handler_open('Not used', 'Not used'));

        // Trap the error log and send it to stdOut so we can expect output at the right times.
        $errorlog = tempnam(sys_get_temp_dir(), "rediserrorlog");
        $this->iniSet('error_log', $errorlog);

        try {
            $sessblocked->handler_read('sess1');
            $this->fail('Session lock must fail to be obtained.');
        } catch (\Exception $e) {
            $this->resetDebugging(); // Ignore the debug, we just care the exception was thrown.
            $this->assertStringContainsString("sessionwaiterr", $e->getMessage());
        }

        $this->assertTrue($sessblocked->handler_close());
        $this->assertTrue($sess->handler_write('sess1', 'DATA-new'));
        $this->assertTrue($sess->handler_close());
        $this->assert_session_no_locks();
    }

    public function test_session_is_destroyed_when_it_does_not_exist() {
        $sess = new \cachestore_rediscluster\session();
        $sess->init();
        $this->assertTrue($sess->handler_open('Not used', 'Not used'));
        $this->assertTrue($sess->handler_destroy('sess-destroy'));
        $this->assert_session_no_locks();
    }

    public function test_session_is_destroyed_when_we_have_it_open() {
        $sess = new \cachestore_rediscluster\session();
        $sess->init();
        $this->assertTrue($sess->handler_open('Not used', 'Not used'));
        $this->assertSame('', $sess->handler_read('sess-destroy'));
        $this->assertTrue($sess->handler_destroy('sess-destroy'));
        $this->assertTrue($sess->handler_close());
        $this->assert_session_no_locks();
    }

    public function test_multiple_sessions_do_not_interfere_with_each_other() {
        $sess1 = new \cachestore_rediscluster\session();
        $sess1->init();
        $sess2 = new \cachestore_rediscluster\session();
        $sess2->init();

        // Initialize session 1.
        $this->assertTrue($sess1->handler_open('Not used', 'Not used'));
        $this->assertSame('', $sess1->handler_read('sess1'));
        $this->assertTrue($sess1->handler_write('sess1', 'DATA'));
        $this->assertTrue($sess1->handler_close());

        // Initialize session 2.
        $this->assertTrue($sess2->handler_open('Not used', 'Not used'));
        $this->assertSame('', $sess2->handler_read('sess2'));
        $this->assertTrue($sess2->handler_write('sess2', 'DATA2'));
        $this->assertTrue($sess2->handler_close());

        // Open and read session 1 and 2.
        $this->assertTrue($sess1->handler_open('Not used', 'Not used'));
        $this->assertSame('DATA', $sess1->handler_read('sess1'));
        $this->assertTrue($sess2->handler_open('Not used', 'Not used'));
        $this->assertSame('DATA2', $sess2->handler_read('sess2'));

        // Write both sessions.
        $this->assertTrue($sess1->handler_write('sess1', 'DATAX'));
        $this->assertTrue($sess2->handler_write('sess2', 'DATA2X'));

        // Read both sessions.
        $this->assertTrue($sess1->handler_open('Not used', 'Not used'));
        $this->assertTrue($sess2->handler_open('Not used', 'Not used'));
        $this->assertEquals('DATAX', $sess1->handler_read('sess1'));
        $this->assertEquals('DATA2X', $sess2->handler_read('sess2'));

        // Close both sessions.
        $this->assertTrue($sess1->handler_close());
        $this->assertTrue($sess2->handler_close());

        // Read the session again to ensure locking did what it should.
        $this->assert_session_no_locks();
    }

    public function test_multiple_sessions_work_with_a_single_instance() {
        $sess = new \cachestore_rediscluster\session();
        $sess->init();

        // Initialize session 1.
        $this->assertTrue($sess->handler_open('Not used', 'Not used'));
        $this->assertSame('', $sess->handler_read('sess1'));
        $this->assertTrue($sess->handler_write('sess1', 'DATA'));
        $this->assertSame('', $sess->handler_read('sess2'));
        $this->assertTrue($sess->handler_write('sess2', 'DATA2'));
        $this->assertSame('DATA', $sess->handler_read('sess1'));
        $this->assertSame('DATA2', $sess->handler_read('sess2'));
        $this->assertTrue($sess->handler_destroy('sess2'));

        $this->assertTrue($sess->handler_close());
        $this->assert_session_no_locks();

        $this->assertTrue($sess->handler_close());
    }

    public function test_session_exists_returns_valid_values() {
        $sess = new \cachestore_rediscluster\session();
        $sess->init();

        $this->assertTrue($sess->handler_open('Not used', 'Not used'));
        $this->assertSame('', $sess->handler_read('sess1'));

        $this->assertFalse($sess->session_exists('sess1'), 'Session must not exist yet, it has not been saved');
        $this->assertTrue($sess->handler_write('sess1', 'DATA'));
        $this->assertTrue($sess->session_exists('sess1'), 'Session must exist now.');
        $this->assertTrue($sess->handler_destroy('sess1'));
        $this->assertFalse($sess->session_exists('sess1'), 'Session should be destroyed.');
    }

    public function test_kill_sessions_removes_the_session_from_redis() {
        global $DB;

        $sess = new \cachestore_rediscluster\session();
        $sess->init();

        $this->assertTrue($sess->handler_open('Not used', 'Not used'));
        $this->assertTrue($sess->handler_write('sess1', 'DATA'));
        $this->assertTrue($sess->handler_write('sess2', 'DATA'));
        $this->assertTrue($sess->handler_write('sess3', 'DATA'));

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

        $this->assertNotEquals('', $sess->handler_read('sess1'));
        $sess->kill_session('sess1');
        $this->assertEquals('', $sess->handler_read('sess1'));

        $this->assertEmpty($this->sesshandler->test_find_keys('sess1.lock'));

        $sess->kill_all_sessions();

        $this->assertEquals(3, $DB->count_records('sessions'), 'Moodle handles session database, plugin must not change it.');
        $this->assert_session_no_locks();
        $this->assertEmpty($this->sesshandler->test_find_keys('*'), 'There should be no session data left.');
    }

    /**
     * Assert that we don't have any session locks in Redis.
     */
    protected function assert_session_no_locks() {
        $this->assertEmpty($this->sesshandler->test_find_keys('*.lock'));
    }
}
