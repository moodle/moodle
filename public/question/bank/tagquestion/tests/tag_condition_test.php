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

namespace qbank_tagquestion;

use core\output\datafilter;
use core_question\local\bank\question_edit_contexts;
use context_module;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Unit tests for tag_condition class.
 *
 * @package    qbank_tagquestion
 * @copyright  2025 Catalyst IT Canada Pty Ltd
 * @author     Niko Hoogeveen <nikohoogeveen@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(\qbank_tagquestion\tag_condition::class)]
#[CoversMethod(\qbank_tagquestion\tag_condition::class, 'build_query_from_filter')]
#[CoversMethod(\qbank_tagquestion\tag_condition::class, 'get_condition_key')]
final class tag_condition_test extends \advanced_testcase {
    /**
     * Create test environment with questions and tags.
     *
     * @return array Contains 'questions', 'tags', and 'questioncategory'
     */
    private function create_test_data(): array {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create course and qbank.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $qbank = $generator->create_module('qbank', ['course' => $course->id]);
        $context = context_module::instance($qbank->cmid);

        // Set up question generator.
        $questiongenerator = $generator->get_plugin_generator('core_question');

        // Create question category.
        $questioncategory = $questiongenerator->create_question_category([
            'contextid' => $context->id,
            'name' => 'Test Category',
        ]);

        // Create tags.
        $tags = \core_tag_tag::create_if_missing(
            \core_tag_area::get_collection('core', 'question'),
            ['math', 'algebra', 'geometry', 'advanced'],
        );

        // Create questions with different tag combinations.
        $questions = [];

        // Question 1: tagged with 'math' only.
        $questions['q1'] = $questiongenerator->create_question('truefalse', null, [
            'category' => $questioncategory->id,
            'name' => 'Question 1 - Math only',
        ]);
        \core_tag_tag::set_item_tags(
            'core_question',
            'question',
            $questions['q1']->id,
            $context,
            ['math'],
        );

        // Question 2: tagged with 'math' and 'algebra'.
        $questions['q2'] = $questiongenerator->create_question('truefalse', null, [
            'category' => $questioncategory->id,
            'name' => 'Question 2 - Math and Algebra',
        ]);
        \core_tag_tag::set_item_tags(
            'core_question',
            'question',
            $questions['q2']->id,
            $context,
            ['math', 'algebra'],
        );

        // Question 3: tagged with 'math', 'algebra', and 'advanced'.
        $questions['q3'] = $questiongenerator->create_question('truefalse', null, [
            'category' => $questioncategory->id,
            'name' => 'Question 3 - Math, Algebra, Advanced',
        ]);
        \core_tag_tag::set_item_tags(
            'core_question',
            'question',
            $questions['q3']->id,
            $context,
            ['math', 'algebra', 'advanced'],
        );

        // Question 4: tagged with 'geometry' only.
        $questions['q4'] = $questiongenerator->create_question('truefalse', null, [
            'category' => $questioncategory->id,
            'name' => 'Question 4 - Geometry only',
        ]);
        \core_tag_tag::set_item_tags(
            'core_question',
            'question',
            $questions['q4']->id,
            $context,
            ['geometry'],
        );

        // Question 5: tagged with 'math' and 'geometry'.
        $questions['q5'] = $questiongenerator->create_question('truefalse', null, [
            'category' => $questioncategory->id,
            'name' => 'Question 5 - Math and Geometry',
        ]);
        \core_tag_tag::set_item_tags(
            'core_question',
            'question',
            $questions['q5']->id,
            $context,
            ['math', 'geometry'],
        );

        // Question 6: no tags.
        $questions['q6'] = $questiongenerator->create_question('truefalse', null, [
            'category' => $questioncategory->id,
            'name' => 'Question 6 - No tags',
        ]);

        return [
            'questions' => $questions,
            'tags' => $tags,
            'questioncategory' => $questioncategory,
        ];
    }

    /**
     * Helper method to get question IDs from filter results.
     *
     * @param array $filter Filter configuration
     * @param int $categoryid The question category ID
     * @return array Array of question IDs that match the filter
     */
    private function get_questions_from_filter(array $filter, int $categoryid): array {
        global $DB;

        [$where, $params] = tag_condition::build_query_from_filter($filter);

        $sql = "SELECT q.id
                FROM {question} q
                JOIN {question_versions} qv ON q.id = qv.questionid
                JOIN {question_bank_entries} qbe ON qv.questionbankentryid = qbe.id
                WHERE qbe.questioncategoryid = :categoryid";
        $params['categoryid'] = $categoryid;

        if (!empty($where)) {
            $sql .= " AND $where";
        }

        $results = $DB->get_records_sql($sql, $params);
        return array_keys($results);
    }

