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

namespace core_course;
use backup;

/**
 * Restore date tests.
 *
 * @package   core_course
 * @copyright 2022 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \backup_module_structure_step
 * @covers \restore_module_structure_step
 */
class backup_restore_activity_test extends \advanced_testcase {

    /**
     * Test that duplicating a page preserves the lang setting.
     */
    public function test_duplicating_page_preserves_lang() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Make a test course.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Create a page with forced language set.
        $page = $generator->create_module('page', ['course' => $course->id, 'lang' => 'en']);

        // Duplicate the page.
        $newpagecm = duplicate_module($course, get_fast_modinfo($course)->get_cm($page->cmid));

        // Verify the settings of the duplicated activity.
        $this->assertEquals('en', $newpagecm->lang);
    }

    public function test_activity_forced_lang_not_restored_without_capability() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Make a test course.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Create a page with forced language set.
        $generator->create_module('page', ['course' => $course->id, 'lang' => 'en']);

        // Backup the course.
        $backupid = $this->backup_course($course);

        // Create a manger user without 'moodle/course:setforcedlanguage' to do the restore.
        $manager = $generator->create_user();
        $generator->role_assign('manager', $manager->id);
        role_change_permission($DB->get_field('role', 'id', ['shortname' => 'manager'], MUST_EXIST),
                \context_system::instance(), 'moodle/course:setforcedlanguage', CAP_INHERIT);
        $this->setUser($manager);

        // Restore the course.
        $newcourseid = $this->restore_course($backupid);

        // Verify the settings of the duplicated activity.
        $newmodinfo = get_fast_modinfo($newcourseid);
        $newcms = $newmodinfo->instances['page'];
        $newpagecm = reset($newcms);
        $this->assertNull($newpagecm->lang);
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
        $bc = new \backup_controller(backup::TYPE_1COURSE, $course->id,
                backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
                $USER->id);
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
    protected function restore_course(string $backupid): int {
        global $CFG, $DB, $USER;

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        $defaultcategoryid = $DB->get_field('course_categories', 'id',
                ['parent' => 0], IGNORE_MULTIPLE);

        // Do restore to new course with default settings.
        $newcourseid = \restore_dbops::create_new_course('Restored course', 'R1', $defaultcategoryid);
        $rc = new \restore_controller($backupid, $newcourseid,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
                backup::TARGET_NEW_COURSE);

        $precheck = $rc->execute_precheck();
        $this->assertTrue($precheck);

        $rc->execute_plan();
        $rc->destroy();

        return $newcourseid;
    }
}
