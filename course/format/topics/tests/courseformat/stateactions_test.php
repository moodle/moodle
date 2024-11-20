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

namespace format_topics\courseformat;

use core_courseformat\stateupdates;
use moodle_exception;
use stdClass;

/**
 * Topics course format related unit tests.
 *
 * @package    format_topics
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stateactions_test extends \advanced_testcase {

    /**
     * Enrol a user into a course and login as this user.
     *
     * @param stdClass $course the course object
     * @param string $rolename the rolename
     */
    private function enrol_user(stdClass $course, string $rolename): void {
        // Create and enrol user using given role.
        if ($rolename == 'admin') {
            $this->setAdminUser();
        } else {
            $user = $this->getDataGenerator()->create_user();
            if ($rolename != 'unenroled') {
                $this->getDataGenerator()->enrol_user($user->id, $course->id, $rolename);
            }
            $this->setUser($user);
        }
    }

    /**
     * Tests for section_highlight method.
     *
     * @dataProvider basic_role_provider
     * @covers ::section_highlight
     * @param string $rolename The role of the user that will execute the method.
     * @param bool $expectedexception If this call will raise an exception.
     */
    public function test_section_highlight(string $rolename, bool $expectedexception = false): void {
        global $DB;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
            ['numsections' => 4, 'format' => 'topics'],
            ['createsections' => true]
        );

        $this->enrol_user($course, $rolename);

        $sectionrecords = $DB->get_records('course_sections', ['course' => $course->id], 'section');
        $sectionids = [];
        foreach ($sectionrecords as $section) {
            $sectionids[] = $section->id;
        }

        // Initialise stateupdates.
        $courseformat = course_get_format($course->id);
        $updates = new stateupdates($courseformat);

        // All state actions accepts batch editing (an array of sections in this case). However,
        // only one course section can be marked as highlighted. This means that if we send more
        // than one section id only the first one will be highlighted and the rest will be ignored.
        $methodparam = [
            $sectionids[1],
            $sectionids[2],
            $sectionids[3],
        ];

        // Actions have an array of ids as param but only the first one will be highlighted.
        $highlightid = reset($methodparam);
        $highlight = $sectionrecords[$highlightid];

        if ($expectedexception) {
            $this->expectException(moodle_exception::class);
        }

        // Execute given method.
        $actions = new stateactions();
        $actions->section_highlight(
            $updates,
            $course,
            $methodparam
        );

        // Check state returned after executing given action.
        $updatelist = $updates->jsonSerialize();
        $this->assertCount(1, $updatelist);
        $update = reset($updatelist);
        $this->assertEquals('section', $update->name);
        $this->assertEquals('put', $update->action);
        $this->assertEquals($highlightid, $update->fields->id);
        $this->assertEquals(1, $update->fields->current);

        // Check DB sections.
        $this->assertEquals($highlight->section, $DB->get_field("course", "marker", ['id' => $course->id]));
    }

    /**
     * Tests for section_unhighlight method.
     *
     * @dataProvider basic_role_provider
     * @covers ::section_unhighlight
     * @param string $rolename The role of the user that will execute the method.
     * @param bool $expectedexception If this call will raise an exception.
     */
    public function test_section_unhighlight(string $rolename, bool $expectedexception = false): void {
        global $DB;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
            ['numsections' => 4, 'format' => 'topics'],
            ['createsections' => true]
        );

        // Highlight section 1.
        course_set_marker($course->id, 1);

        $this->enrol_user($course, $rolename);

        $sectionrecords = $DB->get_records('course_sections', ['course' => $course->id], 'section');
        $sectionids = [];
        foreach ($sectionrecords as $section) {
            $sectionids[] = $section->id;
        }

        // Initialise stateupdates.
        $courseformat = course_get_format($course->id);
        $updates = new stateupdates($courseformat);

        // The section_unhighlight accepts extra sections to refresh the state data.
        $methodparam = [
            $sectionids[3],
            $sectionids[4],
        ];

        if ($expectedexception) {
            $this->expectException(moodle_exception::class);
        }

        // Execute given method.
        $actions = new stateactions();
        $actions->section_unhighlight(
            $updates,
            $course,
            $methodparam
        );

        // The Unhilight mutation always return the previous highlighted
        // section (1) and all the extra sections passed (3, and 4) to ensure
        // all of them are updated.
        $returnedsectionnumbers = [1, 3, 4];

        // Check state returned after executing given action.
        $updatelist = $updates->jsonSerialize();
        $this->assertCount(3, $updatelist);
        foreach ($updatelist as $update) {
            $this->assertEquals('section', $update->name);
            $this->assertEquals('put', $update->action);
            $this->assertContains($update->fields->number, $returnedsectionnumbers);
            $this->assertEquals(0, $update->fields->current);
        }

        // Check DB sections.
        $this->assertEquals(0, $DB->get_field("course", "marker", ['id' => $course->id]));
    }

    /**
     * Data provider for basic role tests.
     *
     * @return array the testing scenarios
     */
    public function basic_role_provider(): array {
        return [
            'admin' => [
                'role' => 'admin',
                'expectedexception' => false,
            ],
            'editingteacher' => [
                'role' => 'editingteacher',
                'expectedexception' => false,
            ],
            'teacher' => [
                'role' => 'teacher',
                'expectedexception' => true,
            ],
            'student' => [
                'role' => 'student',
                'expectedexception' => true,
            ],
            'guest' => [
                'role' => 'guest',
                'expectedexception' => true,
            ],
            'unenroled' => [
                'role' => 'unenroled',
                'expectedexception' => true,
            ],
        ];
    }
}
