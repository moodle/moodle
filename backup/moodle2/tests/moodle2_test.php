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
 * Tests for Moodle 2 format backup operation.
 *
 * @package core_backup
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->libdir . '/completionlib.php');

/**
 * Tests for Moodle 2 format backup operation.
 *
 * @package core_backup
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_backup_moodle2_testcase extends advanced_testcase {

    /**
     * Tidy up open files that may be left open.
     */
    protected function tearDown() {
        gc_collect_cycles();
    }

    /**
     * Tests the availability field on modules and sections is correctly
     * backed up and restored.
     */
    public function test_backup_availability() {
        global $DB, $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enableavailability = true;
        $CFG->enablecompletion = true;

        // Create a course with some availability data set.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
                array('format' => 'topics', 'numsections' => 3,
                    'enablecompletion' => COMPLETION_ENABLED),
                array('createsections' => true));
        $forum = $generator->create_module('forum', array(
                'course' => $course->id));
        $forum2 = $generator->create_module('forum', array(
                'course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL));

        // We need a grade, easiest is to add an assignment.
        $assignrow = $generator->create_module('assign', array(
                'course' => $course->id));
        $assign = new assign(context_module::instance($assignrow->cmid), false, false);
        $item = $assign->get_grade_item();

        // Make a test grouping as well.
        $grouping = $generator->create_grouping(array('courseid' => $course->id,
                'name' => 'Grouping!'));

        $availability = '{"op":"|","show":false,"c":[' .
                '{"type":"completion","cm":' . $forum2->cmid .',"e":1},' .
                '{"type":"grade","id":' . $item->id . ',"min":4,"max":94},' .
                '{"type":"grouping","id":' . $grouping->id . '}' .
                ']}';
        $DB->set_field('course_modules', 'availability', $availability, array(
                'id' => $forum->cmid));
        $DB->set_field('course_sections', 'availability', $availability, array(
                'course' => $course->id, 'section' => 1));

        // Backup and restore it.
        $newcourseid = $this->backup_and_restore($course);

        // Check settings in new course.
        $modinfo = get_fast_modinfo($newcourseid);
        $forums = array_values($modinfo->get_instances_of('forum'));
        $assigns = array_values($modinfo->get_instances_of('assign'));
        $newassign = new assign(context_module::instance($assigns[0]->id), false, false);
        $newitem = $newassign->get_grade_item();
        $newgroupingid = $DB->get_field('groupings', 'id', array('courseid' => $newcourseid));

        // Expected availability should have new ID for the forum, grade, and grouping.
        $newavailability = str_replace(
                '"grouping","id":' . $grouping->id,
                '"grouping","id":' . $newgroupingid,
                str_replace(
                    '"grade","id":' . $item->id,
                    '"grade","id":' . $newitem->id,
                    str_replace(
                        '"cm":' . $forum2->cmid,
                        '"cm":' . $forums[1]->id,
                        $availability)));

        $this->assertEquals($newavailability, $forums[0]->availability);
        $this->assertNull($forums[1]->availability);
        $this->assertEquals($newavailability, $modinfo->get_section_info(1, MUST_EXIST)->availability);
        $this->assertNull($modinfo->get_section_info(2, MUST_EXIST)->availability);
    }

    /**
     * The availability data format was changed in Moodle 2.7. This test
     * ensures that a Moodle 2.6 backup with this data can still be correctly
     * restored.
     */
    public function test_restore_legacy_availability() {
        global $DB, $USER, $CFG;
        require_once($CFG->dirroot . '/grade/querylib.php');
        require_once($CFG->libdir . '/completionlib.php');

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enableavailability = true;
        $CFG->enablecompletion = true;

        // Extract backup file.
        $backupid = 'abc';
        $backuppath = $CFG->tempdir . '/backup/' . $backupid;
        check_dir_exists($backuppath);
        get_file_packer('application/vnd.moodle.backup')->extract_to_pathname(
                __DIR__ . '/fixtures/availability_26_format.mbz', $backuppath);

        // Do restore to new course with default settings.
        $generator = $this->getDataGenerator();
        $categoryid = $DB->get_field_sql("SELECT MIN(id) FROM {course_categories}");
        $newcourseid = restore_dbops::create_new_course(
                'Test fullname', 'Test shortname', $categoryid);
        $rc = new restore_controller($backupid, $newcourseid,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
                backup::TARGET_NEW_COURSE);
        $thrown = null;
        try {
            $this->assertTrue($rc->execute_precheck());
            $rc->execute_plan();
            $rc->destroy();
        } catch (Exception $e) {
            $thrown = $e;
            // Because of the PHPUnit exception behaviour in this situation, we
            // will not see this message unless it is explicitly echoed (just
            // using it in a fail() call or similar will not work).
            echo "\n\nEXCEPTION: " . $thrown->getMessage() . '[' .
                    $thrown->getFile() . ':' . $thrown->getLine(). "]\n\n";
        }

        // Must set restore_controller variable to null so that php
        // garbage-collects it; otherwise the file will be left open and
        // attempts to delete it will cause a permission error on Windows
        // systems, breaking unit tests.
        $rc = null;
        $this->assertNull($thrown);

        // Get information about the resulting course and check that it is set
        // up correctly.
        $modinfo = get_fast_modinfo($newcourseid);
        $pages = array_values($modinfo->get_instances_of('page'));
        $forums = array_values($modinfo->get_instances_of('forum'));
        $quizzes = array_values($modinfo->get_instances_of('quiz'));
        $grouping = $DB->get_record('groupings', array('courseid' => $newcourseid));

        // FROM date.
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[{"type":"date","d":">=","t":1893456000}]}',
                $pages[1]->availability);
        // UNTIL date.
        $this->assertEquals(
                '{"op":"&","showc":[false],"c":[{"type":"date","d":"<","t":1393977600}]}',
                $pages[2]->availability);
        // FROM and UNTIL.
        $this->assertEquals(
                '{"op":"&","showc":[true,false],"c":[' .
                '{"type":"date","d":">=","t":1449705600},' .
                '{"type":"date","d":"<","t":1893456000}' .
                ']}',
                $pages[3]->availability);
        // Grade >= 75%.
        $grades = array_values(grade_get_grade_items_for_activity($quizzes[0], true));
        $gradeid = $grades[0]->id;
        $coursegrade = grade_item::fetch_course_item($newcourseid);
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[{"type":"grade","id":' . $gradeid . ',"min":75}]}',
                $pages[4]->availability);
        // Grade < 25%.
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[{"type":"grade","id":' . $gradeid . ',"max":25}]}',
                $pages[5]->availability);
        // Grade 90-100%.
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[{"type":"grade","id":' . $gradeid . ',"min":90,"max":100}]}',
                $pages[6]->availability);
        // Email contains frog.
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[{"type":"profile","op":"contains","sf":"email","v":"frog"}]}',
                $pages[7]->availability);
        // Page marked complete..
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[{"type":"completion","cm":' . $pages[0]->id .
                ',"e":' . COMPLETION_COMPLETE . '}]}',
                $pages[8]->availability);
        // Quiz complete but failed.
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[{"type":"completion","cm":' . $quizzes[0]->id .
                ',"e":' . COMPLETION_COMPLETE_FAIL . '}]}',
                $pages[9]->availability);
        // Quiz complete and succeeded.
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[{"type":"completion","cm":' . $quizzes[0]->id .
                ',"e":' . COMPLETION_COMPLETE_PASS. '}]}',
                $pages[10]->availability);
        // Quiz not complete.
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[{"type":"completion","cm":' . $quizzes[0]->id .
                ',"e":' . COMPLETION_INCOMPLETE . '}]}',
                $pages[11]->availability);
        // Grouping.
        $this->assertEquals(
                '{"op":"&","showc":[false],"c":[{"type":"grouping","id":' . $grouping->id . '}]}',
                $pages[12]->availability);

        // All the options.
        $this->assertEquals('{"op":"&",' .
                '"showc":[false,true,false,true,true,true,true,true,true],' .
                '"c":[' .
                '{"type":"grouping","id":' . $grouping->id . '},' .
                '{"type":"date","d":">=","t":1488585600},' .
                '{"type":"date","d":"<","t":1709510400},' .
                '{"type":"profile","op":"contains","sf":"email","v":"@"},' .
                '{"type":"profile","op":"contains","sf":"city","v":"Frogtown"},' .
                '{"type":"grade","id":' . $gradeid . ',"min":30,"max":35},' .
                '{"type":"grade","id":' . $coursegrade->id . ',"min":5,"max":10},' .
                '{"type":"completion","cm":' . $pages[0]->id . ',"e":' . COMPLETION_COMPLETE . '},' .
                '{"type":"completion","cm":' . $quizzes[0]->id .',"e":' . COMPLETION_INCOMPLETE . '}' .
                ']}', $pages[13]->availability);

        // Group members only forum.
        $this->assertEquals(
                '{"op":"&","showc":[false],"c":[{"type":"group"}]}',
                $forums[0]->availability);

        // Section with lots of conditions.
        $this->assertEquals(
                '{"op":"&","showc":[false,false,false,false],"c":[' .
                '{"type":"date","d":">=","t":1417737600},' .
                '{"type":"profile","op":"contains","sf":"email","v":"@"},' .
                '{"type":"grade","id":' . $gradeid . ',"min":20},' .
                '{"type":"completion","cm":' . $pages[0]->id . ',"e":' . COMPLETION_COMPLETE . '}]}',
                $modinfo->get_section_info(3)->availability);

        // Section with grouping.
        $this->assertEquals(
                '{"op":"&","showc":[false],"c":[{"type":"grouping","id":' . $grouping->id . '}]}',
                $modinfo->get_section_info(4)->availability);
    }

    /**
     * Tests the backup and restore of single activity to same course (duplicate)
     * when it contains availability conditions that depend on other items in
     * course.
     */
    public function test_duplicate_availability() {
        global $DB, $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enableavailability = true;
        $CFG->enablecompletion = true;

        // Create a course with completion enabled and 2 forums.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
                array('format' => 'topics', 'enablecompletion' => COMPLETION_ENABLED));
        $forum = $generator->create_module('forum', array(
                'course' => $course->id));
        $forum2 = $generator->create_module('forum', array(
                'course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL));

        // We need a grade, easiest is to add an assignment.
        $assignrow = $generator->create_module('assign', array(
                'course' => $course->id));
        $assign = new assign(context_module::instance($assignrow->cmid), false, false);
        $item = $assign->get_grade_item();

        // Make a test group and grouping as well.
        $group = $generator->create_group(array('courseid' => $course->id,
                'name' => 'Group!'));
        $grouping = $generator->create_grouping(array('courseid' => $course->id,
                'name' => 'Grouping!'));

        // Set the forum to have availability conditions on all those things,
        // plus some that don't exist or are special values.
        $availability = '{"op":"|","show":false,"c":[' .
                '{"type":"completion","cm":' . $forum2->cmid .',"e":1},' .
                '{"type":"completion","cm":99999999,"e":1},' .
                '{"type":"grade","id":' . $item->id . ',"min":4,"max":94},' .
                '{"type":"grade","id":99999998,"min":4,"max":94},' .
                '{"type":"grouping","id":' . $grouping->id . '},' .
                '{"type":"grouping","id":99999997},' .
                '{"type":"group","id":' . $group->id . '},' .
                '{"type":"group"},' .
                '{"type":"group","id":99999996}' .
                ']}';
        $DB->set_field('course_modules', 'availability', $availability, array(
                'id' => $forum->cmid));

        // Duplicate it.
        $newcmid = $this->duplicate($course, $forum->cmid);

        // For those which still exist on the course we expect it to keep using
        // the real ID. For those which do not exist on the course any more
        // (e.g. simulating backup/restore of single activity between 2 courses)
        // we expect the IDs to be replaced with marker value: 0 for cmid
        // and grade, -1 for group/grouping.
        $expected = str_replace(
                array('99999999', '99999998', '99999997', '99999996'),
                array(0, 0, -1, -1),
                $availability);

        // Check settings in new activity.
        $actual = $DB->get_field('course_modules', 'availability', array('id' => $newcmid));
        $this->assertEquals($expected, $actual);
    }

    /**
     * When restoring a course, you can change the start date, which shifts other
     * dates. This test checks that certain dates are correctly modified.
     */
    public function test_restore_dates() {
        global $DB, $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enableavailability = true;

        // Create a course with specific start date.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(array(
                'startdate' => strtotime('1 Jan 2014 00:00 GMT')));

        // Add a forum with conditional availability date restriction, including
        // one of them nested inside a tree.
        $availability = '{"op":"&","showc":[true,true],"c":[' .
                '{"op":"&","c":[{"type":"date","d":">=","t":DATE1}]},' .
                '{"type":"date","d":"<","t":DATE2}]}';
        $before = str_replace(
                array('DATE1', 'DATE2'),
                array(strtotime('1 Feb 2014 00:00 GMT'), strtotime('10 Feb 2014 00:00 GMT')),
                $availability);
        $forum = $generator->create_module('forum', array('course' => $course->id,
                'availability' => $before));

        // Add an assign with defined start date.
        $assign = $generator->create_module('assign', array('course' => $course->id,
                'allowsubmissionsfromdate' => strtotime('7 Jan 2014 16:00 GMT')));

        // Do backup and restore.
        $newcourseid = $this->backup_and_restore($course, strtotime('3 Jan 2015 00:00 GMT'));

        $modinfo = get_fast_modinfo($newcourseid);

        // Check forum dates are modified by the same amount as the course start.
        $newforums = $modinfo->get_instances_of('forum');
        $newforum = reset($newforums);
        $after = str_replace(
            array('DATE1', 'DATE2'),
            array(strtotime('3 Feb 2015 00:00 GMT'), strtotime('12 Feb 2015 00:00 GMT')),
            $availability);
        $this->assertEquals($after, $newforum->availability);

        // Check assign date.
        $newassigns = $modinfo->get_instances_of('assign');
        $newassign = reset($newassigns);
        $this->assertEquals(strtotime('9 Jan 2015 16:00 GMT'), $DB->get_field(
                'assign', 'allowsubmissionsfromdate', array('id' => $newassign->instance)));
    }

    /**
     * Backs a course up and restores it.
     *
     * @param stdClass $course Course object to backup
     * @param int $newdate If non-zero, specifies custom date for new course
     * @return int ID of newly restored course
     */
    protected function backup_and_restore($course, $newdate = 0) {
        global $USER, $CFG;

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        // Do backup with default settings. MODE_IMPORT means it will just
        // create the directory and not zip it.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id,
                backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
                $USER->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Do restore to new course with default settings.
        $newcourseid = restore_dbops::create_new_course(
                $course->fullname, $course->shortname . '_2', $course->category);
        $rc = new restore_controller($backupid, $newcourseid,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
                backup::TARGET_NEW_COURSE);
        if ($newdate) {
            $rc->get_plan()->get_setting('course_startdate')->set_value($newdate);
        }
        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        return $newcourseid;
    }

    /**
     * Duplicates a single activity within a course.
     *
     * This is based on the code from course/modduplicate.php, but reduced for
     * simplicity.
     *
     * @param stdClass $course Course object
     * @param int $cmid Activity to duplicate
     * @return int ID of new activity
     */
    protected function duplicate($course, $cmid) {
        global $USER;

        // Do backup.
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $cmid, backup::FORMAT_MOODLE,
                backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Do restore.
        $rc = new restore_controller($backupid, $course->id,
                backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id, backup::TARGET_CURRENT_ADDING);
        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();

        // Find cmid.
        $tasks = $rc->get_plan()->get_tasks();
        $cmcontext = context_module::instance($cmid);
        $newcmid = 0;
        foreach ($tasks as $task) {
            if (is_subclass_of($task, 'restore_activity_task')) {
                if ($task->get_old_contextid() == $cmcontext->id) {
                    $newcmid = $task->get_moduleid();
                    break;
                }
            }
        }
        $rc->destroy();
        if (!$newcmid) {
            throw new coding_exception('Unexpected: failure to find restored cmid');
        }
        return $newcmid;
    }
}
