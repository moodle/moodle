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
 * Tests for report_helper.
 *
 * @package    core
 * @category   test
 * @copyright  2021 Sujith Haridasan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

use moodle_url;

/**
 * Tests the functions for report_helper class.
 */
final class report_helper_test extends \advanced_testcase {
    /**
     * Data provider for testing selected report for same and different courses
     *
     * @return array
     */
    public function data_selected_report():array {
        return [
            ['course_url_id' => [
                ['url' => '/test', 'id' => 1],
                ['url' => '/foo', 'id' => 1]]
            ],
            ['course_url_id' => [
                ['url' => '/test', 'id' => 1],
                ['url' => '/foo/bar', 'id' => 2]]
            ]
        ];
    }

    /**
     * Testing selected report saved in $USER session.
     *
     * @dataProvider data_selected_report
     * @param array $courseurlid The array has both course url and course id
     */
    public function test_save_selected_report(array $courseurlid):void {
        global $USER;

        $url1 = new moodle_url($courseurlid[0]['url']);
        $courseid1 = $courseurlid[0]['id'];
        report_helper::save_selected_report($courseid1, $url1);
        $this->assertDebuggingCalled('save_selected_report() has been deprecated because it is no ' .
            'longer used and will be removed in future versions of Moodle');

        $this->assertEquals($USER->course_last_report[$courseid1], $url1);

        $url2 = new moodle_url($courseurlid[1]['url']);
        $courseid2 = $courseurlid[1]['id'];
        report_helper::save_selected_report($courseid2, $url2);
        $this->assertDebuggingCalled('save_selected_report() has been deprecated because it is no ' .
            'longer used and will be removed in future versions of Moodle');

        $this->assertEquals($USER->course_last_report[$courseid2], $url2);
    }

    /**
     * Testing the report selector dropdown shown.
     *
     * Verify that the dropdowns have the pages to be displayed.
     *
     * @return void
     */
    public function test_print_report_selector():void {
        global $PAGE;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        $PAGE->set_url('/');

        $course = $this->getDataGenerator()->create_course();
        $PAGE->set_course($course);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'teacher');

        $this->setUser($user);

        ob_start();
        report_helper::print_report_selector('Logs');
        $output = $this->getActualOutput();
        ob_end_clean();

