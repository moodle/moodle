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

use block_quickmail\messenger\message\subject_prepender;

class block_quickmail_subject_prepender_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses;

    public function test_format_course_subject_with_no_setting() {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();

        $subject = 'Hello world!';

        $formattedsubject = subject_prepender::format_course_subject($course, $subject);

        $this->assertEquals('Hello world!', $formattedsubject);
    }

    public function test_format_course_subject_with_idnumber_setting() {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();

        $subject = 'Hello world!';

        $this->update_system_config_value('block_quickmail_prepend_class', 'idnumber');

        $formattedsubject = subject_prepender::format_course_subject($course, $subject);

        $this->assertEquals('[' . $course->idnumber . '] Hello world!', $formattedsubject);
    }

    public function test_format_course_subject_with_shortname_setting() {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();

        $subject = 'Hello world!';

        $this->update_system_config_value('block_quickmail_prepend_class', 'shortname');

        $formattedsubject = subject_prepender::format_course_subject($course, $subject);

        $this->assertEquals('[' . $course->shortname . '] Hello world!', $formattedsubject);
    }

    public function test_format_course_subject_with_fullname_setting() {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();

        $subject = 'Hello world!';

        $this->update_system_config_value('block_quickmail_prepend_class', 'fullname');

        $formattedsubject = subject_prepender::format_course_subject($course, $subject);

        $this->assertEquals('[' . $course->fullname . '] Hello world!', $formattedsubject);
    }

}
