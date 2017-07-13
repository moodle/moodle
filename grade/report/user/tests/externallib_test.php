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
 * User grade report functions unit tests
 *
 * @package    gradereport_user
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/grade/report/user/externallib.php');

/**
 * User grade report functions unit tests
 *
 * @package    gradereport_user
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradereport_user_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Loads some data to be used by the different tests
     * @param  int $s1grade Student 1 grade
     * @param  int $s2grade Student 2 grade
     * @return array          Course and users instances
     */
    private function load_data($s1grade, $s2grade) {
        global $DB;

        $course = $this->getDataGenerator()->create_course(array('groupmode' => SEPARATEGROUPS, 'groupmodeforce' => 1));

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $student1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, $studentrole->id);

        $student2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, $studentrole->id);

        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);

        $context = context_course::instance($course->id);
        assign_capability('moodle/site:accessallgroups', CAP_PROHIBIT, $teacherrole->id, $context);
        accesslib_clear_all_caches_for_unit_testing();

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        groups_add_member($group1->id, $student1->id);
        groups_add_member($group1->id, $teacher->id);
        groups_add_member($group2->id, $student2->id);

        $assignment = $this->getDataGenerator()->create_module('assign', array('name' => "Test assign", 'course' => $course->id));
        $modcontext = get_coursemodule_from_instance('assign', $assignment->id, $course->id);
        $assignment->cmidnumber = $modcontext->id;

        $student1grade = array('userid' => $student1->id, 'rawgrade' => $s1grade);
        $student2grade = array('userid' => $student2->id, 'rawgrade' => $s2grade);
        $studentgrades = array($student1->id => $student1grade, $student2->id => $student2grade);
        assign_grade_item_update($assignment, $studentgrades);

        return array($course, $teacher, $student1, $student2, $assignment);
    }

    /**
     * Test get_grades_table function case teacher
     */
    public function test_get_grades_table_teacher() {

        $this->resetAfterTest(true);

        $s1grade = 80;
        $s2grade = 60;

        list($course, $teacher, $student1, $student2, $assignment) = $this->load_data($s1grade, $s2grade);

        // A teacher must see all student grades (in their group only).
        $this->setUser($teacher);

        $studentgrades = gradereport_user_external::get_grades_table($course->id);
        $studentgrades = external_api::clean_returnvalue(gradereport_user_external::get_grades_table_returns(), $studentgrades);

        // No warnings returned.
        $this->assertCount(0, $studentgrades['warnings']);

        // Check that only grades for the student in the teacher group are returned.
        $this->assertCount(1, $studentgrades['tables']);

        // Read returned grades.
        $studentreturnedgrades = array();
        $studentreturnedgrades[$studentgrades['tables'][0]['userid']] =
            (int) $studentgrades['tables'][0]['tabledata'][1]['grade']['content'];

        $this->assertEquals($s1grade, $studentreturnedgrades[$student1->id]);
    }

    /**
     * Test get_grades_table function case student
     */
    public function test_get_grades_table_student() {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        $s1grade = 80;
        $s2grade = 60;

        list($course, $teacher, $student1, $student2, $assignment) = $this->load_data($s1grade, $s2grade);

        // A user can see his own grades.
        $this->setUser($student1);
        $studentgrade = gradereport_user_external::get_grades_table($course->id, $student1->id);
        $studentgrade = external_api::clean_returnvalue(gradereport_user_external::get_grades_table_returns(), $studentgrade);

        // No warnings returned.
        $this->assertTrue(count($studentgrade['warnings']) == 0);

        $this->assertTrue(count($studentgrade['tables']) == 1);
        $student1returnedgrade = (int) $studentgrade['tables'][0]['tabledata'][1]['grade']['content'];
        $this->assertEquals($s1grade, $student1returnedgrade);

    }

    /**
     * Test get_grades_table function case incorrect permissions
     */
    public function test_get_grades_table_permissions() {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        $s1grade = 80;
        $s2grade = 60;

        list($course, $teacher, $student1, $student2, $assignment) = $this->load_data($s1grade, $s2grade);

        $this->setUser($student2);

        try {
            $studentgrade = gradereport_user_external::get_grades_table($course->id, $student1->id);
            $this->fail('Exception expected due to not perissions to view other user grades.');
        } catch (moodle_exception $e) {
            $this->assertEquals('notingroup', $e->errorcode);
        }
    }

    /**
     * Test view_grade_report function
     */
    public function test_view_grade_report() {
        global $USER;

        $this->resetAfterTest(true);

        $s1grade = 80;
        $s2grade = 60;
        list($course, $teacher, $student1, $student2, $assignment) = $this->load_data($s1grade, $s2grade);

        // Redirect events to the sink, so we can recover them later.
        $sink = $this->redirectEvents();

        $this->setUser($student1);
        $result = gradereport_user_external::view_grade_report($course->id);
        $result = external_api::clean_returnvalue(gradereport_user_external::view_grade_report_returns(), $result);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check the event details are correct.
        $this->assertInstanceOf('\gradereport_user\event\grade_report_viewed', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($USER->id, $event->get_data()['relateduserid']);

        $this->setUser($teacher);
        $result = gradereport_user_external::view_grade_report($course->id, $student1->id);
        $result = external_api::clean_returnvalue(gradereport_user_external::view_grade_report_returns(), $result);
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        // Check the event details are correct.
        $this->assertInstanceOf('\gradereport_user\event\grade_report_viewed', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($student1->id, $event->get_data()['relateduserid']);

        $this->setUser($student2);
        try {
            $studentgrade = gradereport_user_external::view_grade_report($course->id, $student1->id);
            $this->fail('Exception expected due to not permissions to view other user grades.');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissiontoviewgrades', $e->errorcode);
        }
    }

    /**
     * Test get_grades_items function case teacher
     */
    public function test_get_grade_items_teacher() {

        $this->resetAfterTest(true);

        $s1grade = 80;
        $s2grade = 60;

        list($course, $teacher, $student1, $student2, $assignment) = $this->load_data($s1grade, $s2grade);

        // A teacher must see all student grades (in their group only).
        $this->setUser($teacher);

        grade_set_setting($course->id, 'report_user_showrank', 1);
        grade_set_setting($course->id, 'report_user_showpercentage', 1);
        grade_set_setting($course->id, 'report_user_showhiddenitems', 1);
        grade_set_setting($course->id, 'report_user_showgrade', 1);
        grade_set_setting($course->id, 'report_user_showfeedback', 1);
        grade_set_setting($course->id, 'report_user_showweight', 1);
        grade_set_setting($course->id, 'report_user_showcontributiontocoursetotal', 1);
        grade_set_setting($course->id, 'report_user_showlettergrade', 1);
        grade_set_setting($course->id, 'report_user_showaverage', 1);

        $studentgrades = gradereport_user_external::get_grade_items($course->id);
        $studentgrades = external_api::clean_returnvalue(gradereport_user_external::get_grade_items_returns(), $studentgrades);
        // No warnings returned.
        $this->assertCount(0, $studentgrades['warnings']);

        // Check that only grades for the student in the teacher group are returned.
        $this->assertCount(1, $studentgrades['usergrades']);
        $this->assertCount(2, $studentgrades['usergrades'][0]['gradeitems']);

        $this->assertEquals($course->id, $studentgrades['usergrades'][0]['courseid']);
        $this->assertEquals($student1->id, $studentgrades['usergrades'][0]['userid']);
        // Module grades.
        $this->assertEquals($assignment->name, $studentgrades['usergrades'][0]['gradeitems'][0]['itemname']);
        $this->assertEquals('mod', $studentgrades['usergrades'][0]['gradeitems'][0]['itemtype']);
        $this->assertEquals('assign', $studentgrades['usergrades'][0]['gradeitems'][0]['itemmodule']);
        $this->assertEquals($assignment->id, $studentgrades['usergrades'][0]['gradeitems'][0]['iteminstance']);
        $this->assertEquals($assignment->cmidnumber, $studentgrades['usergrades'][0]['gradeitems'][0]['cmid']);
        $this->assertEquals(0, $studentgrades['usergrades'][0]['gradeitems'][0]['itemnumber']);
        $this->assertEmpty($studentgrades['usergrades'][0]['gradeitems'][0]['outcomeid']);
        $this->assertEmpty($studentgrades['usergrades'][0]['gradeitems'][0]['scaleid']);
        $this->assertEquals(80, $studentgrades['usergrades'][0]['gradeitems'][0]['graderaw']);
        $this->assertEquals('80.00', $studentgrades['usergrades'][0]['gradeitems'][0]['gradeformatted']);
        $this->assertEquals(0, $studentgrades['usergrades'][0]['gradeitems'][0]['grademin']);
        $this->assertEquals(100, $studentgrades['usergrades'][0]['gradeitems'][0]['grademax']);
        $this->assertEquals('0&ndash;100', $studentgrades['usergrades'][0]['gradeitems'][0]['rangeformatted']);
        $this->assertEquals('80.00 %', $studentgrades['usergrades'][0]['gradeitems'][0]['percentageformatted']);
        $this->assertEmpty($studentgrades['usergrades'][0]['gradeitems'][0]['feedback']);
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][0]['gradehiddenbydate']);
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][0]['gradeneedsupdate']);
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][0]['gradeishidden']);
        $this->assertEquals('B-', $studentgrades['usergrades'][0]['gradeitems'][0]['lettergradeformatted']);
        $this->assertEquals(1, $studentgrades['usergrades'][0]['gradeitems'][0]['rank']);
        $this->assertEquals(2, $studentgrades['usergrades'][0]['gradeitems'][0]['numusers']);
        $this->assertEquals(70, $studentgrades['usergrades'][0]['gradeitems'][0]['averageformatted']);

        // Course grades.
        $this->assertEquals('course', $studentgrades['usergrades'][0]['gradeitems'][1]['itemtype']);
        $this->assertEquals(80, $studentgrades['usergrades'][0]['gradeitems'][1]['graderaw']);
        $this->assertEquals('80.00', $studentgrades['usergrades'][0]['gradeitems'][1]['gradeformatted']);
        $this->assertEquals(0, $studentgrades['usergrades'][0]['gradeitems'][1]['grademin']);
        $this->assertEquals(100, $studentgrades['usergrades'][0]['gradeitems'][1]['grademax']);
        $this->assertEquals('0&ndash;100', $studentgrades['usergrades'][0]['gradeitems'][1]['rangeformatted']);
        $this->assertEquals('80.00 %', $studentgrades['usergrades'][0]['gradeitems'][1]['percentageformatted']);
        $this->assertEmpty($studentgrades['usergrades'][0]['gradeitems'][1]['feedback']);
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][1]['gradehiddenbydate']);
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][1]['gradeneedsupdate']);
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][1]['gradeishidden']);
        $this->assertEquals('B-', $studentgrades['usergrades'][0]['gradeitems'][1]['lettergradeformatted']);
        $this->assertEquals(1, $studentgrades['usergrades'][0]['gradeitems'][1]['rank']);
        $this->assertEquals(2, $studentgrades['usergrades'][0]['gradeitems'][1]['numusers']);
        $this->assertEquals(70, $studentgrades['usergrades'][0]['gradeitems'][1]['averageformatted']);
    }

    /**
     * Test get_grades_items function case student
     */
    public function test_get_grade_items_student() {

        $this->resetAfterTest(true);

        $s1grade = 80;
        $s2grade = 60;

        list($course, $teacher, $student1, $student2, $assignment) = $this->load_data($s1grade, $s2grade);

        grade_set_setting($course->id, 'report_user_showrank', 1);
        grade_set_setting($course->id, 'report_user_showpercentage', 1);
        grade_set_setting($course->id, 'report_user_showgrade', 1);
        grade_set_setting($course->id, 'report_user_showfeedback', 1);
        grade_set_setting($course->id, 'report_user_showweight', 1);
        grade_set_setting($course->id, 'report_user_showcontributiontocoursetotal', 1);
        grade_set_setting($course->id, 'report_user_showlettergrade', 1);
        grade_set_setting($course->id, 'report_user_showaverage', 1);

        $this->setUser($student1);

        $studentgrades = gradereport_user_external::get_grade_items($course->id, $student1->id);
        $studentgrades = external_api::clean_returnvalue(gradereport_user_external::get_grade_items_returns(), $studentgrades);
        // No warnings returned.
        $this->assertCount(0, $studentgrades['warnings']);

        // Check that only grades for the student in the teacher group are returned.
        $this->assertCount(1, $studentgrades['usergrades']);
        $this->assertCount(2, $studentgrades['usergrades'][0]['gradeitems']);

        $this->assertEquals($course->id, $studentgrades['usergrades'][0]['courseid']);
        $this->assertEquals($student1->id, $studentgrades['usergrades'][0]['userid']);
        $this->assertEquals($assignment->name, $studentgrades['usergrades'][0]['gradeitems'][0]['itemname']);
        $this->assertEquals('mod', $studentgrades['usergrades'][0]['gradeitems'][0]['itemtype']);
        $this->assertEquals('assign', $studentgrades['usergrades'][0]['gradeitems'][0]['itemmodule']);
        $this->assertEquals($assignment->id, $studentgrades['usergrades'][0]['gradeitems'][0]['iteminstance']);
        $this->assertEquals($assignment->cmidnumber, $studentgrades['usergrades'][0]['gradeitems'][0]['cmid']);
        $this->assertEquals(0, $studentgrades['usergrades'][0]['gradeitems'][0]['itemnumber']);
        $this->assertEmpty($studentgrades['usergrades'][0]['gradeitems'][0]['outcomeid']);
        $this->assertEmpty($studentgrades['usergrades'][0]['gradeitems'][0]['scaleid']);
        $this->assertEquals(80, $studentgrades['usergrades'][0]['gradeitems'][0]['graderaw']);
        $this->assertEquals('80.00', $studentgrades['usergrades'][0]['gradeitems'][0]['gradeformatted']);
        $this->assertEquals(0, $studentgrades['usergrades'][0]['gradeitems'][0]['grademin']);
        $this->assertEquals(100, $studentgrades['usergrades'][0]['gradeitems'][0]['grademax']);
        $this->assertEquals('0&ndash;100', $studentgrades['usergrades'][0]['gradeitems'][0]['rangeformatted']);
        $this->assertEquals('80.00 %', $studentgrades['usergrades'][0]['gradeitems'][0]['percentageformatted']);
        $this->assertEmpty($studentgrades['usergrades'][0]['gradeitems'][0]['feedback']);
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][0]['gradehiddenbydate']);
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][0]['gradeneedsupdate']);
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][0]['gradeishidden']);
        $this->assertEquals('B-', $studentgrades['usergrades'][0]['gradeitems'][0]['lettergradeformatted']);
        $this->assertEquals(1, $studentgrades['usergrades'][0]['gradeitems'][0]['rank']);
        $this->assertEquals(2, $studentgrades['usergrades'][0]['gradeitems'][0]['numusers']);
        $this->assertEquals(70, $studentgrades['usergrades'][0]['gradeitems'][0]['averageformatted']);

        // Hide one grade for the user.
        $gradegrade = new grade_grade(array('userid' => $student1->id,
                                        'itemid' => $studentgrades['usergrades'][0]['gradeitems'][0]['id']), true);
        $gradegrade->set_hidden(1);
        $studentgrades = gradereport_user_external::get_grade_items($course->id, $student1->id);
        $studentgrades = external_api::clean_returnvalue(gradereport_user_external::get_grade_items_returns(), $studentgrades);

        // Check we get only the course final grade.
        $this->assertCount(1, $studentgrades['usergrades']);
        $this->assertCount(1, $studentgrades['usergrades'][0]['gradeitems']);
        $this->assertEquals('course', $studentgrades['usergrades'][0]['gradeitems'][0]['itemtype']);
    }

}
