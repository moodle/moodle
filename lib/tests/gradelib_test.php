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

        // Pre-warm the cache, ensure that that the letter is cached.
        $cache = cache::make('core', 'grade_letters');

        // Check that the cache is empty beforehand.
        $letters = $cache->get($context->id);
        $this->assertFalse($letters);

        grade_get_letters($context);

        $letters = $cache->get($context->id);
        $this->assertEquals(1, count($letters));
        $this->assertTrue(in_array($letter->letter, $letters));

        remove_grade_letters($context, false);

        // Confirm grade letter was deleted.
        $this->assertEquals(0, $DB->count_records('grade_letters'));

        // Confirm grade letter is also deleted from cache.
        $letters = $cache->get($context->id);
        $this->assertFalse($letters);
    }

    /**
     * Tests the function grade_course_category_delete().
     */
    public function test_grade_course_category_delete() {
        global $DB;

        $this->resetAfterTest();

        $category = core_course_category::create(array('name' => 'Cat1'));

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

    /**
     * Tests for the grade_get_date_for_user_grade function.
     *
     * @dataProvider grade_get_date_for_user_grade_provider
     * @param stdClass $grade
     * @param stdClass $user
     * @param int $expected
     */
    public function test_grade_get_date_for_user_grade(stdClass $grade, stdClass $user, ?int $expected): void {
        $this->assertEquals($expected, grade_get_date_for_user_grade($grade, $user));
    }

    /**
     * Data provider for tests of the grade_get_date_for_user_grade function.
     *
     * @return array
     */
    public function grade_get_date_for_user_grade_provider(): array {
        $u1 = (object) [
            'id' => 42,
        ];
        $u2 = (object) [
            'id' => 930,
        ];

        $d1 = 1234567890;
        $d2 = 9876543210;

        $g1 = (object) [
            'usermodified' => $u1->id,
            'dategraded' => $d1,
            'datesubmitted' => $d2,
        ];
        $g2 = (object) [
            'usermodified' => $u1->id,
            'dategraded' => $d1,
            'datesubmitted' => 0,
        ];

        $g3 = (object) [
            'usermodified' => $u1->id,
            'dategraded' => null,
            'datesubmitted' => $d2,
        ];

        return [
            'If the user is the last person to have modified the grade_item then show the date that it was graded' => [
                $g1,
                $u1,
                $d1,
            ],
            'If there is no grade and there is no feedback, then show graded date as null' => [
                $g3,
                $u1,
                null,
            ],
            'If the user is not the last person to have modified the grade_item, ' .
            'and there is no submission date, then show the date that it was submitted' => [
                $g1,
                $u2,
                $d2,
            ],
            'If the user is not the last person to have modified the grade_item, ' .
            'but there is no submission date, then show the date that it was graded' => [
                $g2,
                $u2,
                $d1,
            ],
            'If the user is the last person to have modified the grade_item, ' .
            'and there is no submission date, then still show the date that it was graded' => [
                $g2,
                $u1,
                $d1,
            ],
        ];
    }

    /**
     * Test the caching of grade letters.
     */
    public function test_get_grade_letters() {

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        $cache = cache::make('core', 'grade_letters');
        $letters = $cache->get($context->id);

        // Make sure the cache is empty.
        $this->assertFalse($letters);

        // Now check to see if the letters get cached.
        $actual = grade_get_letters($context);

        $expected = $cache->get($context->id);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test custom letters.
     */
    public function test_get_grade_letters_custom() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        $cache = cache::make('core', 'grade_letters');
        $letters = $cache->get($context->id);

        // Make sure the cache is empty.
        $this->assertFalse($letters);

        // Add a grade letter to the course.
        $letter = new stdClass();
        $letter->letter = 'M';
        $letter->lowerboundary = '100';
        $letter->contextid = $context->id;
        $DB->insert_record('grade_letters', $letter);

        $actual = grade_get_letters($context);
        $expected = $cache->get($context->id);

        $this->assertEquals($expected, $actual);
    }
}
