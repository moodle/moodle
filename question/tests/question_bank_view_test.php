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

namespace core_question;

use core_question\local\bank\question_edit_contexts;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/editlib.php');

/**
 * Unit tests for the question own question bank view class.
 *
 * @package    core_question
 * @copyright  2024 the Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_question\local\bank\view
 */
final class question_bank_view_test extends \advanced_testcase {

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
            'qbshowtext' => false,
            'tabname' => 'editq',
        ];
        $extraparams = ['cmid' => $cm->id];
        $view = new \core_question\local\bank\view($contexts, new \moodle_url('/'), $course, $cm, $params, $extraparams);
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

        // Use show hidden question filter.
        $params['filter'] = [
            'category' => [
                'name' => 'category',
                'jointype' => 1,
                'values' => [$cat->id],
                'filteroptions' => [],
            ],
            'hidden' => [
                'name' => 'hidden',
                'jointype' => 1,
                'values' => [1],
                'filteroptions' => [],
            ],
        ];
        $view = new \core_question\local\bank\view($contexts, new \moodle_url('/'), $course, $cm, $params, $extraparams);
        ob_start();
        $view->display();
        $html = ob_get_clean();
        $this->assertStringContainsString('This is the latest version', $html);
        $this->assertStringNotContainsString('Example question', $html);
    }
}