    /**
     * Data provider for tag filter tests.
     *
     * @return array Test cases with filter configuration and expected results
     */
    public static function filter_provider(): array {
        return [
            'no tags returns all questions' => [
                'filtertags' => [],
                'jointype' => datafilter::JOINTYPE_ALL,
                'expectedcount' => 6,
                'expectedmatches' => ['q1', 'q2', 'q3', 'q4', 'q5', 'q6'],
                'expectednomatches' => [],
            ],
            'single tag math with ANY jointype' => [
                'filtertags' => ['math'],
                'jointype' => datafilter::JOINTYPE_ANY,
                'expectedcount' => 4,
                'expectedmatches' => ['q1', 'q2', 'q3', 'q5'],
                'expectednomatches' => ['q4', 'q6'],
            ],
            'multiple tags math and geometry with ANY jointype' => [
                'filtertags' => ['math', 'geometry'],
                'jointype' => datafilter::JOINTYPE_ANY,
                'expectedcount' => 5,
                'expectedmatches' => ['q1', 'q2', 'q3', 'q4', 'q5'],
                'expectednomatches' => ['q6'],
            ],
            'multiple tags math and algebra with ALL jointype' => [
                'filtertags' => ['math', 'algebra'],
                'jointype' => datafilter::JOINTYPE_ALL,
                'expectedcount' => 2,
                'expectedmatches' => ['q2', 'q3'],
                'expectednomatches' => ['q1', 'q4', 'q5', 'q6'],
            ],
            'three tags math algebra advanced with ALL jointype' => [
                'filtertags' => ['math', 'algebra', 'advanced'],
                'jointype' => datafilter::JOINTYPE_ALL,
                'expectedcount' => 1,
                'expectedmatches' => ['q3'],
                'expectednomatches' => ['q1', 'q2', 'q4', 'q5', 'q6'],
            ],
            'single tag math with NONE jointype' => [
                'filtertags' => ['math'],
                'jointype' => datafilter::JOINTYPE_NONE,
                'expectedcount' => 2,
                'expectedmatches' => ['q4', 'q6'],
                'expectednomatches' => ['q1', 'q2', 'q3', 'q5'],
            ],
            'multiple tags math and geometry with NONE jointype' => [
                'filtertags' => ['math', 'geometry'],
                'jointype' => datafilter::JOINTYPE_NONE,
                'expectedcount' => 1,
                'expectedmatches' => ['q6'],
                'expectednomatches' => ['q1', 'q2', 'q3', 'q4', 'q5'],
            ],
        ];
    }

    /**
     * Test tag filtering with various configurations.
     *
     * @param array $filtertags Tag names to filter by
     * @param int $jointype The join type for the filter
     * @param int $expectedcount Expected number of matching questions
     * @param array $expectedmatches Question keys that should match
     * @param array $expectednomatches Question keys that should not match
     */
    #[DataProvider('filter_provider')]
    public function test_filter(
        array $filtertags,
        int $jointype,
        int $expectedcount,
        array $expectedmatches,
        array $expectednomatches,
    ): void {
        $testdata = $this->create_test_data();
        $questions = $testdata['questions'];
        $tags = $testdata['tags'];
        $questioncategory = $testdata['questioncategory'];

        // Convert tag names to tag IDs.
        $tagids = [];
        foreach ($filtertags as $tagname) {
            $tagids[] = $tags[$tagname]->id;
        }

        $filter = [
            'values' => $tagids,
            'jointype' => $jointype,
        ];

        $questionids = $this->get_questions_from_filter($filter, $questioncategory->id);

        $this->assertCount($expectedcount, $questionids);

        foreach ($expectedmatches as $key) {
            $this->assertContains(
                $questions[$key]->id,
                $questionids,
                "Question $key should be in the results",
            );
        }

        foreach ($expectednomatches as $key) {
            $this->assertNotContains(
                $questions[$key]->id,
                $questionids,
                "Question $key should not be in the results",
            );
        }
    }

    /**
     * Test filtering with non-existent tag ID.
     */
    public function test_filter_nonexistent_tag(): void {
        $testdata = $this->create_test_data();

        $filter = [
            'values' => [99999],
            'jointype' => datafilter::JOINTYPE_ANY,
        ];

        $questionids = $this->get_questions_from_filter($filter, $testdata['questioncategory']->id);

        // Should return no questions.
        $this->assertCount(0, $questionids);
    }

    /**
     * Test get_condition_key returns expected value.
     */
    public function test_get_condition_key(): void {
        $this->assertSame('qtagids', tag_condition::get_condition_key());
    }

    /**
     * Test the JOINTYPE_DEFAULT constant.
     */
    public function test_jointype_default_constant(): void {
        $this->assertSame(datafilter::JOINTYPE_ALL, tag_condition::JOINTYPE_DEFAULT);
    }

    /**
     * Test filtering with default jointype (should be ALL).
     */
    public function test_filter_default_jointype(): void {
        $testdata = $this->create_test_data();
        $questions = $testdata['questions'];
        $tags = $testdata['tags'];

        $filter = [
            'values' => [$tags['math']->id, $tags['algebra']->id],
        ];

        $questionids = $this->get_questions_from_filter($filter, $testdata['questioncategory']->id);

        // Should behave like ALL jointype: return questions with both tags.
        $this->assertCount(2, $questionids);
        $this->assertContains($questions['q2']->id, $questionids);
        $this->assertContains($questions['q3']->id, $questionids);
    }

    /**
     * Test build_query_from_filter returns empty when no tags provided.
     */
    public function test_build_query_empty_filter(): void {
        $filter = [
            'values' => [],
            'jointype' => datafilter::JOINTYPE_ALL,
        ];

        [$where, $params] = tag_condition::build_query_from_filter($filter);

        $this->assertSame('', $where);
        $this->assertSame([], $params);
    }
}
