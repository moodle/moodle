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
use context_course;
use stdClass;

/**
 * Tests for section state class.
 *
 * @package    core_courseformat
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_courseformat\output\local\state\section
 */
class section_test extends \advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest.php');
        require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest_output_course_format_state.php');
        require_once($CFG->libdir . '/externallib.php');
    }

    /**
     * Test the behaviour of state\section hasavailability attribute.
     *
     * @dataProvider hasrestrictions_state_provider
     * @covers ::export_for_template
     *
     * @param string $format the course format
     * @param bool $addcanviewhidden if the canviewhidden capabilities must be added to the role.
     * @param string $rolename the user role name (editingteacher or student)
     * @param bool $hasavailability if the activity|section has availability
     * @param bool $available if the activity availability condition is available or not to the user
     * @param bool $expected the expected result
     */
    public function test_section_hasrestrictions_state(
        string $format = 'topics',
        bool $addcanviewhidden = false,
        string $rolename = 'editingteacher',
        bool $hasavailability = false,
        bool $available = false,
        bool $expected = false
    ) {
        $data = $this->setup_hasrestrictions_scenario($format, $addcanviewhidden, $rolename, $hasavailability, $available);

        // Get the cm state.
        $courseformat = $data->courseformat;
        $renderer = $data->renderer;

        $sectionclass = $courseformat->get_output_classname('state\\section');

        $sectionstate = new $sectionclass(
            $courseformat,
            $data->section
        );
        $state = $sectionstate->export_for_template($renderer);

        $this->assertEquals($expected, $state->hasrestrictions);
    }

    /**
     * Setup section or cm has restrictions scenario.
     *
     * @param string $format the course format
     * @param bool $addcanviewhidden if the canviewhidden capabilities must be added to the role.
     * @param string $rolename the user role name (editingteacher or student)
     * @param bool $hasavailability if the section has availability
     * @param bool $available if the section availability condition is available or not to the user
     * @return stdClass the scenario instances.
     */
    private function setup_hasrestrictions_scenario(
        string $format = 'topics',
        bool $addcanviewhidden = false,
        string $rolename = 'editingteacher',
        bool $hasavailability = false,
        bool $available = false
    ): stdClass {
        global $PAGE, $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 1, 'format' => $format]);

        // Create and enrol user.
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user(
            $user->id,
            $course->id,
            $rolename
        );
        $this->setUser($user);

        // Add capabilities if necessary.
        if ($addcanviewhidden) {
            $roleid = $DB->get_field('role', 'id', array('shortname' => $rolename), MUST_EXIST);
            $coursecontext = context_course::instance($course->id);
            assign_capability('moodle/course:viewhiddenactivities', CAP_ALLOW, $roleid, $coursecontext->id);
        }

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
            $modinfo = get_fast_modinfo($course);
            $sectioninfo = $modinfo->get_section_info(1);
            $selector = ['id' => $sectioninfo->id];
            $DB->set_field('course_sections', 'availability', trim($availabilityjson), $selector);
        }
        rebuild_course_cache($course->id, true);

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
            'Topics, can edit, has availability and is available' => [
                'format' => 'topics',
                'addcanviewhidden' => false,
                'rolename' => 'editingteacher',
                'hasavailability' => true,
                'available' => true,
                'expected' => true,
            ],
            'Topics, can edit, has availability and is not available' => [
                'format' => 'topics',
                'addcanviewhidden' => false,
                'rolename' => 'editingteacher',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Topics, can edit and has not availability' => [
                'format' => 'topics',
                'addcanviewhidden' => false,
                'rolename' => 'editingteacher',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
            // Teacher scenarios (weeks).
            'Weeks, can edit, has availability and is available' => [
                'format' => 'weeks',
                'addcanviewhidden' => false,
                'rolename' => 'editingteacher',
                'hasavailability' => true,
                'available' => true,
                'expected' => true,
            ],
            'Weeks, can edit, has availability and is not available' => [
                'format' => 'weeks',
                'addcanviewhidden' => false,
                'rolename' => 'editingteacher',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Weeks, can edit and has not availability' => [
                'format' => 'weeks',
                'addcanviewhidden' => false,
                'rolename' => 'editingteacher',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
            // Teacher scenarios (mock format).
            'Mock format, can edit, has availability and is available' => [
                'format' => 'theunittest',
                'addcanviewhidden' => false,
                'rolename' => 'editingteacher',
                'hasavailability' => true,
                'available' => true,
                'expected' => true,
            ],
            'Mock format, can edit, has availability and is not available' => [
                'format' => 'theunittest',
                'addcanviewhidden' => false,
                'rolename' => 'editingteacher',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Mock format, can edit and has not availability' => [
                'format' => 'theunittest',
                'addcanviewhidden' => false,
                'rolename' => 'editingteacher',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
            // Student scenarios (topics).
            'Topics, cannot edit, has availability and is available' => [
                'format' => 'topics',
                'addcanviewhidden' => false,
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => true,
                'expected' => false,
            ],
            'Topics, cannot edit, has availability and is not available' => [
                'format' => 'topics',
                'addcanviewhidden' => false,
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Topics, cannot edit and has not availability' => [
                'format' => 'topics',
                'addcanviewhidden' => false,
                'rolename' => 'student',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
            // Student scenarios (weeks).
            'Weeks, cannot edit, has availability and is available' => [
                'format' => 'weeks',
                'addcanviewhidden' => false,
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => true,
                'expected' => false,
            ],
            'Weeks, cannot edit, has availability and is not available' => [
                'format' => 'weeks',
                'addcanviewhidden' => false,
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Weeks, cannot edit and has not availability' => [
                'format' => 'weeks',
                'addcanviewhidden' => false,
                'rolename' => 'student',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
            // Student scenarios (mock format).
            'Mock format, cannot edit, has availability and is available' => [
                'format' => 'theunittest',
                'addcanviewhidden' => false,
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => true,
                'expected' => false,
            ],
            'Mock format, cannot edit, has availability and is not available' => [
                'format' => 'theunittest',
                'addcanviewhidden' => false,
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Mock format, cannot edit and has not availability' => [
                'format' => 'theunittest',
                'addcanviewhidden' => false,
                'rolename' => 'student',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
            // Students with view hidden activities capabilities (topics).
            'Topics, can view hidden but not edit, has availability and is available' => [
                'format' => 'topics',
                'addcanviewhidden' => true,
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => true,
                'expected' => false,
            ],
            'Topics, can view hidden but not edit, has availability and is not available' => [
                'format' => 'topics',
                'addcanviewhidden' => true,
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Topics, can view hidden but not edit and has not availability' => [
                'format' => 'topics',
                'addcanviewhidden' => true,
                'rolename' => 'student',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
            // Students with view hidden activities capabilities (weeks).
            'Weeks, can view hidden but not edit, has availability and is available' => [
                'format' => 'weeks',
                'addcanviewhidden' => true,
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => true,
                'expected' => false,
            ],
            'Weeks, can view hidden but not edit, has availability and is not available' => [
                'format' => 'weeks',
                'addcanviewhidden' => true,
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Weeks, can view hidden but not edit and has not availability' => [
                'format' => 'weeks',
                'addcanviewhidden' => true,
                'rolename' => 'student',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
            // Students with view hidden activities capabilities (mock format).
            'Mock format, can view hidden but not edit, has availability and is available' => [
                'format' => 'theunittest',
                'addcanviewhidden' => true,
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => true,
                'expected' => false,
            ],
            'Mock format, can view hidden but not edit, has availability and is not available' => [
                'format' => 'theunittest',
                'addcanviewhidden' => true,
                'rolename' => 'student',
                'hasavailability' => true,
                'available' => false,
                'expected' => true,
            ],
            'Mock format, can view hidden but not edit and has not availability' => [
                'format' => 'theunittest',
                'addcanviewhidden' => true,
                'rolename' => 'student',
                'hasavailability' => false,
                'available' => true,
                'expected' => false,
            ],
        ];
    }
}
