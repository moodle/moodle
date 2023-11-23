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

namespace core_backup;

use backup;

/**
 * Tests for Moodle 2 restore steplib classes.
 *
 * @package core_backup
 * @copyright 2023 Ferran Recio <ferran@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_stepslib_test extends \advanced_testcase {
    /**
     * Setup to include all libraries.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        require_once($CFG->dirroot . '/backup/moodle2/restore_stepslib.php');
    }

    /**
     * Makes a backup of the course.
     *
     * @param \stdClass $course The course object.
     * @return string Unique identifier for this backup.
     */
    protected function backup_course(\stdClass $course): string {
        global $CFG, $USER;

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        // Do backup with default settings. MODE_IMPORT means it will just
        // create the directory and not zip it.
        $bc = new \backup_controller(
            backup::TYPE_1COURSE,
            $course->id,
            backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO,
            backup::MODE_IMPORT,
            $USER->id
        );
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        return $backupid;
    }

    /**
     * Restores a backup that has been made earlier.
     *
     * @param string $backupid The unique identifier of the backup.
     * @return int The new course id.
     */
    protected function restore_replacing_content(string $backupid): int {
        global $CFG, $USER;

        // Create course to restore into, and a user to do the restore.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        // Do restore to new course with default settings.
        $rc = new \restore_controller(
            $backupid,
            $course->id,
            backup::INTERACTIVE_NO,
            backup::MODE_GENERAL,
            $USER->id,
            backup::TARGET_EXISTING_DELETING
        );

        $precheck = $rc->execute_precheck();
        $this->assertTrue($precheck);
        $rc->get_plan()->get_setting('role_assignments')->set_value(true);
        $rc->get_plan()->get_setting('permissions')->set_value(true);
        $rc->execute_plan();
        $rc->destroy();

        return $course->id;
    }

    /**
     * Test for the section structure step included elements.
     *
     * @covers \restore_section_structure_step::process_section
     */
    public function test_restore_section_structure_step(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['numsections' => 2, 'format' => 'topics']);
        // Section 2 has an existing delegate class.
        course_update_section(
            $course,
            $DB->get_record('course_sections', ['course' => $course->id, 'section' => 2]),
            [
                'component' => 'test_component',
                'itemid' => 1,
            ]
        );

        $backupid = $this->backup_course($course);
        $newcourseid = $this->restore_replacing_content($backupid);

        $originalsections = get_fast_modinfo($course->id)->get_section_info_all();
        $restoredsections = get_fast_modinfo($newcourseid)->get_section_info_all();

        $this->assertEquals(count($originalsections), count($restoredsections));

        $validatefields = ['name', 'summary', 'summaryformat', 'visible', 'component', 'itemid'];

        $this->assertEquals($originalsections[1]->name, $restoredsections[1]->name);

        foreach ($validatefields as $field) {
            $this->assertEquals($originalsections[1]->$field, $restoredsections[1]->$field);
            $this->assertEquals($originalsections[2]->$field, $restoredsections[2]->$field);
        }

    }
}
