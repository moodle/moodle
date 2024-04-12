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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->libdir . '/completionlib.php');

/**
 * Course copy tests.
 *
 * @package    core_backup
 * @copyright  2020 onward The Moodle Users Association <https://moodleassociation.org/>
 * @author     Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \copy_helper
 */
class copy_helper_test extends \advanced_testcase {

    /**
     *
     * @var \stdClass Course used for testing.
     */
    protected $course;

    /**
     *
     * @var int User used to perform backups.
     */
    protected $userid;

    /**
     *
     * @var array Ids of users in test course.
     */
    protected $courseusers;

    /**
     *
     * @var array Names of the created activities.
     */
    protected $activitynames;

    /**
     * Set up tasks for all tests.
     */
    protected function setUp(): void {
        global $DB, $CFG, $USER;

        $this->resetAfterTest(true);

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

        // Create some users.
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();
        $user4 = $generator->create_user();
        $this->courseusers = array(
            $user1->id, $user2->id, $user3->id, $user4->id
        );

        // Enrol users into the course.
        $generator->enrol_user($user1->id, $course->id, 'student');
        $generator->enrol_user($user2->id, $course->id, 'editingteacher');
        $generator->enrol_user($user3->id, $course->id, 'manager');
        $generator->enrol_user($user4->id, $course->id, 'editingteacher');
        $generator->enrol_user($user4->id, $course->id, 'manager');

        $availability = '{"op":"|","show":false,"c":[' .
            '{"type":"completion","cm":' . $forum2->cmid .',"e":1},' .
            '{"type":"grade","id":' . $item->id . ',"min":4,"max":94},' .
            '{"type":"grouping","id":' . $grouping->id . '}' .
            ']}';
        $DB->set_field('course_modules', 'availability', $availability, array(
            'id' => $forum->cmid));
        $DB->set_field('course_sections', 'availability', $availability, array(
            'course' => $course->id, 'section' => 1));

        // Add some user data to the course.
        $discussion = $generator->get_plugin_generator('mod_forum')->create_discussion(['course' => $course->id,
            'forum' => $forum->id, 'userid' => $user1->id, 'timemodified' => time(),
            'name' => 'Frog']);
        $generator->get_plugin_generator('mod_forum')->create_post(['discussion' => $discussion->id, 'userid' => $user1->id]);

        $this->course  = $course;
        $this->userid = $USER->id; // Admin.
        $this->activitynames = array(
            $forum->name,
            $forum2->name,
            $assignrow->name
        );

        // Set the user doing the backup to be a manager in the course.
        // By default Managers can restore courses AND users, teachers can only do users.
        $this->setUser($user3);

        // Disable all loggers.
        $CFG->backup_error_log_logger_level = backup::LOG_NONE;
        $CFG->backup_output_indented_logger_level = backup::LOG_NONE;
        $CFG->backup_file_logger_level = backup::LOG_NONE;
        $CFG->backup_database_logger_level = backup::LOG_NONE;
        $CFG->backup_file_logger_level_extra = backup::LOG_NONE;
    }

    /**
     * Test process form data with invalid data.
     *
     * @covers ::process_formdata
     */
    public function test_process_formdata_missing_fields() {
        $this->expectException(\moodle_exception::class);
        \copy_helper::process_formdata(new \stdClass);
    }

    /**
     * Test processing form data.
     *
     * @covers ::process_formdata
     */
    public function test_process_formdata() {
        $validformdata = [
            'courseid' => 1729,
            'fullname' => 'Taxicab Numbers',
            'shortname' => 'Taxi101',
            'category' => 2,
            'visible' => 1,
            'startdate' => 87539319,
            'enddate' => 6963472309248,
            'idnumber' => 1730,
            'userdata' => 1
        ];

        $roles = [
            'role_one' => 1,
            'role_two' => 2,
            'role_three' => 0
        ];

        $expected = (object)array_merge($validformdata, ['keptroles' => []]);
        $expected->keptroles = [1, 2];
        $processed = \copy_helper::process_formdata(
            (object)array_merge(
                $validformdata,
                $roles,
                ['extra' => 'stuff', 'remove' => 'this'])
        );

        $this->assertEquals($expected, $processed);
    }

