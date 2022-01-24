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

/**
 * Question external functions tests.
 *
 * @package    core_question
 * @category   external
 * @copyright  2016 Pau Ferrer <pau@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */

namespace core_question;

use core_question_external;
use externallib_advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Question external functions tests
 *
 * @package    core_question
 * @category   external
 * @copyright  2016 Pau Ferrer <pau@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
class externallib_test extends externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();

        // Create users.
        $this->student = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
    }

    /**
     * Test update question flag
     */
    public function test_core_question_update_flag() {

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a question category.
        $cat = $questiongenerator->create_question_category();

        $quba = \question_engine::make_questions_usage_by_activity('core_question_update_flag', \context_system::instance());
        $quba->set_preferred_behaviour('deferredfeedback');
        $questiondata = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);
        $question = \question_bank::load_question($questiondata->id);
        $slot = $quba->add_question($question);
        $qa = $quba->get_question_attempt($slot);

        self::setUser($this->student);

        $quba->start_all_questions();
        \question_engine::save_questions_usage_by_activity($quba);

        $qubaid = $quba->get_id();
        $questionid = $question->id;
        $qaid = $qa->get_database_id();
        $checksum = md5($qubaid . "_" . $this->student->secret . "_" . $questionid . "_" . $qaid . "_" . $slot);

        $flag = core_question_external::update_flag($qubaid, $questionid, $qaid, $slot, $checksum, true);
        $this->assertTrue($flag['status']);

        // Test invalid checksum.
        try {
            // Using random_string to force failing.
            $checksum = md5($qubaid . "_" . random_string(11) . "_" . $questionid . "_" . $qaid . "_" . $slot);

            core_question_external::update_flag($qubaid, $questionid, $qaid, $slot, $checksum, true);
            $this->fail('Exception expected due to invalid checksum.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('errorsavingflags', $e->errorcode);
        }
    }

    /**
     * Data provider for the get_random_question_summaries test.
     */
    public function get_random_question_summaries_test_cases() {
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
     * Test the get_random_question_summaries function with various parameter combinations.
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
     * @dataProvider get_random_question_summaries_test_cases()
     * @param string $categoryindex The named index for the category to use
     * @param bool $includesubcategories If the search should include subcategories
     * @param string[] $usetagnames The tag names to include in the search
     * @param string[] $expectedquestionindexes The questions expected in the result
     */
    public function test_get_random_question_summaries_variations(
        $categoryindex,
        $includesubcategories,
        $usetagnames,
        $expectedquestionindexes
    ) {
        $this->resetAfterTest();

        $context = \context_system::instance();
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
        list($category, $categoryquestions) = $this->create_category_and_questions(2, ['cat1', 'foo']);
        $categories['cat1'] = $category;
        $questions['cat1q1'] = $categoryquestions[0];
        $questions['cat1q2'] = $categoryquestions[1];
        // Second category and questions.
        list($category, $categoryquestions) = $this->create_category_and_questions(2, ['cat2', 'foo']);
        $categories['cat2'] = $category;
        $questions['cat2q1'] = $categoryquestions[0];
        $questions['cat2q2'] = $categoryquestions[1];
        // Sub category and questions.
        list($category, $categoryquestions) = $this->create_category_and_questions(2, ['subcat', 'foo'], $categories['cat1']);
        $categories['subcat'] = $category;
        $questions['subcatq1'] = $categoryquestions[0];
        $questions['subcatq2'] = $categoryquestions[1];
        // Empty category.
        list($category, $categoryquestions) = $this->create_category_and_questions(0);
        $categories['emptycat'] = $category;

        // Generate the arguments for the get_questions function.
        $category = $categories[$categoryindex];
        $tagids = array_map(function($tagname) use ($tags) {
            return $tags[$tagname]->id;
        }, $usetagnames);

        $result = core_question_external::get_random_question_summaries($category->id, $includesubcategories, $tagids, $context->id);
        $resultquestions = $result['questions'];
        $resulttotalcount = $result['totalcount'];
        // Generate the expected question set.
        $expectedquestions = array_map(function($index) use ($questions) {
            return $questions[$index];
        }, $expectedquestionindexes);

        // Ensure the resultquestions matches what was expected.
        $this->assertCount(count($expectedquestions), $resultquestions);
        $this->assertEquals(count($expectedquestions), $resulttotalcount);
        foreach ($expectedquestions as $question) {
            $this->assertEquals($resultquestions[$question->id]->id, $question->id);
            $this->assertEquals($resultquestions[$question->id]->category, $question->category);
        }
    }

    /**
     * get_random_question_summaries should throw an invalid_parameter_exception if not
     * given an integer for the category id.
     */
    public function test_get_random_question_summaries_invalid_category_id_param() {
        $this->resetAfterTest();

        $context = \context_system::instance();
        $this->expectException('\invalid_parameter_exception');
        core_question_external::get_random_question_summaries('invalid value', false, [], $context->id);
    }

    /**
     * get_random_question_summaries should throw an invalid_parameter_exception if not
     * given a boolean for the $includesubcategories parameter.
     */
    public function test_get_random_question_summaries_invalid_includesubcategories_param() {
        $this->resetAfterTest();

        $context = \context_system::instance();
        $this->expectException('\invalid_parameter_exception');
        core_question_external::get_random_question_summaries(1, 'invalid value', [], $context->id);
    }

    /**
     * get_random_question_summaries should throw an invalid_parameter_exception if not
     * given an array of integers for the tag ids parameter.
     */
    public function test_get_random_question_summaries_invalid_tagids_param() {
        $this->resetAfterTest();

        $context = \context_system::instance();
        $this->expectException('\invalid_parameter_exception');
        core_question_external::get_random_question_summaries(1, false, ['invalid', 'values'], $context->id);
    }

    /**
     * get_random_question_summaries should throw an invalid_parameter_exception if not
     * given a context.
     */
    public function test_get_random_question_summaries_invalid_context() {
        $this->resetAfterTest();

        $this->expectException('\invalid_parameter_exception');
        core_question_external::get_random_question_summaries(1, false, [1, 2], 'context');
    }

    /**
     * get_random_question_summaries should throw an restricted_context_exception
     * if the given context is outside of the set of restricted contexts the user
     * is allowed to call external functions in.
     */
    public function test_get_random_question_summaries_restricted_context() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $systemcontext = \context_system::instance();
        // Restrict access to external functions for the logged in user to only
        // the course we just created. External functions should not be allowed
        // to execute in any contexts above the course context.
        core_question_external::set_context_restriction($coursecontext);

        // An exception should be thrown when we try to execute at the system context
        // since we're restricted to the course context.
        try {
            // Do this in a try/catch statement to allow the context restriction
            // to be reset afterwards.
            core_question_external::get_random_question_summaries(1, false, [], $systemcontext->id);
        } catch (\Exception $e) {
            $this->assertInstanceOf('restricted_context_exception', $e);
        }
        // Reset the restriction so that other tests don't fail aftwards.
        core_question_external::set_context_restriction($systemcontext);
    }

    /**
     * get_random_question_summaries should return a question that is formatted correctly.
     */
    public function test_get_random_question_summaries_formats_returned_questions() {
        $this->resetAfterTest();

        list($category, $questions) = $this->create_category_and_questions(1);
        $context = \context_system::instance();
        $question = $questions[0];
        $expected = (object) [
            'id' => $question->id,
            'category' => $question->category,
            'parent' => $question->parent,
            'name' => $question->name,
            'qtype' => $question->qtype
        ];

        $result = core_question_external::get_random_question_summaries($category->id, false, [], $context->id);
        $actual = $result['questions'][$question->id];

        $this->assertEquals($expected->id, $actual->id);
        $this->assertEquals($expected->category, $actual->category);
        $this->assertEquals($expected->parent, $actual->parent);
        $this->assertEquals($expected->name, $actual->name);
        $this->assertEquals($expected->qtype, $actual->qtype);
        // These values are added by the formatting. It doesn't matter what the
        // exact values are just that they are returned.
        $this->assertObjectHasAttribute('icon', $actual);
        $this->assertObjectHasAttribute('key', $actual->icon);
        $this->assertObjectHasAttribute('component', $actual->icon);
        $this->assertObjectHasAttribute('alttext', $actual->icon);
    }

    /**
     * get_random_question_summaries should allow limiting and offsetting of the result set.
     */
    public function test_get_random_question_summaries_with_limit_and_offset() {
        $this->resetAfterTest();
        $numberofquestions = 5;
        $includesubcategories = false;
        $tagids = [];
        $limit = 1;
        $offset = 0;
        $context = \context_system::instance();
        list($category, $questions) = $this->create_category_and_questions($numberofquestions);

        // Sort the questions by id to match the ordering of the result.
        usort($questions, function($a, $b) {
            $aid = $a->id;
            $bid = $b->id;

            if ($aid == $bid) {
                return 0;
            }
            return $aid < $bid ? -1 : 1;
        });

        for ($i = 0; $i < $numberofquestions; $i++) {
            $result = core_question_external::get_random_question_summaries(
                $category->id,
                $includesubcategories,
                $tagids,
                $context->id,
                $limit,
                $offset
            );

            $resultquestions = $result['questions'];
            $totalcount = $result['totalcount'];

            $this->assertCount($limit, $resultquestions);
            $this->assertEquals($numberofquestions, $totalcount);
            $actual = array_shift($resultquestions);
            $expected = $questions[$i];
            $this->assertEquals($expected->id, $actual->id);
            $offset++;
        }
    }

    /**
     * get_random_question_summaries should throw an exception if the user doesn't
     * have the capability to use the questions in the requested category.
     */
    public function test_get_random_question_summaries_without_capability() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $roleid = $generator->create_role();
        $systemcontext = \context_system::instance();
        $numberofquestions = 5;
        $includesubcategories = false;
        $tagids = [];
        $context = \context_system::instance();
        list($category, $questions) = $this->create_category_and_questions($numberofquestions);
        $categorycontext = \context::instance_by_id($category->contextid);

        $generator->role_assign($roleid, $user->id, $systemcontext->id);
        // Prohibit all of the tag capabilities.
        assign_capability('moodle/question:viewall', CAP_PROHIBIT, $roleid, $categorycontext->id);

        $this->setUser($user);
        $this->expectException('moodle_exception');
        core_question_external::get_random_question_summaries(
            $category->id,
            $includesubcategories,
            $tagids,
            $context->id
        );
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
    protected function create_category_and_questions($questioncount, $tagnames = [], $parentcategory = null) {
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
}
