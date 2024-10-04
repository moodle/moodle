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

/**
 * Unit tests for classes/session/memcached.php.
 *
 * NOTE: in order to execute this test you need to set up
 *       Memcached server and add configuration a constant
 *       to config.php or phpunit.xml configuration file:
 *
 * define('TEST_SESSION_MEMCACHED_SERVER', 'localhost:11211');
 * define('TEST_SESSION_MEMCACHED_PREFIX', 'memc.sess.key.');
 *
 * The 'TEST_SESSION_MEMCACHED_PREFIX' is optional and if not set the default value will be used.
 *
 * @package   core
 * @copyright Meirza <meirza.arson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @runTestsInSeparateProcesses
 * @covers core\session\memcached
 */
final class memcached_test extends \advanced_testcase {
    /** @var memcached|null $memcachedession An instance of the memcached session or null if not initialized. */
    private ?memcached $memcachedession = null;

    /** @var \Memcached $memcached An instance of the Memcached class used for handling session storage. */
    private \Memcached $memcached;

    /** @var mock_handler $mockhandler Dedicated testing handler. */
    private mock_handler $mockhandler;

    /** @var string $keyprefix The prefix used for keys in the Memcached session storage. */
    private string $keyprefix = 'memc.sess.key.';

    #[\Override]
    public function setUp(): void {
        global $CFG;
        parent::setUp();
        $this->resetAfterTest();

        if (!extension_loaded('memcached')) {
            $this->markTestSkipped('memcached extension is not loaded');
        }

        $version = phpversion('memcached');
        if (!$version || version_compare($version, '2.0') < 0) {
            $this->markTestSkipped('memcached extension version must be at least 2.0');
        }

        if (!defined('TEST_SESSION_MEMCACHED_SERVER')) {
            $this->markTestSkipped('Session test server not set. define: TEST_SESSION_MEMCACHED_SERVER');
        }

        if (defined('TEST_SESSION_MEMCACHED_PREFIX')) {
            $this->keyprefix = TEST_SESSION_MEMCACHED_PREFIX;
            $CFG->session_memcached_prefix = TEST_SESSION_MEMCACHED_PREFIX;
        }

        $this->mockhandler = new mock_handler();

        $CFG->session_memcached_save_path = TEST_SESSION_MEMCACHED_SERVER;
        $this->memcachedession = new memcached();
        $this->memcachedession->init();

        [$host, $port] = explode(':', TEST_SESSION_MEMCACHED_SERVER);
        $this->memcached = new \Memcached();
        $this->memcached->addServer($host, $port);
    }

    #[\Override]
    public function tearDown(): void {
        $this->memcached->quit();
        parent::tearDown();
    }

    /**
     * Test the destruction of a session.
     */
    public function test_destroy(): void {
        $sid = $this->add_session('sesstest');

        $this->assertTrue($this->memcachedession->session_exists($sid));
        $this->assertTrue(manager::session_exists($sid));

        $this->memcachedession->destroy($sid);

        $this->assertFalse($this->memcachedession->session_exists($sid));
        $this->assertFalse(manager::session_exists($sid));
    }

    /**
     * Test the destruction of all sessions.
     */
    public function test_destroy_all(): void {
        global $DB;

        $sid1 = $this->add_session('sesstest1');
        $sid2 = $this->add_session('sesstest2');

        $this->assertTrue($this->memcachedession->session_exists($sid1));
        $this->assertTrue($this->memcachedession->session_exists($sid2));
        $this->assertEquals(2, $DB->count_records('sessions'));

        $this->memcachedession->destroy_all();

        $this->assertFalse($this->memcachedession->session_exists($sid1));
        $this->assertFalse($this->memcachedession->session_exists($sid2));
        $this->assertEquals(0, $DB->count_records('sessions'));
    }

    /**
     * Adds a session with the given session ID.
     *
     * @param string $sid The session ID to add.
     * @return string The result of adding the session.
     */
    private function add_session(string $sid): string {
        $sid = md5($sid);
        $this->memcached->set($this->keyprefix . $sid, 'abc');

        $record = new \stdClass();
        $record->sid = $sid;
        $this->mockhandler->add_test_session($record);

        return $sid;
    }
}