    /**
     * Test orphaned controller cleanup.
     *
     * @covers ::cleanup_orphaned_copy_controllers
     */
    public function test_cleanup_orphaned_copy_controllers() {
        global $DB;

        // Mock up the form data.
        $formdata = new \stdClass;
        $formdata->courseid = $this->course->id;
        $formdata->fullname = 'foo';
        $formdata->shortname = 'data1';
        $formdata->category = 1;
        $formdata->visible = 1;
        $formdata->startdate = 1582376400;
        $formdata->enddate = 0;
        $formdata->idnumber = 123;
        $formdata->userdata = 1;
        $formdata->role_1 = 1;
        $formdata->role_3 = 3;
        $formdata->role_5 = 5;

        $copies = [];
        for ($i = 0; $i < 5; $i++) {
            $formdata->shortname = 'data' . $i;
            $copies[] = \copy_helper::create_copy($formdata);
        }

        // Delete one of the restore controllers. Simulates a situation where copy creation
        // interrupted and the restore controller never gets created.
        $DB->delete_records('backup_controllers', ['backupid' => $copies[0]['restoreid']]);

        // Set a backup/restore controller pair to be in an intermediate state.
        \backup_controller::load_controller($copies[2]['backupid'])->set_status(backup::STATUS_FINISHED_OK);

        // Set a backup/restore controller pair to completed.
        \backup_controller::load_controller($copies[3]['backupid'])->set_status(backup::STATUS_FINISHED_OK);
        \restore_controller::load_controller($copies[3]['restoreid'])->set_status(backup::STATUS_FINISHED_OK);

        // Set a backup/restore controller pair to have a failed backup.
        \backup_controller::load_controller($copies[4]['backupid'])->set_status(backup::STATUS_FINISHED_ERR);

        // Create some backup/restore controllers that are unrelated to course copies.
        $bc = new \backup_controller(backup::TYPE_1COURSE, 1, backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_ASYNC,
                2, backup::RELEASESESSION_YES);
        $rc = new \restore_controller('restore-test-1729', 1, backup::INTERACTIVE_NO, backup::MODE_ASYNC, 1, 2);
        $rc->save_controller();
        $unrelatedvanillacontrollers = ['backupid' => $bc->get_backupid(), 'restoreid' => $rc->get_restoreid()];

        $bc = new \backup_controller(backup::TYPE_1COURSE, 1, backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_ASYNC,
                2, backup::RELEASESESSION_YES);
        $rc = new \restore_controller('restore-test-1729', 1, backup::INTERACTIVE_NO, backup::MODE_ASYNC, 1, 2);
        $bc->set_status(backup::STATUS_FINISHED_OK);
        $rc->set_status(backup::STATUS_FINISHED_OK);
        $unrelatedfinishedcontrollers = ['backupid' => $bc->get_backupid(), 'restoreid' => $rc->get_restoreid()];

        $bc = new \backup_controller(backup::TYPE_1COURSE, 1, backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_ASYNC,
                2, backup::RELEASESESSION_YES);
        $rc = new \restore_controller('restore-test-1729', 1, backup::INTERACTIVE_NO, backup::MODE_ASYNC, 1, 2);
        $bc->set_status(backup::STATUS_FINISHED_ERR);
        $rc->set_status(backup::STATUS_FINISHED_ERR);
        $unrelatedfailedcontrollers = ['backupid' => $bc->get_backupid(), 'restoreid' => $rc->get_restoreid()];

        // Clean up the backup_controllers table.
        $records = $DB->get_records('backup_controllers', null, '', 'id, backupid, status, operation, purpose, timecreated');
        \copy_helper::cleanup_orphaned_copy_controllers($records, 0);

        // Retrieve them again and check.
        $records = $DB->get_records('backup_controllers', null, '', 'backupid, status');

        // Verify the backup associated with the deleted restore is marked as failed.
        $this->assertEquals(backup::STATUS_FINISHED_ERR, $records[$copies[0]['backupid']]->status);

        // Verify other controllers remain untouched.
        $this->assertEquals(backup::STATUS_AWAITING, $records[$copies[1]['backupid']]->status);
        $this->assertEquals(backup::STATUS_REQUIRE_CONV, $records[$copies[1]['restoreid']]->status);
        $this->assertEquals(backup::STATUS_FINISHED_OK, $records[$copies[2]['backupid']]->status);
        $this->assertEquals(backup::STATUS_REQUIRE_CONV, $records[$copies[2]['restoreid']]->status);
        $this->assertEquals(backup::STATUS_FINISHED_OK, $records[$copies[3]['restoreid']]->status);
        $this->assertEquals(backup::STATUS_FINISHED_OK, $records[$copies[3]['backupid']]->status);

        // Verify that the restore associated with the failed backup is also marked as failed.
        $this->assertEquals(backup::STATUS_FINISHED_ERR, $records[$copies[4]['restoreid']]->status);

        // Verify that the unrelated controllers remain unchanged.
        $this->assertEquals(backup::STATUS_AWAITING, $records[$unrelatedvanillacontrollers['backupid']]->status);
        $this->assertEquals(backup::STATUS_REQUIRE_CONV, $records[$unrelatedvanillacontrollers['restoreid']]->status);
        $this->assertEquals(backup::STATUS_FINISHED_OK, $records[$unrelatedfinishedcontrollers['backupid']]->status);
        $this->assertEquals(backup::STATUS_FINISHED_OK, $records[$unrelatedfinishedcontrollers['restoreid']]->status);
        $this->assertEquals(backup::STATUS_FINISHED_ERR, $records[$unrelatedfailedcontrollers['backupid']]->status);
        $this->assertEquals(backup::STATUS_FINISHED_ERR, $records[$unrelatedfailedcontrollers['restoreid']]->status);
    }

