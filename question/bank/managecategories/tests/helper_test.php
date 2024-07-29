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

namespace qbank_managecategories;

use core\exception\moodle_exception;
use core_question\category_manager;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/question/bank/managecategories/tests/manage_category_test_base.php');
require_once($CFG->dirroot . '/mod/quiz/tests/quiz_question_helper_test_trait.php');

/**
 * Unit tests for helper class.
 *
 * @package    qbank_managecategories
 * @copyright  2006 The Open University
 * @author     2021, Guillermo Gomez Arias <guillermogomez@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \qbank_managecategories\helper
 */
final class helper_test extends manage_category_test_base {
    use \quiz_question_helper_test_trait;

    /**
     * @var \context_module module context.
     */
    protected $context;

    /**
     * @var \stdClass course object.
     */
    protected $course;

    /**
     * @var \component_generator_base question generator.
     */
    protected $qgenerator;

    /**
     * @var \stdClass quiz object.
     */
    protected $quiz;

    /**
     * Tests initial setup.
     */
    protected function setUp(): void {
        parent::setUp();
        self::setAdminUser();
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $this->course = $datagenerator->create_course();
        $this->quiz = $datagenerator->create_module(
            'quiz',
            ['course' => $this->course->id, 'name' => 'Quiz 1'],
        );
        $this->qgenerator = $datagenerator->get_plugin_generator('core_question');
        $this->context = \context_module::instance($this->quiz->cmid);
    }

    /**
     * Test question_remove_stale_questions_from_category function.
     *
     * @covers ::question_remove_stale_questions_from_category
     */
    public function test_question_remove_stale_questions_from_category(): void {
        global $DB;

        $this->setAdminUser();
        $this->resetAfterTest();

        // Quiz and its context.
        $quiz = $this->create_quiz();

        // Create category 1 and one question.
        $qcat1 = $this->create_question_category_for_a_quiz($quiz);
        $q1a = $this->create_question_in_a_category('shortanswer', $qcat1->id);
        $DB->set_field('question_versions', 'status', 'hidden', ['questionid' => $q1a->id]);

        // Create category 2 and two questions.
        $qcat2 = $this->create_question_category_for_a_quiz($quiz);
        $q2a = $this->create_question_in_a_category('shortanswer', $qcat2->id);
        $q2b = $this->create_question_in_a_category('shortanswer', $qcat2->id);
        $DB->set_field('question_versions', 'status', 'hidden', ['questionid' => $q2a->id]);
        $DB->set_field('question_versions', 'status', 'hidden', ['questionid' => $q2b->id]);

        // Add question to the quiz.
        quiz_add_quiz_question($q2b->id, $quiz);

        // Adding a new random question does not add a new question, adds a question_set_references record.
        $this->add_random_questions($quiz->id, 0, $qcat2->id, 1);

        // We added one random question to the quiz and we expect the quiz to have only one random question.
        $q2d = $DB->get_record_sql(
            "SELECT qsr.*
               FROM {quiz_slots} qs
               JOIN {question_set_references} qsr ON qsr.itemid = qs.id
              WHERE qs.quizid = ?
                AND qsr.component = ?
                AND qsr.questionarea = ?",
            [$quiz->id, 'mod_quiz', 'slot'],
            MUST_EXIST
        );

        // The following 2 lines have to be after the quiz_add_random_questions() call above.
        // Otherwise, quiz_add_random_questions() will to be "smart" and use them instead of creating a new "random" question.
        $q1b = $this->create_question_in_a_category('random', $qcat1->id);
        $q2c = $this->create_question_in_a_category('random', $qcat2->id);

        $contexts = new \core_question\local\bank\question_edit_contexts(\context_module::instance($quiz->cmid));
        $manager = new category_manager();
        $this->assertEquals(2, count($manager->get_real_question_ids_in_category($qcat1->id, $contexts)));
        $this->assertEquals(3, count($manager->get_real_question_ids_in_category($qcat2->id, $contexts)));

        // Non-existing category, nothing will happen.
        helper::question_remove_stale_questions_from_category(0);
        $this->assertEquals(2, count($manager->get_real_question_ids_in_category($qcat1->id, $contexts)));
        $this->assertEquals(3, count($manager->get_real_question_ids_in_category($qcat2->id, $contexts)));

        // First category, should be empty afterwards.
        helper::question_remove_stale_questions_from_category($qcat1->id);
        $this->assertEquals(0, count($manager->get_real_question_ids_in_category($qcat1->id, $contexts)));
        $this->assertEquals(3, count($manager->get_real_question_ids_in_category($qcat2->id, $contexts)));
        $this->assertFalse($DB->record_exists('question', ['id' => $q1a->id]));
        $this->assertFalse($DB->record_exists('question', ['id' => $q1b->id]));

        // Second category, used questions should be left untouched.
        helper::question_remove_stale_questions_from_category($qcat2->id);
        $this->assertEquals(0, count($manager->get_real_question_ids_in_category($qcat1->id, $contexts)));
        $this->assertEquals(1, count($manager->get_real_question_ids_in_category($qcat2->id, $contexts)));
        $this->assertFalse($DB->record_exists('question', ['id' => $q2a->id]));
        $this->assertTrue($DB->record_exists('question', ['id' => $q2b->id]));
        $this->assertFalse($DB->record_exists('question', ['id' => $q2c->id]));
        $this->assertTrue($DB->record_exists(
            'question_set_references',
            ['id' => $q2d->id, 'component' => 'mod_quiz', 'questionarea' => 'slot'],
        ));
    }


