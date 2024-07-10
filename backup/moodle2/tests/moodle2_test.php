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
use backup_controller;
use backup_setting;
use restore_controller;
use restore_dbops;

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
final class moodle2_test extends \advanced_testcase {

    /**
     * Tests the availability field on modules and sections is correctly
     * backed up and restored.
     */
    public function test_backup_availability(): void {
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
        $assign = new \assign(\context_module::instance($assignrow->cmid), false, false);
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
        $newassign = new \assign(\context_module::instance($assigns[0]->id), false, false);
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
    public function test_restore_legacy_availability(): void {
        global $DB, $USER, $CFG;
        require_once($CFG->dirroot . '/grade/querylib.php');
        require_once($CFG->libdir . '/completionlib.php');

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enableavailability = true;
        $CFG->enablecompletion = true;

        // Extract backup file.
        $backupid = 'abc';
        $backuppath = make_backup_temp_directory($backupid);
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
        $coursegrade = \grade_item::fetch_course_item($newcourseid);
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
    public function test_duplicate_availability(): void {
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
        $assign = new \assign(\context_module::instance($assignrow->cmid), false, false);
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
    public function test_restore_dates(): void {
        global $DB, $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enableavailability = true;

        // Create a course with specific start date.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(array(
            'startdate' => strtotime('1 Jan 2014 00:00 GMT'),
            'enddate' => strtotime('3 Aug 2014 00:00 GMT')
        ));

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

        $newcourse = $DB->get_record('course', array('id' => $newcourseid));
        $this->assertEquals(strtotime('5 Aug 2015 00:00 GMT'), $newcourse->enddate);

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
     * Test front page backup/restore and duplicate activities
     * @return void
     */
    public function test_restore_frontpage(): void {
        global $DB, $CFG, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();

        $frontpage = $DB->get_record('course', array('id' => SITEID));
        $forum = $generator->create_module('forum', array('course' => $frontpage->id));

        // Activities can be duplicated.
        $this->duplicate($frontpage, $forum->cmid);

        $modinfo = get_fast_modinfo($frontpage);
        $this->assertEquals(2, count($modinfo->get_instances_of('forum')));

        // Front page backup.
        $frontpagebc = new backup_controller(backup::TYPE_1COURSE, $frontpage->id,
                backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
                $USER->id);
        $frontpagebackupid = $frontpagebc->get_backupid();
        $frontpagebc->execute_plan();
        $frontpagebc->destroy();

        $course = $generator->create_course();
        $newcourseid = restore_dbops::create_new_course(
                $course->fullname . ' 2', $course->shortname . '_2', $course->category);

        // Other course backup.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id,
                backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
                $USER->id);
        $otherbackupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // We can only restore a front page over the front page.
        $rc = new restore_controller($frontpagebackupid, $course->id,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
                backup::TARGET_CURRENT_ADDING);
        $this->assertFalse($rc->execute_precheck());
        $rc->destroy();

        $rc = new restore_controller($frontpagebackupid, $newcourseid,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
                backup::TARGET_NEW_COURSE);
        $this->assertFalse($rc->execute_precheck());
        $rc->destroy();

        $rc = new restore_controller($frontpagebackupid, $frontpage->id,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
                backup::TARGET_CURRENT_ADDING);
        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        // We can't restore a non-front page course on the front page course.
        $rc = new restore_controller($otherbackupid, $frontpage->id,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
                backup::TARGET_CURRENT_ADDING);
        $this->assertFalse($rc->execute_precheck());
        $rc->destroy();

        $rc = new restore_controller($otherbackupid, $newcourseid,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
                backup::TARGET_NEW_COURSE);
        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();
    }

    /**
     * Backs a course up and restores it.
     *
     * @param \stdClass $course Course object to backup
     * @param int $newdate If non-zero, specifies custom date for new course
     * @param callable|null $inbetween If specified, function that is called before restore
     * @param bool $userdata Whether the backup/restory must be with user data or not.
     * @return int ID of newly restored course
     */
    protected function backup_and_restore($course, $newdate = 0, $inbetween = null, bool $userdata = false) {
        global $USER, $CFG;

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        // Do backup with default settings. MODE_IMPORT means it will just
        // create the directory and not zip it.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id,
                backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
                $USER->id);
        $bc->get_plan()->get_setting('users')->set_status(backup_setting::NOT_LOCKED);
        $bc->get_plan()->get_setting('users')->set_value($userdata);

        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        if ($inbetween) {
            $inbetween($backupid);
        }

        // Do restore to new course with default settings.
        $newcourseid = restore_dbops::create_new_course(
                $course->fullname, $course->shortname . '_2', $course->category);
        $rc = new restore_controller($backupid, $newcourseid,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
                backup::TARGET_NEW_COURSE);
        if ($newdate) {
            $rc->get_plan()->get_setting('course_startdate')->set_value($newdate);
        }

        $rc->get_plan()->get_setting('users')->set_status(backup_setting::NOT_LOCKED);
        $rc->get_plan()->get_setting('users')->set_value($userdata);
        if ($userdata) {
            $rc->get_plan()->get_setting('xapistate')->set_value(true);
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
     * @param \stdClass $course Course object
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
        $cmcontext = \context_module::instance($cmid);
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
            throw new \coding_exception('Unexpected: failure to find restored cmid');
        }
        return $newcmid;
    }