    /**
     * Test creating a course copy.
     *
     * @covers ::create_copy
     */
    public function test_create_copy() {

        // Mock up the form data.
        $formdata = new \stdClass;
        $formdata->courseid = $this->course->id;
        $formdata->fullname = 'foo';
        $formdata->shortname = 'bar';
        $formdata->category = 1;
        $formdata->visible = 1;
        $formdata->startdate = 1582376400;
        $formdata->enddate = 0;
        $formdata->idnumber = 123;
        $formdata->userdata = 1;
        $formdata->role_1 = 1;
        $formdata->role_3 = 3;
        $formdata->role_5 = 5;

        $copydata = \copy_helper::process_formdata($formdata);
        $result = \copy_helper::create_copy($copydata);

        // Load the controllers, to extract the data we need.
        $bc = \backup_controller::load_controller($result['backupid']);
        $rc = \restore_controller::load_controller($result['restoreid']);

        // Check the backup controller.
        $this->assertEquals(backup::MODE_COPY, $bc->get_mode());
        $this->assertEquals($this->course->id, $bc->get_courseid());
        $this->assertEquals(backup::TYPE_1COURSE, $bc->get_type());

        // Check the restore controller.
        $newcourseid = $rc->get_courseid();
        $newcourse = get_course($newcourseid);

        $this->assertEquals(get_string('copyingcourse', 'backup'), $newcourse->fullname);
        $this->assertEquals(get_string('copyingcourseshortname', 'backup'), $newcourse->shortname);
        $this->assertEquals(backup::MODE_COPY, $rc->get_mode());
        $this->assertEquals($newcourseid, $rc->get_courseid());

        // Check the created ad-hoc task.
        $now = time();
        $task = \core\task\manager::get_next_adhoc_task($now);

        $this->assertInstanceOf('\\core\\task\\asynchronous_copy_task', $task);
        $this->assertEquals($result, (array)$task->get_custom_data());

        \core\task\manager::adhoc_task_complete($task);
    }

