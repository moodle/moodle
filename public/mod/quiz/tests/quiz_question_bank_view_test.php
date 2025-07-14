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

namespace mod_quiz;

use core_question\local\bank\question_edit_contexts;
use mod_quiz\question\bank\custom_view;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/editlib.php');

/**
 * Unit tests for the quiz's own question bank view class.
 *
 * @package    mod_quiz
 * @category   test
 * @copyright  2018 the Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_quiz\question\bank\custom_view
 */
final class quiz_question_bank_view_test extends \advanced_testcase {

    public function test_viewing_question_bank_should_not_load_individual_questions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $generator->get_plugin_generator('core_question');

        // Create a course and a quiz.
        $course = $generator->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $context = \context_module::instance($quiz->cmid);
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);

        // Create a question in the default category.
        $contexts = new question_edit_contexts($context);
        $cat = question_get_default_category($context->id, true);
        $questiondata = $questiongenerator->create_question('numerical', null,
                ['name' => 'Example question', 'category' => $cat->id]);

        // Ensure the question is not in the cache.
        $cache = \cache::make('core', 'questiondata');
        $cache->delete($questiondata->id);

        // Generate the view.
        $params = [
            'qpage' => 0,
            'qperpage' => 20,
            'cat' => $cat->id . ',' . $context->id,
            'recurse' => false,
            'showhidden' => false,
            'qbshowtext' => false,
            'tabname' => 'editq'
        ];
        $extraparams = ['cmid' => $cm->id, 'quizcmid' => $cm->id];
        $view = new custom_view($contexts, new \moodle_url('/'), $course, $cm, $params, $extraparams);
        ob_start();
        $view->display();
        $html = ob_get_clean();

        // Verify the output includes the expected question.
        $this->assertStringContainsString('Example question', $html);

        // Verify the question has not been loaded into the cache.
        $this->assertFalse($cache->has($questiondata->id));
    }

    public function test_viewing_question_bank_should_not_load_hidden_question(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $generator->get_plugin_generator('core_question');

        // Create a course and a quiz.
        $course = $generator->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $context = \context_module::instance($quiz->cmid);
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);

        // Create a question in the default category.
        $contexts = new question_edit_contexts($context);
        $cat = question_get_default_category($context->id, true);
        $question = $questiongenerator->create_question('numerical', null,
            ['name' => 'Example question', 'category' => $cat->id]);

        // Create another version.
        $newversion = $questiongenerator->update_question($question, null, ['name' => 'This is the latest version']);

        // Add them to the quiz.
        quiz_add_quiz_question($newversion->id, $quiz);
        // Generate the view.
        $params = [
            'qpage' => 0,
            'qperpage' => 20,
            'cat' => $cat->id . ',' . $context->id,
            'recurse' => false,
            'showhidden' => false,
            'qbshowtext' => false,
            'tabname' => 'editq',
        ];
        $extraparams = ['quizcmid' => $cm->id];
        $view = new custom_view($contexts, new \moodle_url('/'), $course, $cm, $params, $extraparams);
        ob_start();
        $view->display();
        $html = ob_get_clean();
        // Verify the output should included the latest version.
        $this->assertStringContainsString('This is the latest version', $html);
        $this->assertStringNotContainsString('Example question', $html);
        // Delete the latest version.
        question_delete_question($newversion->id);
        // Verify the output should display the old version with status ready.
        ob_start();
        $view->display();
        $html = ob_get_clean();
        $this->assertStringContainsString('Example question', $html);
        $this->assertStringNotContainsString('This is the latest version', $html);
    }

    public function test_viewing_question_bank_when_paging_out_of_limit(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $generator->get_plugin_generator('core_question');

        // Create a course and a quiz.
        $course = $generator->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $context = \context_module::instance($quiz->cmid);

        // Create a question in the default category.
        $contexts = new question_edit_contexts($context);
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);
        $cat = question_get_default_category($context->id, true);

        // Create three questions.
        $questiongenerator->create_question('numerical', null,
            ['name' => 'Example question 1', 'category' => $cat->id]);
        $questiongenerator->create_question('numerical', null,
            ['name' => 'Example question 2', 'category' => $cat->id]);
        $question3 = $questiongenerator->create_question('numerical', null,
            ['name' => 'Example question 3', 'category' => $cat->id]);

        // Retrieve the question bank view on page 3 with 1 questions per page.
        $params = [
            'qpage' => 3,
            'qperpage' => 1,
            'cat' => $cat->id . ',' . $context->id,
            'recurse' => false,
            'showhidden' => false,
            'qbshowtext' => false,
            'tabname' => 'editq',
        ];

        // Load the question bank view.
        $view = new custom_view($contexts, new \moodle_url('/'), $course, $cm, $params, ['quizcmid' => $cm->id]);
        ob_start();
        $view->display();
        $html = ob_get_clean();

        // Verify that questions exist in the view.
        $this->assertStringNotContainsString('Example question 1', $html);
        $this->assertStringNotContainsString('Example question 2', $html);
        $this->assertStringContainsString('Example question 3', $html);

        // Set the param per page is 2.
        // The view only has 2 pages.
        $params['qperpage'] = 2;

        // Reload the question bank view on page 3.
        $view = new custom_view($contexts, new \moodle_url('/'), $course, $cm, $params, ['quizcmid' => $cm->id]);
        ob_start();
        $view->display();
        $html = ob_get_clean();

        // Since the view has only 2 pages and the requested page is out of range,
        // the view will move to the nearest available page.
        // Verify that the view is in the page 2.
        $this->assertEquals(1, $view->get_pagevars('qpage'));
        // Verify that questions exist in the view.
        $this->assertStringNotContainsString('Example question 1', $html);
        $this->assertStringNotContainsString('Example question 2', $html);
        $this->assertStringContainsString('Example question 3', $html);

        // Create a new category.
        $newcategory = $generator->create_category();
        $newcontext = \context_coursecat::instance($newcategory->id);
        $newquestioncat = $questiongenerator->create_question_category([
            'contextid' => $newcontext->id,
        ]);
        // Move question 3 to a new category.
        question_move_questions_to_category([$question3->id], $newquestioncat->id);
        // Load the question bank view from the new category.
        $params['cat'] = $newquestioncat->id . ',' . $newquestioncat->contextid;
        $view = new custom_view($contexts, new \moodle_url('/'), $course, $cm, $params, ['quizcmid' => $cm->id]);
        ob_start();
        $view->display();
        $html = ob_get_clean();
        // Verify that the view is in the page 1 and exist only one question.
        $this->assertEquals(0, $view->get_pagevars('qpage'));
        $this->assertStringContainsString('Example question 3', $html);
    }
}
