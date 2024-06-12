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
     * Count how many questions in the list belong to the given category.
     *
     * @param string $categoryid a category id
     * @param array $questionids list of question ids
     * @return int
     */
    private function count_category_questions(string $categoryid, array $questionids): int {
        global $DB;
        $this->assertNotEmpty($questionids);
        list($insql, $inparams) = $DB->get_in_or_equal($questionids, SQL_PARAMS_NAMED);
        $sql = "SELECT COUNT(q.id)
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                  JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
                 WHERE qc.id = :categoryid
                   AND q.id $insql";

        return $DB->count_records_sql($sql, array_merge(['categoryid' => $categoryid], $inparams));
    }

    /**
     * Assert that the given category contains following questions
     *
     * @param string $categoryid a category id
     * @param array $questionids list of question ids
     * @return void
     */
    protected function assert_category_contains_questions(string $categoryid, array $questionids) {
        // The category need to contain all the questions.
        $this->assertEquals(count($questionids), $this->count_category_questions($categoryid, $questionids));
    }

    /**
     * Assert that the given category does not contain following questions
     *
     * @param string $categoryid a category id
     * @param array $questionids list of question ids
     * @return void
     */
    protected function assert_category_does_not_contain_questions(string $categoryid, array $questionids) {
        // The category does not contain any question.
        $this->assertEquals(0, $this->count_category_questions($categoryid, $questionids));
    }

    /**
     * Test bulk move of questions.
     *
     * @covers ::bulk_move_questions
     */
    public function test_bulk_move_questions(): void {
        global $DB;
        $this->helper_setup();

        // Get the processed question ids.
        $questionlist = $this->process_question_ids_test();
        $questionids = array_map('intval', explode(',', $questionlist));

        // Verify that the questions are available in the current view.
        $this->assert_category_contains_questions($this->cat->id, $questionids);
        helper::bulk_move_questions($questionlist, $this->secondcategory);

        // Verify the questions are not in the current category.
        $this->assert_category_does_not_contain_questions($this->cat->id, $questionids);

        // Verify the questions are in the new category.
        $this->assert_category_contains_questions($this->secondcategory->id, $questionids);
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
    public function test_get_displaydata(): void {
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
