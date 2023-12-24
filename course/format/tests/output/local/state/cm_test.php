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

namespace core_courseformat\output\local\state;

use availability_date\condition;
use core_availability\tree;
use stdClass;

/**
 * Tests for cm state class.
 *
 * @package    core_courseformat
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_courseformat\output\local\state\cm
 */
class cm_test extends \advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest.php');
        require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest_output_course_format_state.php');
    }

    /**
     * Test the behaviour of state\cm hasavailability attribute.
     *
     * @dataProvider hasrestrictions_state_provider
     * @covers ::export_for_template
     *
     * @param string $format the course format
     * @param string $rolename the user role name (editingteacher or student)
     * @param bool $hasavailability if the activity|section has availability
     * @param bool $available if the activity availability condition is available or not to the user
     * @param bool $expected the expected result
     */
    public function test_cm_hasrestrictions_state(
        string $format = 'topics',
        string $rolename = 'editingteacher',
        bool $hasavailability = false,
        bool $available = false,
        bool $expected = false
    ) {
        $data = $this->setup_hasrestrictions_scenario($format, $rolename, $hasavailability, $available);

        // Get the cm state.
        $courseformat = $data->courseformat;
        $renderer = $data->renderer;

        $cmclass = $courseformat->get_output_classname('state\\cm');

        $cmstate = new $cmclass(
            $courseformat,
            $data->section,
            $data->cm
        );
        $state = $cmstate->export_for_template($renderer);

        $this->assertEquals($expected, $state->hascmrestrictions);
    }

    /**
     * Setup section or cm has restrictions scenario.
     *
     * @param string $format the course format
     * @param string $rolename the user role name (editingteacher or student)
     * @param bool $hasavailability if the activity|section has availability
     * @param bool $available if the activity availability condition is available or not to the user
     * @return stdClass the scenario instances.
     */
    private function setup_hasrestrictions_scenario(
        string $format = 'topics',
        string $rolename = 'editingteacher',
        bool $hasavailability = false,
        bool $available = false
    ): stdClass {
        global $PAGE, $DB;
        $this->resetAfterTest();

        set_config('enableavailability', 1);

        $course = $this->getDataGenerator()->create_course(['numsections' => 1, 'format' => $format]);

        // Create and enrol user.
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user(
            $user->id,
            $course->id,
            $rolename
        );
        $this->setUser($user);

        // Create an activity.
        $activity = $this->getDataGenerator()->create_module('page', ['course' => $course->id], [
            'section' => 1,
            'visible' => 1
        ]);

        // Set up the availability settings.
        if ($hasavailability) {
            $operation = ($available) ? condition::DIRECTION_UNTIL : condition::DIRECTION_FROM;
            $availabilityjson = json_encode(tree::get_root_json(
                [
                    condition::get_json($operation, time() + 3600),
                ],
                '&',
                true
            ));
            $selector = ['id' => $activity->cmid];
            $DB->set_field('course_modules', 'availability', trim($availabilityjson), $selector);
        }

        // Get the cm state.
        $courseformat = course_get_format($course->id);
        $modinfo = $courseformat->get_modinfo();
        $renderer = $courseformat->get_renderer($PAGE);

        if ($format == 'theunittest') {
            // These course format's hasn't the renderer file, so a debugging message will be displayed.
            $this->assertDebuggingCalled();
        }

        return (object)[
            'courseformat' => $courseformat,
            'section' => $modinfo->get_section_info(1),
            'cm' => $modinfo->get_cm($activity->cmid),
            'renderer' => $renderer,
        ];
    }

    /**
     * Data provider for test_state().
     *
     * @return array
     */
    public function hasrestrictions_state_provider(): array {
        return [
            // Teacher scenarios (topics).
            'Teacher, Topics, can edit, has availability and is available' => [
                'format' => 'topics',
                'rolename' => 'editingteacher',
                'hasavailability' => true,
                'available' => true,
                'expected' => true,
            ],
            'Teacher, Topics, can edit, has availability and is not available' => [
                'format' => 'topics',
                'rolename' => 'editingteacher',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Teacher, Topics, can edit and has not availability' => [
                'format' => 'topics',
                'rolename' => 'editingteacher',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
            // Teacher scenarios (weeks).
            'Teacher, Weeks, can edit, has availability and is available' => [
                'format' => 'weeks',
                'rolename' => 'editingteacher',
                'hasavailability' => true,
                'available' => true,
                'expected' => true,
            ],
            'Teacher, Weeks, can edit, has availability and is not available' => [
                'format' => 'weeks',
                'rolename' => 'editingteacher',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Teacher, Weeks, can edit and has not availability' => [
                'format' => 'weeks',
                'rolename' => 'editingteacher',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
            // Teacher scenarios (mock format).
            'Teacher, Mock format, can edit, has availability and is available' => [
                'format' => 'theunittest',
                'rolename' => 'editingteacher',
                'hasavailability' => true,
                'available' => true,
                'expected' => true,
            ],
            'Teacher, Mock format, can edit, has availability and is not available' => [
                'format' => 'theunittest',
                'rolename' => 'editingteacher',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Teacher, Mock format, can edit and has not availability' => [
                'format' => 'theunittest',
                'rolename' => 'editingteacher',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
            // Non editing teacher scenarios (topics).
            'Non editing teacher, Topics, can edit, has availability and is available' => [
                'format' => 'topics',
                'rolename' => 'teacher',
                'hasavailability' => true,
                'available' => true,
                'expected' => true,
            ],
            'Non editing teacher, Topics, can edit, has availability and is not available' => [
                'format' => 'topics',
                'rolename' => 'teacher',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Non editing teacher, Topics, can edit and has not availability' => [
                'format' => 'topics',
                'rolename' => 'teacher',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
            // Non editing teacher scenarios (weeks).
            'Non editing teacher, Weeks, can edit, has availability and is available' => [
                'format' => 'weeks',
                'rolename' => 'teacher',
                'hasavailability' => true,
                'available' => true,
                'expected' => true,
            ],
            'Non editing teacher, Weeks, can edit, has availability and is not available' => [
                'format' => 'weeks',
                'rolename' => 'teacher',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Non editing teacher, Weeks, can edit and has not availability' => [
                'format' => 'weeks',
                'rolename' => 'teacher',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
            // Non editing teacher scenarios (mock format).
            'Non editing teacher, Mock format, can edit, has availability and is available' => [
                'format' => 'theunittest',
                'rolename' => 'teacher',
                'hasavailability' => true,
                'available' => true,
                'expected' => true,
            ],
            'Non editing teacher, Mock format, can edit, has availability and is not available' => [
                'format' => 'theunittest',
                'rolename' => 'teacher',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Non editing teacher, Mock format, can edit and has not availability' => [
                'format' => 'theunittest',
                'rolename' => 'teacher',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
            // Student scenarios (topics).
            'Student, Topics, cannot edit, has availability and is available' => [
                'format' => 'topics',
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => true,
                'expected' => false,
            ],
            'Student, Topics, cannot edit, has availability and is not available' => [
                'format' => 'topics',
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Student, Topics, cannot edit and has not availability' => [
                'format' => 'topics',
                'rolename' => 'student',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
            // Student scenarios (weeks).
            'Student, Weeks, cannot edit, has availability and is available' => [
                'format' => 'weeks',
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => true,
                'expected' => false,
            ],
            'Student, Weeks, cannot edit, has availability and is not available' => [
                'format' => 'weeks',
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Student, Weeks, cannot edit and has not availability' => [
                'format' => 'weeks',
                'rolename' => 'student',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
            // Student scenarios (mock format).
            'Student, Mock format, cannot edit, has availability and is available' => [
                'format' => 'theunittest',
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => true,
                'expected' => false,
            ],
            'Student, Mock format, cannot edit, has availability and is not available' => [
                'format' => 'theunittest',
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Student, Mock format, cannot edit and has not availability' => [
                'format' => 'theunittest',
                'rolename' => 'student',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
        ];
    }
}
