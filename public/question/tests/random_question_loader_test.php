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

use core_question\local\bank\condition;
use core_question\local\bank\question_version_status;
use core_question\local\bank\random_question_loader;
use core_question_generator;
use mod_quiz\quiz_settings;
use qubaid_list;
use question_bank;
use question_engine;
use question_filter_test_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/mod/quiz/tests/quiz_question_helper_test_trait.php');

/**
 * Tests for the {@see \core_question\local\bank\random_question_loader} class.
 *
 * @package   core_question
 * @copyright 2015 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \random_question_loader
 */
final class random_question_loader_test extends \advanced_testcase {

    use \quiz_question_helper_test_trait;

    public function test_empty_category_gives_null(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $loader = new random_question_loader(new qubaid_list([]));

        $filters = question_filter_test_helper::create_filters([$cat->id]);
        $this->assertNull($loader->get_next_filtered_question_id($filters));

        $filters = question_filter_test_helper::create_filters([$cat->id], 1);
        $this->assertNull($loader->get_next_filtered_question_id($filters));
    }

    public function test_unknown_category_behaves_like_empty(): void {
        // It is up the caller to make sure the category id is valid.
        $loader = new random_question_loader(new qubaid_list([]));
        $filters = question_filter_test_helper::create_filters([-1], 1);
        $this->assertNull($loader->get_next_filtered_question_id($filters));
    }

    public function test_descriptions_not_returned(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $info = $generator->create_question('description', null, ['category' => $cat->id]);
        $loader = new random_question_loader(new qubaid_list([]));

        $filters = question_filter_test_helper::create_filters([$cat->id]);
        $this->assertNull($loader->get_next_filtered_question_id($filters));
    }

    public function test_hidden_questions_not_returned(): void {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $question1 = $generator->create_question('shortanswer', null, ['category' => $cat->id]);
        $DB->set_field('question_versions', 'status',
                \core_question\local\bank\question_version_status::QUESTION_STATUS_HIDDEN, ['questionid' => $question1->id]);
        $loader = new random_question_loader(new qubaid_list([]));

        $filters = question_filter_test_helper::create_filters([$cat->id]);
        $this->assertNull($loader->get_next_filtered_question_id($filters));
    }

    public function test_cloze_subquestions_not_returned(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $question1 = $generator->create_question('multianswer', null, ['category' => $cat->id]);
        $loader = new random_question_loader(new qubaid_list([]));

        $filters = question_filter_test_helper::create_filters([$cat->id]);
        $this->assertEquals($question1->id, $loader->get_next_filtered_question_id($filters));
        $this->assertNull($loader->get_next_filtered_question_id($filters));
    }

