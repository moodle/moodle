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
 * Unit tests for (some of) ../questionlib.php.
 *
 * @package    core_question
 * @category   phpunit
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use core_tag\output\tag;


defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

// Get the necessary files to perform backup and restore.
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Unit tests for (some of) ../questionlib.php.
 *
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_questionlib_testcase extends advanced_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Return true and false to test functions with feedback on and off.
     *
     * @return array Test data
     */
    public function provider_feedback() {
        return array(
            'Feedback test' => array(true),
            'No feedback test' => array(false)
        );
    }

    /**
     * Setup a course, a quiz, a question category and a question for testing.
     *
     * @param string $type The type of question category to create.
     * @return array The created data objects
     */
    public function setup_quiz_and_questions($type = 'module') {
        // Create course category.
        $category = $this->getDataGenerator()->create_category();

        // Create course.
        $course = $this->getDataGenerator()->create_course(array(
            'numsections' => 5,
            'category' => $category->id
        ));

        $options = array(
            'course' => $course->id,
            'duedate' => time(),
        );

        // Generate an assignment with due date (will generate a course event).
        $quiz = $this->getDataGenerator()->create_module('quiz', $options);

        $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');

        switch ($type) {
            case 'course':
                $context = context_course::instance($course->id);
                break;

            case 'category':
                $context = context_coursecat::instance($category->id);
                break;

            case 'system':
                $context = context_system::instance();
                break;

            default:
                $context = context_module::instance($quiz->cmid);
                break;
        }

        $qcat = $qgen->create_question_category(array('contextid' => $context->id));

        $questions = array(
                $qgen->create_question('shortanswer', null, array('category' => $qcat->id)),
                $qgen->create_question('shortanswer', null, array('category' => $qcat->id)),
        );

        quiz_add_quiz_question($questions[0]->id, $quiz);

        return array($category, $course, $quiz, $qcat, $questions);
    }

    public function test_question_reorder_qtypes() {
        $this->assertEquals(
            array(0 => 't2', 1 => 't1', 2 => 't3'),
            question_reorder_qtypes(array('t1' => '', 't2' => '', 't3' => ''), 't1', +1));
        $this->assertEquals(
            array(0 => 't1', 1 => 't2', 2 => 't3'),
            question_reorder_qtypes(array('t1' => '', 't2' => '', 't3' => ''), 't1', -1));
        $this->assertEquals(
            array(0 => 't2', 1 => 't1', 2 => 't3'),
            question_reorder_qtypes(array('t1' => '', 't2' => '', 't3' => ''), 't2', -1));
        $this->assertEquals(
            array(0 => 't1', 1 => 't2', 2 => 't3'),
            question_reorder_qtypes(array('t1' => '', 't2' => '', 't3' => ''), 't3', +1));
        $this->assertEquals(
            array(0 => 't1', 1 => 't2', 2 => 't3'),
            question_reorder_qtypes(array('t1' => '', 't2' => '', 't3' => ''), 'missing', +1));
    }

    public function test_match_grade_options() {
        $gradeoptions = question_bank::fraction_options_full();

        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.3333333, 'error'));
        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.333333, 'error'));
        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.33333, 'error'));
        $this->assertFalse(match_grade_options($gradeoptions, 0.3333, 'error'));

        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.3333333, 'nearest'));
        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.333333, 'nearest'));
        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.33333, 'nearest'));
        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.33, 'nearest'));

        $this->assertEquals(-0.1428571, match_grade_options($gradeoptions, -0.15, 'nearest'));
    }

    /**
     * This function tests that the functions responsible for moving questions to
     * different contexts also updates the tag instances associated with the questions.
     */
    public function test_altering_tag_instance_context() {
        global $CFG, $DB;

        // Set to admin user.
        $this->setAdminUser();

        // Create two course categories - we are going to delete one of these later and will expect
        // all the questions belonging to the course in the deleted category to be moved.
        $coursecat1 = $this->getDataGenerator()->create_category();
        $coursecat2 = $this->getDataGenerator()->create_category();

        // Create a couple of categories and questions.
        $context1 = context_coursecat::instance($coursecat1->id);
        $context2 = context_coursecat::instance($coursecat2->id);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $questioncat1 = $questiongenerator->create_question_category(array('contextid' =>
            $context1->id));
        $questioncat2 = $questiongenerator->create_question_category(array('contextid' =>
            $context2->id));
        $question1 = $questiongenerator->create_question('shortanswer', null, array('category' => $questioncat1->id));
        $question2 = $questiongenerator->create_question('shortanswer', null, array('category' => $questioncat1->id));
        $question3 = $questiongenerator->create_question('shortanswer', null, array('category' => $questioncat2->id));
        $question4 = $questiongenerator->create_question('shortanswer', null, array('category' => $questioncat2->id));

        // Now lets tag these questions.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $context1, array('tag 1', 'tag 2'));
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $context1, array('tag 3', 'tag 4'));
        core_tag_tag::set_item_tags('core_question', 'question', $question3->id, $context2, array('tag 5', 'tag 6'));
        core_tag_tag::set_item_tags('core_question', 'question', $question4->id, $context2, array('tag 7', 'tag 8'));

        // Test moving the questions to another category.
        question_move_questions_to_category(array($question1->id, $question2->id), $questioncat2->id);

        // Test that all tag_instances belong to one context.
        $this->assertEquals(8, $DB->count_records('tag_instance', array('component' => 'core_question',
            'contextid' => $questioncat2->contextid)));

        // Test moving them back.
        question_move_questions_to_category(array($question1->id, $question2->id), $questioncat1->id);

        // Test that all tag_instances are now reset to how they were initially.
        $this->assertEquals(4, $DB->count_records('tag_instance', array('component' => 'core_question',
            'contextid' => $questioncat1->contextid)));
        $this->assertEquals(4, $DB->count_records('tag_instance', array('component' => 'core_question',
            'contextid' => $questioncat2->contextid)));

        // Now test moving a whole question category to another context.
        question_move_category_to_context($questioncat1->id, $questioncat1->contextid, $questioncat2->contextid);

        // Test that all tag_instances belong to one context.
        $this->assertEquals(8, $DB->count_records('tag_instance', array('component' => 'core_question',
            'contextid' => $questioncat2->contextid)));

        // Now test moving them back.
        question_move_category_to_context($questioncat1->id, $questioncat2->contextid,
            context_coursecat::instance($coursecat1->id)->id);

        // Test that all tag_instances are now reset to how they were initially.
        $this->assertEquals(4, $DB->count_records('tag_instance', array('component' => 'core_question',
            'contextid' => $questioncat1->contextid)));
        $this->assertEquals(4, $DB->count_records('tag_instance', array('component' => 'core_question',
            'contextid' => $questioncat2->contextid)));

        // Now we want to test deleting the course category and moving the questions to another category.
        question_delete_course_category($coursecat1, $coursecat2, false);

        // Test that all tag_instances belong to one context.
        $this->assertEquals(8, $DB->count_records('tag_instance', array('component' => 'core_question',
            'contextid' => $questioncat2->contextid)));

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create some question categories and questions in this course.
        $coursecontext = context_course::instance($course->id);
        $questioncat = $questiongenerator->create_question_category(array('contextid' =>
            $coursecontext->id));
        $question1 = $questiongenerator->create_question('shortanswer', null, array('category' => $questioncat->id));
        $question2 = $questiongenerator->create_question('shortanswer', null, array('category' => $questioncat->id));

        // Add some tags to these questions.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, array('tag 1', 'tag 2'));
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, array('tag 1', 'tag 2'));

        // Create a course that we are going to restore the other course to.
        $course2 = $this->getDataGenerator()->create_course();

        // Create backup file and save it to the backup location.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, 2);
        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];
        $fp = get_file_packer('application/vnd.moodle.backup');
        $filepath = $CFG->dataroot . '/temp/backup/test-restore-course';
        $file->extract_to_pathname($fp, $filepath);
        $bc->destroy();

        // Now restore the course.
        $rc = new restore_controller('test-restore-course', $course2->id, backup::INTERACTIVE_NO,
            backup::MODE_GENERAL, 2, backup::TARGET_NEW_COURSE);
        $rc->execute_precheck();
        $rc->execute_plan();

        // Get the created question category.
        $restoredcategory = $DB->get_record_select('question_categories', 'contextid = ? AND parent <> 0',
                array(context_course::instance($course2->id)->id), '*', MUST_EXIST);

        // Check that there are two questions in the restored to course's context.
        $this->assertEquals(2, $DB->count_records('question', array('category' => $restoredcategory->id)));

        $rc->destroy();
    }

    /**
     * Test that deleting a question from the question bank works in the normal case.
     */
    public function test_question_delete_question() {
        global $DB;

        // Setup.
        $context = context_system::instance();
        $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcat = $qgen->create_question_category(array('contextid' => $context->id));
        $q1 = $qgen->create_question('shortanswer', null, array('category' => $qcat->id));
        $q2 = $qgen->create_question('shortanswer', null, array('category' => $qcat->id));

        // Do.
        question_delete_question($q1->id);

        // Verify.
        $this->assertFalse($DB->record_exists('question', ['id' => $q1->id]));
        // Check that we did not delete too much.
        $this->assertTrue($DB->record_exists('question', ['id' => $q2->id]));
    }

    /**
     * Test that deleting a broken question from the question bank does not cause fatal errors.
     */
    public function test_question_delete_question_broken_data() {
        global $DB;

        // Setup.
        $context = context_system::instance();
        $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcat = $qgen->create_question_category(array('contextid' => $context->id));
        $q1 = $qgen->create_question('shortanswer', null, array('category' => $qcat->id));

        // Now delete the category, to simulate what happens in old sites where
        // referential integrity has failed.
        $DB->delete_records('question_categories', ['id' => $qcat->id]);

        // Do.
        question_delete_question($q1->id);

        // Verify.
        $this->assertDebuggingCalled('Deleting question ' . $q1->id .
                ' which is no longer linked to a context. Assuming system context ' .
                'to avoid errors, but this may mean that some data like ' .
                'files, tags, are not cleaned up.');
        $this->assertFalse($DB->record_exists('question', ['id' => $q1->id]));
    }

    /**
     * This function tests the question_category_delete_safe function.
     */
    public function test_question_category_delete_safe() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions();

        question_category_delete_safe($qcat);

        // Verify category deleted.
        $criteria = array('id' => $qcat->id);
        $this->assertEquals(0, $DB->count_records('question_categories', $criteria));

        // Verify questions deleted or moved.
        $criteria = array('category' => $qcat->id);
        $this->assertEquals(0, $DB->count_records('question', $criteria));

        // Verify question not deleted.
        $criteria = array('id' => $questions[0]->id);
        $this->assertEquals(1, $DB->count_records('question', $criteria));
    }

    /**
     * This function tests the question_delete_activity function.
     *
     * @param bool $feedback Whether to return feedback
     * @dataProvider provider_feedback
     */
    public function test_question_delete_activity($feedback) {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions();

        $cm = get_coursemodule_from_instance('quiz', $quiz->id);
        // Test that the feedback works.
        if ($feedback) {
            $this->expectOutputRegex('|'.get_string('unusedcategorydeleted', 'question').'|');
        }
        question_delete_activity($cm, $feedback);

        // Verify category deleted.
        $criteria = array('id' => $qcat->id);
        $this->assertEquals(0, $DB->count_records('question_categories', $criteria));

        // Verify questions deleted or moved.
        $criteria = array('category' => $qcat->id);
        $this->assertEquals(0, $DB->count_records('question', $criteria));
    }

    /**
     * This function tests the question_delete_context function.
     */
    public function test_question_delete_context() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions();

        // Get the module context id.
        $result = question_delete_context($qcat->contextid);

        // Verify category deleted.
        $criteria = array('id' => $qcat->id);
        $this->assertEquals(0, $DB->count_records('question_categories', $criteria));

        // Verify questions deleted or moved.
        $criteria = array('category' => $qcat->id);
        $this->assertEquals(0, $DB->count_records('question', $criteria));

        // Test that the feedback works.
        $expected[] = array('top', get_string('unusedcategorydeleted', 'question'));
        $expected[] = array($qcat->name, get_string('unusedcategorydeleted', 'question'));
        $this->assertEquals($expected, $result);
    }

    /**
     * This function tests the question_delete_course function.
     *
     * @param bool $feedback Whether to return feedback
     * @dataProvider provider_feedback
     */
    public function test_question_delete_course($feedback) {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('course');

        // Test that the feedback works.
        if ($feedback) {
            $this->expectOutputRegex('|'.get_string('unusedcategorydeleted', 'question').'|');
        }
        question_delete_course($course, $feedback);

        // Verify category deleted.
        $criteria = array('id' => $qcat->id);
        $this->assertEquals(0, $DB->count_records('question_categories', $criteria));

        // Verify questions deleted or moved.
        $criteria = array('category' => $qcat->id);
        $this->assertEquals(0, $DB->count_records('question', $criteria));
    }

    /**
     * This function tests the question_delete_course_category function.
     *
     * @param bool $feedback Whether to return feedback
     * @dataProvider provider_feedback
     */
    public function test_question_delete_course_category($feedback) {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');

        // Test that the feedback works.
        if ($feedback) {
            $this->expectOutputRegex('|'.get_string('unusedcategorydeleted', 'question').'|');
        }
        question_delete_course_category($category, 0, $feedback);

        // Verify category deleted.
        $criteria = array('id' => $qcat->id);
        $this->assertEquals(0, $DB->count_records('question_categories', $criteria));

        // Verify questions deleted or moved.
        $criteria = array('category' => $qcat->id);
        $this->assertEquals(0, $DB->count_records('question', $criteria));
    }

    /**
     * This function tests the question_delete_course_category function when it is supposed to move question categories.
     *
     * @param bool $feedback Whether to return feedback
     * @dataProvider provider_feedback
     */
    public function test_question_delete_course_category_move_qcats($feedback) {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        list($category1, $course1, $quiz1, $qcat1, $questions1) = $this->setup_quiz_and_questions('category');
        list($category2, $course2, $quiz2, $qcat2, $questions2) = $this->setup_quiz_and_questions('category');

        $questionsinqcat1 = count($questions1);
        $questionsinqcat2 = count($questions2);

        // Test that the feedback works.
        if ($feedback) {
            $a = new stdClass();
            $a->oldplace = context::instance_by_id($qcat1->contextid)->get_context_name();
            $a->newplace = context::instance_by_id($qcat2->contextid)->get_context_name();
            $this->expectOutputRegex('|'.get_string('movedquestionsandcategories', 'question', $a).'|');
        }
        question_delete_course_category($category1, $category2, $feedback);

        // Verify category not deleted.
        $criteria = array('id' => $qcat1->id);
        $this->assertEquals(1, $DB->count_records('question_categories', $criteria));

        // Verify questions are moved.
        $criteria = array('category' => $qcat1->id);
        $params = array($qcat2->contextid);
        $actualquestionscount = $DB->count_records_sql("SELECT COUNT(*)
                                                          FROM {question} q
                                                          JOIN {question_categories} qc ON q.category = qc.id
                                                         WHERE qc.contextid = ?", $params, $criteria);
        $this->assertEquals($questionsinqcat1 + $questionsinqcat2, $actualquestionscount);

        // Verify there is just a single top-level category.
        $criteria = array('contextid' => $qcat2->contextid, 'parent' => 0);
        $this->assertEquals(1, $DB->count_records('question_categories', $criteria));

        // Verify there is no question category in previous context.
        $criteria = array('contextid' => $qcat1->contextid);
        $this->assertEquals(0, $DB->count_records('question_categories', $criteria));
    }

    /**
     * This function tests the question_save_from_deletion function when it is supposed to make a new category and
     * move question categories to that new category.
     */
    public function test_question_save_from_deletion() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions();

        $context = context::instance_by_id($qcat->contextid);

        $newcat = question_save_from_deletion(array_column($questions, 'id'),
                $context->get_parent_context()->id, $context->get_context_name());

        // Verify that the newcat itself is not a tep level category.
        $this->assertNotEquals(0, $newcat->parent);

        // Verify there is just a single top-level category.
        $this->assertEquals(1, $DB->count_records('question_categories', ['contextid' => $qcat->contextid, 'parent' => 0]));
    }

    /**
     * This function tests the question_save_from_deletion function when it is supposed to make a new category and
     * move question categories to that new category when quiz name is very long but less than 256 characters.
     */
    public function test_question_save_from_deletion_quiz_with_long_name() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions();

        // Moodle doesn't allow you to enter a name longer than 255 characters.
        $quiz->name = shorten_text(str_repeat('123456789 ', 26), 255);

        $DB->update_record('quiz', $quiz);

        $context = context::instance_by_id($qcat->contextid);

        $newcat = question_save_from_deletion(array_column($questions, 'id'),
                $context->get_parent_context()->id, $context->get_context_name());

        // Verifying that the inserted record's name is expected or not.
        $this->assertEquals($DB->get_record('question_categories', ['id' => $newcat->id])->name, $newcat->name);

        // Verify that the newcat itself is not a top level category.
        $this->assertNotEquals(0, $newcat->parent);

        // Verify there is just a single top-level category.
        $this->assertEquals(1, $DB->count_records('question_categories', ['contextid' => $qcat->contextid, 'parent' => 0]));
    }

    public function test_question_remove_stale_questions_from_category() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $course = $dg->create_course();
        $quiz = $dg->create_module('quiz', ['course' => $course->id]);

        $qgen = $dg->get_plugin_generator('core_question');
        $context = context_system::instance();

        $qcat1 = $qgen->create_question_category(['contextid' => $context->id]);
        $q1a = $qgen->create_question('shortanswer', null, ['category' => $qcat1->id]);     // Will be hidden.
        $DB->set_field('question', 'hidden', 1, ['id' => $q1a->id]);

        $qcat2 = $qgen->create_question_category(['contextid' => $context->id]);
        $q2a = $qgen->create_question('shortanswer', null, ['category' => $qcat2->id]);     // Will be hidden.
        $q2b = $qgen->create_question('shortanswer', null, ['category' => $qcat2->id]);     // Will be hidden but used.
        $DB->set_field('question', 'hidden', 1, ['id' => $q2a->id]);
        $DB->set_field('question', 'hidden', 1, ['id' => $q2b->id]);
        quiz_add_quiz_question($q2b->id, $quiz);
        quiz_add_random_questions($quiz, 0, $qcat2->id, 1, false);

        // We added one random question to the quiz and we expect the quiz to have only one random question.
        $q2d = $DB->get_record_sql("SELECT q.*
                                      FROM {question} q
                                      JOIN {quiz_slots} s ON s.questionid = q.id
                                     WHERE q.qtype = :qtype
                                           AND s.quizid = :quizid",
                array('qtype' => 'random', 'quizid' => $quiz->id), MUST_EXIST);

        // The following 2 lines have to be after the quiz_add_random_questions() call above.
        // Otherwise, quiz_add_random_questions() will to be "smart" and use them instead of creating a new "random" question.
        $q1b = $qgen->create_question('random', null, ['category' => $qcat1->id]);          // Will not be used.
        $q2c = $qgen->create_question('random', null, ['category' => $qcat2->id]);          // Will not be used.

        $this->assertEquals(2, $DB->count_records('question', ['category' => $qcat1->id]));
        $this->assertEquals(4, $DB->count_records('question', ['category' => $qcat2->id]));

        // Non-existing category, nothing will happen.
        question_remove_stale_questions_from_category(0);
        $this->assertEquals(2, $DB->count_records('question', ['category' => $qcat1->id]));
        $this->assertEquals(4, $DB->count_records('question', ['category' => $qcat2->id]));

        // First category, should be empty afterwards.
        question_remove_stale_questions_from_category($qcat1->id);
        $this->assertEquals(0, $DB->count_records('question', ['category' => $qcat1->id]));
        $this->assertEquals(4, $DB->count_records('question', ['category' => $qcat2->id]));
        $this->assertFalse($DB->record_exists('question', ['id' => $q1a->id]));
        $this->assertFalse($DB->record_exists('question', ['id' => $q1b->id]));

        // Second category, used questions should be left untouched.
        question_remove_stale_questions_from_category($qcat2->id);
        $this->assertEquals(0, $DB->count_records('question', ['category' => $qcat1->id]));
        $this->assertEquals(2, $DB->count_records('question', ['category' => $qcat2->id]));
        $this->assertFalse($DB->record_exists('question', ['id' => $q2a->id]));
        $this->assertTrue($DB->record_exists('question', ['id' => $q2b->id]));
        $this->assertFalse($DB->record_exists('question', ['id' => $q2c->id]));
        $this->assertTrue($DB->record_exists('question', ['id' => $q2d->id]));
    }

    /**
     * get_question_options should add the category object to the given question.
     */
    public function test_get_question_options_includes_category_object_single_question() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        $question = array_shift($questions);

        get_question_options($question);

        $this->assertEquals($qcat, $question->categoryobject);
    }

    /**
     * get_question_options should add the category object to all of the questions in
     * the given list.
     */
    public function test_get_question_options_includes_category_object_multiple_questions() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');

        get_question_options($questions);

        foreach ($questions as $question) {
            $this->assertEquals($qcat, $question->categoryobject);
        }
    }

    /**
     * get_question_options includes the tags for all questions in the list.
     */
    public function test_get_question_options_includes_question_tags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = context::instance_by_id($qcat->contextid);

        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo', 'bar']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['baz', 'bop']);

        get_question_options($questions, true);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            $expectedtags = [];
            $actualtags = $question->tags;
            foreach ($tags as $tag) {
                $expectedtags[$tag->id] = $tag->get_display_name();
            }

            // The question should have a tags property populated with each tag id
            // and display name as a key vale pair.
            $this->assertEquals($expectedtags, $actualtags);

            $actualtagobjects = $question->tagobjects;
            sort($tags);
            sort($actualtagobjects);

            // The question should have a full set of each tag object.
            $this->assertEquals($tags, $actualtagobjects);
            // The question should not have any course tags.
            $this->assertEmpty($question->coursetagobjects);
        }
    }

    /**
     * get_question_options includes the course tags for all questions in the list.
     */
    public function test_get_question_options_includes_course_tags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $coursecontext = context_course::instance($course->id);

        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['foo', 'bar']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['baz', 'bop']);

        get_question_options($questions, true);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            $expectedcoursetags = [];
            $actualcoursetags = $question->coursetags;
            foreach ($tags as $tag) {
                $expectedcoursetags[$tag->id] = $tag->get_display_name();
            }

            // The question should have a coursetags property populated with each tag id
            // and display name as a key vale pair.
            $this->assertEquals($expectedcoursetags, $actualcoursetags);

            $actualcoursetagobjects = $question->coursetagobjects;
            sort($tags);
            sort($actualcoursetagobjects);

            // The question should have a full set of the course tag objects.
            $this->assertEquals($tags, $actualcoursetagobjects);
            // The question should not have any other tags.
            $this->assertEmpty($question->tagobjects);
            $this->assertEmpty($question->tags);
        }
    }

    /**
     * get_question_options only categorises a tag as a course tag if it is in a
     * course context that is different from the question context.
     */
    public function test_get_question_options_course_tags_in_course_question_context() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('course');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $coursecontext = context_course::instance($course->id);

        // Create course level tags in the course context that matches the question
        // course context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['foo', 'bar']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['baz', 'bop']);

        get_question_options($questions, true);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);

            $actualtagobjects = $question->tagobjects;
            sort($tags);
            sort($actualtagobjects);

            // The tags should not be considered course tags because they are in
            // the same context as the question. That makes them question tags.
            $this->assertEmpty($question->coursetagobjects);
            // The course context tags should be returned in the regular tag object
            // list.
            $this->assertEquals($tags, $actualtagobjects);
        }
    }

    /**
     * get_question_options includes the tags and course tags for all questions in the list
     * if each question has course and question level tags.
     */
    public function test_get_question_options_includes_question_and_course_tags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = context::instance_by_id($qcat->contextid);
        $coursecontext = context_course::instance($course->id);

        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo', 'bar']);
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['cfoo', 'cbar']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['baz', 'bop']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['cbaz', 'cbop']);

        get_question_options($questions, true);

        foreach ($questions as $question) {
            $alltags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            $tags = array_filter($alltags, function($tag) use ($qcontext) {
                return $tag->taginstancecontextid == $qcontext->id;
            });
            $coursetags = array_filter($alltags, function($tag) use ($coursecontext) {
                return $tag->taginstancecontextid == $coursecontext->id;
            });

            $expectedtags = [];
            $actualtags = $question->tags;
            foreach ($tags as $tag) {
                $expectedtags[$tag->id] = $tag->get_display_name();
            }

            // The question should have a tags property populated with each tag id
            // and display name as a key vale pair.
            $this->assertEquals($expectedtags, $actualtags);

            $actualtagobjects = $question->tagobjects;
            sort($tags);
            sort($actualtagobjects);
            // The question should have a full set of each tag object.
            $this->assertEquals($tags, $actualtagobjects);

            $actualcoursetagobjects = $question->coursetagobjects;
            sort($coursetags);
            sort($actualcoursetagobjects);
            // The question should have a full set of course tag objects.
            $this->assertEquals($coursetags, $actualcoursetagobjects);
        }
    }

    /**
     * get_question_options should update the context id to the question category
     * context id for any non-course context tag that isn't in the question category
     * context.
     */
    public function test_get_question_options_normalises_question_tags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = context::instance_by_id($qcat->contextid);
        $systemcontext = context_system::instance();

        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo', 'bar']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['baz', 'bop']);

        $q1tags = core_tag_tag::get_item_tags('core_question', 'question', $question1->id);
        $q2tags = core_tag_tag::get_item_tags('core_question', 'question', $question2->id);
        $q1tag = array_shift($q1tags);
        $q2tag = array_shift($q2tags);

        // Change two of the tag instances to be a different (non-course) context to the
        // question tag context. These tags should then be normalised back to the question
        // tag context.
        core_tag_tag::change_instances_context([$q1tag->taginstanceid, $q2tag->taginstanceid], $systemcontext);

        get_question_options($questions, true);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);

            // The database should have been updated with the correct context id.
            foreach ($tags as $tag) {
                $this->assertEquals($qcontext->id, $tag->taginstancecontextid);
            }

            // The tag objects on the question should have been updated with the
            // correct context id.
            foreach ($question->tagobjects as $tag) {
                $this->assertEquals($qcontext->id, $tag->taginstancecontextid);
            }
        }
    }

    /**
     * get_question_options if the question is a course level question then tags
     * in that context should not be consdered course tags, they are question tags.
     */
    public function test_get_question_options_includes_course_context_question_tags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('course');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $coursecontext = context_course::instance($course->id);

        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['foo', 'bar']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['baz', 'bop']);

        get_question_options($questions, true);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            // Tags in a course context that matches the question context should
            // not be considered course tags.
            $this->assertEmpty($question->coursetagobjects);
            $this->assertEmpty($question->coursetags);

            $actualtagobjects = $question->tagobjects;
            sort($tags);
            sort($actualtagobjects);
            // The tags should be considered question tags not course tags.
            $this->assertEquals($tags, $actualtagobjects);
        }
    }

    /**
     * get_question_options should return tags from all course contexts by default.
     */
    public function test_get_question_options_includes_multiple_courses_tags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $coursecontext = context_course::instance($course->id);
        // Create a sibling course.
        $siblingcourse = $this->getDataGenerator()->create_course(['category' => $course->category]);
        $siblingcoursecontext = context_course::instance($siblingcourse->id);

        // Create course tags.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['c1']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['c1']);
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $siblingcoursecontext, ['c2']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $siblingcoursecontext, ['c2']);

        get_question_options($questions, true);

        foreach ($questions as $question) {
            $this->assertCount(2, $question->coursetagobjects);

            foreach ($question->coursetagobjects as $tag) {
                if ($tag->name == 'c1') {
                    $this->assertEquals($coursecontext->id, $tag->taginstancecontextid);
                } else {
                    $this->assertEquals($siblingcoursecontext->id, $tag->taginstancecontextid);
                }
            }
        }
    }

    /**
     * get_question_options should filter the course tags by the given list of courses.
     */
    public function test_get_question_options_includes_filter_course_tags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $coursecontext = context_course::instance($course->id);
        // Create a sibling course.
        $siblingcourse = $this->getDataGenerator()->create_course(['category' => $course->category]);
        $siblingcoursecontext = context_course::instance($siblingcourse->id);

        // Create course tags.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['foo']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['bar']);
        // Create sibling course tags. These should be filtered out.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $siblingcoursecontext, ['filtered1']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $siblingcoursecontext, ['filtered2']);

        // Ask to only receive course tags from $course (ignoring $siblingcourse tags).
        get_question_options($questions, true, [$course]);

        foreach ($questions as $question) {
            foreach ($question->coursetagobjects as $tag) {
                // We should only be seeing course tags from $course. The tags from
                // $siblingcourse should have been filtered out.
                $this->assertEquals($coursecontext->id, $tag->taginstancecontextid);
            }
        }
    }

    /**
     * question_move_question_tags_to_new_context should update all of the
     * question tags contexts when they are moving down (from system to course
     * category context).
     */
    public function test_question_move_question_tags_to_new_context_system_to_course_cat_qtags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('system');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = context::instance_by_id($qcat->contextid);
        $newcontext = context_coursecat::instance($category->id);

        foreach ($questions as $question) {
            $question->contextid = $qcat->contextid;
        }

        // Create tags in the system context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo', 'bar']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['foo', 'bar']);

        question_move_question_tags_to_new_context($questions, $newcontext);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);

            // All of the tags should have their context id set to the new context.
            foreach ($tags as $tag) {
                $this->assertEquals($newcontext->id, $tag->taginstancecontextid);
            }
        }
    }

    /**
     * question_move_question_tags_to_new_context should update all of the question tags
     * contexts when they are moving down (from system to course category context)
     * but leave any tags in the course context where they are.
     */
    public function test_question_move_question_tags_to_new_context_system_to_course_cat_qtags_and_course_tags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('system');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = context::instance_by_id($qcat->contextid);
        $coursecontext = context_course::instance($course->id);
        $newcontext = context_coursecat::instance($category->id);

        foreach ($questions as $question) {
            $question->contextid = $qcat->contextid;
        }

        // Create tags in the system context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['foo']);
        // Create tags in the course context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['ctag']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['ctag']);

        question_move_question_tags_to_new_context($questions, $newcontext);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);

            foreach ($tags as $tag) {
                if ($tag->name == 'ctag') {
                    // Course tags should remain in the course context.
                    $this->assertEquals($coursecontext->id, $tag->taginstancecontextid);
                } else {
                    // Other tags should be updated.
                    $this->assertEquals($newcontext->id, $tag->taginstancecontextid);
                }
            }
        }
    }

    /**
     * question_move_question_tags_to_new_context should update all of the question
     * contexts tags when they are moving up (from course category to system context).
     */
    public function test_question_move_question_tags_to_new_context_course_cat_to_system_qtags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = context::instance_by_id($qcat->contextid);
        $newcontext = context_system::instance();

        foreach ($questions as $question) {
            $question->contextid = $qcat->contextid;
        }

        // Create tags in the course category context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo', 'bar']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['foo', 'bar']);

        question_move_question_tags_to_new_context($questions, $newcontext);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);

            // All of the tags should have their context id set to the new context.
            foreach ($tags as $tag) {
                $this->assertEquals($newcontext->id, $tag->taginstancecontextid);
            }
        }
    }

    /**
     * question_move_question_tags_to_new_context should update all of the question
     * tags contexts when they are moving up (from course category context to system
     * context) but leave any tags in the course context where they are.
     */
    public function test_question_move_question_tags_to_new_context_course_cat_to_system_qtags_and_course_tags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = context::instance_by_id($qcat->contextid);
        $coursecontext = context_course::instance($course->id);
        $newcontext = context_system::instance();

        foreach ($questions as $question) {
            $question->contextid = $qcat->contextid;
        }

        // Create tags in the system context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['foo']);
        // Create tags in the course context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['ctag']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['ctag']);

        question_move_question_tags_to_new_context($questions, $newcontext);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);

            foreach ($tags as $tag) {
                if ($tag->name == 'ctag') {
                    // Course tags should remain in the course context.
                    $this->assertEquals($coursecontext->id, $tag->taginstancecontextid);
                } else {
                    // Other tags should be updated.
                    $this->assertEquals($newcontext->id, $tag->taginstancecontextid);
                }
            }
        }
    }

    /**
     * question_move_question_tags_to_new_context should merge all tags into the course
     * context when moving down from course category context into course context.
     */
    public function test_question_move_question_tags_to_new_context_course_cat_to_coures_qtags_and_course_tags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = context::instance_by_id($qcat->contextid);
        $coursecontext = context_course::instance($course->id);
        $newcontext = $coursecontext;

        foreach ($questions as $question) {
            $question->contextid = $qcat->contextid;
        }

        // Create tags in the system context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['foo']);
        // Create tags in the course context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['ctag']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['ctag']);

        question_move_question_tags_to_new_context($questions, $newcontext);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            // Each question should have 2 tags.
            $this->assertCount(2, $tags);

            foreach ($tags as $tag) {
                // All tags should be updated to the course context and merged in.
                $this->assertEquals($newcontext->id, $tag->taginstancecontextid);
            }
        }
    }

    /**
     * question_move_question_tags_to_new_context should delete all of the tag
     * instances from sibling courses when moving the context of a question down
     * from a course category into a course context because the other courses will
     * no longer have access to the question.
     */
    public function test_question_move_question_tags_to_new_context_remove_other_course_tags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        // Create a sibling course.
        $siblingcourse = $this->getDataGenerator()->create_course(['category' => $course->category]);
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = context::instance_by_id($qcat->contextid);
        $coursecontext = context_course::instance($course->id);
        $siblingcoursecontext = context_course::instance($siblingcourse->id);
        $newcontext = $coursecontext;

        foreach ($questions as $question) {
            $question->contextid = $qcat->contextid;
        }

        // Create tags in the system context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['foo']);
        // Create tags in the target course context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['ctag']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['ctag']);
        // Create tags in the sibling course context. These should be deleted as
        // part of the move.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $siblingcoursecontext, ['stag']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $siblingcoursecontext, ['stag']);

        question_move_question_tags_to_new_context($questions, $newcontext);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            // Each question should have 2 tags, 'foo' and 'ctag'.
            $this->assertCount(2, $tags);

            foreach ($tags as $tag) {
                $tagname = $tag->name;
                // The 'stag' should have been deleted because it's in a sibling
                // course context.
                $this->assertContains($tagname, ['foo', 'ctag']);
                // All tags should be in the course context now.
                $this->assertEquals($coursecontext->id, $tag->taginstancecontextid);
            }
        }
    }

    /**
     * question_move_question_tags_to_new_context should update all of the question
     * tags to be the course category context when moving the tags from a course
     * context to a course category context.
     */
    public function test_question_move_question_tags_to_new_context_course_to_course_cat() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('course');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = context::instance_by_id($qcat->contextid);
        // Moving up into the course category context.
        $newcontext = context_coursecat::instance($category->id);

        foreach ($questions as $question) {
            $question->contextid = $qcat->contextid;
        }

        // Create tags in the course context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['foo']);

        question_move_question_tags_to_new_context($questions, $newcontext);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);

            // All of the tags should have their context id set to the new context.
            foreach ($tags as $tag) {
                $this->assertEquals($newcontext->id, $tag->taginstancecontextid);
            }
        }
    }

    /**
     * question_move_question_tags_to_new_context should update all of the
     * question tags contexts when they are moving down (from system to course
     * category context).
     */
    public function test_question_move_question_tags_to_new_context_orphaned_tag_contexts() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('system');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $othercategory = $this->getDataGenerator()->create_category();
        $qcontext = context::instance_by_id($qcat->contextid);
        $newcontext = context_coursecat::instance($category->id);
        $othercategorycontext = context_coursecat::instance($othercategory->id);

        foreach ($questions as $question) {
            $question->contextid = $qcat->contextid;
        }

        // Create tags in the system context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['foo']);
        // Create tags in the other course category context. These should be
        // update to the next context id because they represent erroneous data
        // from a time before context id was mandatory in the tag API.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $othercategorycontext, ['bar']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $othercategorycontext, ['bar']);

        question_move_question_tags_to_new_context($questions, $newcontext);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            // Each question should have two tags, 'foo' and 'bar'.
            $this->assertCount(2, $tags);

            // All of the tags should have their context id set to the new context
            // (course category context).
            foreach ($tags as $tag) {
                $this->assertEquals($newcontext->id, $tag->taginstancecontextid);
            }
        }
    }

    /**
     * When moving from a course category context down into an activity context
     * all question context tags and course tags (where the course is a parent of
     * the activity) should move into the new context.
     */
    public function test_question_move_question_tags_to_new_context_course_cat_to_activity_qtags_and_course_tags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = context::instance_by_id($qcat->contextid);
        $coursecontext = context_course::instance($course->id);
        $newcontext = context_module::instance($quiz->cmid);

        foreach ($questions as $question) {
            $question->contextid = $qcat->contextid;
        }

        // Create tags in the course category context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['foo']);
        // Move the questions to the activity context which is a child context of
        // $coursecontext.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['ctag']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['ctag']);

        question_move_question_tags_to_new_context($questions, $newcontext);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            // Each question should have 2 tags.
            $this->assertCount(2, $tags);

            foreach ($tags as $tag) {
                $this->assertEquals($newcontext->id, $tag->taginstancecontextid);
            }
        }
    }

    /**
     * When moving from a course category context down into an activity context
     * all question context tags and course tags (where the course is a parent of
     * the activity) should move into the new context. Tags in course contexts
     * that are not a parent of the activity context should be deleted.
     */
    public function test_question_move_question_tags_to_new_context_course_cat_to_activity_orphaned_tags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = context::instance_by_id($qcat->contextid);
        $coursecontext = context_course::instance($course->id);
        $newcontext = context_module::instance($quiz->cmid);
        $othercourse = $this->getDataGenerator()->create_course();
        $othercoursecontext = context_course::instance($othercourse->id);

        foreach ($questions as $question) {
            $question->contextid = $qcat->contextid;
        }

        // Create tags in the course category context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['foo']);
        // Create tags in the course context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['ctag']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['ctag']);
        // Create tags in the other course context. These should be deleted.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $othercoursecontext, ['delete']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $othercoursecontext, ['delete']);

        // Move the questions to the activity context which is a child context of
        // $coursecontext.
        question_move_question_tags_to_new_context($questions, $newcontext);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            // Each question should have 2 tags.
            $this->assertCount(2, $tags);

            foreach ($tags as $tag) {
                // Make sure we don't have any 'delete' tags.
                $this->assertContains($tag->name, ['foo', 'ctag']);
                $this->assertEquals($newcontext->id, $tag->taginstancecontextid);
            }
        }
    }

    /**
     * When moving from a course context down into an activity context all of the
     * course tags should move into the activity context.
     */
    public function test_question_move_question_tags_to_new_context_course_to_activity_qtags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('course');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = context::instance_by_id($qcat->contextid);
        $newcontext = context_module::instance($quiz->cmid);

        foreach ($questions as $question) {
            $question->contextid = $qcat->contextid;
        }

        // Create tags in the course context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['foo']);

        question_move_question_tags_to_new_context($questions, $newcontext);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);

            foreach ($tags as $tag) {
                $this->assertEquals($newcontext->id, $tag->taginstancecontextid);
            }
        }
    }

    /**
     * When moving from a course context down into an activity context all of the
     * course tags should move into the activity context.
     */
    public function test_question_move_question_tags_to_new_context_activity_to_course_qtags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions();
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = context::instance_by_id($qcat->contextid);
        $newcontext = context_course::instance($course->id);

        foreach ($questions as $question) {
            $question->contextid = $qcat->contextid;
        }

        // Create tags in the activity context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['foo']);

        question_move_question_tags_to_new_context($questions, $newcontext);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);

            foreach ($tags as $tag) {
                $this->assertEquals($newcontext->id, $tag->taginstancecontextid);
            }
        }
    }

    /**
     * question_move_question_tags_to_new_context should update all of the
     * question tags contexts when they are moving down (from system to course
     * category context).
     *
     * Course tags within the new category context should remain while any course
     * tags in course contexts that can no longer access the question should be
     * deleted.
     */
    public function test_question_move_question_tags_to_new_context_system_to_course_cat_with_orphaned_tags() {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('system');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $othercategory = $this->getDataGenerator()->create_category();
        $othercourse = $this->getDataGenerator()->create_course(['category' => $othercategory->id]);
        $qcontext = context::instance_by_id($qcat->contextid);
        $newcontext = context_coursecat::instance($category->id);
        $othercategorycontext = context_coursecat::instance($othercategory->id);
        $coursecontext = context_course::instance($course->id);
        $othercoursecontext = context_course::instance($othercourse->id);

        foreach ($questions as $question) {
            $question->contextid = $qcat->contextid;
        }

        // Create tags in the system context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['foo']);
        // Create tags in the child course context of the new context.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['bar']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['bar']);
        // Create tags in the other course context. These should be deleted when
        // the question moves to the new course category context because this
        // course belongs to a different category, which means it will no longer
        // have access to the question.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $othercoursecontext, ['delete']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $othercoursecontext, ['delete']);

        question_move_question_tags_to_new_context($questions, $newcontext);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            // Each question should have two tags, 'foo' and 'bar'.
            $this->assertCount(2, $tags);

            // All of the tags should have their context id set to the new context
            // (course category context).
            foreach ($tags as $tag) {
                $this->assertContains($tag->name, ['foo', 'bar']);

                if ($tag->name == 'foo') {
                    $this->assertEquals($newcontext->id, $tag->taginstancecontextid);
                } else {
                    $this->assertEquals($coursecontext->id, $tag->taginstancecontextid);
                }
            }
        }
    }

    /**
     * question_sort_tags() includes the tags for all questions in the list.
     */
    public function test_question_sort_tags_includes_question_tags() {

        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = context::instance_by_id($qcat->contextid);

        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo', 'bar']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['baz', 'bop']);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            $categorycontext = context::instance_by_id($qcat->contextid);
            $tagobjects = question_sort_tags($tags, $categorycontext);
            $expectedtags = [];
            $actualtags = $tagobjects->tags;
            foreach ($tagobjects->tagobjects as $tag) {
                $expectedtags[$tag->id] = $tag->name;
            }

            // The question should have a tags property populated with each tag id
            // and display name as a key vale pair.
            $this->assertEquals($expectedtags, $actualtags);

            $actualtagobjects = $tagobjects->tagobjects;
            sort($tags);
            sort($actualtagobjects);

            // The question should have a full set of each tag object.
            $this->assertEquals($tags, $actualtagobjects);
            // The question should not have any course tags.
            $this->assertEmpty($tagobjects->coursetagobjects);
        }
    }

    /**
     * question_sort_tags() includes course tags for all questions in the list.
     */
    public function test_question_sort_tags_includes_question_course_tags() {
        global $DB;

        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $coursecontext = context_course::instance($course->id);

        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['foo', 'bar']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['baz', 'bop']);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            $tagobjects = question_sort_tags($tags, $qcat);

            $expectedtags = [];
            $actualtags = $tagobjects->coursetags;
            foreach ($actualtags as $coursetagid => $coursetagname) {
                $expectedtags[$coursetagid] = $coursetagname;
            }

            // The question should have a tags property populated with each tag id
            // and display name as a key vale pair.
            $this->assertEquals($expectedtags, $actualtags);

            $actualtagobjects = $tagobjects->coursetagobjects;
            sort($tags);
            sort($actualtagobjects);

            // The question should have a full set of each tag object.
            $this->assertEquals($tags, $actualtagobjects);
            // The question should not have any course tags.
            $this->assertEmpty($tagobjects->tagobjects);
        }
    }

    /**
     * question_sort_tags() should return tags from all course contexts by default.
     */
    public function test_question_sort_tags_includes_multiple_courses_tags() {
        global $DB;

        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $coursecontext = context_course::instance($course->id);
        // Create a sibling course.
        $siblingcourse = $this->getDataGenerator()->create_course(['category' => $course->category]);
        $siblingcoursecontext = context_course::instance($siblingcourse->id);

        // Create course tags.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['c1']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['c1']);
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $siblingcoursecontext, ['c2']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $siblingcoursecontext, ['c2']);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            $tagobjects = question_sort_tags($tags, $qcat);
            $this->assertCount(2, $tagobjects->coursetagobjects);

            foreach ($tagobjects->coursetagobjects as $tag) {
                if ($tag->name == 'c1') {
                    $this->assertEquals($coursecontext->id, $tag->taginstancecontextid);
                } else {
                    $this->assertEquals($siblingcoursecontext->id, $tag->taginstancecontextid);
                }
            }
        }
    }

    /**
     * question_sort_tags() should filter the course tags by the given list of courses.
     */
    public function test_question_sort_tags_includes_filter_course_tags() {
        global $DB;

        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions('category');
        $question1 = $questions[0];
        $question2 = $questions[1];
        $coursecontext = context_course::instance($course->id);
        // Create a sibling course.
        $siblingcourse = $this->getDataGenerator()->create_course(['category' => $course->category]);
        $siblingcoursecontext = context_course::instance($siblingcourse->id);

        // Create course tags.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['foo']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['bar']);
        // Create sibling course tags. These should be filtered out.
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $siblingcoursecontext, ['filtered1']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $siblingcoursecontext, ['filtered2']);

        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            $tagobjects = question_sort_tags($tags, $qcat, [$course]);
            foreach ($tagobjects->coursetagobjects as $tag) {

                // We should only be seeing course tags from $course. The tags from
                // $siblingcourse should have been filtered out.
                $this->assertEquals($coursecontext->id, $tag->taginstancecontextid);
            }
        }
    }

    /**
     * Data provider for tests of question_has_capability_on_context and question_require_capability_on_context.
     *
     * @return  array
     */
    public function question_capability_on_question_provider() {
        return [
            'Unrelated capability which is present' => [
                'capabilities' => [
                    'moodle/question:config' => CAP_ALLOW,
                ],
                'testcapability' => 'config',
                'isowner' => true,
                'expect' => true,
            ],
            'Unrelated capability which is present (not owner)' => [
                'capabilities' => [
                    'moodle/question:config' => CAP_ALLOW,
                ],
                'testcapability' => 'config',
                'isowner' => false,
                'expect' => true,
            ],
            'Unrelated capability which is not set' => [
                'capabilities' => [
                ],
                'testcapability' => 'config',
                'isowner' => true,
                'expect' => false,
            ],
            'Unrelated capability which is not set (not owner)' => [
                'capabilities' => [
                ],
                'testcapability' => 'config',
                'isowner' => false,
                'expect' => false,
            ],
            'Unrelated capability which is prevented' => [
                'capabilities' => [
                    'moodle/question:config' => CAP_PREVENT,
                ],
                'testcapability' => 'config',
                'isowner' => true,
                'expect' => false,
            ],
            'Unrelated capability which is prevented (not owner)' => [
                'capabilities' => [
                    'moodle/question:config' => CAP_PREVENT,
                ],
                'testcapability' => 'config',
                'isowner' => false,
                'expect' => false,
            ],
            'Related capability which is not set' => [
                'capabilities' => [
                ],
                'testcapability' => 'edit',
                'isowner' => true,
                'expect' => false,
            ],
            'Related capability which is not set (not owner)' => [
                'capabilities' => [
                ],
                'testcapability' => 'edit',
                'isowner' => false,
                'expect' => false,
            ],
            'Related capability which is allowed at all, unset at mine' => [
                'capabilities' => [
                    'moodle/question:editall' => CAP_ALLOW,
                ],
                'testcapability' => 'edit',
                'isowner' => true,
                'expect' => true,
            ],
            'Related capability which is allowed at all, unset at mine (not owner)' => [
                'capabilities' => [
                    'moodle/question:editall' => CAP_ALLOW,
                ],
                'testcapability' => 'edit',
                'isowner' => false,
                'expect' => true,
            ],
            'Related capability which is allowed at all, prevented at mine' => [
                'capabilities' => [
                    'moodle/question:editall' => CAP_ALLOW,
                    'moodle/question:editmine' => CAP_PREVENT,
                ],
                'testcapability' => 'edit',
                'isowner' => true,
                'expect' => true,
            ],
            'Related capability which is allowed at all, prevented at mine (not owner)' => [
                'capabilities' => [
                    'moodle/question:editall' => CAP_ALLOW,
                    'moodle/question:editmine' => CAP_PREVENT,
                ],
                'testcapability' => 'edit',
                'isowner' => false,
                'expect' => true,
            ],
            'Related capability which is unset all, allowed at mine' => [
                'capabilities' => [
                    'moodle/question:editall' => CAP_PREVENT,
                    'moodle/question:editmine' => CAP_ALLOW,
                ],
                'testcapability' => 'edit',
                'isowner' => true,
                'expect' => true,
            ],
            'Related capability which is unset all, allowed at mine (not owner)' => [
                'capabilities' => [
                    'moodle/question:editall' => CAP_PREVENT,
                    'moodle/question:editmine' => CAP_ALLOW,
                ],
                'testcapability' => 'edit',
                'isowner' => false,
                'expect' => false,
            ],
        ];
    }

    /**
     * Tests that question_has_capability_on does not throw exception on broken questions.
     */
    public function test_question_has_capability_on_broken_question() {
        global $DB;

        // Create the test data.
        $generator = $this->getDataGenerator();
        $questiongenerator = $generator->get_plugin_generator('core_question');

        $category = $generator->create_category();
        $context = context_coursecat::instance($category->id);
        $questioncat = $questiongenerator->create_question_category([
            'contextid' => $context->id,
        ]);

        // Create a cloze question.
        $question = $questiongenerator->create_question('multianswer', null, [
            'category' => $questioncat->id,
        ]);
        // Now, break the question.
        $DB->delete_records('question_multianswer', ['question' => $question->id]);

        $this->setAdminUser();

        $result = question_has_capability_on($question->id, 'tag');
        $this->assertTrue($result);

        $this->assertDebuggingCalled();
    }

    /**
     * Tests for the deprecated question_has_capability_on function when passing a stdClass as parameter.
     *
     * @dataProvider question_capability_on_question_provider
     * @param   array   $capabilities The capability assignments to set.
     * @param   string  $capability The capability to test
     * @param   bool    $isowner Whether the user to create the question should be the owner or not.
     * @param   bool    $expect The expected result.
     */
    public function test_question_has_capability_on_using_stdclass($capabilities, $capability, $isowner, $expect) {
        $this->resetAfterTest();

        // Create the test data.
        $user = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();
        $roleid = $this->getDataGenerator()->create_role();
        $category = $this->getDataGenerator()->create_category();
        $context = context_coursecat::instance($category->id);

        // Assign the user to the role.
        role_assign($roleid, $user->id, $context->id);

        // Assign the capabilities to the role.
        foreach ($capabilities as $capname => $capvalue) {
            assign_capability($capname, $capvalue, $roleid, $context->id);
        }

        $this->setUser($user);

        // The current fake question we make use of is always a stdClass and typically has no ID.
        $fakequestion = (object) [
            'contextid' => $context->id,
        ];

        if ($isowner) {
            $fakequestion->createdby = $user->id;
        } else {
            $fakequestion->createdby = $otheruser->id;
        }

        $result = question_has_capability_on($fakequestion, $capability);
        $this->assertEquals($expect, $result);
    }

    /**
     * Tests for the deprecated question_has_capability_on function when using question definition.
     *
     * @dataProvider question_capability_on_question_provider
     * @param   array   $capabilities The capability assignments to set.
     * @param   string  $capability The capability to test
     * @param   bool    $isowner Whether the user to create the question should be the owner or not.
     * @param   bool    $expect The expected result.
     */
    public function test_question_has_capability_on_using_question_definition($capabilities, $capability, $isowner, $expect) {
        $this->resetAfterTest();

        // Create the test data.
        $generator = $this->getDataGenerator();
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $user = $generator->create_user();
        $otheruser = $generator->create_user();
        $roleid = $generator->create_role();
        $category = $generator->create_category();
        $context = context_coursecat::instance($category->id);
        $questioncat = $questiongenerator->create_question_category([
            'contextid' => $context->id,
        ]);

        // Assign the user to the role.
        role_assign($roleid, $user->id, $context->id);

        // Assign the capabilities to the role.
        foreach ($capabilities as $capname => $capvalue) {
            assign_capability($capname, $capvalue, $roleid, $context->id);
        }

        // Create the question.
        $qtype = 'truefalse';
        $overrides = [
            'category' => $questioncat->id,
        ];

        $question = $questiongenerator->create_question($qtype, null, $overrides);

        // The question generator does not support setting of the createdby for some reason.
        $question->createdby = ($isowner) ? $user->id : $otheruser->id;
        $fromform = test_question_maker::get_question_form_data($qtype, null);
        $fromform = (object) $generator->combine_defaults_and_record((array) $fromform, $overrides);
        question_bank::get_qtype($qtype)->save_question($question, $fromform);

        $this->setUser($user);
        $result = question_has_capability_on($question, $capability);
        $this->assertEquals($expect, $result);
    }

    /**
     * Tests for the deprecated question_has_capability_on function when using a real question id.
     *
     * @dataProvider question_capability_on_question_provider
     * @param   array   $capabilities The capability assignments to set.
     * @param   string  $capability The capability to test
     * @param   bool    $isowner Whether the user to create the question should be the owner or not.
     * @param   bool    $expect The expected result.
     */
    public function test_question_has_capability_on_using_question_id($capabilities, $capability, $isowner, $expect) {
        $this->resetAfterTest();

        // Create the test data.
        $generator = $this->getDataGenerator();
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $user = $generator->create_user();
        $otheruser = $generator->create_user();
        $roleid = $generator->create_role();
        $category = $generator->create_category();
        $context = context_coursecat::instance($category->id);
        $questioncat = $questiongenerator->create_question_category([
            'contextid' => $context->id,
        ]);

        // Assign the user to the role.
        role_assign($roleid, $user->id, $context->id);

        // Assign the capabilities to the role.
        foreach ($capabilities as $capname => $capvalue) {
            assign_capability($capname, $capvalue, $roleid, $context->id);
        }

        // Create the question.
        $qtype = 'truefalse';
        $overrides = [
            'category' => $questioncat->id,
        ];

        $question = $questiongenerator->create_question($qtype, null, $overrides);

        // The question generator does not support setting of the createdby for some reason.
        $question->createdby = ($isowner) ? $user->id : $otheruser->id;
        $fromform = test_question_maker::get_question_form_data($qtype, null);
        $fromform = (object) $generator->combine_defaults_and_record((array) $fromform, $overrides);
        question_bank::get_qtype($qtype)->save_question($question, $fromform);

        $this->setUser($user);
        $result = question_has_capability_on($question->id, $capability);
        $this->assertEquals($expect, $result);
    }

    /**
     * Tests for the deprecated question_has_capability_on function when using a string as question id.
     *
     * @dataProvider question_capability_on_question_provider
     * @param   array   $capabilities The capability assignments to set.
     * @param   string  $capability The capability to test
     * @param   bool    $isowner Whether the user to create the question should be the owner or not.
     * @param   bool    $expect The expected result.
     */
    public function test_question_has_capability_on_using_question_string_id($capabilities, $capability, $isowner, $expect) {
        $this->resetAfterTest();

        // Create the test data.
        $generator = $this->getDataGenerator();
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $user = $generator->create_user();
        $otheruser = $generator->create_user();
        $roleid = $generator->create_role();
        $category = $generator->create_category();
        $context = context_coursecat::instance($category->id);
        $questioncat = $questiongenerator->create_question_category([
            'contextid' => $context->id,
        ]);

        // Assign the user to the role.
        role_assign($roleid, $user->id, $context->id);

        // Assign the capabilities to the role.
        foreach ($capabilities as $capname => $capvalue) {
            assign_capability($capname, $capvalue, $roleid, $context->id);
        }

        // Create the question.
        $qtype = 'truefalse';
        $overrides = [
            'category' => $questioncat->id,
        ];

        $question = $questiongenerator->create_question($qtype, null, $overrides);

        // The question generator does not support setting of the createdby for some reason.
        $question->createdby = ($isowner) ? $user->id : $otheruser->id;
        $fromform = test_question_maker::get_question_form_data($qtype, null);
        $fromform = (object) $generator->combine_defaults_and_record((array) $fromform, $overrides);
        question_bank::get_qtype($qtype)->save_question($question, $fromform);

        $this->setUser($user);
        $result = question_has_capability_on((string) $question->id, $capability);
        $this->assertEquals($expect, $result);
    }

    /**
     * Tests for the question_has_capability_on function when using a moved question.
     *
     * @dataProvider question_capability_on_question_provider
     * @param   array   $capabilities The capability assignments to set.
     * @param   string  $capability The capability to test
     * @param   bool    $isowner Whether the user to create the question should be the owner or not.
     * @param   bool    $expect The expected result.
     */
    public function test_question_has_capability_on_using_moved_question($capabilities, $capability, $isowner, $expect) {
        $this->resetAfterTest();

        // Create the test data.
        $generator = $this->getDataGenerator();
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $user = $generator->create_user();
        $otheruser = $generator->create_user();
        $roleid = $generator->create_role();
        $category = $generator->create_category();
        $context = context_coursecat::instance($category->id);
        $questioncat = $questiongenerator->create_question_category([
            'contextid' => $context->id,
        ]);

        $newcategory = $generator->create_category();
        $newcontext = context_coursecat::instance($newcategory->id);
        $newquestioncat = $questiongenerator->create_question_category([
            'contextid' => $newcontext->id,
        ]);

        // Assign the user to the role in the _new_ context..
        role_assign($roleid, $user->id, $newcontext->id);

        // Assign the capabilities to the role in the _new_ context.
        foreach ($capabilities as $capname => $capvalue) {
            assign_capability($capname, $capvalue, $roleid, $newcontext->id);
        }

        // Create the question.
        $qtype = 'truefalse';
        $overrides = [
            'category' => $questioncat->id,
        ];

        $question = $questiongenerator->create_question($qtype, null, $overrides);

        // The question generator does not support setting of the createdby for some reason.
        $question->createdby = ($isowner) ? $user->id : $otheruser->id;
        $fromform = test_question_maker::get_question_form_data($qtype, null);
        $fromform = (object) $generator->combine_defaults_and_record((array) $fromform, $overrides);
        question_bank::get_qtype($qtype)->save_question($question, $fromform);

        // Move the question.
        question_move_questions_to_category([$question->id], $newquestioncat->id);

        // Test that the capability is correct after the question has been moved.
        $this->setUser($user);
        $result = question_has_capability_on($question->id, $capability);
        $this->assertEquals($expect, $result);
    }

    /**
     * Tests for the question_has_capability_on function when using a real question.
     *
     * @dataProvider question_capability_on_question_provider
     * @param   array   $capabilities The capability assignments to set.
     * @param   string  $capability The capability to test
     * @param   bool    $isowner Whether the user to create the question should be the owner or not.
     * @param   bool    $expect The expected result.
     */
    public function test_question_has_capability_on_using_question($capabilities, $capability, $isowner, $expect) {
        $this->resetAfterTest();

        // Create the test data.
        $generator = $this->getDataGenerator();
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $user = $generator->create_user();
        $otheruser = $generator->create_user();
        $roleid = $generator->create_role();
        $category = $generator->create_category();
        $context = context_coursecat::instance($category->id);
        $questioncat = $questiongenerator->create_question_category([
            'contextid' => $context->id,
        ]);

        // Assign the user to the role.
        role_assign($roleid, $user->id, $context->id);

        // Assign the capabilities to the role.
        foreach ($capabilities as $capname => $capvalue) {
            assign_capability($capname, $capvalue, $roleid, $context->id);
        }

        // Create the question.
        $question = $questiongenerator->create_question('truefalse', null, [
            'category' => $questioncat->id,
        ]);
        $question = question_bank::load_question_data($question->id);

        // The question generator does not support setting of the createdby for some reason.
        $question->createdby = ($isowner) ? $user->id : $otheruser->id;

        $this->setUser($user);
        $result = question_has_capability_on($question, $capability);
        $this->assertEquals($expect, $result);
    }

    /**
     * Tests that question_has_capability_on throws an exception for wrong parameter types.
     */
    public function test_question_has_capability_on_wrong_param_type() {
        // Create the test data.
        $generator = $this->getDataGenerator();
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $user = $generator->create_user();

        $category = $generator->create_category();
        $context = context_coursecat::instance($category->id);
        $questioncat = $questiongenerator->create_question_category([
            'contextid' => $context->id,
        ]);

        // Create the question.
        $question = $questiongenerator->create_question('truefalse', null, [
            'category' => $questioncat->id,
        ]);
        $question = question_bank::load_question_data($question->id);

        // The question generator does not support setting of the createdby for some reason.
        $question->createdby = $user->id;

        $this->setUser($user);
        $result = question_has_capability_on((string)$question->id, 'tag');
        $this->assertFalse($result);

        $this->expectException('coding_exception');
        $this->expectExceptionMessage('$questionorid parameter needs to be an integer or an object.');
        question_has_capability_on('one', 'tag');
    }

    /**
     * Test of question_categorylist_parents function.
     */
    public function test_question_categorylist_parents() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $category = $generator->create_category();
        $context = context_coursecat::instance($category->id);
        // Create a top category.
        $cat0 = question_get_top_category($context->id, true);
        // Add sub-categories.
        $cat1 = $questiongenerator->create_question_category(['parent' => $cat0->id]);
        $cat2 = $questiongenerator->create_question_category(['parent' => $cat1->id]);
        // Test the 'get parents' function.
        $parentcategories = question_categorylist_parents($cat2->id);
        $this->assertEquals($cat0->id, $parentcategories[0]);
        $this->assertEquals($cat1->id, $parentcategories[1]);
        $this->assertCount(2, $parentcategories);
    }

    public function test_question_get_export_single_question_url() {
        $generator = $this->getDataGenerator();

        // Create a course and an activity.
        $course = $generator->create_course();
        $quiz = $generator->create_module('quiz', ['course' => $course->id]);

        // Create a question in each place.
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $courseqcat = $questiongenerator->create_question_category(['contextid' => context_course::instance($course->id)->id]);
        $courseq = $questiongenerator->create_question('truefalse', null, ['category' => $courseqcat->id]);
        $quizqcat = $questiongenerator->create_question_category(['contextid' => context_module::instance($quiz->cmid)->id]);
        $quizq = $questiongenerator->create_question('truefalse', null, ['category' => $quizqcat->id]);
        $systemqcat = $questiongenerator->create_question_category();
        $systemq = $questiongenerator->create_question('truefalse', null, ['category' => $systemqcat->id]);

        // Verify some URLs.
        $this->assertEquals(new moodle_url('/question/exportone.php',
                ['id' => $courseq->id, 'courseid' => $course->id, 'sesskey' => sesskey()]),
                question_get_export_single_question_url(question_bank::load_question_data($courseq->id)));

        $this->assertEquals(new moodle_url('/question/exportone.php',
                ['id' => $quizq->id, 'cmid' => $quiz->cmid, 'sesskey' => sesskey()]),
                question_get_export_single_question_url(question_bank::load_question($quizq->id)));

        $this->assertEquals(new moodle_url('/question/exportone.php',
                ['id' => $systemq->id, 'courseid' => SITEID, 'sesskey' => sesskey()]),
                question_get_export_single_question_url(question_bank::load_question($systemq->id)));
    }
}
