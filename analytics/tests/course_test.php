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
 * Unit tests for course.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for course.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_analytics_course_testcase extends advanced_testcase {

    public function setUp() {
        global $DB;

        $this->course = $this->getDataGenerator()->create_course(['startdate' => 0]);
        $this->stu1 = $this->getDataGenerator()->create_user();
        $this->stu2 = $this->getDataGenerator()->create_user();
        $this->both = $this->getDataGenerator()->create_user();
        $this->editingteacher = $this->getDataGenerator()->create_user();
        $this->teacher = $this->getDataGenerator()->create_user();

        $this->studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $this->editingteacherroleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
        $this->teacherroleid = $DB->get_field('role', 'id', array('shortname' => 'teacher'));

        $this->getDataGenerator()->enrol_user($this->stu1->id, $this->course->id, $this->studentroleid);
        $this->getDataGenerator()->enrol_user($this->stu2->id, $this->course->id, $this->studentroleid);
        $this->getDataGenerator()->enrol_user($this->both->id, $this->course->id, $this->studentroleid);
        $this->getDataGenerator()->enrol_user($this->both->id, $this->course->id, $this->editingteacherroleid);
        $this->getDataGenerator()->enrol_user($this->editingteacher->id, $this->course->id, $this->editingteacherroleid);
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherroleid);
    }

    /**
     * Users tests.
     */
    public function test_users() {
        global $DB;

        $this->resetAfterTest(true);

        $courseman = new \core_analytics\course($this->course->id);
        $this->assertCount(3, $courseman->get_user_ids(array($this->studentroleid)));
        $this->assertCount(2, $courseman->get_user_ids(array($this->editingteacherroleid)));
        $this->assertCount(1, $courseman->get_user_ids(array($this->teacherroleid)));

        // Distinct is applied.
        $this->assertCount(3, $courseman->get_user_ids(array($this->editingteacherroleid, $this->teacherroleid)));
        $this->assertCount(4, $courseman->get_user_ids(array($this->editingteacherroleid, $this->studentroleid)));
    }

    /**
     * Course validation tests.
     *
     * @return void
     */
    public function test_course_validation() {
        global $DB;

        $this->resetAfterTest(true);

        $courseman = new \core_analytics\course($this->course->id);
        $this->assertFalse($courseman->was_started());
        $this->assertFalse($courseman->is_finished());

        // Nothing should change when assigning as teacher.
        for ($i = 0; $i < 10; $i++) {
            $user = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($user->id, $this->course->id, $this->teacherroleid);
        }
        $courseman = new \core_analytics\course($this->course->id);
        $this->assertFalse($courseman->was_started());
        $this->assertFalse($courseman->is_finished());

        // More students now.
        for ($i = 0; $i < 10; $i++) {
            $user = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($user->id, $this->course->id, $this->studentroleid);
        }
        $courseman = new \core_analytics\course($this->course->id);
        $this->assertFalse($courseman->was_started());
        $this->assertFalse($courseman->is_finished());

        // Valid start date unknown end date.
        $this->course->startdate = gmmktime('0', '0', '0', 10, 24, 2015);
        $DB->update_record('course', $this->course);
        $courseman = new \core_analytics\course($this->course->id);
        $this->assertTrue($courseman->was_started());
        $this->assertFalse($courseman->is_finished());

        // Valid start and end date.
        $this->course->enddate = gmmktime('0', '0', '0', 8, 27, 2016);
        $DB->update_record('course', $this->course);
        $courseman = new \core_analytics\course($this->course->id);
        $this->assertTrue($courseman->was_started());
        $this->assertTrue($courseman->is_finished());

        // Valid start and ongoing course.
        $this->course->enddate = gmmktime('0', '0', '0', 8, 27, 2286);
        $DB->update_record('course', $this->course);
        $courseman = new \core_analytics\course($this->course->id);
        $this->assertTrue($courseman->was_started());
        $this->assertFalse($courseman->is_finished());
    }

    /**
     * Get the minimum time that is considered valid according to guess_end logic.
     *
     * @param int $time
     * @return int
     */
    protected function time_greater_than($time) {
        return $time - (WEEKSECS * 2);
    }

    /**
     * Get the maximum time that is considered valid according to guess_end logic.
     *
     * @param int $time
     * @return int
     */
    protected function time_less_than($time) {
        return $time + (WEEKSECS * 2);
    }
}
