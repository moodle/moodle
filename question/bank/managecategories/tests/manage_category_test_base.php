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

namespace qbank_managecategories;

use core_question\local\bank\question_edit_contexts;
use moodle_url;
/**
 * Test base for category tests
 *
 * @package    qbank_managecategories
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class manage_category_test_base extends \advanced_testcase {
    /**
     * Create a question category for a context.
     *
     * @param int $contextid the context where question category will be created for
     * @param array $categorydetails details of the category
     * @return \stdClass question category record
     */
    private function create_new_question_category_for_a_context(int $contextid, array $categorydetails = []) {
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $categorydetails['contextid'] = $contextid;
        return $questiongenerator->create_question_category($categorydetails);
    }

    /**
     * Create a question category for the system.
     *
     * @param array $categorydetails details of the category
     * @return \stdClass question category record
     */
    protected function create_question_category_for_the_system(array $categorydetails = []): \stdClass {
        $context = \context_system::instance();
        return $this->create_new_question_category_for_a_context($context->id, $categorydetails);
    }

    /**
     * Create a course category
     *
     * @return \core_course_category new course category
     */
    protected function create_course_category(): \core_course_category {
        // Course category.
        return $this->getDataGenerator()->create_category();
    }

    /**
     * Create a question category for a course category.
     *
     * @param \core_course_category $coursecategory the course category that new question category will be created for
     * @param array $categorydetails details of the category
     * @return \stdClass question category record
     */
    protected function create_question_category_for_a_course_category(
        \core_course_category $coursecategory,
        array $categorydetails = [],
    ): \stdClass {
        $context = \context_coursecat::instance($coursecategory->id);
        return $this->create_new_question_category_for_a_context($context->id, $categorydetails);
    }

    /**
     * Create a course
     *
     * @return \stdClass new course
     */
    protected function create_course(): \stdClass {
        // Course.
        return $this->getDataGenerator()->create_course();
    }

    /**
     * Create a question category for a course.
     *
     * @param \stdClass $course the course that new question category will be created for
     * @param array $categorydetails details of the category
     * @return \stdClass category record
     */
    protected function create_question_category_for_a_course(\stdClass $course, array $categorydetails = []): \stdClass {
        $context = \context_course::instance($course->id);
        return $this->create_new_question_category_for_a_context($context->id, $categorydetails);
    }

    /**
     * Create a quiz
     *
     * @return \stdClass the new quiz
     */
    protected function create_quiz(): \stdClass {
        // Quiz.
        $course = $this->getDataGenerator()->create_course();
        return $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
    }

    /**
     * Create a question category for a quiz.
     *
     * @param \stdClass $quiz the quiz that new category will be created for
     * @param array $categorydetails details of the category
     * @return \stdClass category record
     */
    protected function create_question_category_for_a_quiz(\stdClass $quiz, array $categorydetails = []): \stdClass {
        $context = \context_module::instance($quiz->cmid);
        return $this->create_new_question_category_for_a_context($context->id, $categorydetails);
    }

    /**
     * Create a new question in a category
     *
     * @param string $qtype the question type
     * @param int $categoryid the category that new question will be created on
     * @return \stdClass new question
     */
    protected function create_question_in_a_category(string $qtype, int $categoryid): \stdClass {
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        return $questiongenerator->create_question($qtype, null, ['category' => $categoryid]);
    }

    /**
     * Get Parent of a question category
     *
     * @param int $questioncategoryid a question category
     * @return int the id of the parent
     */
    protected function get_parent_of_a_question_category(int $questioncategoryid): int {
        global $DB;
        $parent = $DB->get_field('question_categories', 'parent', ['id' => $questioncategoryid]);
        return $parent ?: 0;
    }
}
