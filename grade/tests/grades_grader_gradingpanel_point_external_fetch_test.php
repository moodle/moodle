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
 * Unit tests for core_grades\component_gradeitems;
 *
 * @package   core_grades
 * @category  test
 * @copyright 2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

declare(strict_types = 1);

namespace core_grades\grades\grader\gradingpanel\point\external;

use advanced_testcase;
use coding_exception;
use core_grades\component_gradeitem;
use external_api;
use mod_forum\local\entities\forum as forum_entity;
use moodle_exception;

/**
 * Unit tests for core_grades\component_gradeitems;
 *
 * @package   core_grades
 * @category  test
 * @copyright 2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fetch_test extends advanced_testcase {

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
        fetch::execute('mod_invalid', 1, 'foo', 2);
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
        fetch::execute('mod_forum', 1, 'foo', 2);
    }

    /**
     * Ensure that an execute against a different grading method is rejected.
     */
    public function test_execute_incorrect_type(): void {
        $this->resetAfterTest();

        $forum = $this->get_forum_instance([
            // Negative numbers mean a scale.
            'grade_forum' => -1,
        ]);
        $course = $forum->get_course_record();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($teacher);

        $gradeitem = component_gradeitem::instance('mod_forum', $forum->get_context(), 'forum');

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage("not configured for direct grading");
        fetch::execute('mod_forum', (int) $forum->get_context()->id, 'forum', (int) $student->id);
    }

    /**
     * Ensure that an execute against the correct grading method returns the current state of the user.
     */
    public function test_execute_fetch_empty(): void {
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

        $result = fetch::execute('mod_forum', (int) $forum->get_context()->id, 'forum', (int) $student->id);
        $result = external_api::clean_returnvalue(fetch::execute_returns(), $result);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('templatename', $result);

        $this->assertEquals('core_grades/grades/grader/gradingpanel/point', $result['templatename']);

        $this->assertArrayHasKey('grade', $result);
        $this->assertIsArray($result['grade']);
        $this->assertArrayHasKey('grade', $result['grade']);
        $this->assertEmpty($result['grade']['grade']);
        $this->assertArrayHasKey('timecreated', $result['grade']);
        $this->assertIsInt($result['grade']['timecreated']);
        $this->assertArrayHasKey('timemodified', $result['grade']);
        $this->assertIsInt($result['grade']['timemodified']);

        $this->assertArrayHasKey('warnings', $result);
        $this->assertIsArray($result['warnings']);
        $this->assertEmpty($result['warnings']);
    }

    /**
     * Ensure that an execute against the correct grading method returns the current state of the user.
     */
    public function test_execute_fetch_graded(): void {
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
        $gradeitem->store_grade_from_formdata($student, $teacher, (object) [
            'grade' => 4,
        ]);

        $result = fetch::execute('mod_forum', (int) $forum->get_context()->id, 'forum', (int) $student->id);
        $result = external_api::clean_returnvalue(fetch::execute_returns(), $result);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('templatename', $result);

        $this->assertEquals('core_grades/grades/grader/gradingpanel/point', $result['templatename']);

        $this->assertArrayHasKey('grade', $result);
        $this->assertIsArray($result['grade']);
        $this->assertArrayHasKey('grade', $result['grade']);
        $this->assertIsFloat($result['grade']['grade']);
        $this->assertEquals(grade_floatval(unformat_float(4)), $result['grade']['grade']);
        $this->assertArrayHasKey('timecreated', $result['grade']);
        $this->assertIsInt($result['grade']['timecreated']);
        $this->assertArrayHasKey('timemodified', $result['grade']);
        $this->assertIsInt($result['grade']['timemodified']);

        $this->assertArrayHasKey('warnings', $result);
        $this->assertIsArray($result['warnings']);
        $this->assertEmpty($result['warnings']);
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
}
