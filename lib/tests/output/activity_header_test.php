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

namespace core\output;

/**
 * Unit tests for activity header
 *
 * @package   core
 * @category  test
 * @copyright 2021 Peter
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_header_test extends \advanced_testcase {
    /**
     * Test the title setter
     *
     * @dataProvider test_set_title_provider
     * @param string $value
     * @param string $expected
     */
    public function test_set_title(string $value, string $expected): void {
        global $PAGE, $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => true]);
        $assign = $this->getDataGenerator()->create_module('assign', [
            'course' => $course->id,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'completionview' => 1
        ]);
        $this->setAdminUser();

        $cm = $DB->get_record('course_modules', ['id' => $assign->cmid]);
        $PAGE->set_cm($cm);
        $PAGE->set_activity_record($assign);

        $header = $PAGE->activityheader;
        $header->set_title($value);
        $data = $header->export_for_template($PAGE->get_renderer('core'));
        $this->assertEquals($expected, $data['title']);
    }

    /**
     * Provider for the test_set_title unit test.
     * @return array
     */
    public function test_set_title_provider(): array {
        return [
            "Set the title with a plain text" => [
                "Activity title", "Activity title"
            ],
            "Set the title with a string with standard header tags" => [
                "<h2>Activity title</h2>", "Activity title"
            ],
            "Set the title with a string with multiple header content" => [
                "<h2 id='heading'>Activity title</h2><h2>Header 2</h2>", "Activity title</h2><h2>Header 2"
            ],
        ];
    }
}