    /**
     * Test getting the current copies.
     *
     * @covers ::get_copies
     */
    public function test_get_copies() {
        global $USER;

        // Mock up the form data.
        $formdata = new \stdClass;
        $formdata->courseid = $this->course->id;
        $formdata->fullname = 'foo';
        $formdata->shortname = 'bar';
        $formdata->category = 1;
        $formdata->visible = 1;
        $formdata->startdate = 1582376400;
        $formdata->enddate = 0;
        $formdata->idnumber = '';
        $formdata->userdata = 1;
        $formdata->role_1 = 1;
        $formdata->role_3 = 3;
        $formdata->role_5 = 5;

        $formdata2 = clone($formdata);
        $formdata2->shortname = 'tree';

        // Create some copies.
        $copydata = \copy_helper::process_formdata($formdata);
        $result = \copy_helper::create_copy($copydata);

        // Backup, awaiting.
        $copies = \copy_helper::get_copies($USER->id);
        $this->assertEquals($result['backupid'], $copies[0]->backupid);
        $this->assertEquals($result['restoreid'], $copies[0]->restoreid);
        $this->assertEquals(\backup::STATUS_AWAITING, $copies[0]->status);
        $this->assertEquals(\backup::OPERATION_BACKUP, $copies[0]->operation);

        $bc = \backup_controller::load_controller($result['backupid']);

        // Backup, in progress.
        $bc->set_status(\backup::STATUS_EXECUTING);
        $copies = \copy_helper::get_copies($USER->id);
        $this->assertEquals($result['backupid'], $copies[0]->backupid);
        $this->assertEquals($result['restoreid'], $copies[0]->restoreid);
        $this->assertEquals(\backup::STATUS_EXECUTING, $copies[0]->status);
        $this->assertEquals(\backup::OPERATION_BACKUP, $copies[0]->operation);

        // Restore, ready to process.
        $bc->set_status(\backup::STATUS_FINISHED_OK);
        $copies = \copy_helper::get_copies($USER->id);
        $this->assertEquals(null, $copies[0]->backupid);
        $this->assertEquals($result['restoreid'], $copies[0]->restoreid);
        $this->assertEquals(\backup::STATUS_REQUIRE_CONV, $copies[0]->status);
        $this->assertEquals(\backup::OPERATION_RESTORE, $copies[0]->operation);

        // No records.
        $bc->set_status(\backup::STATUS_FINISHED_ERR);
        $copies = \copy_helper::get_copies($USER->id);
        $this->assertEmpty($copies);

        $copydata2 = \copy_helper::process_formdata($formdata2);
        $result2 = \copy_helper::create_copy($copydata2);
        // Set the second copy to be complete.
        $bc = \backup_controller::load_controller($result2['backupid']);
        $bc->set_status(\backup::STATUS_FINISHED_OK);
        // Set the restore to be finished.
        $rc = \backup_controller::load_controller($result2['restoreid']);
        $rc->set_status(\backup::STATUS_FINISHED_OK);

        // No records.
        $copies = \copy_helper::get_copies($USER->id);
        $this->assertEmpty($copies);
    }

    /**
     * Test getting the current copies when they are in an invalid state.
     *
     * @covers ::get_copies
     */
    public function test_get_copies_invalid_state() {
        global $DB, $USER;

        // Mock up the form data.
        $formdata = new \stdClass;
        $formdata->courseid = $this->course->id;
        $formdata->fullname = 'foo';
        $formdata->shortname = 'bar';
        $formdata->category = 1;
        $formdata->visible = 1;
        $formdata->startdate = 1582376400;
        $formdata->enddate = 0;
        $formdata->idnumber = '';
        $formdata->userdata = 1;
        $formdata->role_1 = 1;
        $formdata->role_3 = 3;
        $formdata->role_5 = 5;

        $formdata2 = clone ($formdata);
        $formdata2->shortname = 'tree';

        // Create some copies.
        $copydata = \copy_helper::process_formdata($formdata);
        $result = \copy_helper::create_copy($copydata);
        $copydata2 = \copy_helper::process_formdata($formdata2);
        $result2 = \copy_helper::create_copy($copydata2);

        $copies = \copy_helper::get_copies($USER->id);

        // Verify get_copies gives back both backup controllers.
        $this->assertEqualsCanonicalizing([$result['backupid'], $result2['backupid']], array_column($copies, 'backupid'));

        // Set one of the backup controllers to failed, this should cause it to not be present.
        \backup_controller::load_controller($result['backupid'])->set_status(backup::STATUS_FINISHED_ERR);
        $copies = \copy_helper::get_copies($USER->id);

        // Verify there is only one backup listed, and that it is not the failed one.
        $this->assertEqualsCanonicalizing([$result2['backupid']], array_column($copies, 'backupid'));

        // Set the controller back to awaiting.
        \backup_controller::load_controller($result['backupid'])->set_status(backup::STATUS_AWAITING);
        $copies = \copy_helper::get_copies($USER->id);

        // Verify both backup controllers are back.
        $this->assertEqualsCanonicalizing([$result['backupid'], $result2['backupid']], array_column($copies, 'backupid'));

        // Delete the restore controller for one of the copies, this should cause it to not be present.
        $DB->delete_records('backup_controllers', ['backupid' => $result['restoreid']]);
        $copies = \copy_helper::get_copies($USER->id);

        // Verify there is only one backup listed, and that it is not the failed one.
        $this->assertEqualsCanonicalizing([$result2['backupid']], array_column($copies, 'backupid'));
    }

