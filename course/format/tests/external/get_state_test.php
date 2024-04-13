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

namespace core_courseformat\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use core_external\external_api;

/**
 * Tests for the get_state class.
 *
 * @package    core_course
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_courseformat\external\get_state
 */
class get_state_test extends \externallib_advanced_testcase {

    /** @var array Sections in the testing course. */
    private $sections;

    /** @var array Activities in the testing course. */
    private $activities;

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
     * Setup testcase.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        $this->sections = [];
        $this->activities = [];
    }

    /**
     * Test tearDown.
     */
    public function tearDown(): void {
        unset($this->sections);
        unset($this->activities);
        parent::tearDown();
    }

    /**
     * Test the behaviour of get_state::execute().
     *
     * @dataProvider get_state_provider
     * @covers ::execute
     *
     * @param string $role The role of the user that will execute the method.
     * @param string $format The course format of the course where the method will be executed.
     * @param string|null $expectedexception If this call will raise an exception, this is its name.
     */
    public function test_get_state(string $role, string $format = 'topics', ?string $expectedexception = null): void {
        $this->resetAfterTest();

        // Create a course.
        $numsections = 6;
        $visiblesections = $numsections + 1; // Include topic 0.
        $course = $this->getDataGenerator()->create_course(['numsections' => $numsections, 'format' => $format]);
        $hiddensections = [4, 6];
        foreach ($hiddensections as $section) {
            set_section_visible($course->id, $section, 0);
        }

        // Create and enrol user.
        $isadmin = ($role == 'admin');
        $canedit = $isadmin || ($role == 'editingteacher');
        if ($isadmin) {
            $this->setAdminUser();
        } else {
            if (!$canedit) {
                // User won't see the hidden sections. Remove them from the total.
                $visiblesections = $visiblesections - count($hiddensections);
            }
            $user = $this->getDataGenerator()->create_user();
            if ($role != 'unenroled') {
                $this->getDataGenerator()->enrol_user($user->id, $course->id, $role);
            }
            $this->setUser($user);
        }

        // Social course format automatically creates a forum activity.
        if (course_get_format($course)->get_format() === 'social') {
            $cms = get_fast_modinfo($course)->get_cms();

            // Let's add this assertion just to ensure course format has only one activity.
            $this->assertCount(1, $cms);
            $activitycm = reset($cms);

            // And that activity is a forum.
            $this->assertEquals('forum', $activitycm->modname);

            // Assign the activity cm to the activities array.
            $this->activities[$activitycm->id] = $activitycm;
        } else {
            // Add some activities to the course.
            $this->create_activity($course->id, 'page', 1, true, $canedit);
            $this->create_activity($course->id, 'forum', 1, true, $canedit);
            $this->create_activity($course->id, 'book', 1, false, $canedit);
            $this->create_activity($course->id, 'assign', 2, false, $canedit);
            $this->create_activity($course->id, 'glossary', 4, true, $canedit);
            $this->create_activity($course->id, 'label', 5, false, $canedit);
            $this->create_activity($course->id, 'feedback', 5, true, $canedit);
        }

        if ($expectedexception) {
            $this->expectException($expectedexception);
        }

        // Get course state.
        $result = get_state::execute($course->id);
        $result = external_api::clean_returnvalue(get_state::execute_returns(), $result);
        $result = json_decode($result);
        if ($format == 'theunittest') {
            // These course format's hasn't the renderer file, so a debugging message will be displayed.
            $this->assertDebuggingCalled();
        }

        // Check course information.
        $this->assertEquals($numsections, $result->course->numsections);
        $this->assertCount($visiblesections, $result->section);
        $this->assertCount(count($this->activities), $result->cm);
        $this->assertCount(count($result->course->sectionlist), $result->section);
        if ($format == 'theunittest') {
            $this->assertTrue(property_exists($result->course, 'newfancyelement'));
        } else {
            $this->assertFalse(property_exists($result->course, 'newfancyelement'));
        }

        // Check sections information.
        foreach ($result->section as $section) {
            if (in_array($section->number, $hiddensections)) {
                $this->assertFalse($section->visible);
            } else {
                $this->assertTrue($section->visible);
            }
            // Check section is defined in course->sectionlist.
            $this->assertContains($section->id, $result->course->sectionlist);
            // Check course modules list for this section is the expected.
            if (array_key_exists($section->number, $this->sections)) {
                $this->assertEquals($this->sections[$section->number], $section->cmlist);
            }
        }
        // Check course modules information.
        foreach ($result->cm as $cm) {
            $this->assertEquals($this->activities[$cm->id]->name, $cm->name);
            $this->assertEquals((bool) $this->activities[$cm->id]->visible, $cm->visible);
        }
    }

    /**
     * Data provider for test_get_state().
     *
     * @return array
     */
    public static function get_state_provider(): array {
        return [
            // ROLES. Testing behaviour depending on the user role calling the method.
            'Admin user should work' => [
                'role' => 'admin',
            ],
            'Editing teacher should work' => [
                'role' => 'editingteacher',
            ],
            'Student should work' => [
                'role' => 'student',
            ],
            'Unenroled user should raise an exception' => [
                'role' => 'unenroled',
                'format' => 'topics',
                'expectedexception' => 'moodle_exception',
            ],

            // COURSEFORMAT. Test behaviour depending on course formats.
            'Single activity format should work (admin)' => [
                'role' => 'admin',
                'format' => 'singleactivity',
            ],
            'Social format should work (admin)' => [
                'role' => 'admin',
                'format' => 'social',
            ],
            'Weeks format should work (admin)' => [
                'role' => 'admin',
                'format' => 'weeks',
            ],
            'The unit tests format should work (admin)' => [
                'role' => 'admin',
                'format' => 'theunittest',
            ],
            'Single activity format should work (student)' => [
                'role' => 'student',
                'format' => 'singleactivity',
            ],
            'Social format should work (student)' => [
                'role' => 'student',
                'format' => 'social',
            ],
            'Weeks format should work (student)' => [
                'role' => 'student',
                'format' => 'weeks',
            ],
            'The unit tests format should work (student)' => [
                'role' => 'student',
                'format' => 'theunittest',
            ],
            'Single activity format should raise an exception (unenroled)' => [
                'role' => 'unenroled',
                'format' => 'singleactivity',
                'expectedexception' => 'moodle_exception',
            ],
            'Social format should raise an exception (unenroled)' => [
                'role' => 'unenroled',
                'format' => 'social',
                'expectedexception' => 'moodle_exception',
            ],
            'Weeks format should raise an exception (unenroled)' => [
                'role' => 'unenroled',
                'format' => 'weeks',
                'expectedexception' => 'moodle_exception',
            ],
            'The unit tests format should raise an exception (unenroled)' => [
                'role' => 'unenroled',
                'format' => 'theunittest',
                'expectedexception' => 'moodle_exception',
            ],
        ];
    }

    /**
     * Helper method to create an activity into a section and add it to the $sections and $activities arrays.
     * For non-admin users, only visible activities will be added to the activities and sections arrays.
     *
     * @param int $courseid Course identifier where the activity will be added.
     * @param string $type Activity type ('forum', 'assign', ...).
     * @param int $section Section number where the activity will be added.
     * @param bool $visible Whether the activity will be visible or not.
     * @param bool $canedit Whether the activity will be accessed later by a user with editing capabilities
     */
    private function create_activity(int $courseid, string $type, int $section, bool $visible = true, bool $canedit = true): void {
        $activity = $this->getDataGenerator()->create_module(
            $type,
            ['course' => $courseid],
            ['section' => $section, 'visible' => $visible]
        );

        list(, $activitycm) = get_course_and_cm_from_instance($activity->id, $type);

        if ($visible || $canedit) {
            $this->activities[$activitycm->id] = $activitycm;
            $this->sections[$section][] = $activitycm->id;
        }
    }
}
