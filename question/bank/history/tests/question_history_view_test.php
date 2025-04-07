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

namespace qbank_history;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/editlib.php');

/**
 * Custom history view - qbank api test.
 *
 * @package    qbank_history
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \qbank_history\question_history_view
 */
final class question_history_view_test extends \advanced_testcase {

    /**
     * Test that the history page shows all the versions of a question.
     *
     * @covers ::display
     */
    public function test_question_history_shows_all_versions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a course.
        $course = $generator->create_course();
        $qbank = $generator->create_module('qbank', ['course' => $course->id]);
        $cm = get_coursemodule_from_id('qbank', $qbank->cmid);
        $context = \context_module::instance($qbank->cmid);

        // Create a question in the default category.
        $contexts = new \core_question\local\bank\question_edit_contexts($context);
        $cat = $questiongenerator->create_question_category(['contextid' => $context->id]);
        $questiondata1 = $questiongenerator->create_question('numerical', null,
            ['name' => 'Example question', 'category' => $cat->id]);

        // Create a new version.
        $questiondata2 = $questiongenerator->update_question($questiondata1, null,
            ['name' => 'Example question second version']);

        $entry = get_question_bank_entry($questiondata1->id);

        $pagevars = [
            'qpage' => 0,
            'qperpage' => DEFAULT_QUESTIONS_PER_PAGE,
            'cat' => $cat->id . ',' . $cat->contextid,
            'tabname' => 'questions'
        ];
        // Generate the view.
        $viewclass = \qbank_history\question_history_view::class;
        $extraparams = [
            'view' => $viewclass,
            'entryid' => $entry->id,
            'returnurl' => "/",
        ];
        $view = new $viewclass($contexts, new \moodle_url('/'), $course, $cm, $pagevars, $extraparams);
        ob_start();
        $view->display();
        $html = ob_get_clean();

        // Verify the output includes the first version.
        $this->assertStringContainsString($questiondata1->name, $html);

        // Verify the output includes the second version.
        $this->assertStringContainsString($questiondata2->name, $html);
    }

    /**
     * Test that the question bank header in the history page shows the latest question.
     *
     * @covers ::display_question_bank_header
     */
    public function test_display_question_bank_header(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a course.
        $course = $generator->create_course();
        $qbank = $generator->create_module('qbank', ['course' => $course->id]);
        $cm = get_coursemodule_from_id('qbank', $qbank->cmid);
        $context = \context_module::instance($cm->id);

        // Create a question in the default category.
        $contexts = new \core_question\local\bank\question_edit_contexts($context);
        $cat = $questiongenerator->create_question_category();
        $questiondata1 = $questiongenerator->create_question('numerical', null,
            ['name' => 'First version', 'category' => $cat->id]);

        $entry = get_question_bank_entry($questiondata1->id);
        $pagevars = [
            'qpage' => 0,
            'qperpage' => DEFAULT_QUESTIONS_PER_PAGE,
            'cat' => $cat->id . ',' . $cat->contextid,
            'tabname' => 'questions'
        ];
        // Generate the view.
        $viewclass = \qbank_history\question_history_view::class;
        $extraparams = [
            'view' => $viewclass,
            'entryid' => $entry->id,
            'returnurl' => "/",
        ];
        $view = new $viewclass($contexts, new \moodle_url('/'), $course, $cm, $pagevars, $extraparams);
        ob_start();
        $view->display_question_bank_header();
        $headerhtml = ob_get_clean();
        // Verify the output includes the latest version.
        $this->assertStringContainsString($questiondata1->name, $headerhtml);

        $questiondata2 = $questiongenerator->update_question($questiondata1, null,
            ['name' => 'Second version']);
        $view = new $viewclass($contexts, new \moodle_url('/'), $course, $cm, $pagevars, $extraparams);
        ob_start();
        $view->display_question_bank_header();
        $headerhtml = ob_get_clean();

        $this->assertStringContainsString($questiondata2->name, $headerhtml);
    }
}
