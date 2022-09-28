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

declare(strict_types = 1);

namespace core_grades\grades\grader\gradingpanel\scale\external;

use advanced_testcase;
use coding_exception;
use core_grades\component_gradeitem;
use external_api;
use mod_forum\local\entities\forum as forum_entity;
use moodle_exception;
use grade_grade;
use grade_item;

/**
 * Unit tests for core_grades\component_gradeitems;
 *
 * @package   core_grades
 * @category  test
 * @copyright 2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class store_test extends advanced_testcase {

    public static function setupBeforeClass(): void {
        global $CFG;
        require_once("{$CFG->libdir}/externallib.php");
    }

    /**
     * Ensure that an execute with an invalid component is rejected.
     */
    public function test_execute_invalid_component(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage("The 'foo' item is not valid for the 'mod_invalid' component");
        store::execute('mod_invalid', 1, 'foo', 2, false, 'formdata');
    }

    /**
     * Ensure that an execute with an invalid itemname on a valid component is rejected.
     */
    public function test_execute_invalid_itemname(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage("The 'foo' item is not valid for the 'mod_forum' component");
        store::execute('mod_forum', 1, 'foo', 2, false, 'formdata');
    }

    /**
     * Ensure that an execute against a different grading method is rejected.
     */
    public function test_execute_incorrect_type(): void {
        $this->resetAfterTest();

        $forum = $this->get_forum_instance([
            // Negative numbers mean a scale.
            'grade_forum' => 5,
        ]);
        $course = $forum->get_course_record();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($teacher);

        $gradeitem = component_gradeitem::instance('mod_forum', $forum->get_context(), 'forum');

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage("not configured for grading with scales");
        store::execute('mod_forum', (int) $forum->get_context()->id, 'forum', (int) $student->id, false, 'formdata');
    }

    /**
     * Ensure that an execute against a different grading method is rejected.
     */
    public function test_execute_disabled(): void {
        $this->resetAfterTest();

        $forum = $this->get_forum_instance();
        $course = $forum->get_course_record();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($teacher);

        $gradeitem = component_gradeitem::instance('mod_forum', $forum->get_context(), 'forum');

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage("Grading is not enabled");
        store::execute('mod_forum', (int) $forum->get_context()->id, 'forum', (int) $student->id, false, 'formdata');
    }

    /**
     * Ensure that an execute against the correct grading method returns the current state of the user.
     */
    public function test_execute_store_empty(): void {
        [
            'forum' => $forum,
            'options' => $options,
            'student' => $student,
            'teacher' => $teacher,
        ] = $this->get_test_data();

        $this->setUser($teacher);

        $formdata = [
            'grade' => null,
        ];

        $gradeitem = component_gradeitem::instance('mod_forum', $forum->get_context(), 'forum');

        $result = store::execute('mod_forum', (int) $forum->get_context()->id, 'forum',
                (int) $student->id, false, http_build_query($formdata));
        $result = external_api::clean_returnvalue(store::execute_returns(), $result);

        // The result should still be empty.
        $this->assertIsArray($result);
        $this->assertArrayHasKey('templatename', $result);

        $this->assertEquals('core_grades/grades/grader/gradingpanel/scale', $result['templatename']);

        $this->assertArrayHasKey('warnings', $result);
        $this->assertIsArray($result['warnings']);
        $this->assertEmpty($result['warnings']);

        // Test the grade array items.
        $this->assertArrayHasKey('grade', $result);
        $this->assertIsArray($result['grade']);

        $this->assertIsInt($result['grade']['timecreated']);
        $this->assertArrayHasKey('timemodified', $result['grade']);
        $this->assertIsInt($result['grade']['timemodified']);

        $this->assertArrayHasKey('usergrade', $result['grade']);
        $this->assertEquals('-', $result['grade']['usergrade']);

        $this->assertArrayHasKey('maxgrade', $result['grade']);
        $this->assertIsInt($result['grade']['maxgrade']);
        $this->assertEquals(3, $result['grade']['maxgrade']);

        $this->assertArrayHasKey('gradedby', $result['grade']);
        $this->assertEquals(fullname($teacher), $result['grade']['gradedby']);

        $this->assertArrayHasKey('options', $result['grade']);
        $this->assertCount(count($options), $result['grade']['options']);
        rsort($options);
        foreach ($options as $index => $option) {
            $this->assertArrayHasKey($index, $result['grade']['options']);

            $returnedoption = $result['grade']['options'][$index];
            $this->assertArrayHasKey('value', $returnedoption);
            $this->assertEquals(3 - $index, $returnedoption['value']);

            $this->assertArrayHasKey('title', $returnedoption);
            $this->assertEquals($option, $returnedoption['title']);

            $this->assertArrayHasKey('selected', $returnedoption);
            $this->assertFalse($returnedoption['selected']);
        }

        // Compare against the grade stored in the database.
        $storedgradeitem = grade_item::fetch([
            'courseid' => $forum->get_course_id(),
            'itemtype' => 'mod',
            'itemmodule' => 'forum',
            'iteminstance' => $forum->get_id(),
            'itemnumber' => $gradeitem->get_grade_itemid(),
        ]);
        $storedgrade = grade_grade::fetch([
            'userid' => $student->id,
            'itemid' => $storedgradeitem->id,
        ]);

        $this->assertEmpty($storedgrade->rawgrade);
    }

    /**
     * Ensure that an execute against the correct grading method returns the current state of the user.
     */
    public function test_execute_store_not_selected(): void {
        [
            'forum' => $forum,
            'options' => $options,
            'student' => $student,
            'teacher' => $teacher,
        ] = $this->get_test_data();

        $this->setUser($teacher);

        $formdata = [
            'grade' => -1,
        ];

        $gradeitem = component_gradeitem::instance('mod_forum', $forum->get_context(), 'forum');

        $result = store::execute('mod_forum', (int) $forum->get_context()->id, 'forum',
                (int) $student->id, false, http_build_query($formdata));
        $result = external_api::clean_returnvalue(store::execute_returns(), $result);

        // The result should still be empty.
        $this->assertIsArray($result);
        $this->assertArrayHasKey('templatename', $result);

        $this->assertEquals('core_grades/grades/grader/gradingpanel/scale', $result['templatename']);

        $this->assertArrayHasKey('warnings', $result);
        $this->assertIsArray($result['warnings']);
        $this->assertEmpty($result['warnings']);

        // Test the grade array items.
        $this->assertArrayHasKey('grade', $result);
        $this->assertIsArray($result['grade']);

        $this->assertIsInt($result['grade']['timecreated']);
        $this->assertArrayHasKey('timemodified', $result['grade']);
        $this->assertIsInt($result['grade']['timemodified']);

        $this->assertArrayHasKey('usergrade', $result['grade']);
        $this->assertEquals('-', $result['grade']['usergrade']);

        $this->assertArrayHasKey('maxgrade', $result['grade']);
        $this->assertIsInt($result['grade']['maxgrade']);
        $this->assertEquals(3, $result['grade']['maxgrade']);

        $this->assertArrayHasKey('gradedby', $result['grade']);
        $this->assertEquals(null, $result['grade']['gradedby']);

        $this->assertArrayHasKey('options', $result['grade']);
        $this->assertCount(count($options), $result['grade']['options']);
        rsort($options);
        foreach ($options as $index => $option) {
            $this->assertArrayHasKey($index, $result['grade']['options']);

            $returnedoption = $result['grade']['options'][$index];
            $this->assertArrayHasKey('value', $returnedoption);
            $this->assertEquals(3 - $index, $returnedoption['value']);

            $this->assertArrayHasKey('title', $returnedoption);
            $this->assertEquals($option, $returnedoption['title']);

            $this->assertArrayHasKey('selected', $returnedoption);
            $this->assertFalse($returnedoption['selected']);
        }

        // Compare against the grade stored in the database.
        $storedgradeitem = grade_item::fetch([
            'courseid' => $forum->get_course_id(),
            'itemtype' => 'mod',
            'itemmodule' => 'forum',
            'iteminstance' => $forum->get_id(),
            'itemnumber' => $gradeitem->get_grade_itemid(),
        ]);
        $storedgrade = grade_grade::fetch([
            'userid' => $student->id,
            'itemid' => $storedgradeitem->id,
        ]);

        // No grade will have been saved.
        $this->assertFalse($storedgrade);
    }

    /**
     * Ensure that an execute against the correct grading method returns the current state of the user.
     */
    public function test_execute_store_graded(): void {
        [
            'scale' => $scale,
            'forum' => $forum,
            'options' => $options,
            'student' => $student,
            'teacher' => $teacher,
        ] = $this->get_test_data();

        $this->setUser($teacher);

        $formdata = [
            'grade' => 2,
        ];
        $formattedvalue = grade_floatval(unformat_float($formdata['grade']));

        $gradeitem = component_gradeitem::instance('mod_forum', $forum->get_context(), 'forum');

        $result = store::execute('mod_forum', (int) $forum->get_context()->id, 'forum',
                (int) $student->id, false, http_build_query($formdata));
        $result = external_api::clean_returnvalue(store::execute_returns(), $result);

        // The result should still be empty.
        $this->assertIsArray($result);
        $this->assertArrayHasKey('templatename', $result);

        $this->assertEquals('core_grades/grades/grader/gradingpanel/scale', $result['templatename']);

        $this->assertArrayHasKey('warnings', $result);
        $this->assertIsArray($result['warnings']);
        $this->assertEmpty($result['warnings']);

        // Test the grade array items.
        $this->assertArrayHasKey('grade', $result);
        $this->assertIsArray($result['grade']);

        $this->assertIsInt($result['grade']['timecreated']);
        $this->assertArrayHasKey('timemodified', $result['grade']);
        $this->assertIsInt($result['grade']['timemodified']);

        $this->assertArrayHasKey('usergrade', $result['grade']);
        $this->assertEquals('B', $result['grade']['usergrade']);

        $this->assertArrayHasKey('maxgrade', $result['grade']);
        $this->assertIsInt($result['grade']['maxgrade']);
        $this->assertEquals(3, $result['grade']['maxgrade']);

        $this->assertArrayHasKey('gradedby', $result['grade']);
        $this->assertEquals(fullname($teacher), $result['grade']['gradedby']);

        $this->assertArrayHasKey('options', $result['grade']);
        $this->assertCount(count($options), $result['grade']['options']);
        rsort($options);
        foreach ($options as $index => $option) {
            $this->assertArrayHasKey($index, $result['grade']['options']);

            $returnedoption = $result['grade']['options'][$index];
            $this->assertArrayHasKey('value', $returnedoption);
            $this->assertEquals(3 - $index, $returnedoption['value']);

            $this->assertArrayHasKey('title', $returnedoption);
            $this->assertEquals($option, $returnedoption['title']);

            $this->assertArrayHasKey('selected', $returnedoption);
        }

        // Compare against the grade stored in the database.
        $storedgradeitem = grade_item::fetch([
            'courseid' => $forum->get_course_id(),
            'itemtype' => 'mod',
            'itemmodule' => 'forum',
            'iteminstance' => $forum->get_id(),
            'itemnumber' => $gradeitem->get_grade_itemid(),
        ]);
        $storedgrade = grade_grade::fetch([
            'userid' => $student->id,
            'itemid' => $storedgradeitem->id,
        ]);

        $this->assertEquals($formattedvalue, $storedgrade->rawgrade);
        $this->assertEquals($scale->id, $storedgrade->rawscaleid);

        // The grade was 2, which relates to the middle option.
        $this->assertFalse($result['grade']['options'][0]['selected']);
        $this->assertTrue($result['grade']['options'][1]['selected']);
        $this->assertFalse($result['grade']['options'][2]['selected']);
    }

    /**
     * Ensure that an out-of-range value is rejected.
     *
     * @dataProvider execute_out_of_range_provider
     * @param int $suppliedvalue The value that was submitted
     */
    public function test_execute_store_out_of_range(int $suppliedvalue): void {
        [
            'scale' => $scale,
            'forum' => $forum,
            'options' => $options,
            'student' => $student,
            'teacher' => $teacher,
        ] = $this->get_test_data();

        $this->setUser($teacher);

        $formdata = [
            'grade' => $suppliedvalue,
        ];

        $gradeitem = component_gradeitem::instance('mod_forum', $forum->get_context(), 'forum');

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage("Invalid grade '{$suppliedvalue}' provided. Grades must be between 0 and 3.");
        store::execute('mod_forum', (int) $forum->get_context()->id, 'forum',
                (int) $student->id, false, http_build_query($formdata));
    }

    /**
     * Data provider for out of range tests.
     *
     * @return array
     */
    public function execute_out_of_range_provider(): array {
        return [
            'above' => [
                'supplied' => 500,
            ],
            'above just' => [
                'supplied' => 4,
            ],
            'below' => [
                'supplied' => -100,
            ],
            '-10' => [
                'supplied' => -10,
            ],
        ];
    }


    /**
     * Get a forum instance.
     *
     * @param array $config
     * @return forum_entity
     */
    protected function get_forum_instance(array $config = []): forum_entity {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', array_merge($config, ['course' => $course->id]));

        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $vault = $vaultfactory->get_forum_vault();

        return $vault->get_from_id((int) $forum->id);
    }

    /**
     * Get test data for scaled forums.
     *
     * @return array
     */
    protected function get_test_data(): array {
        $this->resetAfterTest();

        $options = [
            'A',
            'B',
            'C'
        ];
        $scale = $this->getDataGenerator()->create_scale(['scale' => implode(',', $options)]);

        $forum = $this->get_forum_instance([
            // Negative numbers mean a scale.
            'grade_forum' => -1 * $scale->id
        ]);
        $course = $forum->get_course_record();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        return [
            'forum' => $forum,
            'scale' => $scale,
            'options' => $options,
            'student' => $student,
            'teacher' => $teacher,
        ];
    }
}
