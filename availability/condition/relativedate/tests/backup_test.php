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
 * Unit tests for the relativedate condition.
 *
 * @package   availability_relativedate
 * @copyright eWallah (www.eWallah.net)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace availability_relativedate;

use availability_relativedate\condition;
use core_availability\info_module;

/**
 * Unit tests for the relativedate condition.
 *
 * @package   availability_relativedate
 * @copyright eWallah (www.eWallah.net)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \availability_relativedate\condition
 */
final class backup_test extends \advanced_testcase {
    /**
     * Backup check.
     * @covers \availability_relativedate\condition
     */
    public function test_backup(): void {
        global $CFG, $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

        $dg = $this->getDataGenerator();
        $now = time();
        $course = $dg->create_course(['startdate' => $now, 'enddate' => $now + 7 * WEEKSECS, 'enablecompletion' => 1]);

        $pg = $this->getDataGenerator()->get_plugin_generator('mod_page');
        $page0 = $pg->create_instance(['course' => $course, 'completion' => COMPLETION_TRACKING_MANUAL]);
        $page1 = $pg->create_instance(['course' => $course, 'completion' => COMPLETION_TRACKING_MANUAL]);
        $page2 = $pg->create_instance(['course' => $course, 'completion' => COMPLETION_TRACKING_MANUAL]);
        $str = '{"op":"|","show":true,"c":[{"type":"relativedate","n":4,"d":4,"s":7,"m":' . $page1->cmid . '}]}';
        $DB->set_field('course_modules', 'availability', $str, ['id' => $page0->cmid]);
        $str = '{"op":"|","c":[{"type":"relativedate","n":1,"d":1,"s":6,"m":999999}], "show":true}';
        $DB->set_field('course_modules', 'availability', $str, ['id' => $page2->cmid]);
        rebuild_course_cache($course->id, true);
        $bc = new \backup_controller(
            \backup::TYPE_1COURSE,
            $course->id,
            \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO,
            \backup::MODE_GENERAL,
            2
        );
        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];
        $fp = get_file_packer('application/vnd.moodle.backup');
        $filepath = $CFG->dataroot . '/temp/backup/test-restore-course-event';
        $file->extract_to_pathname($fp, $filepath);
        $bc->destroy();
        $rc = new \restore_controller(
            'test-restore-course-event',
            $course->id,
            \backup::INTERACTIVE_NO,
            \backup::MODE_GENERAL,
            2,
            \backup::TARGET_NEW_COURSE
        );
        $rc->execute_precheck();
        $rc->execute_plan();
        $newid = $rc->get_courseid();
        $rc->destroy();
        $newcourse = get_course($newid);
        $modinfo = get_fast_modinfo($newcourse);
        $this->assertCount(6, $modinfo->get_instances_of('page'));

        $bc = new \backup_controller(
            \backup::TYPE_1COURSE,
            $course->id,
            \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO,
            \backup::MODE_GENERAL,
            2
        );
        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];
        $fp = get_file_packer('application/vnd.moodle.backup');
        $filepath = $CFG->dataroot . '/temp/backup/test-restore-course-event';
        $file->extract_to_pathname($fp, $filepath);
        $bc->destroy();
        $rc = new \restore_controller(
            'test-restore-course-event',
            $course->id,
            \backup::INTERACTIVE_NO,
            \backup::MODE_GENERAL,
            2,
            \backup::TARGET_CURRENT_ADDING
        );
        $rc->execute_precheck();
        $rc->execute_plan();
        $newid = $rc->get_courseid();
        $rc->destroy();
        $course = get_course($newid);
        $modinfo = get_fast_modinfo($course);
        $pages = $modinfo->get_instances_of('page');
        $this->assertCount(12, $pages);
    }
}
