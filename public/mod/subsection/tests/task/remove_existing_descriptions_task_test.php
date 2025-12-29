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

namespace mod_subsection\task;

/**
 * Class containing unit tests for the remove existing descriptions task.
 *
 * @package   mod_subsection
 * @copyright 2025 Sara Arjona <sara@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(remove_existing_descriptions_task::class)]
final class remove_existing_descriptions_task_test extends \advanced_testcase {
    /**
     * Test remove_existing_descriptions task.
     */
    public function test_remove_existing_descriptions(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 1]);
        $this->getDataGenerator()->create_module('subsection', ['course' => $course->id, 'section' => 1]);
        // Add description to course sections and the subsection.
        $DB->set_field(
            'course_sections',
            'summary',
            'Section with description',
            ['course' => $course->id],
        );
        // Add another subsection without description.
        $this->getDataGenerator()->create_module('subsection', ['course' => $course->id, 'section' => 1]);

        // Check only 2 sections and 1 subsection have description.
        $this->assertEquals(
            3,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid AND summary != \'\'',
                ['courseid' => $course->id],
            ),
        );
        $this->assertEquals(
            2,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid AND component = \'mod_subsection\'',
                ['courseid' => $course->id],
            ),
        );
        $this->assertEquals(
            1,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid  AND component = \'mod_subsection\' AND summary != \'\'',
                ['courseid' => $course->id],
            ),
        );

        // Run the task.
        ob_start();
        $task = new remove_existing_descriptions_task();
        $task->execute();
        ob_end_clean();

        // Check only 2 sections keep having description after running the task.
        $this->assertEquals(
            2,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid AND summary != \'\'',
                ['courseid' => $course->id],
            ),
        );
        // Check no subsection has description after running the task.
        $this->assertEquals(
            0,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid  AND component = \'mod_subsection\' AND summary != \'\'',
                ['courseid' => $course->id],
            ),
        );
    }
}