    /**
     * Help function for enrolment methods backup/restore tests:
     *
     * - Creates a course ($course), adds self-enrolment method and a user
     * - Makes a backup
     * - Creates a target course (if requested) ($newcourseid)
     * - Initialises restore controller for this backup file ($rc)
     *
     * @param int $target target for restoring: backup::TARGET_NEW_COURSE etc.
     * @param array $additionalcaps - additional capabilities to give to user
     * @return array array of original course, new course id, restore controller: [$course, $newcourseid, $rc]
     */
    protected function prepare_for_enrolments_test($target, $additionalcaps = []) {
        global $CFG, $DB;
        $this->resetAfterTest(true);

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        $user = $this->getDataGenerator()->create_user();
        $roleidcat = create_role('Category role', 'dummyrole1', 'dummy role description');

        $course = $this->getDataGenerator()->create_course();

        // Enable instance of self-enrolment plugin (it should already be present) and enrol a student with it.
        $selfplugin = enrol_get_plugin('self');
        $selfinstance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'self'));
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $selfplugin->update_status($selfinstance, ENROL_INSTANCE_ENABLED);
        $selfplugin->enrol_user($selfinstance, $user->id, $studentrole->id);

        // Give current user capabilities to do backup and restore and assign student role.
        $categorycontext = \context_course::instance($course->id)->get_parent_context();

        $caps = array_merge([
            'moodle/course:view',
            'moodle/course:create',
            'moodle/backup:backupcourse',
            'moodle/backup:configure',
            'moodle/backup:backuptargetimport',
            'moodle/restore:restorecourse',
            'moodle/role:assign',
            'moodle/restore:configure',
        ], $additionalcaps);

        foreach ($caps as $cap) {
            assign_capability($cap, CAP_ALLOW, $roleidcat, $categorycontext);
        }

        core_role_set_assign_allowed($roleidcat, $studentrole->id);
        role_assign($roleidcat, $user->id, $categorycontext);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($user);

        // Do backup with default settings. MODE_IMPORT means it will just
        // create the directory and not zip it.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id,
            backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_SAMESITE,
            $user->id);
        $backupid = $bc->get_backupid();
        $backupbasepath = $bc->get_plan()->get_basepath();
        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];
        $bc->destroy();

        // Restore the backup immediately.

        // Check if we need to unzip the file because the backup temp dir does not contains backup files.
        if (!file_exists($backupbasepath . "/moodle_backup.xml")) {
            $file->extract_to_pathname(get_file_packer('application/vnd.moodle.backup'), $backupbasepath);
        }

        if ($target == backup::TARGET_NEW_COURSE) {
            $newcourseid = restore_dbops::create_new_course($course->fullname . '_2',
                $course->shortname . '_2',
                $course->category);
        } else {
            $newcourse = $this->getDataGenerator()->create_course();
            $newcourseid = $newcourse->id;
        }
        $rc = new restore_controller($backupid, $newcourseid,
            backup::INTERACTIVE_NO, backup::MODE_SAMESITE, $user->id, $target);

        return [$course, $newcourseid, $rc];
    }

    /**
     * Backup a course with enrolment methods and restore it without user data and without enrolment methods
     */
    public function test_restore_without_users_without_enrolments(): void {
        global $DB;

        list($course, $newcourseid, $rc) = $this->prepare_for_enrolments_test(backup::TARGET_NEW_COURSE);

        // Ensure enrolment methods will not be restored without capability.
        $this->assertEquals(backup::ENROL_NEVER, $rc->get_plan()->get_setting('enrolments')->get_value());
        $this->assertEquals(false, $rc->get_plan()->get_setting('users')->get_value());

        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        // Self-enrolment method was not enabled, users were not restored.
        $this->assertEmpty($DB->count_records('enrol', ['enrol' => 'self', 'courseid' => $newcourseid,
            'status' => ENROL_INSTANCE_ENABLED]));
        $sql = "select ue.id, ue.userid, e.enrol from {user_enrolments} ue
          join {enrol} e on ue.enrolid = e.id WHERE e.courseid = ?";
        $enrolments = $DB->get_records_sql($sql, [$newcourseid]);
        $this->assertEmpty($enrolments);
    }

    /**
     * Backup a course with enrolment methods and restore it without user data with enrolment methods
     */
    public function test_restore_without_users_with_enrolments(): void {
        global $DB;

        list($course, $newcourseid, $rc) = $this->prepare_for_enrolments_test(backup::TARGET_NEW_COURSE,
            ['moodle/course:enrolconfig']);

        // Ensure enrolment methods will be restored.
        $this->assertEquals(backup::ENROL_NEVER, $rc->get_plan()->get_setting('enrolments')->get_value());
        $this->assertEquals(false, $rc->get_plan()->get_setting('users')->get_value());
        // Set "Include enrolment methods" to "Always" so they can be restored without users.
        $rc->get_plan()->get_setting('enrolments')->set_value(backup::ENROL_ALWAYS);

        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        // Self-enrolment method was restored (it is enabled), users were not restored.
        $enrol = $DB->get_records('enrol', ['enrol' => 'self', 'courseid' => $newcourseid,
            'status' => ENROL_INSTANCE_ENABLED]);
        $this->assertNotEmpty($enrol);

        $sql = "select ue.id, ue.userid, e.enrol from {user_enrolments} ue
            join {enrol} e on ue.enrolid = e.id WHERE e.courseid = ?";
        $enrolments = $DB->get_records_sql($sql, [$newcourseid]);
        $this->assertEmpty($enrolments);
    }

    /**
     * Backup a course with enrolment methods and restore it with user data and without enrolment methods
     */
    public function test_restore_with_users_without_enrolments(): void {
        global $DB;

        list($course, $newcourseid, $rc) = $this->prepare_for_enrolments_test(backup::TARGET_NEW_COURSE,
            ['moodle/backup:userinfo', 'moodle/restore:userinfo']);

        // Ensure enrolment methods will not be restored without capability.
        $this->assertEquals(backup::ENROL_NEVER, $rc->get_plan()->get_setting('enrolments')->get_value());
        $this->assertEquals(true, $rc->get_plan()->get_setting('users')->get_value());

        global $qwerty;
        $qwerty = 1;
        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();
        $qwerty = 0;

        // Self-enrolment method was not restored, student was restored as manual enrolment.
        $this->assertEmpty($DB->count_records('enrol', ['enrol' => 'self', 'courseid' => $newcourseid,
            'status' => ENROL_INSTANCE_ENABLED]));

        $enrol = $DB->get_record('enrol', ['enrol' => 'manual', 'courseid' => $newcourseid]);
        $this->assertEquals(1, $DB->count_records('user_enrolments', ['enrolid' => $enrol->id]));
    }

    /**
     * Backup a course with enrolment methods and restore it with user data with enrolment methods
     */
    public function test_restore_with_users_with_enrolments(): void {
        global $DB;

        list($course, $newcourseid, $rc) = $this->prepare_for_enrolments_test(backup::TARGET_NEW_COURSE,
            ['moodle/backup:userinfo', 'moodle/restore:userinfo', 'moodle/course:enrolconfig']);

        // Ensure enrolment methods will be restored.
        $this->assertEquals(backup::ENROL_WITHUSERS, $rc->get_plan()->get_setting('enrolments')->get_value());
        $this->assertEquals(true, $rc->get_plan()->get_setting('users')->get_value());

        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        // Self-enrolment method was restored (it is enabled), student was restored.
        $enrol = $DB->get_records('enrol', ['enrol' => 'self', 'courseid' => $newcourseid,
            'status' => ENROL_INSTANCE_ENABLED]);
        $this->assertNotEmpty($enrol);

        $sql = "select ue.id, ue.userid, e.enrol from {user_enrolments} ue
            join {enrol} e on ue.enrolid = e.id WHERE e.courseid = ?";
        $enrolments = $DB->get_records_sql($sql, [$newcourseid]);
        $this->assertEquals(1, count($enrolments));
        $enrolment = reset($enrolments);
        $this->assertEquals('self', $enrolment->enrol);
    }

    /**
     * Backup a course with enrolment methods and restore it with user data with enrolment methods merging into another course
     */
    public function test_restore_with_users_with_enrolments_merging(): void {
        global $DB;

        list($course, $newcourseid, $rc) = $this->prepare_for_enrolments_test(backup::TARGET_EXISTING_ADDING,
            ['moodle/backup:userinfo', 'moodle/restore:userinfo', 'moodle/course:enrolconfig']);

        // Ensure enrolment methods will be restored.
        $this->assertEquals(backup::ENROL_WITHUSERS, $rc->get_plan()->get_setting('enrolments')->get_value());
        $this->assertEquals(true, $rc->get_plan()->get_setting('users')->get_value());

        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        // User was restored with self-enrolment method.
        $enrol = $DB->get_records('enrol', ['enrol' => 'self', 'courseid' => $newcourseid,
            'status' => ENROL_INSTANCE_ENABLED]);
        $this->assertNotEmpty($enrol);

        $sql = "select ue.id, ue.userid, e.enrol from {user_enrolments} ue
            join {enrol} e on ue.enrolid = e.id WHERE e.courseid = ?";
        $enrolments = $DB->get_records_sql($sql, [$newcourseid]);
        $this->assertEquals(1, count($enrolments));
        $enrolment = reset($enrolments);
        $this->assertEquals('self', $enrolment->enrol);
    }

    /**
     * Backup a course with enrolment methods and restore it with user data with enrolment methods into another course deleting it's contents
     */
    public function test_restore_with_users_with_enrolments_deleting(): void {
        global $DB;

        list($course, $newcourseid, $rc) = $this->prepare_for_enrolments_test(backup::TARGET_EXISTING_DELETING,
            ['moodle/backup:userinfo', 'moodle/restore:userinfo', 'moodle/course:enrolconfig']);

        // Ensure enrolment methods will be restored.
        $this->assertEquals(backup::ENROL_WITHUSERS, $rc->get_plan()->get_setting('enrolments')->get_value());
        $this->assertEquals(true, $rc->get_plan()->get_setting('users')->get_value());

        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        // Self-enrolment method was restored (it is enabled), student was restored.
        $enrol = $DB->get_records('enrol', ['enrol' => 'self', 'courseid' => $newcourseid,
            'status' => ENROL_INSTANCE_ENABLED]);
        $this->assertNotEmpty($enrol);

        $sql = "select ue.id, ue.userid, e.enrol from {user_enrolments} ue
            join {enrol} e on ue.enrolid = e.id WHERE e.courseid = ?";
        $enrolments = $DB->get_records_sql($sql, [$newcourseid]);
        $this->assertEquals(1, count($enrolments));
        $enrolment = reset($enrolments);
        $this->assertEquals('self', $enrolment->enrol);
    }

    /**
     * Test the block instance time fields (timecreated, timemodified) through a backup and restore.
     */
    public function test_block_instance_times_backup(): void {
        global $DB;
        $this->resetAfterTest();

        $this->setAdminUser();
        $generator = $this->getDataGenerator();

        // Create course and add HTML block.
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);
        $page = new \moodle_page();
        $page->set_context($context);
        $page->set_course($course);
        $page->set_pagelayout('standard');
        $page->set_pagetype('course-view');
        $page->blocks->load_blocks();
        $page->blocks->add_block_at_end_of_default_region('html');

        // Update (hack in database) timemodified and timecreated to specific values for testing.
        $blockdata = $DB->get_record('block_instances',
                ['blockname' => 'html', 'parentcontextid' => $context->id]);
        $originalblockid = $blockdata->id;
        $blockdata->timecreated = 12345;
        $blockdata->timemodified = 67890;
        $DB->update_record('block_instances', $blockdata);

        // Do backup and restore.
        $newcourseid = $this->backup_and_restore($course);

        // Confirm that values were transferred correctly into HTML block on new course.
        $newcontext = \context_course::instance($newcourseid);
        $blockdata = $DB->get_record('block_instances',
                ['blockname' => 'html', 'parentcontextid' => $newcontext->id]);
        $this->assertEquals(12345, $blockdata->timecreated);
        $this->assertEquals(67890, $blockdata->timemodified);

        // Simulate what happens with an older backup that doesn't have those fields, by removing
        // them from the backup before doing a restore.
        $before = time();
        $newcourseid = $this->backup_and_restore($course, 0, function($backupid) use($originalblockid) {
            global $CFG;
            $path = $CFG->dataroot . '/temp/backup/' . $backupid . '/course/blocks/html_' .
                    $originalblockid . '/block.xml';
            $xml = file_get_contents($path);
            $xml = preg_replace('~<timecreated>.*?</timemodified>~s', '', $xml);
            file_put_contents($path, $xml);
        });
        $after = time();

        // The fields not specified should default to current time.
        $newcontext = \context_course::instance($newcourseid);
        $blockdata = $DB->get_record('block_instances',
                ['blockname' => 'html', 'parentcontextid' => $newcontext->id]);
        $this->assertTrue($before <= $blockdata->timecreated && $after >= $blockdata->timecreated);
        $this->assertTrue($before <= $blockdata->timemodified && $after >= $blockdata->timemodified);
    }

    /**
     * When you restore a site with global search (or search indexing) turned on, then it should
     * add entries to the search index requests table so that the data gets indexed.
     */
    public function test_restore_search_index_requests(): void {
        global $DB, $CFG, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enableglobalsearch = true;

        // Create a course.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Add a forum.
        $forum = $generator->create_module('forum', ['course' => $course->id]);

        // Add a block.
        $context = \context_course::instance($course->id);
        $page = new \moodle_page();
        $page->set_context($context);
        $page->set_course($course);
        $page->set_pagelayout('standard');
        $page->set_pagetype('course-view');
        $page->blocks->load_blocks();
        $page->blocks->add_block_at_end_of_default_region('html');

        // Initially there should be no search index requests.
        $this->assertEquals(0, $DB->count_records('search_index_requests'));

        // Do backup and restore.
        $newcourseid = $this->backup_and_restore($course);

        // Now the course should be requested for index (all search areas).
        $newcontext = \context_course::instance($newcourseid);
        $requests = array_values($DB->get_records('search_index_requests'));
        $this->assertCount(1, $requests);
        $this->assertEquals($newcontext->id, $requests[0]->contextid);
        $this->assertEquals('', $requests[0]->searcharea);

        get_fast_modinfo($newcourseid);

        // Backup the new course...
        $CFG->backup_file_logger_level = backup::LOG_NONE;
        $bc = new backup_controller(backup::TYPE_1COURSE, $newcourseid,
                backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
                $USER->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Restore it on top of old course (should duplicate the forum).
        $rc = new restore_controller($backupid, $course->id,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
                backup::TARGET_EXISTING_ADDING);
        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        // Get the forums now on the old course.
        $modinfo = get_fast_modinfo($course->id);
        $forums = $modinfo->get_instances_of('forum');
        $this->assertCount(2, $forums);

        // The newer one will be the one with larger ID. (Safe to assume for unit test.)
        $biggest = null;
        foreach ($forums as $forum) {
            if ($biggest === null || $biggest->id < $forum->id) {
                $biggest = $forum;
            }
        }
        $restoredforumcontext = \context_module::instance($biggest->id);

        // Get the HTML blocks now on the old course.
        $blockdata = array_values($DB->get_records('block_instances',
                ['blockname' => 'html', 'parentcontextid' => $context->id], 'id DESC'));
        $restoredblockcontext = \context_block::instance($blockdata[0]->id);

        // Check that we have requested index update on both the module and the block.
        $requests = array_values($DB->get_records('search_index_requests', null, 'id'));
        $this->assertCount(3, $requests);
        $this->assertEquals($restoredblockcontext->id, $requests[1]->contextid);
        $this->assertEquals('', $requests[1]->searcharea);
        $this->assertEquals($restoredforumcontext->id, $requests[2]->contextid);
        $this->assertEquals('', $requests[2]->searcharea);
    }

    /**
     * Test restoring courses based on the backup plan. Primarily used with
     * the import functionality
     */
    public function test_restore_course_using_plan_defaults(): void {
        global $DB, $CFG, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enableglobalsearch = true;

        // Set admin config setting so that activities are not restored by default.
        set_config('restore_general_activities', 0, 'restore');

        // Create a course.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $course2 = $generator->create_course();
        $course3 = $generator->create_course();

        // Add a forum.
        $forum = $generator->create_module('forum', ['course' => $course->id]);

        // Backup course...
        $CFG->backup_file_logger_level = backup::LOG_NONE;
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id,
            backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
            $USER->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Restore it on top of course2 (should duplicate the forum).
        $rc = new restore_controller($backupid, $course2->id,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id,
            backup::TARGET_EXISTING_ADDING, null, backup::RELEASESESSION_NO);
        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        // Get the forums now on the old course.
        $modinfo = get_fast_modinfo($course2->id);
        $forums = $modinfo->get_instances_of('forum');
        $this->assertCount(0, $forums);
    }

    /**
     * The Question category hierarchical structure was changed in Moodle 3.5.
     * From 3.5, all question categories in each context are a child of a single top level question category for that context.
     * This test ensures that both Moodle 3.4 and 3.5 backups can still be correctly restored.
     */
    public function test_restore_question_category_34_35(): void {
        global $DB, $USER, $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $backupfiles = array('question_category_34_format', 'question_category_35_format');

        foreach ($backupfiles as $backupfile) {
            // Extract backup file.
            $backupid = $backupfile;
            $backuppath = make_backup_temp_directory($backupid);
            get_file_packer('application/vnd.moodle.backup')->extract_to_pathname(
                    __DIR__ . "/fixtures/$backupfile.mbz", $backuppath);

            // Do restore to new course with default settings.
            $categoryid = $DB->get_field_sql("SELECT MIN(id) FROM {course_categories}");
            $newcourseid = restore_dbops::create_new_course(
                    'Test fullname', 'Test shortname', $categoryid);
            $rc = new restore_controller($backupid, $newcourseid,
                    backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
                    backup::TARGET_NEW_COURSE);

            $this->assertTrue($rc->execute_precheck());
            $rc->execute_plan();
            $rc->destroy();

            // Get information about the resulting course and check that it is set up correctly.
            $modinfo = get_fast_modinfo($newcourseid);
            $quizzes = array_values($modinfo->get_instances_of('quiz'));
            $contexts = $quizzes[0]->context->get_parent_contexts(true);

            $topcategorycount = [];
            foreach ($contexts as $context) {
                $cats = $DB->get_records('question_categories', array('contextid' => $context->id), 'parent', 'id, name, parent');

                // Make sure all question categories that were inside the backup file were restored correctly.
                if ($context->contextlevel == CONTEXT_COURSE) {
                    $this->assertEquals(['top', 'Default for C101'], array_column($cats, 'name'));
                } else if ($context->contextlevel == CONTEXT_MODULE) {
                    $this->assertEquals(['top', 'Default for Q1'], array_column($cats, 'name'));
                }

                $topcategorycount[$context->id] = 0;
                foreach ($cats as $cat) {
                    if (!$cat->parent) {
                        $topcategorycount[$context->id]++;
                    }
                }

                // Make sure there is a single top level category in this context.
                if ($cats) {
                    $this->assertEquals(1, $topcategorycount[$context->id]);
                }
            }
        }
    }

    /**
     * Test the content bank content through a backup and restore.
     */
    public function test_contentbank_content_backup(): void {
        global $DB, $USER, $CFG;
        $this->resetAfterTest();

        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $cbgenerator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');

        // Create course and add content bank content.
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);
        $filepath = $CFG->dirroot . '/h5p/tests/fixtures/filltheblanks.h5p';
        $contents = $cbgenerator->generate_contentbank_data('contenttype_h5p', 2, $USER->id, $context, true, $filepath);
        $this->assertEquals(2, $DB->count_records('contentbank_content'));

        // Do backup and restore.
        $newcourseid = $this->backup_and_restore($course);

        // Confirm that values were transferred correctly into content bank on new course.
        $newcontext = \context_course::instance($newcourseid);

        $this->assertEquals(4, $DB->count_records('contentbank_content'));
        $this->assertEquals(2, $DB->count_records('contentbank_content', ['contextid' => $newcontext->id]));
    }

    /**
     * Test the xAPI state through a backup and restore.
     *
     * @covers \backup_xapistate_structure_step
     * @covers \restore_xapistate_structure_step
     */
    public function test_xapistate_backup(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $this->setUser($user);

        /** @var \mod_h5pactivity_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        /** @var \core_h5p_generator $h5pgenerator */
        $h5pgenerator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Add an attempt to the H5P activity.
        $attemptinfo = [
            'userid' => $user->id,
            'h5pactivityid' => $activity->id,
            'attempt' => 1,
            'interactiontype' => 'compound',
            'rawscore' => 2,
            'maxscore' => 2,
            'duration' => 1,
            'completion' => 1,
            'success' => 0,
        ];
        $generator->create_attempt($attemptinfo);

        // Add also a xAPI state to the H5P activity.
        $filerecord = [
            'contextid' => \context_module::instance($activity->cmid)->id,
            'component' => 'mod_h5pactivity',
            'filearea' => 'package',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'dummy.h5p',
            'addxapistate' => true,
        ];
        $h5pgenerator->generate_h5p_data(false, $filerecord);

        // Check the H5P activity exists and the attempt has been created.
        $this->assertEquals(1, $DB->count_records('h5pactivity'));
        $this->assertEquals(2, $DB->count_records('grade_items'));
        $this->assertEquals(2, $DB->count_records('grade_grades'));
        $this->assertEquals(1, $DB->count_records('xapi_states'));

        // Do backup and restore.
        $this->setAdminUser();
        $newcourseid = $this->backup_and_restore($course, 0, null, true);

        // Confirm that values were transferred correctly into H5P activity on new course.
        $this->assertEquals(2, $DB->count_records('h5pactivity'));
        $this->assertEquals(4, $DB->count_records('grade_items'));
        $this->assertEquals(4, $DB->count_records('grade_grades'));
        $this->assertEquals(2, $DB->count_records('xapi_states'));

        $newactivity = $DB->get_record('h5pactivity', ['course' => $newcourseid]);
        $cm = get_coursemodule_from_instance('h5pactivity', $newactivity->id);
        $context = \context_module::instance($cm->id);
        $this->assertEquals(1, $DB->count_records('xapi_states', ['itemid' => $context->id]));
    }
}
