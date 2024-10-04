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

namespace gradereport_user;

use core_external\external_api;
use externallib_advanced_testcase;
use gradereport_user\external\user as user_external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * User grade report functions unit tests
 *
 * @package    gradereport_user
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class externallib_test extends externallib_advanced_testcase {

    /**
     * Loads some data to be used by the different tests
     * @param  int $s1grade Student 1 grade
     * @param  int $s2grade Student 2 grade
     * @return array Course and users instances
     */
    private function load_data(int $s1grade, int $s2grade, int $s3grade): array {
        global $DB;

        $course = $this->getDataGenerator()->create_course(['groupmode' => SEPARATEGROUPS, 'groupmodeforce' => 1]);

        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $student1 = $this->getDataGenerator()->create_user(['idnumber' => 'testidnumber']);
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, $studentrole->id);

        $student2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, $studentrole->id);

        // Student 3 is in no groups.
        $student3 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, $studentrole->id);

        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);

        $context = \context_course::instance($course->id);
        assign_capability('moodle/site:accessallgroups', CAP_PROHIBIT, $teacherrole->id, $context);
        accesslib_clear_all_caches_for_unit_testing();

        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_add_member($group1->id, $student1->id);
        groups_add_member($group1->id, $teacher->id);
        groups_add_member($group2->id, $student2->id);

        $assignment = $this->getDataGenerator()->create_module('assign', ['name' => "Test assign & grade items", 'course' => $course->id]);
        $modcontext = get_coursemodule_from_instance('assign', $assignment->id, $course->id);
        $assignment->cmidnumber = $modcontext->id;

        $student1grade = ['userid' => $student1->id, 'rawgrade' => $s1grade, 'idnumber' => 'testidnumber1'];
        $student2grade = ['userid' => $student2->id, 'rawgrade' => $s2grade, 'idnumber' => 'testidnumber2'];
        $student3grade = ['userid' => $student3->id, 'rawgrade' => $s3grade, 'idnumber' => 'testidnumber3'];
        $studentgrades = [$student1->id => $student1grade, $student2->id => $student2grade, $student3->id => $student3grade];
        assign_grade_item_update($assignment, $studentgrades);

        return [
            $course,
            $teacher,
            $student1,
            $student2,
            $student3,
            $assignment
        ];
    }

    /**
     * Test get_grades_table function case teacher
     */
    public function test_get_grades_table_teacher(): void {

        $this->resetAfterTest(true);

        $s1grade = 80;
        $s2grade = 60;
        $s3grade = 50;

        list($course, $teacher, $student1, $student2, $student3, $assignment) = $this->load_data($s1grade, $s2grade, $s3grade);

        // A teacher must see all student grades (in their group only).
        $this->setUser($teacher);

        $studentgrades = user_external::get_grades_table($course->id);
        $studentgrades = external_api::clean_returnvalue(user_external::get_grades_table_returns(), $studentgrades);

        // No warnings returned.
        $this->assertCount(0, $studentgrades['warnings']);

        // Check that only grades for the student in the teacher group are returned.
        $this->assertCount(1, $studentgrades['tables']);

        // Read returned grades.
        $studentreturnedgrades = [];

        $studentreturnedgrades[$studentgrades['tables'][0]['userid']] =
            (int) $studentgrades['tables'][0]['tabledata'][2]['grade']['content'];

        $this->assertEquals($s1grade, $studentreturnedgrades[$student1->id]);
    }

    /**
     * Test get_grades_table function case student
     */
    public function test_get_grades_table_student(): void {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        $s1grade = 80;
        $s2grade = 60;
        $s3grade = 50;

        list($course, $teacher, $student1, $student2, $student3, $assignment) = $this->load_data($s1grade, $s2grade, $s3grade);

        // A user can see his own grades.
        $this->setUser($student1);
        $studentgrade = user_external::get_grades_table($course->id, $student1->id);
        $studentgrade = external_api::clean_returnvalue(user_external::get_grades_table_returns(), $studentgrade);

        // No warnings returned.
        $this->assertTrue(count($studentgrade['warnings']) == 0);

        $this->assertTrue(count($studentgrade['tables']) == 1);
        $student1returnedgrade = (int) $studentgrade['tables'][0]['tabledata'][2]['grade']['content'];
        $this->assertEquals($s1grade, $student1returnedgrade);

        // A user can see his own even when in no groups.
        $this->setUser($student3);
        $studentgrade = user_external::get_grades_table($course->id, $student3->id);
        $studentgrade = external_api::clean_returnvalue(user_external::get_grades_table_returns(), $studentgrade);

        // No warnings returned.
        $this->assertTrue(count($studentgrade['warnings']) == 0);

        $this->assertTrue(count($studentgrade['tables']) == 1);
        $student3returnedgrade = (int) $studentgrade['tables'][0]['tabledata'][2]['grade']['content'];
        $this->assertEquals($s3grade, $student3returnedgrade);

        // Expect exception when user is not indicated.
        $this->setUser($student3);
        $this->expectException(\required_capability_exception::class);
        user_external::get_grades_table($course->id);
    }

    /**
     * Test get_grades_table function case incorrect permissions
     */
    public function test_get_grades_table_permissions(): void {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        $s1grade = 80;
        $s2grade = 60;
        $s3grade = 50;

        list($course, $teacher, $student1, $student2, $student3, $assignment) = $this->load_data($s1grade, $s2grade, $s3grade);

        $this->setUser($student2);

        try {
            $studentgrade = user_external::get_grades_table($course->id, $student1->id);
            $this->fail('Exception expected due to not perissions to view other user grades.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('notingroup', $e->errorcode);
        }
    }

    /**
     * Test view_grade_report function
     */
    public function test_view_grade_report(): void {
        global $USER;

        $this->resetAfterTest(true);

        $s1grade = 80;
        $s2grade = 60;
        $s3grade = 50;
        list($course, $teacher, $student1, $student2, $student3, $assignment) = $this->load_data($s1grade, $s2grade, $s3grade);

        // Redirect events to the sink, so we can recover them later.
        $sink = $this->redirectEvents();

        $this->setUser($student1);
        $result = user_external::view_grade_report($course->id);
        $result = external_api::clean_returnvalue(user_external::view_grade_report_returns(), $result);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check the event details are correct.
        $this->assertInstanceOf('\gradereport_user\event\grade_report_viewed', $event);
        $this->assertEquals(\context_course::instance($course->id), $event->get_context());
        $this->assertEquals($USER->id, $event->get_data()['relateduserid']);

        $this->setUser($teacher);
        $result = user_external::view_grade_report($course->id, $student1->id);
        $result = external_api::clean_returnvalue(user_external::view_grade_report_returns(), $result);
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        // Check the event details are correct.
        $this->assertInstanceOf('\gradereport_user\event\grade_report_viewed', $event);
        $this->assertEquals(\context_course::instance($course->id), $event->get_context());
        $this->assertEquals($student1->id, $event->get_data()['relateduserid']);

        $this->setUser($student2);
        try {
            $studentgrade = user_external::view_grade_report($course->id, $student1->id);
            $this->fail('Exception expected due to not permissions to view other user grades.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('nopermissiontoviewgrades', $e->errorcode);
        }
    }

    /**
     * Test get_grades_items function case teacher
     */
    public function test_get_grade_items_teacher(): void {

        $this->resetAfterTest(true);

        $s1grade = 80;
        $s2grade = 60;
        $s3grade = 50;

        list($course, $teacher, $student1, $student2, $student3, $assignment) = $this->load_data($s1grade, $s2grade, $s3grade);

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

        $studentgrades = user_external::get_grade_items($course->id);
        $studentgrades = external_api::clean_returnvalue(user_external::get_grade_items_returns(), $studentgrades);
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
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][0]['locked']);
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
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][0]['gradeislocked']);
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][0]['gradeisoverridden']);
        $this->assertEquals('B-', $studentgrades['usergrades'][0]['gradeitems'][0]['lettergradeformatted']);
        $this->assertEquals(1, $studentgrades['usergrades'][0]['gradeitems'][0]['rank']);
        $this->assertEquals(3, $studentgrades['usergrades'][0]['gradeitems'][0]['numusers']);
        $this->assertEquals(
            round(array_sum([$s1grade, $s2grade, $s3grade]) / 3, 2),
            $studentgrades['usergrades'][0]['gradeitems'][0]['averageformatted']);

        // Course grades.
        $this->assertEquals('course', $studentgrades['usergrades'][0]['gradeitems'][1]['itemtype']);
        $this->assertEquals(80, $studentgrades['usergrades'][0]['gradeitems'][1]['graderaw']);
        $this->assertEquals('80.00', $studentgrades['usergrades'][0]['gradeitems'][1]['gradeformatted']);
        $this->assertEquals(0, $studentgrades['usergrades'][0]['gradeitems'][1]['grademin']);
        $this->assertEquals(100, $studentgrades['usergrades'][0]['gradeitems'][1]['grademax']);
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][1]['locked']);
        $this->assertEquals('0&ndash;100', $studentgrades['usergrades'][0]['gradeitems'][1]['rangeformatted']);
        $this->assertEquals('80.00 %', $studentgrades['usergrades'][0]['gradeitems'][1]['percentageformatted']);
        $this->assertEmpty($studentgrades['usergrades'][0]['gradeitems'][1]['feedback']);
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][1]['gradehiddenbydate']);
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][1]['gradeneedsupdate']);
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][1]['gradeishidden']);
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][1]['gradeislocked']);
        $this->assertFalse($studentgrades['usergrades'][0]['gradeitems'][1]['gradeisoverridden']);
        $this->assertEquals('B-', $studentgrades['usergrades'][0]['gradeitems'][1]['lettergradeformatted']);
        $this->assertEquals(1, $studentgrades['usergrades'][0]['gradeitems'][1]['rank']);
        $this->assertEquals(3, $studentgrades['usergrades'][0]['gradeitems'][1]['numusers']);
        $this->assertEquals(
            round(array_sum([$s1grade, $s2grade, $s3grade]) / 3, 2),
            $studentgrades['usergrades'][0]['gradeitems'][1]['averageformatted']);

        // Now, override and lock a grade.
        $gradegrade = \grade_grade::fetch(['itemid' => $studentgrades['usergrades'][0]['gradeitems'][0]['id'],
            'userid' => $studentgrades['usergrades'][0]['userid']]);
        $gradegrade->set_overridden(true);
        $gradegrade->set_locked(1);

        $studentgrades = user_external::get_grade_items($course->id);
        $studentgrades = external_api::clean_returnvalue(user_external::get_grade_items_returns(), $studentgrades);
        // No warnings returned.
        $this->assertCount(0, $studentgrades['warnings']);

        // Module grades.
        $this->assertTrue($studentgrades['usergrades'][0]['gradeitems'][0]['gradeislocked']);
        $this->assertTrue($studentgrades['usergrades'][0]['gradeitems'][0]['gradeisoverridden']);
    }

    /**
     * Test get_grades_items function case student
     */
    public function test_get_grade_items_student(): void {

        $this->resetAfterTest(true);

        $s1grade = 80;
        $s2grade = 60;
        $s3grade = 50;

        list($course, $teacher, $student1, $student2, $student3, $assignment) = $this->load_data($s1grade, $s2grade, $s3grade);

        grade_set_setting($course->id, 'report_user_showrank', 1);
        grade_set_setting($course->id, 'report_user_showpercentage', 1);
        grade_set_setting($course->id, 'report_user_showgrade', 1);
        grade_set_setting($course->id, 'report_user_showfeedback', 1);
        grade_set_setting($course->id, 'report_user_showweight', 1);
        grade_set_setting($course->id, 'report_user_showcontributiontocoursetotal', 1);
        grade_set_setting($course->id, 'report_user_showlettergrade', 1);
        grade_set_setting($course->id, 'report_user_showaverage', 1);

        $this->setUser($student1);

        $studentgrades = user_external::get_grade_items($course->id, $student1->id);
        $studentgrades = external_api::clean_returnvalue(user_external::get_grade_items_returns(), $studentgrades);
        // No warnings returned.
        $this->assertCount(0, $studentgrades['warnings']);

        // Check that only grades for the student in the teacher group are returned.
        $this->assertCount(1, $studentgrades['usergrades']);
        $this->assertCount(2, $studentgrades['usergrades'][0]['gradeitems']);

        $this->assertEquals($course->id, $studentgrades['usergrades'][0]['courseid']);
        $this->assertEquals($student1->id, $studentgrades['usergrades'][0]['userid']);
        $this->assertEquals($student1->idnumber, $studentgrades['usergrades'][0]['useridnumber']);
        $this->assertEquals($assignment->name, $studentgrades['usergrades'][0]['gradeitems'][0]['itemname']);
        $this->assertEquals('mod', $studentgrades['usergrades'][0]['gradeitems'][0]['itemtype']);
        $this->assertEquals('assign', $studentgrades['usergrades'][0]['gradeitems'][0]['itemmodule']);
        $this->assertEquals($assignment->id, $studentgrades['usergrades'][0]['gradeitems'][0]['iteminstance']);
        $this->assertNull($studentgrades['usergrades'][0]['gradeitems'][0]['locked']);
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
        $this->assertNull($studentgrades['usergrades'][0]['gradeitems'][0]['gradeislocked']);
        $this->assertNull($studentgrades['usergrades'][0]['gradeitems'][0]['gradeisoverridden']);
        $this->assertEquals('B-', $studentgrades['usergrades'][0]['gradeitems'][0]['lettergradeformatted']);
        $this->assertEquals(1, $studentgrades['usergrades'][0]['gradeitems'][0]['rank']);
        $this->assertEquals(3, $studentgrades['usergrades'][0]['gradeitems'][0]['numusers']);
        $this->assertEquals(
            round(array_sum([$s1grade, $s2grade, $s3grade]) / 3, 2),
            $studentgrades['usergrades'][0]['gradeitems'][0]['averageformatted']);

        // Check that the idnumber for assignment grades is equal to the cmid.
        $this->assertEquals((string) $studentgrades['usergrades'][0]['gradeitems'][0]['cmid'],
            $studentgrades['usergrades'][0]['gradeitems'][0]['idnumber']);

        // Hide one grade for the user.
        $gradegrade = new \grade_grade([
            'userid' => $student1->id,
            'itemid' => $studentgrades['usergrades'][0]['gradeitems'][0]['id']
        ], true);
        $gradegrade->set_hidden(1);
        $studentgrades = user_external::get_grade_items($course->id, $student1->id);
        $studentgrades = external_api::clean_returnvalue(user_external::get_grade_items_returns(), $studentgrades);

        // Check we get only the course final grade.
        $this->assertCount(1, $studentgrades['usergrades']);
        $this->assertCount(1, $studentgrades['usergrades'][0]['gradeitems']);
        $this->assertEquals('course', $studentgrades['usergrades'][0]['gradeitems'][0]['itemtype']);
    }

}
