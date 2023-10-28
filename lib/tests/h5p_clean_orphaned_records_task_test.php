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
 * Class containing unit tests for the task to clean orphaned h5p records.
 *
 * @package   core
 * @copyright  2021 Ilya Tregubov <ilya@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class h5p_clean_orphaned_records_task_test extends advanced_testcase {

    /**
     * Test task execution
     *
     * return void
     */
    public function test_task_execution(): void {
        global $CFG, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        $params = [
            'course' => $course->id,
            'packagefilepath' => $CFG->dirroot.'/h5p/tests/fixtures/greeting-card.h5p',
            'introformat' => 1
        ];

        // Create h5pactivity.
        $activity = $this->getDataGenerator()->create_module('h5pactivity', $params);
        $activity->filename = 'greeting-card.h5p';
        $context = context_module::instance($activity->cmid);

        // Create a fake deploy H5P file.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        $generator->create_export_file($activity->filename, $context->id,
            'mod_h5pactivity', 'package');

        // Delete activity.
        course_delete_module($activity->cmid);

        $orphanedh5psql = "SELECT h5p.id, h5p.pathnamehash
                             FROM {h5p} h5p
                        LEFT JOIN {files} f ON f.pathnamehash = h5p.pathnamehash
                            WHERE f.pathnamehash IS NULL";
        $orphanedh5p = $DB->get_records_sql($orphanedh5psql);
        $this->assertEquals(1, count($orphanedh5p));

        $h5pid = end($orphanedh5p)->id;
        $orphanedfilessql = "SELECT id
                               FROM {files}
                              WHERE itemid = :h5pid
                                AND filearea = 'content'
                                AND component = 'core_h5p'";
        $orphanedfiles = $DB->get_records_sql($orphanedfilessql, ['h5pid' => $h5pid]);
        $this->assertEquals(2, count($orphanedfiles));

        // Execute task.
        $task = new \core\task\h5p_clean_orphaned_records_task();
        $task->execute();

        $orphanedh5p = $DB->get_record_sql($orphanedh5psql);
        $this->assertFalse($orphanedh5p);

        $orphanedfiles = $DB->get_record_sql($orphanedfilessql, ['h5pid' => $h5pid]);
        $this->assertFalse($orphanedfiles);
    }
}