    public function test_random_questions_not_returned(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course]);
        $this->add_random_questions($quiz->id, 1, $cat->id, 1);
        $loader = new random_question_loader(new qubaid_list([]));

        $filters = question_filter_test_helper::create_filters([$cat->id]);
        $this->assertNull($loader->get_next_filtered_question_id($filters));
    }

    public function test_draft_questions_not_returned(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a question in draft state.
        $category = $questiongenerator->create_question_category();
        $questiongenerator->create_question('shortanswer', null,
                ['category' => $category->id, 'status' => question_version_status::QUESTION_STATUS_DRAFT]);

        // Try to a random question from that category - should not be one.
        $filtercondition = [
            'category' => [
                'jointype' => condition::JOINTYPE_DEFAULT,
                'values' => [$category->id],
                'filteroptions' => ['includesubcategories' => false],
            ],
        ];
        $loader = new random_question_loader(new qubaid_list([]));
        $this->assertNull($loader->get_next_filtered_question_id($filtercondition));
    }

    public function test_questions_with_later_draft_version_is_returned(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a question in draft state.
        $category = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('shortanswer', null,
                ['questiontext' => 'V1', 'category' => $category->id]);
        $questiongenerator->update_question($question, null,
                ['questiontext' => 'V2', 'status' => question_version_status::QUESTION_STATUS_DRAFT]);

        // Try to a random question from that category - should get V1.
        $filtercondition = [
            'category' => [
                'jointype' => condition::JOINTYPE_DEFAULT,
                'values' => [$category->id],
                'filteroptions' => ['includesubcategories' => false],
            ],
        ];
        $loader = new random_question_loader(new qubaid_list([]));
        $this->assertEquals($question->id, $loader->get_next_filtered_question_id($filtercondition));
    }

    public function test_one_question_category_returns_that_q_then_null(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $question1 = $generator->create_question('shortanswer', null, ['category' => $cat->id]);
        $loader = new random_question_loader(new qubaid_list([]));

        $filters = question_filter_test_helper::create_filters([$cat->id], 1);
        $this->assertEquals($question1->id, $loader->get_next_filtered_question_id($filters));

        $filters = question_filter_test_helper::create_filters([$cat->id]);
        $this->assertNull($loader->get_next_filtered_question_id($filters));
    }

    public function test_two_question_category_returns_both_then_null(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $question1 = $generator->create_question('shortanswer', null, ['category' => $cat->id]);
        $question2 = $generator->create_question('shortanswer', null, ['category' => $cat->id]);
        $loader = new random_question_loader(new qubaid_list([]));

        $questionids = [];
        $filters = question_filter_test_helper::create_filters([$cat->id]);
        $questionids[] = $loader->get_next_filtered_question_id($filters);
        $questionids[] = $loader->get_next_filtered_question_id($filters);
        sort($questionids);
        $this->assertEquals([$question1->id, $question2->id], $questionids);

        $filters = question_filter_test_helper::create_filters([$cat->id], 1);
        $this->assertNull($loader->get_next_filtered_question_id($filters));
    }

    public function test_nested_categories(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat1 = $generator->create_question_category();
        $cat2 = $generator->create_question_category(['parent' => $cat1->id]);
        $question1 = $generator->create_question('shortanswer', null, ['category' => $cat1->id]);
        $question2 = $generator->create_question('shortanswer', null, ['category' => $cat2->id]);
        $loader = new random_question_loader(new qubaid_list([]));

        $filters = question_filter_test_helper::create_filters([$cat2->id], 1);
        $this->assertEquals($question2->id, $loader->get_next_filtered_question_id($filters));
        $filters = question_filter_test_helper::create_filters([$cat1->id], 1);
        $this->assertEquals($question1->id, $loader->get_next_filtered_question_id($filters));

        $filters = question_filter_test_helper::create_filters([$cat1->id]);
        $this->assertNull($loader->get_next_filtered_question_id($filters));
    }

    public function test_used_question_not_returned_until_later(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $question1 = $generator->create_question('shortanswer', null, ['category' => $cat->id]);
        $question2 = $generator->create_question('shortanswer', null, ['category' => $cat->id]);
        $loader = new random_question_loader(new qubaid_list([]),
                [$question2->id => 2]);

        $filters = question_filter_test_helper::create_filters([$cat->id]);
        $this->assertEquals($question1->id, $loader->get_next_filtered_question_id($filters));
        $this->assertNull($loader->get_next_filtered_question_id($filters));
    }

    public function test_previously_used_question_not_returned_until_later(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $question1 = $generator->create_question('shortanswer', null, ['category' => $cat->id]);
        $question2 = $generator->create_question('shortanswer', null, ['category' => $cat->id]);
        $quba = question_engine::make_questions_usage_by_activity('test', \context_system::instance());
        $quba->set_preferred_behaviour('deferredfeedback');
        $question = question_bank::load_question($question2->id);
        $quba->add_question($question);
        $quba->add_question($question);
        $quba->start_all_questions();
        question_engine::save_questions_usage_by_activity($quba);

        $loader = new random_question_loader(new qubaid_list([$quba->get_id()]));

        $filters = question_filter_test_helper::create_filters([$cat->id]);
        $this->assertEquals($question1->id, $loader->get_next_filtered_question_id($filters));
        $this->assertEquals($question2->id, $loader->get_next_filtered_question_id($filters));
        $this->assertNull($loader->get_next_filtered_question_id($filters));
    }

    public function test_empty_category_does_not_have_question_available(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $loader = new random_question_loader(new qubaid_list([]));

        $filters = question_filter_test_helper::create_filters([$cat->id]);
        $this->assertFalse($loader->is_filtered_question_available($filters, 1));
        $filters = question_filter_test_helper::create_filters([$cat->id], 1);
        $this->assertFalse($loader->is_filtered_question_available($filters, 1));
    }

    public function test_descriptions_not_available(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $info = $generator->create_question('description', null, ['category' => $cat->id]);
        $loader = new random_question_loader(new qubaid_list([]));

        $filters = question_filter_test_helper::create_filters([$cat->id]);
        $this->assertFalse($loader->is_filtered_question_available($filters, $info->id));
        $filters = question_filter_test_helper::create_filters([$cat->id], 1);
        $this->assertFalse($loader->is_filtered_question_available($filters, $info->id));
    }

    public function test_existing_question_is_available_but_then_marked_used(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $question1 = $generator->create_question('shortanswer', null, ['category' => $cat->id]);
        $loader = new random_question_loader(new qubaid_list([]));

        $filters = question_filter_test_helper::create_filters([$cat->id]);
        $this->assertTrue($loader->is_filtered_question_available($filters, $question1->id));
        $this->assertFalse($loader->is_filtered_question_available($filters, $question1->id));

        $this->assertFalse($loader->is_filtered_question_available($filters, -1));
    }

    /**
     * Data provider for the get_questions test.
     *
     * @return array testcases.
     */
    public static function get_questions_test_cases(): array {
        return [
                'empty category' => [
                        'categoryindex' => 'emptycat',
                        'includesubcategories' => false,
                        'usetagnames' => [],
                        'expectedquestionindexes' => []
                ],
                'single category' => [
                        'categoryindex' => 'cat1',
                        'includesubcategories' => false,
                        'usetagnames' => [],
                        'expectedquestionindexes' => ['cat1q1', 'cat1q2']
                ],
                'include sub category' => [
                        'categoryindex' => 'cat1',
                        'includesubcategories' => true,
                        'usetagnames' => [],
                        'expectedquestionindexes' => ['cat1q1', 'cat1q2', 'subcatq1', 'subcatq2']
                ],
                'single category with tags' => [
                        'categoryindex' => 'cat1',
                        'includesubcategories' => false,
                        'usetagnames' => ['cat1'],
                        'expectedquestionindexes' => ['cat1q1']
                ],
                'include sub category with tag on parent' => [
                        'categoryindex' => 'cat1',
                        'includesubcategories' => true,
                        'usetagnames' => ['cat1'],
                        'expectedquestionindexes' => ['cat1q1']
                ],
                'include sub category with tag on sub' => [
                        'categoryindex' => 'cat1',
                        'includesubcategories' => true,
                        'usetagnames' => ['subcat'],
                        'expectedquestionindexes' => ['subcatq1']
                ],
                'include sub category with same tag on parent and sub' => [
                        'categoryindex' => 'cat1',
                        'includesubcategories' => true,
                        'usetagnames' => ['foo'],
                        'expectedquestionindexes' => ['cat1q1', 'subcatq1']
                ],
                'include sub category with tag not matching' => [
                        'categoryindex' => 'cat1',
                        'includesubcategories' => true,
                        'usetagnames' => ['cat1', 'cat2'],
                        'expectedquestionindexes' => []
                ]
        ];
    }

    /**
     * Test the get_questions function with various parameter combinations.
     *
     * This function creates a data set as follows:
     *      Category: cat1
     *          Question: cat1q1
     *              Tags: 'cat1', 'foo'
     *          Question: cat1q2
     *      Category: cat2
     *          Question: cat2q1
     *              Tags: 'cat2', 'foo'
     *          Question: cat2q2
     *      Category: subcat
     *          Question: subcatq1
     *              Tags: 'subcat', 'foo'
     *          Question: subcatq2
     *          Parent: cat1
     *      Category: emptycat
     *
     * @dataProvider get_questions_test_cases
     * @param string $categoryindex The named index for the category to use
     * @param bool $includesubcategories If the search should include subcategories
     * @param string[] $usetagnames The tag names to include in the search
     * @param string[] $expectedquestionindexes The questions expected in the result
     */
    public function test_get_questions_variations(
            $categoryindex,
            $includesubcategories,
            $usetagnames,
            $expectedquestionindexes
    ): void {
        $this->resetAfterTest();

        $categories = [];
        $questions = [];
        $tagnames = [
                'cat1',
                'cat2',
                'subcat',
                'foo'
        ];
        $collid = \core_tag_collection::get_default();
        $tags = \core_tag_tag::create_if_missing($collid, $tagnames);
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // First category and questions.
        [$category, $categoryquestions] = $this->create_category_and_questions(2, ['cat1', 'foo']);
        $categories['cat1'] = $category;
        $questions['cat1q1'] = $categoryquestions[0];
        $questions['cat1q2'] = $categoryquestions[1];
        // Second category and questions.
        [$category, $categoryquestions] = $this->create_category_and_questions(2, ['cat2', 'foo']);
        $categories['cat2'] = $category;
        $questions['cat2q1'] = $categoryquestions[0];
        $questions['cat2q2'] = $categoryquestions[1];
        // Sub category and questions.
        [$category, $categoryquestions] = $this->create_category_and_questions(2, ['subcat', 'foo'], $categories['cat1']);
        $categories['subcat'] = $category;
        $questions['subcatq1'] = $categoryquestions[0];
        $questions['subcatq2'] = $categoryquestions[1];
        // Empty category.
        [$category, $categoryquestions] = $this->create_category_and_questions(0);
        $categories['emptycat'] = $category;

        // Generate the arguments for the get_questions function.
        $category = $categories[$categoryindex];
        $tagids = array_map(function($tagname) use ($tags) {
            return $tags[$tagname]->id;
        }, $usetagnames);

        $loader = new random_question_loader(new qubaid_list([]));
        $filters = question_filter_test_helper::create_filters([$category->id], $includesubcategories, $tagids);
        $result = $loader->get_filtered_questions($filters);
        // Generate the expected question set.
        $expectedquestions = array_map(function($index) use ($questions) {
            return $questions[$index];
        }, $expectedquestionindexes);

        // Ensure the result matches what was expected.
        $this->assertCount(count($expectedquestions), $result);
        foreach ($expectedquestions as $question) {
            $this->assertEquals($result[$question->id]->id, $question->id);
            $this->assertEquals($result[$question->id]->category, $question->category);
        }
    }

    /**
     * get_questions should allow limiting and offsetting of the result set.
     */
    public function test_get_questions_with_limit_and_offset(): void {
        $this->resetAfterTest();
        $numberofquestions = 5;
        $includesubcategories = false;
        $tagids = [];
        $limit = 1;
        $offset = 0;
        $loader = new random_question_loader(new qubaid_list([]));
        [$category, $questions] = $this->create_category_and_questions($numberofquestions);

        // Add questionid as key to find them easily later.
        $questionsbyid = [];
        array_walk($questions, function (&$value) use (&$questionsbyid) {
            $questionsbyid[$value->id] = $value;
        });
        $filters = question_filter_test_helper::create_filters([$category->id], $includesubcategories, $tagids);
        for ($i = 0; $i < $numberofquestions; $i++) {
            $result = $loader->get_filtered_questions(
                    $filters,
                    $limit,
                    $offset
            );

            $this->assertCount($limit, $result);
            $actual = array_shift($result);
            $expected = $questionsbyid[$actual->id];
            $this->assertEquals($expected->id, $actual->id);
            $offset++;
        }
    }

    /**
     * get_questions should allow retrieving questions with only a subset of
     * fields populated.
     */
    public function test_get_questions_with_restricted_fields(): void {
        $this->resetAfterTest();
        $includesubcategories = false;
        $tagids = [];
        $limit = 10;
        $offset = 0;
        $fields = ['id', 'name'];
        $loader = new random_question_loader(new qubaid_list([]));
        [$category, $questions] = $this->create_category_and_questions(1);

        $filters = question_filter_test_helper::create_filters([$category->id], $includesubcategories, $tagids);
        $result = $loader->get_filtered_questions(
                $filters,
                $limit,
                $offset,
                $fields
        );

        $expectedquestion = array_shift($questions);
        $actualquestion = array_shift($result);
        $actualfields = get_object_vars($actualquestion);
        $actualfields = array_keys($actualfields);
        sort($actualfields);
        sort($fields);

        $this->assertEquals($fields, $actualfields);
    }

    /**
     * Data provider for the count_questions test.
     *
     * @return array testcases.
     */
    public static function count_questions_test_cases(): array {
        return [
                'empty category' => [
                        'categoryindex' => 'emptycat',
                        'includesubcategories' => false,
                        'usetagnames' => [],
                        'expectedcount' => 0
                ],
                'single category' => [
                        'categoryindex' => 'cat1',
                        'includesubcategories' => false,
                        'usetagnames' => [],
                        'expectedcount' => 2
                ],
                'include sub category' => [
                        'categoryindex' => 'cat1',
                        'includesubcategories' => true,
                        'usetagnames' => [],
                        'expectedcount' => 4
                ],
                'single category with tags' => [
                        'categoryindex' => 'cat1',
                        'includesubcategories' => false,
                        'usetagnames' => ['cat1'],
                        'expectedcount' => 1
                ],
                'include sub category with tag on parent' => [
                        'categoryindex' => 'cat1',
                        'includesubcategories' => true,
                        'usetagnames' => ['cat1'],
                        'expectedcount' => 1
                ],
                'include sub category with tag on sub' => [
                        'categoryindex' => 'cat1',
                        'includesubcategories' => true,
                        'usetagnames' => ['subcat'],
                        'expectedcount' => 1
                ],
                'include sub category with same tag on parent and sub' => [
                        'categoryindex' => 'cat1',
                        'includesubcategories' => true,
                        'usetagnames' => ['foo'],
                        'expectedcount' => 2
                ],
                'include sub category with tag not matching' => [
                        'categoryindex' => 'cat1',
                        'includesubcategories' => true,
                        'usetagnames' => ['cat1', 'cat2'],
                        'expectedcount' => 0
                ]
        ];
    }

    /**
     * Test the count_questions function with various parameter combinations.
     *
     * This function creates a data set as follows:
     *      Category: cat1
     *          Question: cat1q1
     *              Tags: 'cat1', 'foo'
     *          Question: cat1q2
     *      Category: cat2
     *          Question: cat2q1
     *              Tags: 'cat2', 'foo'
     *          Question: cat2q2
     *      Category: subcat
     *          Question: subcatq1
     *              Tags: 'subcat', 'foo'
     *          Question: subcatq2
     *          Parent: cat1
     *      Category: emptycat
     *
     * @dataProvider count_questions_test_cases
     * @param string $categoryindex The named index for the category to use
     * @param bool $includesubcategories If the search should include subcategories
     * @param string[] $usetagnames The tag names to include in the search
     * @param int $expectedcount The number of questions expected in the result
     */
    public function test_count_questions_variations(
            $categoryindex,
            $includesubcategories,
            $usetagnames,
            $expectedcount
    ): void {
        $this->resetAfterTest();

        $categories = [];
        $questions = [];
        $tagnames = [
                'cat1',
                'cat2',
                'subcat',
                'foo'
        ];
        $collid = \core_tag_collection::get_default();
        $tags = \core_tag_tag::create_if_missing($collid, $tagnames);
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // First category and questions.
        [$category, $categoryquestions] = $this->create_category_and_questions(2, ['cat1', 'foo']);
        $categories['cat1'] = $category;
        $questions['cat1q1'] = $categoryquestions[0];
        $questions['cat1q2'] = $categoryquestions[1];
        // Second category and questions.
        [$category, $categoryquestions] = $this->create_category_and_questions(2, ['cat2', 'foo']);
        $categories['cat2'] = $category;
        $questions['cat2q1'] = $categoryquestions[0];
        $questions['cat2q2'] = $categoryquestions[1];
        // Sub category and questions.
        [$category, $categoryquestions] = $this->create_category_and_questions(2, ['subcat', 'foo'], $categories['cat1']);
        $categories['subcat'] = $category;
        $questions['subcatq1'] = $categoryquestions[0];
        $questions['subcatq2'] = $categoryquestions[1];
        // Empty category.
        [$category, $categoryquestions] = $this->create_category_and_questions(0);
        $categories['emptycat'] = $category;

        // Generate the arguments for the get_questions function.
        $category = $categories[$categoryindex];
        $tagids = array_map(function($tagname) use ($tags) {
            return $tags[$tagname]->id;
        }, $usetagnames);

        $filters = question_filter_test_helper::create_filters([$category->id], $includesubcategories, $tagids);
        $loader = new random_question_loader(new qubaid_list([]));
        $result = $loader->count_filtered_questions($filters);

        // Ensure the result matches what was expected.
        $this->assertEquals($expectedcount, $result);
    }

    /**
     * Create a question category and create questions in that category. Tag
     * the first question in each category with the given tags.
     *
     * @param int $questioncount How many questions to create.
     * @param string[] $tagnames The list of tags to use.
     * @param stdClass|null $parentcategory The category to set as the parent of the created category.
     * @return array The category and questions.
     */
    protected function create_category_and_questions($questioncount, $tagnames = [], $parentcategory = null): array {
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        if ($parentcategory) {
            $catparams = ['parent' => $parentcategory->id];
        } else {
            $catparams = [];
        }

        $category = $generator->create_question_category($catparams);
        $questions = [];

        for ($i = 0; $i < $questioncount; $i++) {
            $questions[] = $generator->create_question('shortanswer', null, ['category' => $category->id]);
        }

        if (!empty($tagnames) && !empty($questions)) {
            $context = \context::instance_by_id($category->contextid);
            \core_tag_tag::set_item_tags('core_question', 'question', $questions[0]->id, $context, $tagnames);
        }

        return [$category, $questions];
    }

    /**
     * Test that the random question loader excludes questions with invalid types.
     * @return void
     * @throws \dml_exception
     */
    public function test_invalid_questions_are_excluded(): void {

        global $DB;

        $this->resetAfterTest();

        [$category, $questions] = $this->create_category_and_questions(4);
        $loader = new random_question_loader(new qubaid_list([]));
        $filters = question_filter_test_helper::create_filters([$category->id], false, []);

        // Update one of the questions to have an invalid type.
        $invalid = $questions[0];
        $invalid->qtype = 'invalid';
        $DB->update_record('question', $invalid);

        // Assert that we get 1 less result back because the invalid type is excluded.
        $result = $loader->get_filtered_questions($filters);
        $this->assertEquals(count($questions) - 1, count($result));

    }

}
