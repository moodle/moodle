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

namespace qbank_bulkmove;

use core_question\local\bank\question_edit_contexts;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/editlib.php');

/**
 * Bulk move helper tests.
 *
 * @package    qbank_bulkmove
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \qbank_bulkmove\helper
 */
class helper_test extends \advanced_testcase {

    /**
     * @var false|object|\stdClass|null $cat
     */
    protected $cat;

    /**
     * @var \stdClass $questiondata1
     */
    protected $questiondata1;

    /**
     * @var \stdClass $questiondata2
     */
    protected $questiondata2;

    /**
     * @var bool|\context|\context_course $context
     */
    protected $context;

    /**
     * @var \core_question\local\bank\question_edit_contexts $contexts
     */
    protected $contexts;

    /**
     * @var \stdClass $course
     */
    protected $course;

    /**
     * @var array $rawdata
     */
    protected $rawdata;

    /**
     * @var object $secondcategory
     */
    protected $secondcategory;

    /**
     * Setup the test.
     */
    protected function helper_setup(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $generator->get_plugin_generator('core_question');

        // Create a course.
        $this->course = $generator->create_course();
        $this->context = \context_course::instance($this->course->id);

        // Create a question in the default category.
        $this->contexts = new question_edit_contexts($this->context);
        $this->cat = question_make_default_categories($this->contexts->all());
        $this->questiondata1 = $questiongenerator->create_question('numerical', null,
            ['name' => 'Example question', 'category' => $this->cat->id]);

        // Create a second category to move questions.
        $this->secondcategory = $questiongenerator->create_question_category(['contextid' => $this->context->id,
            'parent' => $this->cat->id]);

        // Ensure the question is not in the cache.
        $cache = \cache::make('core', 'questiondata');
        $cache->delete($this->questiondata1->id);

        $this->questiondata2 = $questiongenerator->create_question('numerical', null,
            ['name' => 'Example question second', 'category' => $this->cat->id]);

        // Ensure the question is not in the cache.
        $cache = \cache::make('core', 'questiondata');
        $cache->delete($this->questiondata2->id);

        // Posted raw data.
        $this->rawdata = [
            'courseid' => $this->course->id,
            'cat' => "{$this->cat->id},{$this->context->id}",
            'qpage' => '0',
            "q{$this->questiondata1->id}" => '1',
            "q{$this->questiondata2->id}" => '1',
            'move' => 'Move to'
        ];
    }

    /**
     * Test bulk move of questions.
     *
     * @covers ::bulk_move_questions
     */
    public function test_bulk_move_questions() {
        $this->helper_setup();
        // Verify that the questions are available in the current view.
        $view = new \core_question\local\bank\view($this->contexts, new \moodle_url('/'), $this->course);
        ob_start();
        $pagevars = [
            'qpage' => 0,
            'qperpage' => 20,
            'cat' => $this->cat->id . ',' . $this->context->id,
            'recurse' => false,
            'showhidden' => false,
            'qbshowtext' => false
        ];
        $view->display($pagevars, 'editq');
        $html = ob_get_clean();
        $this->assertStringContainsString('Example question', $html);
        $this->assertStringContainsString('Example question second', $html);

        // Get the processed question ids.
        $questionlist = $this->process_question_ids_test();

        helper::bulk_move_questions($questionlist, $this->secondcategory);

        // Verify the questions are not in the current category.
        $view = new \core_question\local\bank\view($this->contexts, new \moodle_url('/'), $this->course);
        ob_start();
        $pagevars = [
            'qpage' => 0,
            'qperpage' => 20,
            'cat' => $this->cat->id . ',' . $this->context->id,
            'recurse' => false,
            'showhidden' => false,
            'qbshowtext' => false
        ];
        $view->display($pagevars, 'editq');
        $html = ob_get_clean();
        $this->assertStringNotContainsString('Example question', $html);
        $this->assertStringNotContainsString('Example question second', $html);

        // Verify the questions are in the new category.
        $view = new \core_question\local\bank\view($this->contexts, new \moodle_url('/'), $this->course);
        ob_start();
        $pagevars = [
            'qpage' => 0,
            'qperpage' => 20,
            'cat' => $this->secondcategory->id . ',' . $this->context->id,
            'category' => $this->secondcategory->id . ',' . $this->context->id,
            'recurse' => false,
            'showhidden' => false,
            'qbshowtext' => false
        ];
        $view->display($pagevars, 'editq');
        $html = ob_get_clean();
        $this->assertStringContainsString('Example question', $html);
        $this->assertStringContainsString('Example question second', $html);
    }

    /**
     * Test the question processing and return the question list.
     *
     * @return mixed
     * @covers ::process_question_ids
     */
    protected function process_question_ids_test() {
        // Test the raw data processing.
        list($questionids, $questionlist) = helper::process_question_ids($this->rawdata);
        $this->assertEquals([$this->questiondata1->id, $this->questiondata2->id], $questionids);
        $this->assertEquals("{$this->questiondata1->id},{$this->questiondata2->id}", $questionlist);
        return $questionlist;
    }

    /**
     * Test the question displaydata.
     *
     * @covers ::get_displaydata
     */
    public function test_get_displaydata() {
        $this->helper_setup();
        $coursecontext = \context_course::instance($this->course->id);
        $contexts = new question_edit_contexts($coursecontext);
        $addcontexts = $contexts->having_cap('moodle/question:add');
        $url = new \moodle_url('/question/bank/bulkmove/move.php');
        $displaydata = \qbank_bulkmove\helper::get_displaydata($addcontexts, $url, $url);
        $this->assertStringContainsString('Test question category 1', $displaydata['categorydropdown']);
        $this->assertStringContainsString('Default for Category 1', $displaydata['categorydropdown']);
        $this->assertEquals($url, $displaydata ['moveurl']);
        $this->assertEquals($url, $displaydata ['returnurl']);
    }
}
