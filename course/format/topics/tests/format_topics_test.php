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
 * format_topics related unit tests
 *
 * @package    format_topics
 * @copyright  2015 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/course/lib.php');

/**
 * format_topics related unit tests
 *
 * @package    format_topics
 * @copyright  2015 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_topics_testcase extends advanced_testcase {

    public function test_update_course_numsections() {
        global $DB;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        $course = $generator->create_course(array('numsections' => 10, 'format' => 'topics'),
            array('createsections' => true));
        $generator->create_module('assign', array('course' => $course, 'section' => 7));

        $this->setAdminUser();

        $this->assertEquals(11, $DB->count_records('course_sections', array('course' => $course->id)));

        // Change the numsections to 8, last two sections did not have any activities, they should be deleted.
        update_course((object)array('id' => $course->id, 'numsections' => 8));
        $this->assertEquals(9, $DB->count_records('course_sections', array('course' => $course->id)));
        $this->assertEquals(9, count(get_fast_modinfo($course)->get_section_info_all()));

        // Change the numsections to 5, section 8 should be deleted but section 7 should remain as it has activities.
        update_course((object)array('id' => $course->id, 'numsections' => 6));
        $this->assertEquals(8, $DB->count_records('course_sections', array('course' => $course->id)));
        $this->assertEquals(8, count(get_fast_modinfo($course)->get_section_info_all()));
        $this->assertEquals(6, course_get_format($course)->get_course()->numsections);
    }
}