    /**
     * Test getting the current copies for specific course.
     *
     * @covers ::get_copies
     */
    public function test_get_copies_course() {
        global $USER;

        // Mock up the form data.
        $formdata = new \stdClass;
        $formdata->courseid = $this->course->id;
        $formdata->fullname = 'foo';
        $formdata->shortname = 'bar';
        $formdata->category = 1;
        $formdata->visible = 1;
        $formdata->startdate = 1582376400;
        $formdata->enddate = 0;
        $formdata->idnumber = '';
        $formdata->userdata = 1;
        $formdata->role_1 = 1;
        $formdata->role_3 = 3;
        $formdata->role_5 = 5;

        // Create some copies.
        $copydata = \copy_helper::process_formdata($formdata);
        \copy_helper::create_copy($copydata);

        // No copies match this course id.
        $copies = \copy_helper::get_copies($USER->id, ($this->course->id + 1));
        $this->assertEmpty($copies);
    }

    /**
     * Test getting the current copies if course has been deleted.
     *
     * @covers ::get_copies
     */
    public function test_get_copies_course_deleted() {
        global $USER;

        // Mock up the form data.
        $formdata = new \stdClass;
        $formdata->courseid = $this->course->id;
        $formdata->fullname = 'foo';
        $formdata->shortname = 'bar';
        $formdata->category = 1;
        $formdata->visible = 1;
        $formdata->startdate = 1582376400;
        $formdata->enddate = 0;
        $formdata->idnumber = '';
        $formdata->userdata = 1;
        $formdata->role_1 = 1;
        $formdata->role_3 = 3;
        $formdata->role_5 = 5;

        // Create some copies.
        $copydata = \copy_helper::process_formdata($formdata);
        \copy_helper::create_copy($copydata);

        delete_course($this->course->id, false);

        // No copies match this course id as it has been deleted.
        $copies = \copy_helper::get_copies($USER->id, ($this->course->id));
        $this->assertEmpty($copies);
    }

    /**
     * Test course copy.
     */
    public function test_course_copy() {
        global $DB;

        // Mock up the form data.
        $formdata = new \stdClass;
        $formdata->courseid = $this->course->id;
        $formdata->fullname = 'copy course';
        $formdata->shortname = 'copy course short';
        $formdata->category = 1;
        $formdata->visible = 0;
        $formdata->startdate = 1582376400;
        $formdata->enddate = 1582386400;
        $formdata->idnumber = 123;
        $formdata->userdata = 1;
        $formdata->role_1 = 1;
        $formdata->role_3 = 3;
        $formdata->role_5 = 5;

        // Create the course copy records and associated ad-hoc task.
        $copydata = \copy_helper::process_formdata($formdata);
        $copyids = \copy_helper::create_copy($copydata);

        $courseid = $this->course->id;

        // We are expecting trace output during this test.
        $this->expectOutputRegex("/$courseid/");

        // Execute adhoc task.
        $now = time();
        $task = \core\task\manager::get_next_adhoc_task($now);
        $this->assertInstanceOf('\\core\\task\\asynchronous_copy_task', $task);
        $task->execute();
        \core\task\manager::adhoc_task_complete($task);

        $postbackuprec = $DB->get_record('backup_controllers', array('backupid' => $copyids['backupid']));
        $postrestorerec = $DB->get_record('backup_controllers', array('backupid' => $copyids['restoreid']));

        // Check backup was completed successfully.
        $this->assertEquals(backup::STATUS_FINISHED_OK, $postbackuprec->status);
        $this->assertEquals(1.0, $postbackuprec->progress);

        // Check restore was completed successfully.
        $this->assertEquals(backup::STATUS_FINISHED_OK, $postrestorerec->status);
        $this->assertEquals(1.0, $postrestorerec->progress);

        // Check the restored course itself.
        $coursecontext = \context_course::instance($postrestorerec->itemid);
        $users = get_enrolled_users($coursecontext);

        $modinfo = get_fast_modinfo($postrestorerec->itemid);
        $forums = $modinfo->get_instances_of('forum');
        $forum = reset($forums);
        $discussions = forum_get_discussions($forum);
        $course = $modinfo->get_course();

        $this->assertEquals($formdata->startdate, $course->startdate);
        $this->assertEquals($formdata->enddate, $course->enddate);
        $this->assertEquals('copy course', $course->fullname);
        $this->assertEquals('copy course short',  $course->shortname);
        $this->assertEquals(0,  $course->visible);
        $this->assertEquals(123,  $course->idnumber);

        foreach ($modinfo->get_cms() as $cm) {
            $this->assertContains($cm->get_formatted_name(), $this->activitynames);
        }

        foreach ($this->courseusers as $user) {
            $this->assertEquals($user, $users[$user]->id);
        }

        $this->assertEquals(count($this->courseusers), count($users));
        $this->assertEquals(2, count($discussions));
    }

