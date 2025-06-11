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
namespace theme_snap;
use theme_snap\webservice\ws_feed;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;

/**
 * Test ws_feed web service
 * @author    Oscar Nadjar <oscar.nadjar@openlms.net>
 * @copyright Copyright (c) 2020 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
class webservice_ws_feed_activity_test extends \advanced_testcase {

    public function test_service_parameters() {
        $params = ws_feed::service_parameters();
        $this->assertTrue($params instanceof external_function_parameters);
    }

    public function test_service_returns() {
        $returns = ws_feed::service_returns();
        $this->assertTrue($returns instanceof external_multiple_structure);
    }

    public function test_service_message() {
        $this->resetAfterTest();

        $userfrom = $this->getDataGenerator()->create_user();
        $userto = $this->getDataGenerator()->create_user();
        $this->setUser($userto);

        $message = 'Message';
        for ($messagen = 1; $messagen <= 4; $messagen++) {
            $this->create_message([$userfrom, $userto], \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
                $message . $messagen, $messagen);
        }
        $serviceresult = ws_feed::service('messages');
        $this->assertTrue(is_array($serviceresult));
        $this->assertCount(3, $serviceresult);
        $this->assertEquals($serviceresult[0]['subTitle'], 'Message4');
        $itemid = $serviceresult[0]['itemId'];

        $this->create_message([$userfrom, $userto], \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            $message . $messagen, $messagen);

        $serviceresult = ws_feed::service('messages', 1, 3, $itemid);
        $this->assertCount(1, $serviceresult);
        $this->assertEquals($serviceresult[0]['subTitle'], 'Message1');
        $itemid = $serviceresult[0]['itemId'];

        $serviceresult = ws_feed::service('messages', 1, 3, $itemid);
        $this->assertEmpty($serviceresult);

        $serviceresult = ws_feed::service('messages');
        $this->assertCount(3, $serviceresult);
        $this->assertEquals($serviceresult[0]['subTitle'], 'Message5');
    }

    public function test_feed_deadline() {
        global $DB, $CFG;
        $this->resetAfterTest();

        $this->setAdminUser();
        $student = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $record = new \stdClass();
        $record->course = $course;

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id);

        // Create an activity, e.g. an Assign activity.
        $assignmodulename = 'assign';
        $assignactivityname = 'Assignment';
        $assign = $this->getDataGenerator()->create_module($assignmodulename, $record);
        $this->getDataGenerator()->create_event(array(
            'userid' => $student->id,
            'modulename' => $assignmodulename,
            'eventtype' => 'due',
            'instance' => $assign->id,
        ));

        $this->setUser($student);
        $deadlines = ws_feed::service('deadlines');

        $this->assertCount(1, $deadlines);
        $this->assertEqualsIgnoringCase($assignactivityname, $deadlines[0]['iconDesc']);

        // Create a second activity, e.g. a Quiz activity.
        $this->setAdminUser();
        $quizmodulename = 'quiz';
        $quiz = $this->getDataGenerator()->create_module($quizmodulename, $record);
        $this->getDataGenerator()->create_event(array(
            'userid' => $student->id,
            'modulename' => $quizmodulename,
            'eventtype' => 'due',
            'instance' => $quiz->id,
        ));

        $this->setUser($student);
        $deadlines = ws_feed::service('deadlines');

        $this->assertCount(2, $deadlines);
        $this->assertEqualsIgnoringCase($assignactivityname, $deadlines[0]['iconDesc']);
        $this->assertEqualsIgnoringCase($quizmodulename, $deadlines[1]['iconDesc']);
        $this->assertEquals($assignmodulename, $deadlines[0]['modName']);
        $this->assertEquals($quizmodulename, $deadlines[1]['modName']);
        // Create a label activity and verify that it is not being returned by web service.
        $this->setAdminUser();
        $labelmodulename = 'label';
        $label = $this->getDataGenerator()->create_module($labelmodulename, $record);
        $this->getDataGenerator()->create_event(array(
            'userid' => $student->id,
            'modulename' => $labelmodulename,
            'eventtype' => 'due',
            'instance' => $label->id,
        ));

        $this->setUser($student);
        $deadlines = ws_feed::service('deadlines');

        // Try all the possible combinations of disabling activities (8 in total).

        // No activities disabled.
        $this->assertCount(3, $deadlines);
        $this->assertEqualsIgnoringCase($assignactivityname, $deadlines[0]['iconDesc']);
        $this->assertEqualsIgnoringCase($quizmodulename, $deadlines[1]['iconDesc']);
        $this->assertEqualsIgnoringCase('Text and media area', $deadlines[2]['iconDesc']);

        // Disable the assign activity.
        $CFG->theme_snap_disable_deadline_mods = ['assign'];

        $deadlinescache = \cache::make('theme_snap', 'activity_deadlines');
        $deadlinescache->purge();
        $deadlines = ws_feed::service('deadlines');
        $labeldisplayname = 'Text and media area';
        $this->assertCount(2, $deadlines);
        $this->assertEqualsIgnoringCase($quizmodulename, $deadlines[0]['iconDesc']);
        $this->assertEqualsIgnoringCase($labeldisplayname, $deadlines[1]['iconDesc']);

        // Disable the quiz activity.
        $CFG->theme_snap_disable_deadline_mods = ['quiz'];

        $deadlinescache->purge();
        $deadlines = ws_feed::service('deadlines');

        $this->assertCount(2, $deadlines);
        $this->assertEqualsIgnoringCase($assignactivityname, $deadlines[0]['iconDesc']);
        $this->assertEqualsIgnoringCase($labeldisplayname, $deadlines[1]['iconDesc']);

        // Disable the label activity.
        $CFG->theme_snap_disable_deadline_mods = ['label'];

        $deadlinescache->purge();
        $deadlines = ws_feed::service('deadlines');

        $this->assertCount(2, $deadlines);
        $this->assertEqualsIgnoringCase($assignactivityname, $deadlines[0]['iconDesc']);
        $this->assertEqualsIgnoringCase($quizmodulename, $deadlines[1]['iconDesc']);

        // Disable the assign and quiz activities.
        $CFG->theme_snap_disable_deadline_mods = ['assign', 'quiz'];

        $deadlinescache->purge();
        $deadlines = ws_feed::service('deadlines');

        $this->assertCount(1, $deadlines);
        $this->assertEqualsIgnoringCase($labeldisplayname, $deadlines[0]['iconDesc']);

        // Disable the assign and label activities.
        $CFG->theme_snap_disable_deadline_mods = ['assign', 'label'];

        $deadlinescache->purge();
        $deadlines = ws_feed::service('deadlines');

        $this->assertCount(1, $deadlines);
        $this->assertEqualsIgnoringCase($quizmodulename, $deadlines[0]['iconDesc']);

        // Disable the quiz and label activities.
        $CFG->theme_snap_disable_deadline_mods = ['quiz', 'label'];

        $deadlinescache->purge();
        $deadlines = ws_feed::service('deadlines');

        $this->assertCount(1, $deadlines);
        $this->assertEqualsIgnoringCase($assignactivityname, $deadlines[0]['iconDesc']);

        // Disable all the created activities.
        $CFG->theme_snap_disable_deadline_mods = ['assign', 'quiz', 'label'];

        $deadlinescache->purge();
        $deadlines = ws_feed::service('deadlines');

        $this->assertCount(0, $deadlines);
    }

    public function create_message(array $users, $messagetype, $message, $time, $subject = 'No subject') {
        global $DB;

        $userids = [];
        foreach ($users as $user) {
            $userids[] = $user->id;
        }
        $conversation = \core_message\api::create_conversation(
            $messagetype,
            $userids);

        // Ok, send the message.
        $record = new \stdClass();
        $record->useridfrom = $users[0]->id;
        $record->conversationid = $conversation->id;
        $record->subject = $subject;
        $record->fullmessage = $message;
        $record->smallmessage = $message;
        $record->timecreated = time() + $time;
        $DB->insert_record('messages', $record);
    }
}
