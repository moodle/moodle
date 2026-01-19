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
 * Class containing unit tests for the migrate subsection descriptions task.
 *
 * @package   mod_subsection
 * @copyright 2025 Sara Arjona <sara@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(migrate_subsection_descriptions_task::class)]
final class migrate_subsection_descriptions_task_test extends \advanced_testcase {
    /**
     * Test migrate_subsection_descriptions task.
     */
    public function test_migrate_subsection_descriptions(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 1]);
        // Add subsection with description.
        $summarytext = 'Section with description';
        $this->getDataGenerator()->create_module(
            'subsection',
            ['course' => $course->id, 'section' => 1, 'summary' => $summarytext],
        );
        // Add forum to the subsection to test the order of the modules is preserved.
        $this->getDataGenerator()->create_module(
            'forum',
            [
                'course' => $course->id,
                'name' => 'Forum in subsection',
                'section' => 2,
            ],
        );
        // Add another subsection without description.
        $this->getDataGenerator()->create_module('subsection', ['course' => $course->id, 'section' => 1]);

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
        $this->assertEquals(
            0,
            $DB->count_records_select(
                'label',
                'course = :courseid',
                ['courseid' => $course->id],
            ),
        );
        $cms = get_fast_modinfo($course->id)->get_cms();
        // Check that the activities are in the expected initial order.
        $this->assertEquals(
            [
                'Subsection 1',
                'Subsection 2',
                'Forum in subsection',
            ],
            array_values(array_map(fn($cminfo) => $cminfo->name, $cms))
        );

        // Run the task.
        $task = new migrate_subsection_descriptions_task();
        \core\task\manager::queue_adhoc_task($task);
        ob_start();
        $this->runAdhocTasks(migrate_subsection_descriptions_task::class);
        $output = ob_get_contents();
        ob_end_clean();

        // Check one subsection migrated message shown.
        $this->assertStringContainsString(
            'Subsection descriptions migration task completed. Total migrated subsections: 1',
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
        // Check text&media module created for the migrated description.
        $this->assertEquals(
            1,
            $DB->count_records_select(
                'label',
                'course = :courseid',
                ['courseid' => $course->id],
            ),
        );
        $this->assertEquals(
            $summarytext,
            $DB->get_field_select(
                'label',
                'intro',
                'course = :courseid',
                ['courseid' => $course->id],
            ),
        );
        $cms = get_fast_modinfo($course->id)->get_cms();
        // Check that the label is in the expected position.
        $this->assertEquals(
            [
                'Subsection 1',
                'Subsection 2',
                'label',
                'Forum in subsection',
            ],
            array_values(array_map(fn($cminfo) => $cminfo->name, $cms))
        );

        // Check no subsections left to migrate.
        $task = new migrate_subsection_descriptions_task();
        \core\task\manager::queue_adhoc_task($task);
        ob_start();
        $this->runAdhocTasks(migrate_subsection_descriptions_task::class);
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertStringContainsString(
            'No subsection descriptions found to migrate.',
            trim($output),
        );
    }

    /**
     * Test migrate_subsection_descriptions task with attached files.
     */
    public function test_migrate_subsection_descriptions_with_files(): void {
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

        // Check subsection has description with file, and there is no label.
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
                'label',
                'course = :courseid',
                ['courseid' => $course->id],
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
        $task = new migrate_subsection_descriptions_task();
        \core\task\manager::queue_adhoc_task($task);
        ob_start();
        $this->runAdhocTasks(migrate_subsection_descriptions_task::class);
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
        // Check text&media module created for the migrated description.
        $this->assertEquals(
            1,
            $DB->count_records_select(
                'label',
                'course = :courseid',
                ['courseid' => $course->id],
            ),
        );
        $this->assertEquals(
            $summarytext,
            $DB->get_field_select(
                'label',
                'intro',
                'course = :courseid',
                ['courseid' => $course->id],
            ),
        );
        // Check the file has been migrated too.
        $this->assertEquals(
            1,
            $DB->count_records_select(
                'files',
                'component = :component  AND filearea = :filearea AND filename = :filename',
                [
                    'component' => 'mod_label',
                    'filearea' => 'intro',
                    'filename' => 'intro.txt',
                ],
            ),
        );
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
     * Test migrate_subsection_descriptions task when label or subsection module is not enabled.
     */
    public function test_migrate_subsection_descriptions_modules_not_enabled(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 1]);
        // Add subsection with description.
        $this->getDataGenerator()->create_module(
            'subsection',
            ['course' => $course->id, 'section' => 1, 'summary' => 'Summary text'],
        );

        // Check subsection has description.
        $this->assertEquals(
            1,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid AND component = \'mod_subsection\' AND summary != \'\'',
                ['courseid' => $course->id],
            ),
        );
        $this->assertEquals(
            0,
            $DB->count_records_select(
                'label',
                'course = :courseid',
                ['courseid' => $course->id],
            ),
        );
        // Disable label module.
        \core\plugininfo\mod::enable_plugin('label', 0);

        // Run the task.
        $task = new migrate_subsection_descriptions_task();
        \core\task\manager::queue_adhoc_task($task);
        ob_start();
        $this->runAdhocTasks(migrate_subsection_descriptions_task::class);
        $output = ob_get_contents();
        ob_end_clean();

        // Check one subsection migrated message shown.
        $this->assertStringContainsString(
            'Text and media area or Subsection module is not enabled. Skipping migration task.',
            trim($output),
        );

        // Check nothing has changed.
        $this->assertEquals(
            1,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid AND component = \'mod_subsection\' AND summary != \'\'',
                ['courseid' => $course->id],
            ),
        );
        $this->assertEquals(
            0,
            $DB->count_records_select(
                'label',
                'course = :courseid',
                ['courseid' => $course->id],
            ),
        );

        // Enable label and disable subsection module.
        \core\plugininfo\mod::enable_plugin('label', 1);
        \core\plugininfo\mod::enable_plugin('subsection', 0);

        // Run the task.
        ob_start();
        $task = new migrate_subsection_descriptions_task();
        $task->execute();
        ob_end_clean();

        // Check nothing has changed.
        $this->assertEquals(
            1,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid  AND component = \'mod_subsection\' AND summary != \'\'',
                ['courseid' => $course->id],
            ),
        );
        $this->assertEquals(
            0,
            $DB->count_records_select(
                'label',
                'course = :courseid',
                ['courseid' => $course->id],
            ),
        );
    }

    /**
     * Test migrate_subsection_descriptions task reschedule when more than 100 subsections to process.
     */
    public function test_migrate_subsection_descriptions_rescheduletask(): void {
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
        $this->assertEquals(
            0,
            $DB->count_records_select(
                'label',
                'course = :courseid',
                ['courseid' => $course->id],
            ),
        );

        // Run the task.
        $task = new migrate_subsection_descriptions_task();
        \core\task\manager::queue_adhoc_task($task);
        ob_start();
        $this->runAdhocTasks(migrate_subsection_descriptions_task::class);
        $output = ob_get_contents();
        ob_end_clean();

        // Check subsection migrated message shown.
        $this->assertStringContainsString(
            'Subsection descriptions migration task completed. Total migrated subsections: 100',
            trim($output),
        );
        $this->assertStringContainsString(
            'Subsection descriptions migration task pending subsections: 1. Scheduled new ad-hoc task.',
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
        // Check text&media module created for the migrated description subsections.
        $this->assertEquals(
            100,
            $DB->count_records_select(
                'label',
                'course = :courseid',
                ['courseid' => $course->id],
            ),
        );

        // Re-run the task to process the remaining subsection (it should have been queued by the previous run).
        ob_start();
        $this->runAdhocTasks(migrate_subsection_descriptions_task::class);
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertStringContainsString(
            'Subsection descriptions migration task completed. Total migrated subsections: 1',
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
        // Check text&media module created for the migrated description subsections.
        $this->assertEquals(
            101,
            $DB->count_records_select(
                'label',
                'course = :courseid',
                ['courseid' => $course->id],
            ),
        );
    }
}
