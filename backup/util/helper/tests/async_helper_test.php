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

use async_helper;
use backup;
use backup_controller;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Asyncronhous helper tests.
 *
 * @package    core_backup
 * @covers     \async_helper
 * @copyright  2018 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class async_helper_test extends \advanced_testcase {

    /**
     * Tests sending message for asynchronous backup.
     */
    public function test_send_message() {
        global $DB, $USER;
        $this->preventResetByRollback();
        $this->resetAfterTest(true);
        $this->setAdminUser();

        set_config('backup_async_message_users', '1', 'backup');
        set_config('backup_async_message_subject', 'Moodle {operation} completed sucessfully', 'backup');
        set_config('backup_async_message',
                'Dear {user_firstname} {user_lastname}, your {operation} (ID: {backupid}) has completed successfully!',
                'backup');
        set_config('allowedemaildomains', 'example.com');

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();  // Create a course with some availability data set.
        $user2 = $generator->create_user(array('firstname' => 'test', 'lastname' => 'human', 'maildisplay' => 1));
        $generator->enrol_user($user2->id, $course->id, 'editingteacher');

        $DB->set_field_select('message_processors', 'enabled', 0, "name <> 'email'");
        set_user_preference('message_provider_moodle_asyncbackupnotification', 'email', $user2);

        // Make the backup controller for an async backup.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE,
                backup::INTERACTIVE_YES, backup::MODE_ASYNC, $user2->id);
        $bc->finish_ui();
        $backupid = $bc->get_backupid();
        $bc->destroy();

        $sink = $this->redirectEmails();

        // Send message.
        $asynchelper = new async_helper('backup', $backupid);
        $messageid = $asynchelper->send_message();

        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);

        $this->assertGreaterThan(0, $messageid);
        $sink->clear();

        $this->assertSame($USER->email, $email->from);
        $this->assertSame($user2->email, $email->to);
        $this->assertSame('Moodle Backup completed sucessfully', $email->subject);

        // Assert body placeholders have all been replaced.
        $this->assertStringContainsString('Dear test human, your Backup', $email->body);
        $this->assertStringContainsString("(ID: {$backupid})", $email->body);
        $this->assertStringNotContainsString('{', $email->body);
    }

    /**
     * Tests getting the asynchronous backup table items.
     */
    public function test_get_async_backups() {
        global $DB, $CFG, $USER, $PAGE;

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

        // Make the backup controller for an async backup.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_YES, backup::MODE_ASYNC, $USER->id);
        $bc->finish_ui();
        $bc->destroy();
        unset($bc);

        $coursecontext = \context_course::instance($course->id);
        $renderer = $PAGE->get_renderer('core', 'backup');

        $result = \async_helper::get_async_backups($renderer, $coursecontext->instanceid);

        $this->assertEquals(1, count($result));
        $this->assertEquals('backup.mbz', $result[0][0]);
    }

    /**
     * Tests getting the backup record.
     */
    public function test_get_backup_record() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Create the initial backupcontoller.
        $bc = new \backup_controller(\backup::TYPE_1COURSE, $course->id, \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO, \backup::MODE_COPY, $USER->id, \backup::RELEASESESSION_YES);
        $backupid = $bc->get_backupid();
        $bc->destroy();
        $copyrec = \async_helper::get_backup_record($backupid);

        $this->assertEquals($backupid, $copyrec->backupid);

    }

    /**
     * Tests is async pending conditions.
     */
    public function test_is_async_pending() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        set_config('enableasyncbackup', '0');
        $ispending = async_helper::is_async_pending($course->id, 'course', 'backup');

        // Should be false as there are no backups and async backup is false.
        $this->assertFalse($ispending);

        // Create the initial backupcontoller.
        $bc = new \backup_controller(\backup::TYPE_1COURSE, $course->id, \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO, \backup::MODE_ASYNC, $USER->id, \backup::RELEASESESSION_YES);
        $bc->destroy();
        $ispending = async_helper::is_async_pending($course->id, 'course', 'backup');

        // Should be false as there as async backup is false.
        $this->assertFalse($ispending);

        set_config('enableasyncbackup', '1');
        // Should be true as there as async backup is true and there is a pending backup.
        $this->assertFalse($ispending);
    }

    /**
     * Tests is async pending conditions for course copies.
     */
    public function test_is_async_pending_copy() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        set_config('enableasyncbackup', '0');
        $ispending = async_helper::is_async_pending($course->id, 'course', 'backup');

        // Should be false as there are no copies and async backup is false.
        $this->assertFalse($ispending);

        // Create the initial backupcontoller.
        $bc = new \backup_controller(\backup::TYPE_1COURSE, $course->id, \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO, \backup::MODE_COPY, $USER->id, \backup::RELEASESESSION_YES);
        $bc->destroy();
        $ispending = async_helper::is_async_pending($course->id, 'course', 'backup');

        // Should be True as this a copy operation.
        $this->assertTrue($ispending);

        set_config('enableasyncbackup', '1');
        $ispending = async_helper::is_async_pending($course->id, 'course', 'backup');

        // Should be true as there as async backup is true and there is a pending copy.
        $this->assertTrue($ispending);
    }

}
