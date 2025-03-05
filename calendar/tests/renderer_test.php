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

namespace core_calendar;

/**
 * Renderer testcase.
 *
 * @covers \core_calendar_renderer
 * @package core_calendar
 * @copyright 2025 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class renderer_test extends \advanced_testcase {
    /**
     * Tests {@see \core_calendar_renderer::course_filter_selector()} shows course names correctly
     * depending on admin settings.
     */
    public function test_course_filter_selector_names(): void {
        global $PAGE, $CFG;

        require_once($CFG->dirroot . '/calendar/lib.php');
        $this->resetAfterTest();

        // Create 2 courses.
        $generator = self::getDataGenerator();
        $course1 = $generator->create_course(['shortname' => 'C1', 'fullname' => 'Course 1']);
        $course2 = $generator->create_course(['shortname' => 'C2', 'fullname' => 'Course 2']);

        // User is enrolled in both courses.
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course1->id, 'teacher');
        $generator->enrol_user($user->id, $course2->id, 'teacher');

        // Get course selector for user.
        $this->setUser($user);
        $renderer = $PAGE->get_renderer('core_calendar');
        $html = $renderer->course_filter_selector(new \moodle_url('/'));

        // It should contain courses by fullname.
        $this->assertStringContainsString(
            '<option value="' . $course1->id . '">Course 1</option>',
            $html,
        );
        $this->assertStringContainsString(
            '<option value="' . $course2->id . '">Course 2</option>',
            $html,
        );

        // Turn on the option to show shortnames as well.
        set_config('courselistshortnames', true);

        $html = $renderer->course_filter_selector(new \moodle_url('/'));

        // It should contain courses by fullname and shortname.
        $this->assertStringContainsString(
            '<option value="' . $course1->id . '">C1 Course 1</option>',
            $html,
        );
        $this->assertStringContainsString(
            '<option value="' . $course2->id . '">C2 Course 2</option>',
            $html,
        );
    }
}