    /**
     * Test course copy, not including any users (or data).
     */
    public function test_course_copy_no_users() {
        global $DB;

        // Mock up the form data.
        $formdata = new \stdClass;
        $formdata->courseid = $this->course->id;
        $formdata->fullname = 'copy course';
        $formdata->shortname = 'copy course short';
        $formdata->category = 1;
        $formdata->visible = 0;
        $formdata->startdate = 1582376400;
        $formdata->enddate = 1582386400;
        $formdata->idnumber = 123;
        $formdata->userdata = 1;
        $formdata->role_1 = 0;
        $formdata->role_3 = 0;
        $formdata->role_5 = 0;

        // Create the course copy records and associated ad-hoc task.
        $copydata = \copy_helper::process_formdata($formdata);
        $copyids = \copy_helper::create_copy($copydata);

        $courseid = $this->course->id;

        // We are expecting trace output during this test.
        $this->expectOutputRegex("/$courseid/");

        // Execute adhoc task.
        $now = time();
        $task = \core\task\manager::get_next_adhoc_task($now);
        $this->assertInstanceOf('\\core\\task\\asynchronous_copy_task', $task);
        $task->execute();
        \core\task\manager::adhoc_task_complete($task);

        $postrestorerec = $DB->get_record('backup_controllers', array('backupid' => $copyids['restoreid']));

        // Check the restored course itself.
        $coursecontext = \context_course::instance($postrestorerec->itemid);
        $users = get_enrolled_users($coursecontext);

        $modinfo = get_fast_modinfo($postrestorerec->itemid);
        $forums = $modinfo->get_instances_of('forum');
        $forum = reset($forums);
        $discussions = forum_get_discussions($forum);
        $course = $modinfo->get_course();

        $this->assertEquals($formdata->startdate, $course->startdate);
        $this->assertEquals($formdata->enddate, $course->enddate);
        $this->assertEquals('copy course', $course->fullname);
        $this->assertEquals('copy course short',  $course->shortname);
        $this->assertEquals(0,  $course->visible);
        $this->assertEquals(123,  $course->idnumber);

        foreach ($modinfo->get_cms() as $cm) {
            $this->assertContains($cm->get_formatted_name(), $this->activitynames);
        }

        // Should be no discussions as the user that made them wasn't included.
        $this->assertEquals(0, count($discussions));

        // There should only be one user in the new course, and that's the user who did the copy.
        $this->assertEquals(1, count($users));
        $this->assertEquals($this->courseusers[2], $users[$this->courseusers[2]]->id);

    }

