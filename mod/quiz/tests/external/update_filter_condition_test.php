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

namespace mod_quiz\external;

use advanced_testcase;
use core_question\local\bank\condition;
use mod_quiz\quiz_settings;

/**
 * Unit tests for the update_filter_condition webservice.
 *
 * @package    mod_quiz
 * @copyright  2025 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Conn Warwicker <conn.warwicker@catalyst-eu.net>
 * @covers \mod_quiz\external\update_filter_condition::execute
 */
final class update_filter_condition_test extends advanced_testcase {

    /**
     * Generate a course with a quiz activity with two random questions for use in tests.
     * @return array
     */
    private function create_quiz_with_random_questions(): array {

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a quiz activity.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $course->id]);
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);
        $quizobj = quiz_settings::create($quiz->id);
        $quizcontext = \core\context\module::instance($quiz->cmid);

        // Create a question category (top level) and question.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $questioncategory = $questiongenerator->create_question_category();

        // Create 2 questions to choose from.
        $questiongenerator->create_question('shortanswer', null, ['category' => $questioncategory->id]);
        $questiongenerator->create_question('shortanswer', null, ['category' => $questioncategory->id]);

        // Add random question to the quiz.
        $filtercondition = [
            'filter' => [
                'category' => [
                    'jointype' => condition::JOINTYPE_DEFAULT,
                    'values' => [$questioncategory->id],
                    'filteroptions' => ['includesubcategories' => false],
                ],
            ],
        ];
        $quizobj->get_structure()->add_random_questions(1, 1, $filtercondition);

        return [
            $course,
            $cm,
            $questioncategory,
            $filtercondition,
            $quizcontext,
        ];

    }

    /**
     * Test updating the filter conditions of a random question
     * @runInSeparateProcess
     * @return void
     */
    public function test_update_filter_condition(): void {

        global $DB;

        [
            $course,
            $cm,
            $questioncategory,
            $filtercondition,
            $quizcontext,
        ] = $this->create_quiz_with_random_questions();

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Check what the question set reference record contains currently.
        $qsetref = $DB->get_record('question_set_references', [
            'usingcontextid' => $quizcontext->id,
        ]);

        $this->assertEquals($questioncategory->contextid, $qsetref->questionscontextid);

        // Create a new question category on the course.
        $coursecontext = \core\context\course::instance($course->id);
        $questioncategory2 = $questiongenerator->create_question_category([
            'contextid' => $coursecontext->id,
            'parent' => 0,
        ]);

        // Call the webservice execute method.
        $filtercondition['filter']['category']['values'] = [$questioncategory2->id];
        update_filter_condition::execute($cm->id, $qsetref->itemid, json_encode($filtercondition));

        // Check that the questionscontextid value has been updated.
        $qsetref = $DB->get_record('question_set_references', [
            'usingcontextid' => $quizcontext->id,
        ]);

        $this->assertEquals($questioncategory2->contextid, $qsetref->questionscontextid);

    }

    /**
     * Test updating the filter conditions of a random question with an invalid array of conditions
     * @runInSeparateProcess
     * @return void
     */
    public function test_invalid_filter_condition(): void {

        global $DB;

        [, $cm, , , $quizcontext] = $this->create_quiz_with_random_questions();

        $qsetref = $DB->get_record('question_set_references', [
            'usingcontextid' => $quizcontext->id,
        ]);

        // Try to call the service with an invalid array of filterconditions.
        $filtercondition = ['invalid' => true];
        $this->expectException(\moodle_exception::class);
        update_filter_condition::execute($cm->id, $qsetref->itemid, json_encode($filtercondition));

    }

    /**
     * Test updating the filter conditions of a random question with an invalid question category
     * @runInSeparateProcess
     * @return void
     */
    public function test_invalid_question_category(): void {

        global $DB;

        [ , $cm, , $filtercondition, $quizcontext] = $this->create_quiz_with_random_questions();

        $qsetref = $DB->get_record('question_set_references', [
            'usingcontextid' => $quizcontext->id,
        ]);

        // Try to call the service with an invalid question category set in the $filtercondition.
        $filtercondition['filter']['category']['values'] = [123];
        $this->expectException(\moodle_exception::class);
        update_filter_condition::execute($cm->id, $qsetref->itemid, json_encode($filtercondition));

    }

}
