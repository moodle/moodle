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

namespace core;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/gradelib.php');

/**
 * Unit tests for /lib/gradelib.php.
 *
 * @package   core
 * @category  test
 * @copyright 2012 Andrew Davis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class gradelib_test extends \advanced_testcase {

    public function test_grade_update_mod_grades(): void {

        $this->resetAfterTest(true);

        // Create a broken module instance.
        $modinstance = new \stdClass();
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
        $modinstance->cmidnumber = $cm->idnumber;
        $modinstance->modname = 'assign';

        $this->assertTrue(grade_update_mod_grades($modinstance));
    }


    /**
     * Tests is_gradable() function return.
     *
     * @covers \is_gradable()
     * @dataProvider graditems_provider
     * @param array $gradetypes Grade item types to create.
     * @param bool $expected The expected result for is_gradable() function.
     * @return void
     */
    public function test_is_gradable(array $gradetypes, bool $expected): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $assignment = $generator->create_module('assign', ['course' => $course->id, 'gradetype' => GRADE_TYPE_NONE]);
        // Create grade items.
        foreach ($gradetypes as $gradetype) {
            $generator->create_grade_item(
                [
                    'courseid' => $course->id,
                    'itemtype' => 'mod',
                    'itemmodule' => 'assign',
                    'iteminstance' => $assignment->id,
                    'gradetype' => $gradetype,
                ]
            );
        }
        $this->assertEquals($expected, is_gradable($course->id, 'mod', 'assign', $assignment->id));
    }

    /**
     * Data provider for testing test_is_gradable function.
     *
     * @return array
     */
    public static function graditems_provider(): array {
        return [
            'No grade items' => [
                'gradetypes' => [],
                'expected' => false,
            ],
            'No grading item' => [
                'gradetypes' => [GRADE_TYPE_NONE],
                'expected' => false,
            ],
            'Grading by feedback' => [
                'gradetypes' => [GRADE_TYPE_TEXT],
                'expected' => false,
            ],
            'Grading by points' => [
                'gradetypes' => [GRADE_TYPE_VALUE],
                'expected' => true,
            ],
            'Grading by scale' => [
                'gradetypes' => [GRADE_TYPE_SCALE],
                'expected' => true,
            ],
            'Mix of grading' => [
                'gradetypes' => [GRADE_TYPE_TEXT, GRADE_TYPE_NONE, GRADE_TYPE_VALUE, GRADE_TYPE_SCALE],
                'expected' => true,
            ],
        ];
    }

    /**
     * Tests the function remove_grade_letters().
     */
    public function test_remove_grade_letters(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $context = \context_course::instance($course->id);

        // Add a grade letter to the course.
        $letter = new \stdClass();
        $letter->letter = 'M';
        $letter->lowerboundary = '100';
        $letter->contextid = $context->id;
        $DB->insert_record('grade_letters', $letter);

        // Pre-warm the cache, ensure that that the letter is cached.
        $cache = \cache::make('core', 'grade_letters');

        // Check that the cache is empty beforehand.
        $letters = $cache->get($context->id);
        $this->assertFalse($letters);

        // Call the function.
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
    public function test_grade_course_category_delete(): void {
        global $DB;

        $this->resetAfterTest();

        $category = \core_course_category::create(array('name' => 'Cat1'));

        // Add a grade letter to the category.
        $letter = new \stdClass();
        $letter->letter = 'M';
        $letter->lowerboundary = '100';
        $letter->contextid = \context_coursecat::instance($category->id)->id;
        $DB->insert_record('grade_letters', $letter);

        grade_course_category_delete($category->id, '', false);

        // Confirm grade letter was deleted.
        $this->assertEquals(0, $DB->count_records('grade_letters'));
    }

    /**
     * Tests the function grade_regrade_final_grades().
     */
    public function test_grade_regrade_final_grades(): void {
        global $DB;

        $this->resetAfterTest();

        // Setup some basics.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        // We need two grade items.
        $params = ['idnumber' => 'g1', 'courseid' => $course->id];
        $g1 = new \grade_item($this->getDataGenerator()->create_grade_item($params));
        unset($params['idnumber']);
        $g2 = new \grade_item($this->getDataGenerator()->create_grade_item($params));

        $category = new \grade_category($this->getDataGenerator()->create_grade_category($params));
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
     * @param \stdClass $grade
     * @param \stdClass $user
     * @param int $expected
     */
    public function test_grade_get_date_for_user_grade(\stdClass $grade, \stdClass $user, ?int $expected): void {
        $this->assertEquals($expected, grade_get_date_for_user_grade($grade, $user));
    }

    /**
     * Data provider for tests of the grade_get_date_for_user_grade function.
     *
     * @return array
     */
    public static function grade_get_date_for_user_grade_provider(): array {
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
    public function test_get_grade_letters(): void {

        $this->resetAfterTest();

        // Setup some basics.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        $cache = \cache::make('core', 'grade_letters');
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
    public function test_get_grade_letters_custom(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        $cache = \cache::make('core', 'grade_letters');
        $letters = $cache->get($context->id);

        // Make sure the cache is empty.
        $this->assertFalse($letters);

        // Add a grade letter to the course.
        $letter = new \stdClass();
        $letter->letter = 'M';
        $letter->lowerboundary = '100';
        $letter->contextid = $context->id;
        $DB->insert_record('grade_letters', $letter);

        $actual = grade_get_letters($context);
        $expected = $cache->get($context->id);

        $this->assertEquals($expected, $actual);
    }

    /**
     * When getting a calculated grade containing an error, we mark grading finished and don't keep trying to regrade.
     *
     * @covers \grade_get_grades()
     * @return void
     */
    public function test_grade_get_grades_errors(): void {
        $this->resetAfterTest();

        // Setup some basics.
        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $user2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'editingteacher');
        // Set up 2 gradeable activities.
        $assign = $this->getDataGenerator()->create_module('assign', ['idnumber' => 'a1', 'course' => $course->id]);
        $quiz = $this->getDataGenerator()->create_module('quiz', ['idnumber' => 'q1', 'course' => $course->id]);

        // Create a calculated grade item using the activities.
        $params = ['courseid' => $course->id];
        $g1 = new \grade_item($this->getDataGenerator()->create_grade_item($params));
        $g1->set_calculation('=[[a1]] + [[q1]]');

        // Now delete one of the activities to break the calculation.
        \core_courseformat\formatactions::cm($course->id)->delete($assign->cmid);

        // Course grade item has needsupdate.
        $this->assertEquals(1, \grade_item::fetch_course_item($course->id)->needsupdate);

        // Get grades for the quiz, to trigger a regrade.
        $this->setUser($user2);
        $grades1 = grade_get_grades($course->id, 'mod', 'quiz', $quiz->id);
        // We should get an error for the broken calculation.
        $this->assertNotEmpty($grades1->errors);
        $this->assertEquals(get_string('errorcalculationbroken', 'grades', $g1->itemname), reset($grades1->errors));
        // Course grade item should not have needsupdate so that we don't try to regrade again.
        $this->assertEquals(0, \grade_item::fetch_course_item($course->id)->needsupdate);

        // Get grades for the quiz again. This should not trigger the regrade and resulting error this time.
        $grades2 = grade_get_grades($course->id, 'mod', 'quiz', $quiz->id);
        $this->assertEmpty($grades2->errors);
    }
}
