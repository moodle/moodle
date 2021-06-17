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
 * Unit tests for the class component_gradeitem.
 *
 * @package   core_grades
 * @category  test
 * @copyright 2021 Mark Nelson <marknelson@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

namespace core_grades;

use advanced_testcase;
use mod_forum\local\container;
use mod_forum\local\entities\forum as forum_entity;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for the class component_gradeitem.
 *
 * @package   core_grades
 * @category  test
 * @copyright 2021 Mark Nelson <marknelson@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class component_gradeitem_test extends advanced_testcase {

    /**
     * Test get_formatted_grade_for_user with points.
     */
    public function test_get_formatted_grade_for_user_with_points() {
        $grade = $this->initialise_test_and_get_grade_item(5, 4);

        $this->assertEquals(4, $grade->grade);
        $this->assertEquals('4.00 / 5.00', $grade->usergrade);
        $this->assertEquals(5, $grade->maxgrade);
    }

    /**
     * Test get_formatted_grade_for_user with letters.
     */
    public function test_get_formatted_grade_for_user_with_letters() {
        $grade = $this->initialise_test_and_get_grade_item(5, 4, GRADE_DISPLAY_TYPE_LETTER);

        $this->assertEquals(4, $grade->grade);
        $this->assertEquals('B-', $grade->usergrade);
        $this->assertEquals(5, $grade->maxgrade);
    }

    /**
     * Test get_formatted_grade_for_user with percentage.
     */
    public function test_get_formatted_grade_for_user_with_percentage() {
        $grade = $this->initialise_test_and_get_grade_item(5, 4, GRADE_DISPLAY_TYPE_PERCENTAGE);

        $this->assertEquals(4, $grade->grade);
        $this->assertEquals('80.00 %', $grade->usergrade);
        $this->assertEquals(5, $grade->maxgrade);
    }

    /**
     * Test get_formatted_grade_for_user with points and letter.
     */
    public function test_get_formatted_grade_for_user_with_points_letter() {
        $grade = $this->initialise_test_and_get_grade_item(5, 4, GRADE_DISPLAY_TYPE_REAL_LETTER);

        $this->assertEquals(4, $grade->grade);
        $this->assertEquals('4.00 (B-)', $grade->usergrade);
        $this->assertEquals(5, $grade->maxgrade);
    }

    /**
     * Test get_formatted_grade_for_user with scales.
     */
    public function test_get_formatted_grade_for_user_with_scales() {
        $grade = $this->initialise_test_and_get_grade_item(-2, 2);

        $this->assertEquals(2, $grade->grade);
        $this->assertEquals('Competent', $grade->usergrade);
        $this->assertEquals(2, $grade->maxgrade);
    }

    /**
     * Test get_formatted_grade_for_user with rubric.
     */
    public function test_get_formatted_grade_for_user_with_rubric() {
        $this->resetAfterTest();

        $generator = \testing_util::get_data_generator();
        $rubricgenerator = $generator->get_plugin_generator('gradingform_rubric');

        $forum = $this->get_forum_instance();
        $course = $forum->get_course_record();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course);

        $this->setUser($teacher);

        $controller = $rubricgenerator->get_test_rubric($forum->get_context(), 'forum', 'forum');

        // In the situation of mod_forum this would be the id from forum_grades.
        $itemid = 1;
        $instance = $controller->create_instance($student->id, $itemid);

        $spellingscore = 1;
        $spellingremark = 'Too many mistakes. Please try again.';
        $picturescore = 2;
        $pictureremark = 'Great number of pictures. Well done.';

        $submissiondata = $rubricgenerator->get_test_form_data(
            $controller,
            (int) $student->id,
            $spellingscore,
            $spellingremark,
            $picturescore,
            $pictureremark
        );

        $gradeitem = component_gradeitem::instance('mod_forum', $forum->get_context(), 'forum');
        $gradeitem->store_grade_from_formdata($student, $teacher, (object) [
            'instanceid' => $instance->get_id(),
            'advancedgrading' => $submissiondata,
        ]);

        $this->setUser($student);

        $result = $gradeitem->get_formatted_grade_for_user($student, $teacher);

        $this->assertEquals(75, $result->grade);
        $this->assertEquals('75.00 / 100.00', $result->usergrade);
        $this->assertEquals(100, $result->maxgrade);
    }

    /**
     * Test get_formatted_grade_for_user with a marking guide.
     */
    public function test_get_formatted_grade_for_user_with_marking_guide() {
        $this->resetAfterTest();

        $generator = \testing_util::get_data_generator();
        $guidegenerator = $generator->get_plugin_generator('gradingform_guide');

        $forum = $this->get_forum_instance();
        $course = $forum->get_course_record();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course);

        $this->setUser($teacher);

        $controller = $guidegenerator->get_test_guide($forum->get_context(), 'forum', 'forum');

        // In the situation of mod_forum this would be the id from forum_grades.
        $itemid = 1;
        $instance = $controller->create_instance($student->id, $itemid);

        $spellingscore = 10;
        $spellingremark = 'Propper good speling';
        $picturescore = 0;
        $pictureremark = 'ASCII art is not a picture';

        $submissiondata = $guidegenerator->get_test_form_data($controller,
            $itemid,
            $spellingscore,
            $spellingremark,
            $picturescore,
            $pictureremark
        );

        $gradeitem = component_gradeitem::instance('mod_forum', $forum->get_context(), 'forum');
        $gradeitem->store_grade_from_formdata($student, $teacher, (object) [
            'instanceid' => $instance->get_id(),
            'advancedgrading' => $submissiondata,
        ]);

        $this->setUser($student);

        $result = $gradeitem->get_formatted_grade_for_user($student, $teacher);

        $this->assertEquals(25, $result->grade);
        $this->assertEquals('25.00 / 100.00', $result->usergrade);
        $this->assertEquals(100, $result->maxgrade);
    }

    /**
     * Initialise test and returns the grade item.
     *
     * @param int $gradeforum The grade_forum value for the forum.
     * @param int $gradegiven The grade given by the teacher.
     * @param int|null $displaytype The display type of the grade.
     * @return \stdClass|null
     */
    protected function initialise_test_and_get_grade_item(int $gradeforum, int $gradegiven, int $displaytype = null): \stdClass {
        $this->resetAfterTest();

        $forum = $this->get_forum_instance([
            // Negative numbers mean a scale, positive numbers represent the maximum mark.
            'grade_forum' => $gradeforum,
        ]);
        $course = $forum->get_course_record();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course);

        $this->setUser($teacher);

        // Get the grade item.
        $gradeitem = component_gradeitem::instance('mod_forum', $forum->get_context(), 'forum');

        // Grade the student.
        $gradeitem->store_grade_from_formdata($student, $teacher, (object) ['grade' => $gradegiven]);

        // Change the 'Grade display type' if specified.
        if ($displaytype) {
            grade_set_setting($course->id, 'displaytype', $displaytype);
        }

        return $gradeitem->get_formatted_grade_for_user($student, $teacher);
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
        $forum = $datagenerator->create_module('forum', array_merge(['course' => $course->id,
            'grade_forum' => 100], $config));

        $vaultfactory = container::get_vault_factory();
        $vault = $vaultfactory->get_forum_vault();

        return $vault->get_from_id((int) $forum->id);
    }
}
