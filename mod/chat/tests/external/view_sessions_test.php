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
 * Tests for external function mod_chat_view_sessions.
 *
 * @package    mod_chat
 * @category   external
 * @copyright  2022 Rodrigo Mady <rodrigo.mady@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.3
 */

namespace mod_chat\external;

use externallib_advanced_testcase;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Test Class for external function mod_chat_view_sessions.
 *
 * @package    mod_chat
 * @category   external
 * @copyright  2023 Rodrigo Mady <rodrigo.mady@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.3
 * @coversDefaultClass \mod_chat\external\view_sessions
 */
class view_sessions_test extends externallib_advanced_testcase {

    /**
     * Prepare the test.
     *
     * @return array
     */
    private function prepare_test_data(): array {
        global $DB;
        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course();
        $student1 = $this->getDataGenerator()->create_and_enrol($course);
        $chat = $this->getDataGenerator()->create_module('chat', ['course' => $course->id]);
        $context = \context_module::instance($chat->cmid);
        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student']);
        assign_capability('mod/chat:readlog', CAP_ALLOW, $studentroleid, $context, true);
        $this->setUser($student1);

        return [
            'chat' => $chat,
        ];
    }

    /**
     * Helper to call view_sessions WS function.
     *
     * @param int $cmid
     * @param int $sessionstart
     * @param int $sessionend
     * @return array
     */
    protected function view_sessions(int $cmid, int $sessionstart = 0, int $sessionend = 0): array {
        $result = view_sessions::execute($cmid, $sessionstart, $sessionend);
        return \core_external\external_api::clean_returnvalue(view_sessions::execute_returns(), $result);
    }

    /**
     * Test for webservice view sessions.
     * @covers ::execute
     */
    public function test_view_sessions(): void {
        $data = $this->prepare_test_data();
        $result = $this->view_sessions($data['chat']->cmid);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('warnings', $result);
        $this->assertTrue($result['status']);
    }

    /**
     * Test for webservice view sessions without capability.
     * @covers ::execute
     */
    public function test_view_sessions_without_capability(): void {
        global $DB;
        $data = $this->prepare_test_data();
        $context = \context_module::instance($data['chat']->cmid);
        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student']);
        assign_capability('mod/chat:readlog', CAP_PROHIBIT, $studentroleid, $context, true);
        $result = $this->view_sessions($data['chat']->cmid);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('warnings', $result);
        $this->assertFalse($result['status']);
        $this->assertEquals(get_string('nopermissiontoseethechatlog', 'chat'), $result['warnings'][0]['message']);
    }

    /**
     * Test for webservice view sessions with start and end dates.
     * @covers ::execute
     */
    public function test_view_sessions_with_start_end_dates(): void {
        $data = $this->prepare_test_data();
        $result = $this->view_sessions($data['chat']->cmid, strtotime('today'), strtotime('tomorrow'));
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('warnings', $result);
        $this->assertTrue($result['status']);
    }

    /**
     * Test execute with no valid instance of cmid.
     * @covers ::execute
     */
    public function test_view_sessions_no_instance(): void {
        $this->expectException(moodle_exception::class);
        $this->view_sessions(1234);
    }
}
