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
 * Unit tests for /lib/gradelib.php.
 *
 * @package   core_grades
 * @category  phpunit
 * @copyright 2012 Andrew Davis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/gradelib.php');

class core_gradelib_testcase extends advanced_testcase {

    public function test_grade_update_mod_grades() {

        $this->resetAfterTest(true);

        // Create a broken module instance.
        $modinstance = new stdClass();
        $modinstance->modname = 'doesntexist';

        $this->assertFalse(grade_update_mod_grades($modinstance));
        // A debug message should have been generated.
        $this->assertDebuggingCalled();

        // Create a course and instance of mod_assign.
        $course = $this->getDataGenerator()->create_course();

        $assigndata['course'] = $course->id;
        $assigndata['name'] = 'lightwork assignment';
        $modinstance = self::getDataGenerator()->create_module('assign', $assigndata);

        // Function grade_update_mod_grades() requires 2 additional properties, cmidnumber and modname.
        $cm = get_coursemodule_from_instance('assign', $modinstance->id, 0, false, MUST_EXIST);
        $modinstance->cmidnumber = $cm->id;
        $modinstance->modname = 'assign';

        $this->assertTrue(grade_update_mod_grades($modinstance));
    }

    /**
     * Tests the function remove_grade_letters().
     */
    public function test_remove_grade_letters() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $context = context_course::instance($course->id);

        // Add a grade letter to the course.
        $letter = new stdClass();
        $letter->letter = 'M';
        $letter->lowerboundary = '100';
        $letter->contextid = $context->id;
        $DB->insert_record('grade_letters', $letter);

        remove_grade_letters($context, false);

        // Confirm grade letter was deleted.
        $this->assertEquals(0, $DB->count_records('grade_letters'));
    }

    /**
     * Tests the function grade_course_category_delete().
     */
    public function test_grade_course_category_delete() {
        global $DB;

        $this->resetAfterTest();

        $category = coursecat::create(array('name' => 'Cat1'));

        // Add a grade letter to the category.
        $letter = new stdClass();
        $letter->letter = 'M';
        $letter->lowerboundary = '100';
        $letter->contextid = context_coursecat::instance($category->id)->id;
        $DB->insert_record('grade_letters', $letter);

        grade_course_category_delete($category->id, '', false);

        // Confirm grade letter was deleted.
        $this->assertEquals(0, $DB->count_records('grade_letters'));
    }

    /**
     * Tests the function grade_regrade_final_grades().
     */
    public function test_grade_regrade_final_grades() {
        global $DB;

        $this->resetAfterTest();

        // Setup some basics.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        // We need two grade items.
        $params = ['idnumber' => 'g1', 'courseid' => $course->id];
        $g1 = new grade_item($this->getDataGenerator()->create_grade_item($params));
        unset($params['idnumber']);
        $g2 = new grade_item($this->getDataGenerator()->create_grade_item($params));

        $category = new grade_category($this->getDataGenerator()->create_grade_category($params));
        $catitem = $category->get_grade_item();

        // Now set a calculation.
        $catitem->set_calculation('=[[g1]]');

        $catitem->update();

        // Everything needs updating.
        $this->assertEquals(4, $DB->count_records('grade_items', ['courseid' => $course->id, 'needsupdate' => 1]));

        grade_regrade_final_grades($course->id);

        // See that everything has been updated.
        $this->assertEquals(0, $DB->count_records('grade_items', ['courseid' => $course->id, 'needsupdate' => 1]));

        $g1->delete();

        // Now there is one that needs updating.
        $this->assertEquals(1, $DB->count_records('grade_items', ['courseid' => $course->id, 'needsupdate' => 1]));

        // This can cause an infinite loop if things go... poorly.
        grade_regrade_final_grades($course->id);

        // Now because of the failure, two things need updating.
        $this->assertEquals(2, $DB->count_records('grade_items', ['courseid' => $course->id, 'needsupdate' => 1]));
    }
}
