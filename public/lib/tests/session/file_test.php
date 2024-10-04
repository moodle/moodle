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
 * Unit tests for classes/session/file.php.
 *
 * @package   core
 * @copyright Meirza <meirza.arson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @runTestsInSeparateProcesses
 * @covers \core\session\file
 */
final class file_test extends \advanced_testcase {
    /** @var file|null $filesession */
    private ?file $filesession = null;

    /** @var mock_handler $mockhandler Dedicated testing handler. */
    private mock_handler $mockhandler;

    #[\Override]
    public function setUp(): void {
        global $CFG;

        parent::setUp();
        $this->resetAfterTest();

        $this->mockhandler = new mock_handler();

        $this->filesession = new file();
        $this->filesession->init();
    }

    /**
     * Test destroy a specific session and delete this session record for this session id.
     */
    public function test_destroy(): void {
        $sid = md5('sesstest');
        $this->add_session($sid);

        $this->assertTrue($this->filesession->session_exists($sid));
        $this->assertTrue(manager::session_exists($sid));

        $this->filesession->destroy($sid);

        $this->assertFalse($this->filesession->session_exists($sid));
        $this->assertFalse(manager::session_exists($sid));
    }

    /**
     * Test destroy all sessions, and delete all the session data.
     */
    public function test_destroy_all(): void {
        global $DB;

        $sid1 = md5('sesstest1');
        $this->add_session($sid1);
        $sid2 = md5('sesstest2');
        $this->add_session($sid2);

        $this->assertTrue($this->filesession->session_exists($sid1));
        $this->assertTrue($this->filesession->session_exists($sid2));
        $this->assertEquals(2, $DB->count_records('sessions'));

        $this->filesession->destroy_all();

        $this->assertFalse($this->filesession->session_exists($sid1));
        $this->assertFalse($this->filesession->session_exists($sid2));
        $this->assertEquals(0, $DB->count_records('sessions'));
    }

    /**
     * Adds a session with the given session ID.
     *
     * @param string $sid The session ID to add.
     */
    private function add_session(string $sid): void {
        global $CFG;

        touch("{$CFG->dataroot}/sessions/sess_{$sid}");

        $record = new \stdClass();
        $record->sid = $sid;
        $this->mockhandler->add_test_session($record);
    }
}
