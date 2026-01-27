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
 * Class containing unit tests for the remove existing subsection descriptions task.
 *
 * @package   mod_subsection
 * @copyright 2026 Sara Arjona <sara@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(remove_subsection_descriptions_task::class)]
final class remove_subsection_descriptions_task_test extends \advanced_testcase {
    /**
     * Test remove_subsection_descriptions task.
     */
    public function test_remove_subsection_descriptions(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 1]);
        // Add subsection with description.
        $summarytext = 'Section with description';
        $this->getDataGenerator()->create_module(
            'subsection',
            ['course' => $course->id, 'section' => 1, 'summary' => $summarytext],
        );
        // Add another subsection without description.
        $this->getDataGenerator()->create_module('subsection', ['course' => $course->id, 'section' => 1]);

        // Check only 1 subsection has description.
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
        $task = new remove_subsection_descriptions_task();
        \core\task\manager::queue_adhoc_task($task);
        ob_start();
        $this->runAdhocTasks(remove_subsection_descriptions_task::class);
        $output = ob_get_contents();
        ob_end_clean();

        // Check one subsection removed message shown.
        $this->assertStringContainsString(
            'Subsection descriptions removal task completed. Total removed subsection descriptions: 1',
            trim($output),
        );
        // Check no subsection has description after running the task.
        $this->assertEquals(
            0,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid AND component = \'mod_subsection\' AND summary != \'\'',
                ['courseid' => $course->id],
            ),
        );

        // Check no subsections left to remove.
        $task = new remove_subsection_descriptions_task();
        \core\task\manager::queue_adhoc_task($task);
        ob_start();
        $this->runAdhocTasks(remove_subsection_descriptions_task::class);
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertStringContainsString(
            'No subsection descriptions found to remove.',
            trim($output),
        );
    }

    /**
     * Test remove_subsection_descriptions task with attached files.
     */
    public function test_remove_subsection_descriptions_with_files(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 1]);
        // Add subsection with file in the description.
        $summarytext = 'Subsection text with <a href="@@PLUGINFILE@@/intro.txt">link</a>';
        $this->getDataGenerator()->create_module(
            'subsection',
            ['course' => $course->id, 'section' => 1, 'summary' => $summarytext],
        );
        $subsection = $DB->get_record(
            'course_sections',
            ['course' => $course->id, 'section' => 2],
        );
        $filerecord = [
            'component' => 'course',
            'filearea' => 'section',
            'contextid' => \context_course::instance($course->id)->id,
            'itemid' => $subsection->id,
            'filename' => 'intro.txt',
            'filepath' => '/',
        ];
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecord, 'Test intro file');

        // Check subsection has description with file.
        $this->assertEquals(
            1,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid AND component = \'mod_subsection\' AND summary != \'\'',
                ['courseid' => $course->id],
            ),
        );
        $this->assertEquals(
            1,
            $DB->count_records_select(
                'files',
                'component = :component AND filearea = :filearea AND filename = :filename',
                [
                    'component' => 'course',
                    'filearea' => 'section',
                    'filename' => 'intro.txt',
                ],
            ),
        );
        $this->assertEquals(
            0,
            $DB->count_records_select(
                'files',
                'component = :component AND filearea = :filearea AND filename = :filename',
                [
                    'component' => 'mod_label',
                    'filearea' => 'intro',
                    'filename' => 'intro.txt',
                ],
            ),
        );

        // Run the task.
        $task = new remove_subsection_descriptions_task();
        \core\task\manager::queue_adhoc_task($task);
        ob_start();
        $this->runAdhocTasks(remove_subsection_descriptions_task::class);
        ob_end_clean();

        // Check no subsection has description after running the task.
        $this->assertEquals(
            0,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid AND component = \'mod_subsection\' AND summary != \'\'',
                ['courseid' => $course->id],
            ),
        );

        // Check the file has been removed too.
        $this->assertEquals(
            0,
            $DB->count_records_select(
                'files',
                'component = :component  AND filearea = :filearea AND filename = :filename',
                [
                    'component' => 'course',
                    'filearea' => 'section',
                    'filename' => 'intro.txt',
                ],
            ),
        );
    }

    /**
     * Test remove_subsection_descriptions task reschedule when more than 100 subsections to process.
     */
    public function test_remove_subsection_descriptions_rescheduletask(): void {
        global $DB;

        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 1]);
        // Add subsections with description.
        for ($i = 0; $i < 101; $i++) {
            $this->getDataGenerator()->create_module(
                'subsection',
                ['course' => $course->id, 'section' => 1, 'summary' => 'Summary text'],
            );
        }

        // Check all subsections have description.
        $this->assertEquals(
            101,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid AND component = \'mod_subsection\'',
                ['courseid' => $course->id],
            ),
        );

        // Run the task.
        $task = new remove_subsection_descriptions_task();
        \core\task\manager::queue_adhoc_task($task);
        ob_start();
        $this->runAdhocTasks(remove_subsection_descriptions_task::class);
        $output = ob_get_contents();
        ob_end_clean();

        // Check subsection removed message shown.
        $this->assertStringContainsString(
            'Subsection descriptions removal task completed. Total removed subsection descriptions: 100',
            trim($output),
        );
        $this->assertStringContainsString(
            'Subsection descriptions removal task pending subsections: 1. Scheduled new ad-hoc task.',
            trim($output),
        );
        // Check only 1 subsection keep having description after running the task.
        $this->assertEquals(
            1,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid AND component = \'mod_subsection\' AND summary != \'\'',
                ['courseid' => $course->id],
            ),
        );

        // Re-run the task to process the remaining subsection (it should have been queued by the previous run).
        ob_start();
        $this->runAdhocTasks(remove_subsection_descriptions_task::class);
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertStringContainsString(
            'Subsection descriptions removal task completed. Total removed subsection descriptions: 1',
            trim($output),
        );
        // Check no subsections keep having description after running the task.
        $this->assertEquals(
            0,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid AND component = \'mod_subsection\' AND summary != \'\'',
                ['courseid' => $course->id],
            ),
        );
    }
}
