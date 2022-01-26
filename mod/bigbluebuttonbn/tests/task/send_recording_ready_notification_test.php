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

namespace mod_bigbluebuttonbn\task;

use advanced_testcase;

/**
 * Class containing the scheduled task for lti module.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2019 onwards, Blindside Networks Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_bigbluebuttonbn\task\send_notification
 * @covers \mod_bigbluebuttonbn\task\send_notification
 * @covers \mod_bigbluebuttonbn\task\send_recording_ready_notification
 */
class send_recording_ready_notification_test extends advanced_testcase {
    /**
     * Test the sending of messages.
     */
    public function test_recipients(): void {
        $this->resetAfterTest();
        $this->preventResetByRollback();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $instancedata = $generator->create_module('bigbluebuttonbn', [
            'course' => $course->id,
        ]);

        // Create some users in the course, and some not.
        $editingteacher = $generator->create_and_enrol($course, 'editingteacher');
        $teacher = $generator->create_and_enrol($course, 'teacher');
        $students = [
            $generator->create_and_enrol($course, 'student'),
            $generator->create_and_enrol($course, 'student'),
            $generator->create_and_enrol($course, 'student'),
            $generator->create_and_enrol($course, 'student'),
            $generator->create_and_enrol($course, 'student'),
        ];

        $recipients = array_map(function($user) {
            return $user->id;
        }, $students);
        $recipients[] = $editingteacher->id;
        $recipients[] = $teacher->id;

        $unrelateduser = $generator->create_user();

        $instancedata = $generator->create_module('bigbluebuttonbn', [
            'course' => $course->id,
        ]);

        $stub = $this->getMockBuilder(send_recording_ready_notification::class)
            ->onlyMethods([])
            ->getMock();

        $stub->set_instance_id($instancedata->id);

        // Capture events.
        $sink = $this->redirectMessages();

        // Now execute.
        $stub->execute();

        // Check the events.
        $messages = $sink->get_messages();
        $this->assertCount(7, $messages);

        foreach ($messages as $message) {
            $this->assertNotFalse(array_search($message->useridto, $recipients));
            $this->assertNotEquals($unrelateduser->id, $message->useridto);
            $this->assertEquals($editingteacher->id, $message->useridfrom);
        }
    }

    /**
     * Test the sending of messages.
     */
    public function test_recipients_no_teacher(): void {
        $this->resetAfterTest();
        $this->preventResetByRollback();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $instancedata = $generator->create_module('bigbluebuttonbn', [
            'course' => $course->id,
        ]);

        // Create some users in the course, and some not.
        $students = [
            $generator->create_and_enrol($course, 'student'),
            $generator->create_and_enrol($course, 'student'),
            $generator->create_and_enrol($course, 'student'),
            $generator->create_and_enrol($course, 'student'),
            $generator->create_and_enrol($course, 'student'),
        ];

        $recipients = array_map(function($user) {
            return $user->id;
        }, $students);

        $unrelateduser = $generator->create_user();

        $instancedata = $generator->create_module('bigbluebuttonbn', [
            'course' => $course->id,
        ]);

        $stub = $this->getMockBuilder(send_recording_ready_notification::class)
            ->onlyMethods([])
            ->getMock();

        $stub->set_instance_id($instancedata->id);

        // Capture events.
        $sink = $this->redirectMessages();

        // Now execute.
        $stub->execute();

        // Check the events.
        $messages = $sink->get_messages();
        $this->assertCount(5, $messages);

        $noreplyuser = \core_user::get_noreply_user();
        foreach ($messages as $message) {
            $this->assertNotFalse(array_search($message->useridto, $recipients));
            $this->assertNotEquals($unrelateduser->id, $message->useridto);
            $this->assertEquals($noreplyuser->id, $message->useridfrom);
        }
    }

    /**
     * Test that messages are not sent to a suspended user.
     */
    public function test_messages_sent_suspended_user(): void {
        global $DB;

        $this->resetAfterTest();
        $this->preventResetByRollback();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $instancedata = $generator->create_module('bigbluebuttonbn', [
            'course' => $course->id,
        ]);

        // Create some users in the course, and some not.
        $student = $generator->create_and_enrol($course, 'student');
        $suspendedstudent = $generator->create_and_enrol($course, 'student');
        $DB->set_field('user', 'suspended', 1, ['id' => $suspendedstudent->id]);

        $instancedata = $generator->create_module('bigbluebuttonbn', [
            'course' => $course->id,
        ]);

        $stub = $this->getMockBuilder(send_recording_ready_notification::class)
            ->onlyMethods([])
            ->getMock();

        $stub->set_instance_id($instancedata->id);

        // Capture events.
        $sink = $this->redirectMessages();

        // Now execute.
        $stub->execute();

        // Check the events.
        $messages = $sink->get_messages();
        $this->assertCount(1, $messages);

        $noreplyuser = \core_user::get_noreply_user();
        foreach ($messages as $message) {
            $this->assertEquals($student->id, $message->useridto);
            $this->assertNotEquals($suspendedstudent->id, $message->useridto);
            $this->assertEquals($noreplyuser->id, $message->useridfrom);
        }
    }
}
