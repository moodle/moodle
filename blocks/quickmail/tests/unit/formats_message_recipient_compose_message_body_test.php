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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/traits/unit_testcase_traits.php');

use block_quickmail\messenger\message\message_body_constructor;

class block_quickmail_parses_compose_message_body_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        creates_message_records;

    public function test_replaces_message_recipient_compose_message_body_with_user_data() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $messagebody = "Hey there [:firstname:].\n";
        $messagebody .= "Don't I know you? Your last name is [:lastname:], right?\n";
        $messagebody .= "In fact, I believe that your full name is [:fullname:].\n";
        $messagebody .= "Your middle name must be [:middlename:], but we will call you [:alternatename:].\n";
        $messagebody .= "Is your email still [:email:]?";
        $message = $this->create_compose_message($course, $userteacher, [], [
            'message_type' => 'email',
            'body' => $messagebody,
        ]);

        $firststudent = $userstudents[0];

        $body = message_body_constructor::get_formatted_body($message, $firststudent, $course);

        $fullname = fullname($firststudent);
        $messagebody = "Hey there {$firststudent->firstname}.\n";
        $messagebody .= "Don't I know you? Your last name is {$firststudent->lastname}, right?\n";
        $messagebody .= "In fact, I believe that your full name is {$fullname}.\n";
        $messagebody .= "Your middle name must be {$firststudent->middlename}, but we".
            " will call you {$firststudent->alternatename}.\n";
        $messagebody .= "Is your email still {$firststudent->email}?";
        $this->assertEquals($messagebody, $body);
    }

    public function test_replaces_message_recipient_compose_message_body_with_course_data() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $messagebody = "Welcome to [:coursefullname:]!\n";
        $messagebody .= "Let's shorten the name to [:courseshortname:] if that's ok with you.\n";
        $messagebody .= "The ID number will remain as [:courseidnumber:] though.\n";
        $messagebody .= "If I had to summarize this course, I'd say it would be: [:coursesummary:]\n";
        $messagebody .= "You can always access the course online by going to [:courselink:].\n";
        $messagebody .= "The course will begin on [:coursestartdate:] and end on [:courseenddate:].\n";
        $messagebody .= "Do we have a mutual understanding?";
        $message = $this->create_compose_message($course, $userteacher, [], [
            'message_type' => 'email',
            'body' => $messagebody,
        ]);

        $firststudent = $userstudents[0];

        $courselink = new \moodle_url('/course/view.php', ['id' => $course->id]);

        $body = message_body_constructor::get_formatted_body($message, $firststudent, $course);

        $startdate = date('F j, Y', $course->startdate);
        $messagebody = "Welcome to {$course->fullname}!\n";
        $messagebody .= "Let's shorten the name to {$course->shortname} if that's ok with you.\n";
        $messagebody .= "The ID number will remain as {$course->idnumber} though.\n";
        $messagebody .= "If I had to summarize this course, I'd say it would be: {$course->summary}\n";
        $messagebody .= "You can always access the course online by going to $courselink.\n";
        $messagebody .= "The course will begin on $startdate and end on Never.\n";
        $messagebody .= "Do we have a mutual understanding?";
        $this->assertEquals($messagebody, $body);
    }

    public function test_replaces_accessed_message_recipient_compose_message_body_with_course_seensince_data() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $message = $this->create_compose_message($course, $userteacher, [], [
            'message_type' => 'email',
            'body' => 'FYI, the last time you access this course was on [:courselastaccess:].',
        ]);

        $firststudent = $userstudents[0];

        $accesstime = time();

        $this->report_user_access_in_course($firststudent, $course, $accesstime);

        $body = message_body_constructor::get_formatted_body($message, $firststudent, $course);

        $this->assertEquals('FYI, the last time you access this course was on ' . date('F j, Y', $accesstime) . '.', $body);
    }

    public function test_replaces_non_accessed_message_recipient_compose_message_body_with_course_seensince_data() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $message = $this->create_compose_message($course, $userteacher, [], [
            'message_type' => 'email',
            'body' => 'FYI, the last time you access this course was on [:courselastaccess:].',
        ]);

        $firststudent = $userstudents[0];

        $body = message_body_constructor::get_formatted_body($message, $firststudent, $course);

        $this->assertEquals('FYI, the last time you access this course was on Never Accessed.', $body);
    }

}