    /**
     * Test course copy, including students and their data.
     */
    public function test_course_copy_students_data() {
        global $DB;

        // Mock up the form data.
        $formdata = new \stdClass;
        $formdata->courseid = $this->course->id;
        $formdata->fullname = 'copy course';
        $formdata->shortname = 'copy course short';
        $formdata->category = 1;
        $formdata->visible = 0;
        $formdata->startdate = 1582376400;
        $formdata->enddate = 1582386400;
        $formdata->idnumber = 123;
        $formdata->userdata = 1;
        $formdata->role_1 = 0;
        $formdata->role_3 = 0;
        $formdata->role_5 = 5;

        // Create the course copy records and associated ad-hoc task.
        $copydata = \copy_helper::process_formdata($formdata);
        $copyids = \copy_helper::create_copy($copydata);

        $courseid = $this->course->id;

        // We are expecting trace output during this test.
        $this->expectOutputRegex("/$courseid/");

        // Execute adhoc task.
        $now = time();
        $task = \core\task\manager::get_next_adhoc_task($now);
        $this->assertInstanceOf('\\core\\task\\asynchronous_copy_task', $task);
        $task->execute();
        \core\task\manager::adhoc_task_complete($task);

        $postrestorerec = $DB->get_record('backup_controllers', array('backupid' => $copyids['restoreid']));

        // Check the restored course itself.
        $coursecontext = \context_course::instance($postrestorerec->itemid);
        $users = get_enrolled_users($coursecontext);

        $modinfo = get_fast_modinfo($postrestorerec->itemid);
        $forums = $modinfo->get_instances_of('forum');
        $forum = reset($forums);
        $discussions = forum_get_discussions($forum);
        $course = $modinfo->get_course();

        $this->assertEquals($formdata->startdate, $course->startdate);
        $this->assertEquals($formdata->enddate, $course->enddate);
        $this->assertEquals('copy course', $course->fullname);
        $this->assertEquals('copy course short',  $course->shortname);
        $this->assertEquals(0,  $course->visible);
        $this->assertEquals(123,  $course->idnumber);

        foreach ($modinfo->get_cms() as $cm) {
            $this->assertContains($cm->get_formatted_name(), $this->activitynames);
        }

        // Should be no discussions as the user that made them wasn't included.
        $this->assertEquals(2, count($discussions));

        // There should only be two users in the new course. The copier and one student.
        $this->assertEquals(2, count($users));
        $this->assertEquals($this->courseusers[2], $users[$this->courseusers[2]]->id);
        $this->assertEquals($this->courseusers[0], $users[$this->courseusers[0]]->id);
    }

    /**
     * Test course copy, not including any users (or data).
     */
    public function test_course_copy_no_data() {
        global $DB;

        // Mock up the form data.
        $formdata = new \stdClass;
        $formdata->courseid = $this->course->id;
        $formdata->fullname = 'copy course';
        $formdata->shortname = 'copy course short';
        $formdata->category = 1;
        $formdata->visible = 0;
        $formdata->startdate = 1582376400;
        $formdata->enddate = 1582386400;
        $formdata->idnumber = 123;
        $formdata->userdata = 0;
        $formdata->role_1 = 1;
        $formdata->role_3 = 3;
        $formdata->role_5 = 5;

        // Create the course copy records and associated ad-hoc task.
        $copydata = \copy_helper::process_formdata($formdata);
        $copyids = \copy_helper::create_copy($copydata);

        $courseid = $this->course->id;

        // We are expecting trace output during this test.
        $this->expectOutputRegex("/$courseid/");

        // Execute adhoc task.
        $now = time();
        $task = \core\task\manager::get_next_adhoc_task($now);
        $this->assertInstanceOf('\\core\\task\\asynchronous_copy_task', $task);
        $task->execute();
        \core\task\manager::adhoc_task_complete($task);

        $postrestorerec = $DB->get_record('backup_controllers', array('backupid' => $copyids['restoreid']));

        // Check the restored course itself.
        $coursecontext = \context_course::instance($postrestorerec->itemid);
        $users = get_enrolled_users($coursecontext);

        get_fast_modinfo($postrestorerec->itemid, 0, true);
        $modinfo = get_fast_modinfo($postrestorerec->itemid);
        $forums = $modinfo->get_instances_of('forum');
        $forum = reset($forums);
        $discussions = forum_get_discussions($forum);
        $course = $modinfo->get_course();

        $this->assertEquals($formdata->startdate, $course->startdate);
        $this->assertEquals($formdata->enddate, $course->enddate);
        $this->assertEquals('copy course', $course->fullname);
        $this->assertEquals('copy course short',  $course->shortname);
        $this->assertEquals(0,  $course->visible);
        $this->assertEquals(123,  $course->idnumber);

        foreach ($modinfo->get_cms() as $cm) {
            $this->assertContains($cm->get_formatted_name(), $this->activitynames);
        }

        // Should be no discussions as the user data wasn't included.
        $this->assertEquals(0, count($discussions));

        // There should only be all users in the new course.
        $this->assertEquals(count($this->courseusers), count($users));
    }

    /**
     * Test instantiation with incomplete formdata.
     */
    public function test_malformed_instantiation() {
        // Mock up the form data, missing things so we get an exception.
        $formdata = new \stdClass;
        $formdata->courseid = $this->course->id;
        $formdata->fullname = 'copy course';
        $formdata->shortname = 'copy course short';
        $formdata->category = 1;

        // Expect and exception as form data is incomplete.
        $this->expectException(\moodle_exception::class);
        $copydata = \copy_helper::process_formdata($formdata);
        \copy_helper::create_copy($copydata);
    }
}
