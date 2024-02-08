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

namespace core_course\task;

use availability_date\condition;
use context_user;
use core_availability\tree;

/**
 * Contains tests for course related notifications.
 *
 * @package    core
 * @subpackage course
 * @covers     \core_course\task\content_notification_task
 * @copyright  2021 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_notification_task_test extends \advanced_testcase {

    /**
     * Test execution of task
     */
    public function test_execute(): void {
        global $DB, $CFG, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create course, with a course image.
        $draft = get_file_storage()->create_file_from_pathname([
            'component' => 'user',
            'filearea' => 'draft',
            'contextid' => context_user::instance($USER->id)->id,
            'itemid' => file_get_unused_draft_itemid(),
            'filename' => 'gd-logo.png',
            'filepath' => '/',
        ], "{$CFG->libdir}/tests/fixtures/gd-logo.png");

        $course = $this->getDataGenerator()->create_course(['overviewfiles_filemanager' => $draft->get_itemid()]);

        // Enrol couple of students to receive a notification and one unactive enrolment.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        self::getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        self::getDataGenerator()->enrol_user($user2->id, $course->id, 'student');
        self::getDataGenerator()->enrol_user($user3->id, $course->id, 'student', 'manual', time() - YEARSECS, time() - WEEKSECS);

        $url = self::getDataGenerator()->create_module('url', ['course' => $course]);

        // Test update.
        $moduleavailability = tree::get_root_json([condition::get_json(condition::DIRECTION_FROM, time() + HOURSECS)]);
        $moduleinfo = $DB->get_record('course_modules', array('id' => $url->cmid));
        $moduleinfo->modulename = 'url';
        $moduleinfo->coursemodule = $url->cmid;
        $moduleinfo->display = 1;
        $moduleinfo->availability = json_encode($moduleavailability);
        $moduleinfo->externalurl = '';
        $moduleinfo->update = 1;
        $draftid = 0;
        file_prepare_draft_area($draftid, \context_module::instance($url->cmid)->id, 'mod_url', 'intro', 0);
        $moduleinfo->introeditor = [
            'itemid' => $draftid,
            'text' => '<p>Yo</p>',
            'format' => FORMAT_HTML
        ];
        $modurl = (new \moodle_url('/mod/url/view.php', ['id' => $url->cmid]))->out(false);

        // Check course content changed notifications.
        $moduleinfo->coursecontentnotification = 1;

        // Create the module.
        update_module(clone $moduleinfo);   // We use clone to keep the original object untouch for later use.

        // Redirect messages to sink and stop buffer output from CLI task.
        $sink = $this->redirectMessages();
        ob_start();
        $this->runAdhocTasks('\core_course\task\content_notification_task');
        $output = ob_get_contents();
        ob_end_clean();
        $messages = $sink->get_messages();
        $sink->close();

        // The module isn't available for one hour, there should be no notifications.
        $this->assertCount(0, $messages);

        // Remove availability condition.
        $this->setAdminUser();
        $moduleinfo->availability = null;
        update_module(clone $moduleinfo);

        // Redirect messages to sink and stop buffer output from CLI task.
        $sink = $this->redirectMessages();
        ob_start();
        $this->runAdhocTasks('\core_course\task\content_notification_task');
        $output = ob_get_contents();
        ob_end_clean();
        $messages = $sink->get_messages();
        $sink->close();

        // We have 3 students, one with a non-active enrolment that should not receive a notification.
        $this->assertCount(2, $messages);
        foreach ($messages as $message) {
            $this->assertEquals('coursecontentupdated', $message->eventtype);
            $this->assertEquals($modurl, $message->contexturl);

            $messagecustomdata = json_decode($message->customdata);
            $this->assertEquals($course->id, $messagecustomdata->courseid);
            $this->assertObjectHasAttribute('notificationiconurl', $messagecustomdata);
            $this->assertObjectHasAttribute('notificationpictureurl', $messagecustomdata);
        }

        // Now, set the course to not visible.
        $DB->set_field('course', 'visible', 0, ['id' => $course->id]);
        $this->setAdminUser();
        update_module(clone $moduleinfo);

        // Redirect messages to sink and stop buffer output from CLI task.
        $sink = $this->redirectMessages();
        ob_start();
        $this->runAdhocTasks('\core_course\task\content_notification_task');
        $output = ob_get_contents();
        ob_end_clean();
        $messages = $sink->get_messages();
        $sink->close();
        // No messages, course not visible.
        $this->assertCount(0, $messages);

        // Now, set the module to not visible.
        $DB->set_field('course', 'visible', 1, ['id' => $course->id]);
        $this->setAdminUser();
        $moduleinfo->visible = 0;
        update_module(clone $moduleinfo);

        // Redirect messages to sink and stop buffer output from CLI task.
        $sink = $this->redirectMessages();
        ob_start();
        $this->runAdhocTasks('\core_course\task\content_notification_task');
        $output = ob_get_contents();
        ob_end_clean();
        $messages = $sink->get_messages();
        $sink->close();
        // No messages, module not visible.
        $this->assertCount(0, $messages);
    }
}