    /**
     * Test delete top category in function question_can_delete_cat.
     *
     * @covers ::question_can_delete_cat
     * @covers ::question_is_top_category
     */
    public function test_question_can_delete_cat_top_category(): void {

        $qcategory1 = $this->qgenerator->create_question_category(['contextid' => $this->context->id]);

        // Try to delete a top category.
        $categorytop = question_get_top_category($qcategory1->id, true)->id;
        try {
            helper::question_can_delete_cat($categorytop);
        } catch (moodle_exception $e) {
            $this->assertEquals(get_string('cannotdeletetopcat', 'question'), $e->getMessage());
        }
        $this->assertDebuggingCalled(
            'Deprecation: qbank_managecategories\helper::question_can_delete_cat has been deprecated since 4.5. ' .
                'Moved to core namespace. ' .
                'Use core_question\category_manager::can_delete_category instead. ' .
                'See MDL-72397 for more information.',
        );
    }

    /**
     * Test delete only child category in function question_can_delete_cat.
     *
     * @covers ::question_can_delete_cat
     * @covers ::question_is_only_child_of_top_category_in_context
     */
    public function test_question_can_delete_cat_child_category(): void {

        $qcategory1 = $this->qgenerator->create_question_category(['contextid' => $this->context->id]);

        // Try to delete an only child of top category having also at least one child.
        try {
            helper::question_can_delete_cat($qcategory1->id);
        } catch (moodle_exception $e) {
            $this->assertEquals(get_string('cannotdeletecate', 'question'), $e->getMessage());
        }
        $this->assertDebuggingCalled(
            'Deprecation: qbank_managecategories\helper::question_can_delete_cat has been deprecated since 4.5. ' .
                'Moved to core namespace. ' .
                'Use core_question\category_manager::can_delete_category instead. ' .
                'See MDL-72397 for more information.',
        );
    }

    /**
     * Test delete category in function question_can_delete_cat without capabilities.
     *
     * @covers ::question_can_delete_cat
     */
    public function test_question_can_delete_cat_capability(): void {

        $qcategory1 = $this->qgenerator->create_question_category(['contextid' => $this->context->id]);
        $qcategory2 = $this->qgenerator->create_question_category(['contextid' => $this->context->id, 'parent' => $qcategory1->id]);

        // This call should not throw an exception as admin user has the capabilities moodle/question:managecategory.
        helper::question_can_delete_cat($qcategory2->id);

        // Try to delete a category with and user without the capability.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        try {
            helper::question_can_delete_cat($qcategory2->id);
        } catch (\required_capability_exception $e) {
            $this->assertEquals(
                get_string('nopermissions', 'error', get_string('question:managecategory', 'role')),
                $e->getMessage(),
            );
        }
        $message = 'Deprecation: qbank_managecategories\helper::question_can_delete_cat has been deprecated since 4.5. ' .
            'Moved to core namespace. ' .
            'Use core_question\category_manager::can_delete_category instead. ' .
            'See MDL-72397 for more information.';
        $this->assertdebuggingcalledcount(2, [$message, $message]);
    }

    /**
     * Test question_category_select_menu function.
     *
     * @covers ::question_category_select_menu
     * @covers ::question_category_options
     */
    public function test_question_category_select_menu(): void {
        $this->setAdminUser();
        $this->resetAfterTest();

        // Create category.
        $quiz = $this->create_quiz();
        $this->create_question_category_for_a_quiz($quiz, ['name' => 'Test this question category']);
        $contexts = new \core_question\local\bank\question_edit_contexts(\context_module::instance($quiz->cmid));

        ob_start();
        helper::question_category_select_menu($contexts->having_cap('moodle/question:add'));
        $output = ob_get_clean();

        // Test the select menu of question categories output.
        $this->assertStringContainsString('Question category', $output);
        $this->assertStringContainsString('Test this question category', $output);
    }

    /**
     * Test that question_category_options function returns the correct category tree.
     *
     * @covers ::question_category_options
     * @covers ::get_categories_for_contexts
     * @covers ::question_fix_top_names
     * @covers ::question_add_context_in_key
     * @covers ::add_indented_names
     */
    public function test_question_category_options(): void {
        $this->setAdminUser();
        $this->resetAfterTest();

        // Create categories.
        $quiz = $this->create_quiz();
        $qcategory1 = $this->create_question_category_for_a_quiz($quiz);
        $this->create_question_category_for_a_quiz($quiz, ['parent' => $qcategory1->id]);
        $this->create_question_category_for_a_quiz($quiz);

        $contexts = new \core_question\local\bank\question_edit_contexts(\context_module::instance($quiz->cmid));

        // Validate that we have the array with the categories tree.
        $categorycontexts = helper::question_category_options($contexts->having_cap('moodle/question:add'));
        // The quiz name 'Quiz 1' is set in setUp function.
        $categorycontext = $categorycontexts['Quiz: ' . $quiz->name];
        $this->assertCount(3, $categorycontext);

        // Validate that we have the array with the categories tree and that top category is there.
        $newcategorycontexts = helper::question_category_options($contexts->having_cap('moodle/question:add'), true);
        foreach ($newcategorycontexts as $key => $categorycontext) {
            $oldcategorycontext = $categorycontexts[$key];
            $count = count($oldcategorycontext);
            $this->assertCount($count + 1, $categorycontext);
        }
    }
}
