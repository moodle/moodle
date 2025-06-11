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

use block_quickmail\messenger\message\substitution_code;

class block_quickmail_substitution_code_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        creates_message_records;

    public function test_gets_user_substitution_codes() {
        $codes = substitution_code::get('user');

        $this->assertCount(6, $codes);
        $this->assertContains('firstname', $codes);
        $this->assertContains('lastname', $codes);
        $this->assertContains('fullname', $codes);
        $this->assertContains('middlename', $codes);
        $this->assertContains('email', $codes);
        $this->assertContains('alternatename', $codes);
    }

    public function test_gets_course_substitution_codes() {
        $codes = substitution_code::get('course');

        $this->assertCount(10, $codes);
        $this->assertContains('coursefullname', $codes);
        $this->assertContains('courseshortname', $codes);
        $this->assertContains('courseidnumber', $codes);
        $this->assertContains('coursesummary', $codes);
        $this->assertContains('coursestartdate', $codes);
        $this->assertContains('courseenddate', $codes);
        $this->assertContains('courselink', $codes);
        $this->assertContains('courselastaccess', $codes);
        $this->assertContains('studentstartdate', $codes);
        $this->assertContains('studentenddate', $codes);
    }

    public function test_gets_activity_substitution_codes() {
        $codes = substitution_code::get('activity');

        $this->assertCount(4, $codes);
        $this->assertContains('activityname', $codes);
        $this->assertContains('activityduedate', $codes);
        $this->assertContains('activitylink', $codes);
        $this->assertContains('activitygradelink', $codes);
    }

    public function test_gets_codes_for_multiple_classes() {
        $codes = substitution_code::get(['user', 'course', 'user']);

        $this->assertCount(16, $codes);
    }

    public function test_gets_all_codes() {
        $codes = substitution_code::get();

        $this->assertCount(20, $codes);
    }

    public function test_gets_substitution_code_classes_from_composed_message_with_no_notification() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $message = $this->create_compose_message($course, $userteacher, [], [
            'message_type' => 'email',
            'body' => 'This is a message with no substitution codes! Should be easy enough, right?',
        ]);

        $codeclasses = substitution_code::get_code_classes_from_message($message);

        $this->assertCount(2, $codeclasses);
        $this->assertContains('user', $codeclasses);
        $this->assertContains('course', $codeclasses);
    }

}
