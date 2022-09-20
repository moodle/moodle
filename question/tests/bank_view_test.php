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
use core_question\local\bank\view;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/editlib.php');

/**
 * Unit tests for the question bank view class.
 *
 * @package    core_question
 * @category   test
 * @copyright  2018 the Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bank_view_test extends \advanced_testcase {

    public function test_viewing_question_bank_should_not_load_individual_questions() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $generator->get_plugin_generator('core_question');

        // Create a course.
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);

        // Create a question in the default category.
        $contexts = new question_edit_contexts($context);
        $cat = question_make_default_categories($contexts->all());
        $questiondata = $questiongenerator->create_question('numerical', null,
                ['name' => 'Example question', 'category' => $cat->id]);

        // Ensure the question is not in the cache.
        $cache = \cache::make('core', 'questiondata');
        $cache->delete($questiondata->id);

        // Generate the view.
        $view = new view($contexts, new \moodle_url('/'), $course);
        ob_start();
        $pagevars = [
            'qpage' => 0,
            'qperpage' => DEFAULT_QUESTIONS_PER_PAGE,
            'cat' => $cat->id . ',' . $context->id,
            'recurse' => false,
            'showhidden' => false,
            'qbshowtext' => false

        ];
        $view->display($pagevars, 'editq');
        $html = ob_get_clean();

        // Verify the output includes the expected question.
        $this->assertStringContainsString('Example question', $html);

        // Verify the question has not been loaded into the cache.
        $this->assertFalse($cache->has($questiondata->id));
    }

    public function test_unknown_qtype_does_not_break_view() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $generator->get_plugin_generator('core_question');

        // Create a course.
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);

        // Create a question in the default category.
        $contexts = new question_edit_contexts($context);
        $cat = question_make_default_categories($contexts->all());
        $questiondata = $questiongenerator->create_question('numerical', null,
                ['name' => 'Example question', 'category' => $cat->id]);
        $DB->set_field('question', 'qtype', 'unknownqtype', ['id' => $questiondata->id]);

        // Generate the view.
        $view = new view($contexts, new \moodle_url('/'), $course);
        ob_start();
        $pagevars = [
            'qpage' => 0,
            'qperpage' => DEFAULT_QUESTIONS_PER_PAGE,
            'cat' => $cat->id . ',' . $context->id,
            'recurse' => false,
            'showhidden' => false,
            'qbshowtext' => false

        ];
        $view->display($pagevars, 'editq');
        $html = ob_get_clean();

        // Mainly we are verifying that there was no fatal error.

        // Verify the output includes the expected question.
        $this->assertStringContainsString('Example question', $html);
    }
}