        $log = '<option value="/report/log/index.php?id=' . $course->id .'" selected>Logs</option>';
        $competency = '<option value="/report/competency/index.php?id=' . $course->id . '" >Competency breakdown</option>';
        $loglive = '<option value="/report/loglive/index.php?id=' . $course->id . '" >Live logs</option>';
        $participation = '<option value="/report/participation/index.php?id=' . $course->id . '" >Course participation</option>';
        $this->assertStringContainsString($log, $output);
        $this->assertStringContainsString($competency, $output);
        $this->assertStringContainsString($loglive, $output);
        $this->assertStringContainsString($participation, $output);
    }

    /**
     * Tests {@see report_helper::has_valid_group()}.
     *
     * @param int $groupmode Group mode for the course
     * @param string $username Username of the user to check
     * @param array $expected Expected result of the check, with 3 boolean values depending on the context:
     * - Course context
     * - Module context
     * - System context
     *
     * @covers       \core\report_helper::has_valid_group
     * @dataProvider  has_valid_group_provider
     */
    public function test_has_valid_group(int $groupmode, string $username, array $expected): void {
        $this->resetAfterTest();

        // Create some test course, groups, and users.
        $generator = self::getDataGenerator();
        $course = $generator->create_course(['groupmode' => $groupmode, 'groupmodeforce' => 1]);
        $assign = $generator->create_module('assign', ['course' => $course->id]);
        $g1 = $generator->create_group(['courseid' => $course->id]);

        $this->userids = [];
        $data = [
            's1' => ['role' => 'student', 'group' => $g1->id],
            's2' => ['role' => 'student', 'group' => null],
            't1' => ['role' => 'teacher', 'group' => $g1->id],
            't2' => ['role' => 'teacher', 'group' => null],
            'et1' => ['role' => 'editingteacher', 'group' => null],
        ];
        foreach ($data as $key => $value) {
            ['group' => $groupid, 'role' => $role] = $value;
            $this->userids[$key] = $generator->create_user(['username' => $key]);
            $generator->enrol_user($this->userids[$key]->id, $course->id, $role);
            if ($groupid) {
                groups_add_member($groupid, $this->userids[$key]->id);
            }
        }
        $coursecontext = \context_course::instance($course->id);
        [$course, $cm] = get_course_and_cm_from_instance($assign->id, 'assign');
        $modulecontext = \context_module::instance($cm->id);
        [$hasvalidgroupcourse, $hasvalidgroupmodule, $hasvalidgroupsystem] = $expected;
        $this->assertEquals(
            $hasvalidgroupcourse,
            report_helper::has_valid_group($coursecontext, $this->userids[$username]->id),
            "Failed for user $username in course context"
        );
        $this->assertEquals(
            $hasvalidgroupmodule,
            report_helper::has_valid_group($modulecontext, $this->userids[$username]->id),
            'Failed for user ' . $username . ' in module context'
        );
        $this->assertEquals(
            $hasvalidgroupsystem,
            report_helper::has_valid_group(\context_system::instance(), $this->userids[$username]->id),
            'Failed for user ' . $username . ' in system context'
        );
    }

    /**
     * Data provider for test_has_valid_group.
     *
     * @return array
     */
    public static function has_valid_group_provider(): array {
        return [
            'student 1 - g1 - separate group' => [
                'groupmode' => SEPARATEGROUPS,
                'username' => 's1',
                'expected' => [true, true, true],
            ],
            'student 2 - no group - separate group' => [
                'groupmode' => SEPARATEGROUPS,
                'username' => 's2',
                'expected' => [false, false, true],
            ],
            'teacher 1 - g1 - separate group' => [
                'groupmode' => SEPARATEGROUPS,
                'username' => 't1',
                'expected' => [true, true, true],
            ],
            'teacher 2 - no group - separate group' => [
                'groupmode' => SEPARATEGROUPS,
                'username' => 't2',
                'expected' => [false, false, true],
            ],
            'editing teacher - no group - separate group' => [
                'groupmode' => SEPARATEGROUPS,
                'username' => 'et1',
                'expected' => [true, true, true],
            ],
            'student 1 - g1 - no group' => [
                'groupmode' => NOGROUPS,
                'username' => 's1',
                'expected' => [true, true, true],
            ],
            'student 2 - no group - no group' => [
                'groupmode' => NOGROUPS,
                'username' => 's2',
                'expected' => [true, true, true],
            ],
            'teacher 1 - g1 - no group' => [
                'groupmode' => NOGROUPS,
                'username' => 't1',
                'expected' => [true, true, true],
            ],
            'teacher 2 - no group - no group' => [
                'groupmode' => NOGROUPS,
                'username' => 't2',
                'expected' => [true, true, true],
            ],
            'editing teacher - no group - no group' => [
                'groupmode' => NOGROUPS,
                'username' => 'et1',
                'expected' => [true, true, true],
            ],
            'student 1 - g1 - visible group' => [
                'groupmode' => VISIBLEGROUPS,
                'username' => 's1',
                'expected' => [true, true, true],
            ],
            'student 2 - no group - visible group' => [
                'groupmode' => VISIBLEGROUPS,
                'username' => 's2',
                'expected' => [true, true, true],
            ],
            'teacher 1 - g1 - visible group' => [
                'groupmode' => VISIBLEGROUPS,
                'username' => 't1',
                'expected' => [true, true, true],
            ],
            'teacher 2 - no group - visible group' => [
                'groupmode' => VISIBLEGROUPS,
                'username' => 't2',
                'expected' => [true, true, true],
            ],
            'editing teacher - visible group - no group' => [
                'groupmode' => VISIBLEGROUPS,
                'username' => 'et1',
                'expected' => [true, true, true],
            ],
        ];
    }
}
