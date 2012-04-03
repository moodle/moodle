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
 * Course related unit tests
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


class courselib_testcase extends advanced_testcase {

    public function test_reorder_sections() {
        global $DB;
        $this->resetAfterTest(true);

        $this->getDataGenerator()->create_course(array('numsections'=>5), array('createsections'=>true));
        $course = $this->getDataGenerator()->create_course(array('numsections'=>10), array('createsections'=>true));
        $oldsections = array();
        $sections = array();
        foreach ($DB->get_records('course_sections', array('course'=>$course->id)) as $section) {
            $oldsections[$section->section] = $section->id;
            $sections[$section->id] = $section->section;
        }
        ksort($oldsections);

        $neworder = reorder_sections($sections, 2, 4);
        $neworder = array_keys($neworder);
        $this->assertEquals($oldsections[0], $neworder[0]);
        $this->assertEquals($oldsections[1], $neworder[1]);
        $this->assertEquals($oldsections[2], $neworder[4]);
        $this->assertEquals($oldsections[3], $neworder[2]);
        $this->assertEquals($oldsections[4], $neworder[3]);
        $this->assertEquals($oldsections[5], $neworder[5]);
        $this->assertEquals($oldsections[6], $neworder[6]);

        $neworder = reorder_sections(1, 2, 4);
        $this->assertFalse($neworder);
    }

    public function test_move_section() {
        global $DB;
        $this->resetAfterTest(true);

        $this->getDataGenerator()->create_course(array('numsections'=>5), array('createsections'=>true));
        $course = $this->getDataGenerator()->create_course(array('numsections'=>10), array('createsections'=>true));
        $oldsections = array();
        foreach ($DB->get_records('course_sections', array('course'=>$course->id)) as $section) {
            $oldsections[$section->section] = $section->id;
        }
        ksort($oldsections);

        move_section_to($course, 2, 4);
        $sections = array();
        foreach ($DB->get_records('course_sections', array('course'=>$course->id)) as $section) {
            $sections[$section->section] = $section->id;
        }
        ksort($sections);

        $this->assertEquals($oldsections[0], $sections[0]);
        $this->assertEquals($oldsections[1], $sections[1]);
        $this->assertEquals($oldsections[2], $sections[4]);
        $this->assertEquals($oldsections[3], $sections[2]);
        $this->assertEquals($oldsections[4], $sections[3]);
        $this->assertEquals($oldsections[5], $sections[5]);
        $this->assertEquals($oldsections[6], $sections[6]);
    }

    public function test_get_course_display_name_for_list() {
        global $CFG;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(array('shortname' => 'FROG101', 'fullname' => 'Introduction to pond life'));

        $CFG->courselistshortnames = 0;
        $this->assertEquals('Introduction to pond life', get_course_display_name_for_list($course));

        $CFG->courselistshortnames = 1;
        $this->assertEquals('FROG101 Introduction to pond life', get_course_display_name_for_list($course));
    }
}
