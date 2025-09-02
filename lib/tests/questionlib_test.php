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

namespace core;

use core_question\local\bank\question_bank_helper;
use mod_quiz\quiz_settings;
use question_bank;

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
 * @package    core
 * @category   test
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class questionlib_test extends \advanced_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Generate a course and question bank module instance for use in test cases, and return the bank context.
     * @return \core\context\module
     */
    protected function create_course_and_question_bank(): \core\context\module {
        $course = self::getDataGenerator()->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        return \context_module::instance($qbank->cmid);
    }

    /**
     * Setup a course, a quiz, a question category and a question for testing.
     *
     * @return array The created data objects
     */
    public function setup_quiz_and_questions() {
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

        /** @var \core_question_generator $qgen */
        $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');

        $context = \context_module::instance($quiz->cmid);

        $qcat = $qgen->create_question_category(array('contextid' => $context->id));

        $questions = array(
                $qgen->create_question('shortanswer', null, array('category' => $qcat->id)),
                $qgen->create_question('shortanswer', null, array('category' => $qcat->id)),
        );

        quiz_add_quiz_question($questions[0]->id, $quiz);

        return array($category, $course, $quiz, $qcat, $questions);
    }

    /**
     * Assert that a category contains a specific number of questions.
     *
     * @param int $categoryid int Category id.
     * @param int $numberofquestions Number of question in a category.
     * @return void Questions in a category.
     */
    protected function assert_category_contains_questions(int $categoryid, int $numberofquestions): void {
        $questionsid = question_bank::get_finder()->get_questions_from_categories([$categoryid], null);
        $this->assertEquals($numberofquestions, count($questionsid));
    }

    public function test_question_reorder_qtypes(): void {
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

    public function test_match_grade_options(): void {
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
    public function test_altering_tag_instance_context(): void {
        global $CFG, $DB;

        // Set to admin user.
        $this->setAdminUser();

        // Create 2 qbank instances - we are going to delete one of these later and will expect
        // all the questions belonging to the deleted module to be moved.
        $coursecat1 = $this->getDataGenerator()->create_category();
        $course1 = $this->getDataGenerator()->create_course(['category' => $coursecat1->id]);
        $modqbank1 = $this->getDataGenerator()->create_module('qbank', ['course' => $course1->id]);
        $coursecat2 = $this->getDataGenerator()->create_category();
        $course2 = $this->getDataGenerator()->create_course(['category' => $coursecat2->id]);
        $modqbank2 = $this->getDataGenerator()->create_module('qbank', ['course' => $course2->id]);

        // Create a couple of categories and questions.
        $context1 = \context_module::instance($modqbank1->cmid);
        $context2 = \context_module::instance($modqbank2->cmid);
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $questioncat1 = question_get_default_category($context1->id);
        $questioncat2 = question_get_default_category($context2->id);
        $question1 = $questiongenerator->create_question('shortanswer', null, ['category' => $questioncat1->id]);
        $question2 = $questiongenerator->create_question('shortanswer', null, ['category' => $questioncat1->id]);
        $question3 = $questiongenerator->create_question('shortanswer', null, ['category' => $questioncat2->id]);
        $question4 = $questiongenerator->create_question('shortanswer', null, ['category' => $questioncat2->id]);

        // Now lets tag these questions.
        \core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $context1, ['tag 1', 'tag 2']);
        \core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $context1, ['tag 3', 'tag 4']);
        \core_tag_tag::set_item_tags('core_question', 'question', $question3->id, $context2, ['tag 5', 'tag 6']);
        \core_tag_tag::set_item_tags('core_question', 'question', $question4->id, $context2, ['tag 7', 'tag 8']);

        // Test moving a whole question category to another context.
        question_move_category_to_context($questioncat1->id, $questioncat1->contextid, $questioncat2->contextid);

        // Test that all tag_instances belong to one context.
        $this->assertEquals(8, $DB->count_records('tag_instance', ['component' => 'core_question',
            'contextid' => $questioncat2->contextid]));

        // Now test moving them back.
        question_move_category_to_context($questioncat1->id, $questioncat2->contextid,
            \context_module::instance($modqbank1->cmid)->id);

        // Test that all tag_instances are now reset to how they were initially.
        $this->assertEquals(4, $DB->count_records('tag_instance', ['component' => 'core_question',
            'contextid' => $questioncat1->contextid]));
        $this->assertEquals(4, $DB->count_records('tag_instance', ['component' => 'core_question',
            'contextid' => $questioncat2->contextid]));

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        $modqbank3 = $this->getDataGenerator()->create_module('qbank', ['course' => $course->id]);

        // Create some question categories and questions in this course.
        $modcontext = \context_module::instance($modqbank3->cmid);
        $questioncat = question_get_default_category($modcontext->id);
        $question1 = $questiongenerator->create_question('shortanswer', null, ['category' => $questioncat->id]);
        $question2 = $questiongenerator->create_question('shortanswer', null, ['category' => $questioncat->id]);

        // Add some tags to these questions.
        \core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $modcontext, ['tag 1', 'tag 2']);
        \core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $modcontext, ['tag 1', 'tag 2']);

        // Create a course that we are going to restore the other course to.
        $course2 = $this->getDataGenerator()->create_course();

        // Create backup file and save it to the backup location.
        $bc = new \backup_controller(\backup::TYPE_1COURSE, $course->id, \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO, \backup::MODE_GENERAL, 2);
        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];
        $fp = get_file_packer('application/vnd.moodle.backup');
        $filepath = $CFG->dataroot . '/temp/backup/test-restore-course';
        $file->extract_to_pathname($fp, $filepath);
        $bc->destroy();

        // Now restore the course.
        $rc = new \restore_controller('test-restore-course', $course2->id, \backup::INTERACTIVE_NO,
            \backup::MODE_GENERAL, 2, \backup::TARGET_NEW_COURSE);
        $rc->execute_precheck();
        $rc->execute_plan();

        $modinfo = get_fast_modinfo($course2);
        $qbanks = $modinfo->get_instances_of('qbank');
        $qbankids = array_column($qbanks, 'instance');
        $qbankrecords = $DB->get_records_list('qbank', 'id', $qbankids, '', 'id, type');
        $qbanks = array_filter($qbanks, static function($bank) use ($qbankrecords) {
            if (isset($qbankrecords[$bank->instance])) {
                return $qbankrecords[$bank->instance]->type === question_bank_helper::TYPE_STANDARD;
            }
            return false;
        });
        $qbank = reset($qbanks);

        // Get the created question category.
        $restoredcategory = $DB->get_record_select('question_categories', 'contextid = ? AND parent <> 0',
                [$qbank->context->id, '*', MUST_EXIST]);

        // Check that there are two questions in the restored to course's context.
        $this->assertEquals(2, $DB->get_record_sql('SELECT COUNT(q.id) as questioncount
                                                               FROM {question} q
                                                               JOIN {question_versions} qv
                                                                 ON qv.questionid = q.id
                                                               JOIN {question_bank_entries} qbe
                                                                 ON qbe.id = qv.questionbankentryid
                                                              WHERE qbe.questioncategoryid = ?',
            [$restoredcategory->id])->questioncount);
        $rc->destroy();
    }

    /**
     * Test that deleting a question from the question bank works in the normal case.
     */
    public function test_question_delete_question(): void {
        global $DB;

        // Setup.
        $context = $this->create_course_and_question_bank();
        /** @var \core_question_generator $qgen */
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
    public function test_question_delete_question_broken_data(): void {
        global $DB;

        // Setup.
        $context = $this->create_course_and_question_bank();
        /** @var \core_question_generator $qgen */
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
     * Test deleting a broken question whose category refers to a missing context
     */
    public function test_question_delete_question_missing_context(): void {
        global $DB;

        $context = $this->create_course_and_question_bank();

        /** @var \core_question_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $questioncategory = $generator->create_question_category(['contextid' => $context->id]);
        $question = $generator->create_question('shortanswer', null, ['category' => $questioncategory->id]);

        // Now delete the context, to simulate what happens in old sites where
        // referential integrity has failed.
        $DB->delete_records('context', ['id' => $context->id]);

        question_delete_question($question->id);

        $this->assertDebuggingCalled('Deleting question ' . $question->id .
            ' which is no longer linked to a context. Assuming system context ' .
            'to avoid errors, but this may mean that some data like ' .
            'files, tags, are not cleaned up.');
        $this->assertFalse($DB->record_exists('question', ['id' => $question->id]));
    }

    /**
     * Test parameters for calling question_category_delete_safe
     *
     * @return array
     */
    public static function delete_category_parameters(): array {
        return [
            'Delete category' => [
                'coursedeletion' => false,
            ],
            'Delete category with course' => [
                'coursedeletion' => true,
            ],
        ];
    }

    /**
     * This function tests the question_category_delete_safe function.
     *
     * @param bool $coursedeletion If true, simulate calling question_category_delete_safe as part of deletion of the whole course.
     * @dataProvider delete_category_parameters
     * @covers ::question_category_delete_safe
     */
    public function test_question_category_delete_safe(bool $coursedeletion): void {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        [, $course, , $qcat, $questions] = $this->setup_quiz_and_questions();

        $targetcourseid = $coursedeletion ? SITEID : $course->id;

        question_category_delete_safe($qcat, $coursedeletion);

        // Verify category deleted.
        $criteria = ['id' => $qcat->id];
        $this->assertEquals(0, $DB->count_records('question_categories', $criteria));

        // Verify questions deleted or moved.
        $this->assert_category_contains_questions($qcat->id, 0);

        // Verify question not deleted.
        $criteria = ['id' => $questions[0]->id];
        $savedquestion = $DB->get_record_sql(
            "SELECT q.*, qbe.questioncategoryid
               FROM {question} q
                    JOIN {question_versions} qv ON qv.questionid = q.id
                    JOIN {question_bank_entries} qbe ON qv.questionbankentryid = qbe.id",
            $criteria
        );
        $this->assertNotEmpty($savedquestion);

        // Verify question now sits in a system qbank in the target course.
        $this->assertNotEquals($qcat->id, $savedquestion->id);
        $newcategory = $DB->get_record('question_categories', ['id' => $savedquestion->questioncategoryid], strictness: MUST_EXIST);
        $newcategorycontext = context::instance_by_id($newcategory->contextid);
        $this->assertEquals(\context_module::LEVEL, $newcategorycontext->contextlevel);
        [$newcourse, $newcm] = get_course_and_cm_from_cmid($newcategorycontext->instanceid);
        $this->assertEquals($newcm->modname, 'qbank');
        $this->assertEquals(question_bank_helper::TYPE_SYSTEM, $DB->get_field('qbank', 'type', ['id' => $newcm->instance]));
        $this->assertEquals($targetcourseid, $newcourse->id);
    }

    /**
     * This function tests the question_delete_activity function.
     */
    public function test_question_delete_activity(): void {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions();

        $cm = get_coursemodule_from_instance('quiz', $quiz->id);

        // Test the deletion.
        question_delete_activity($cm);

        // Verify category deleted.
        $criteria = array('id' => $qcat->id);
        $this->assertEquals(0, $DB->count_records('question_categories', $criteria));

        // Verify questions deleted or moved.
        $this->assert_category_contains_questions($qcat->id, 0);
    }

    /**
     * This function tests the question_delete_context function.
     *
     * @covers ::question_delete_context()
     */
    public function test_question_delete_context(): void {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions();

        // Get the module context id.
        $result = question_delete_context($qcat->contextid);

        // Verify category deleted.
        $criteria = ['id' => $qcat->id];
        $this->assertEquals(0, $DB->count_records('question_categories', $criteria));

        // Verify questions deleted or moved.
        $this->assert_category_contains_questions($qcat->id, 0);
    }

    /**
     * This function tests the question_save_from_deletion function when it is supposed to make a new category and
     * move question categories to that new category.
     *
     * @covers ::question_save_from_deletion()
     */
    public function test_question_save_from_deletion(): void {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        [$category, $course, $quiz, $qcat, $questions] = $this->setup_quiz_and_questions();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $qbankcontext = \context_module::instance($qbank->cmid);

        $context = \context::instance_by_id($qcat->contextid);

        $newcat = question_save_from_deletion(array_column($questions, 'id'), $qbankcontext->id, $context->get_context_name());

        // Verify that the newcat itself is not a tep level category.
        $this->assertNotEquals(0, $newcat->parent);

        // Verify there is just a single top-level category.
        $this->assertEquals(1, $DB->count_records('question_categories', ['contextid' => $qcat->contextid, 'parent' => 0]));
    }

    /**
     * This function tests the question_save_from_deletion function when it is supposed to make a new category and
     * move question categories to that new category when quiz name is very long but less than 256 characters.
     *
     * @covers ::question_save_from_deletion()
     */
    public function test_question_save_from_deletion_quiz_with_long_name(): void {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        [$category, $course, $quiz, $qcat, $questions] = $this->setup_quiz_and_questions();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $qbankcontext = \context_module::instance($qbank->cmid);

        // Moodle doesn't allow you to enter a name longer than 255 characters.
        $quiz->name = shorten_text(str_repeat('123456789 ', 26), 255);

        $DB->update_record('quiz', $quiz);

        $context = \context::instance_by_id($qcat->contextid);

        $newcat = question_save_from_deletion(array_column($questions, 'id'), $qbankcontext->id, $context->get_context_name());

        // Verifying that the inserted record's name is expected or not.
        $this->assertEquals($DB->get_record('question_categories', ['id' => $newcat->id])->name, $newcat->name);

        // Verify that the newcat itself is not a top level category.
        $this->assertNotEquals(0, $newcat->parent);

        // Verify there is just a single top-level category.
        $this->assertEquals(1, $DB->count_records('question_categories', ['contextid' => $qcat->contextid, 'parent' => 0]));
    }

    /**
     * get_question_options should add the category object to the given question.
     *
     * @covers ::get_question_options()
     */
    public function test_get_question_options_includes_category_object_single_question(): void {
        [$category, $course, $quiz, $qcat, $questions] = $this->setup_quiz_and_questions();
        $question = array_shift($questions);

        get_question_options($question);

        $this->assertEquals($qcat, $question->categoryobject);
    }

    /**
     * get_question_options should add the category object to all of the questions in
     * the given list.
     *
     * @covers ::get_question_options()
     */
    public function test_get_question_options_includes_category_object_multiple_questions(): void {
        [$category, $course, $quiz, $qcat, $questions] = $this->setup_quiz_and_questions();

        get_question_options($questions);

        foreach ($questions as $question) {
            $this->assertEquals($qcat, $question->categoryobject);
        }
    }

    /**
     * get_question_options includes the tags for all questions in the list.
     *
     * @covers ::get_question_options()
     */
    public function test_get_question_options_includes_question_tags(): void {
        [$category, $course, $quiz, $qcat, $questions] = $this->setup_quiz_and_questions();
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = \context::instance_by_id($qcat->contextid);

        \core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo', 'bar']);
        \core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['baz', 'bop']);

        get_question_options($questions, true);

        foreach ($questions as $question) {
            $tags = \core_tag_tag::get_item_tags('core_question', 'question', $question->id);
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
        }
    }

    /**
     * get_question_options should update the context id to the question category
     * context id for any non-course context tag that isn't in the question category
     * context.
     *
     * @covers ::get_question_options()
     */
    public function test_get_question_options_normalises_question_tags(): void {
        list($category, $course, $quiz, $qcat, $questions) = $this->setup_quiz_and_questions();
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = \context::instance_by_id($qcat->contextid);
        $systemcontext = \context_system::instance();

        \core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo', 'bar']);
        \core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['baz', 'bop']);

        $q1tags = \core_tag_tag::get_item_tags('core_question', 'question', $question1->id);
        $q2tags = \core_tag_tag::get_item_tags('core_question', 'question', $question2->id);
        $q1tag = array_shift($q1tags);
        $q2tag = array_shift($q2tags);

        // Change two of the tag instances to be a different (non-course) context to the
        // question tag context. These tags should then be normalised back to the question
        // tag context.
        \core_tag_tag::change_instances_context([$q1tag->taginstanceid, $q2tag->taginstanceid], $systemcontext);

        get_question_options($questions, true);

        foreach ($questions as $question) {
            $tags = \core_tag_tag::get_item_tags('core_question', 'question', $question->id);

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
     * When moving all tags from one activity context into another activity context.
     *
     * @covers ::question_move_question_tags_to_new_context()
     */
    public function test_question_move_question_tags_to_new_context_activity_to_activity_qtags(): void {
        [$category, $course, $quiz, $qcat, $questions] = $this->setup_quiz_and_questions();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = \context::instance_by_id($qcat->contextid);
        $newcontext = \context_module::instance($qbank->cmid);

        foreach ($questions as $question) {
            $question->contextid = $qcat->contextid;
        }

        // Create tags in the quiz context.
        \core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo']);
        \core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['foo']);

        question_move_question_tags_to_new_context($questions, $newcontext);

        foreach ($questions as $question) {
            $tags = \core_tag_tag::get_item_tags('core_question', 'question', $question->id);

            foreach ($tags as $tag) {
                $this->assertEquals($newcontext->id, $tag->taginstancecontextid);
            }
        }
    }

    /**
     * question_sort_tags() includes the tags for all questions in the list.
     */
    public function test_question_sort_tags_includes_question_tags(): void {

        [$category, $course, $quiz, $qcat, $questions] = $this->setup_quiz_and_questions();
        $question1 = $questions[0];
        $question2 = $questions[1];
        $qcontext = \context::instance_by_id($qcat->contextid);

        \core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['foo', 'bar']);
        \core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['baz', 'bop']);

        foreach ($questions as $question) {
            $tags = \core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            $categorycontext = \context::instance_by_id($qcat->contextid);
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
        }
    }

    /**
     * Data provider for tests of question_has_capability_on_context and question_require_capability_on_context.
     *
     * @return  array
     */
    public static function question_capability_on_question_provider(): array {
        return [
            'Unrelated capability which is present' => [
                'capabilities' => [
                    'moodle/question:config' => CAP_ALLOW,
                ],
                'capability' => 'config',
                'isowner' => true,
                'expect' => true,
            ],
            'Unrelated capability which is present (not owner)' => [
                'capabilities' => [
                    'moodle/question:config' => CAP_ALLOW,
                ],
                'capability' => 'config',
                'isowner' => false,
                'expect' => true,
            ],
            'Unrelated capability which is not set' => [
                'capabilities' => [
                ],
                'capability' => 'config',
                'isowner' => true,
                'expect' => false,
            ],
            'Unrelated capability which is not set (not owner)' => [
                'capabilities' => [
                ],
                'capability' => 'config',
                'isowner' => false,
                'expect' => false,
            ],
            'Unrelated capability which is prevented' => [
                'capabilities' => [
                    'moodle/question:config' => CAP_PREVENT,
                ],
                'capability' => 'config',
                'isowner' => true,
                'expect' => false,
            ],
            'Unrelated capability which is prevented (not owner)' => [
                'capabilities' => [
                    'moodle/question:config' => CAP_PREVENT,
                ],
                'capability' => 'config',
                'isowner' => false,
                'expect' => false,
            ],
            'Related capability which is not set' => [
                'capabilities' => [
                ],
                'capability' => 'edit',
                'isowner' => true,
                'expect' => false,
            ],
            'Related capability which is not set (not owner)' => [
                'capabilities' => [
                ],
                'capability' => 'edit',
                'isowner' => false,
                'expect' => false,
            ],
            'Related capability which is allowed at all, unset at mine' => [
                'capabilities' => [
                    'moodle/question:editall' => CAP_ALLOW,
                ],
                'capability' => 'edit',
                'isowner' => true,
                'expect' => true,
            ],
            'Related capability which is allowed at all, unset at mine (not owner)' => [
                'capabilities' => [
                    'moodle/question:editall' => CAP_ALLOW,
                ],
                'capability' => 'edit',
                'isowner' => false,
                'expect' => true,
            ],
            'Related capability which is allowed at all, prevented at mine' => [
                'capabilities' => [
                    'moodle/question:editall' => CAP_ALLOW,
                    'moodle/question:editmine' => CAP_PREVENT,
                ],
                'capability' => 'edit',
                'isowner' => true,
                'expect' => true,
            ],
            'Related capability which is allowed at all, prevented at mine (not owner)' => [
                'capabilities' => [
                    'moodle/question:editall' => CAP_ALLOW,
                    'moodle/question:editmine' => CAP_PREVENT,
                ],
                'capability' => 'edit',
                'isowner' => false,
                'expect' => true,
            ],
            'Related capability which is unset all, allowed at mine' => [
                'capabilities' => [
                    'moodle/question:editall' => CAP_PREVENT,
                    'moodle/question:editmine' => CAP_ALLOW,
                ],
                'capability' => 'edit',
                'isowner' => true,
                'expect' => true,
            ],
            'Related capability which is unset all, allowed at mine (not owner)' => [
                'capabilities' => [
                    'moodle/question:editall' => CAP_PREVENT,
                    'moodle/question:editmine' => CAP_ALLOW,
                ],
                'capability' => 'edit',
                'isowner' => false,
                'expect' => false,
            ],
        ];
    }

    /**
     * Tests that question_has_capability_on does not throw exception on broken questions.
     */
    public function test_question_has_capability_on_broken_question(): void {
        global $DB;

        // Create the test data.
        $generator = $this->getDataGenerator();
         /** @var \core_question_generator $questiongenerator */
         $questiongenerator = $generator->get_plugin_generator('core_question');

        $context = $this->create_course_and_question_bank();
        $questioncat = $questiongenerator->create_question_category([
            'contextid' => $context->id,
        ]);

        // Create a cloze question.
        $question = $questiongenerator->create_question('ddwtos', null, [
            'category' => $questioncat->id,
        ]);
        // Now, break the question.
        $DB->delete_records('question_ddwtos', ['questionid' => $question->id]);

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
    public function test_question_has_capability_on_using_stdClass($capabilities, $capability, $isowner, $expect): void {
        $this->resetAfterTest();

        // Create the test data.
        $user = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();
        $roleid = $this->getDataGenerator()->create_role();
        $category = $this->getDataGenerator()->create_category();
        $context = \context_coursecat::instance($category->id);

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
    public function test_question_has_capability_on_using_question_definition($capabilities, $capability, $isowner, $expect): void {
        $this->resetAfterTest();

        // Create the test data.
        $generator = $this->getDataGenerator();
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $user = $generator->create_user();
        $otheruser = $generator->create_user();
        $roleid = $generator->create_role();
        $context = $this->create_course_and_question_bank();
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
            'createdby' => ($isowner) ? $user->id : $otheruser->id,
        ];

        $question = $questiongenerator->create_question($qtype, null, $overrides);

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
    public function test_question_has_capability_on_using_question_id($capabilities, $capability, $isowner, $expect): void {
        $this->resetAfterTest();

        // Create the test data.
        $generator = $this->getDataGenerator();
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $user = $generator->create_user();
        $otheruser = $generator->create_user();
        $roleid = $generator->create_role();
        $context = $this->create_course_and_question_bank();
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
            'createdby' => ($isowner) ? $user->id : $otheruser->id,
        ];

        $question = $questiongenerator->create_question($qtype, null, $overrides);

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
    public function test_question_has_capability_on_using_question_string_id($capabilities, $capability, $isowner, $expect): void {
        $this->resetAfterTest();

        // Create the test data.
        $generator = $this->getDataGenerator();
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $user = $generator->create_user();
        $otheruser = $generator->create_user();
        $roleid = $generator->create_role();
        $context = $this->create_course_and_question_bank();
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
            'createdby' => ($isowner) ? $user->id : $otheruser->id,
        ];

        $question = $questiongenerator->create_question($qtype, null, $overrides);

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
    public function test_question_has_capability_on_using_moved_question($capabilities, $capability, $isowner, $expect): void {
        $this->resetAfterTest();

        // Create the test data.
        $generator = $this->getDataGenerator();
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $user = $generator->create_user();
        $otheruser = $generator->create_user();
        $roleid = $generator->create_role();
        $context = $this->create_course_and_question_bank();
        $coursecontext = $context->get_course_context();
        $questioncat = $questiongenerator->create_question_category([
            'contextid' => $context->id,
        ]);

        $qbank2 = $generator->create_module('qbank', ['course' => $coursecontext->instanceid]);
        $newcontext = \context_module::instance($qbank2->cmid);
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
            'createdby' => ($isowner) ? $user->id : $otheruser->id,
        ];

        $question = $questiongenerator->create_question($qtype, null, $overrides);

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
    public function test_question_has_capability_on_using_question($capabilities, $capability, $isowner, $expect): void {
        $this->resetAfterTest();

        // Create the test data.
        $generator = $this->getDataGenerator();
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $user = $generator->create_user();
        $otheruser = $generator->create_user();
        $roleid = $generator->create_role();
        $context = $this->create_course_and_question_bank();
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
            'createdby' => ($isowner) ? $user->id : $otheruser->id,
        ]);
        $question = question_bank::load_question_data($question->id);

        $this->setUser($user);
        $result = question_has_capability_on($question, $capability);
        $this->assertEquals($expect, $result);
    }

    /**
     * Tests that question_has_capability_on throws an exception for wrong parameter types.
     */
    public function test_question_has_capability_on_wrong_param_type(): void {
        // Create the test data.
        $generator = $this->getDataGenerator();
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $user = $generator->create_user();

        $context = $this->create_course_and_question_bank();
        $questioncat = $questiongenerator->create_question_category([
            'contextid' => $context->id,
        ]);

        // Create the question.
        $question = $questiongenerator->create_question('truefalse', null, [
            'category' => $questioncat->id,
            'createdby' => $user->id,
        ]);
        $question = question_bank::load_question_data($question->id);

        $this->setUser($user);
        $result = question_has_capability_on((string)$question->id, 'tag');
        $this->assertFalse($result);

        $this->expectException('coding_exception');
        $this->expectExceptionMessage('$questionorid parameter needs to be an integer or an object.');
        question_has_capability_on('one', 'tag');
    }

    /**
     * Test question_has_capability_on with an invalid question ID
     */
    public function test_question_has_capability_on_invalid_question(): void {
        try {
            question_has_capability_on(42, 'tag');
            $this->fail('Expected exception');
        } catch (\moodle_exception $exception) {
            $this->assertInstanceOf(\dml_missing_record_exception::class, $exception);

            // We also get debugging from initial attempt to load question data.
            $this->assertDebuggingCalled();
        }
    }

    /**
     * Test that question_has_capability_on does not fail when passed an object with a null
     * createdby property.
     */
    public function test_question_has_capability_on_object_with_null_createdby(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $category = $generator->create_category();
        $context = \context_coursecat::instance($category->id);

        $role = $generator->create_role();
        role_assign($role, $user->id, $context->id);
        assign_capability('moodle/question:editmine', CAP_ALLOW, $role, $context->id);

        $this->setUser($user);

        $fakequestion = (object) [
            'contextid' => $context->id,
            'createdby' => null,
        ];

        $this->assertFalse(question_has_capability_on($fakequestion, 'edit'));

        $fakequestion->createdby = $user->id;

        $this->assertTrue(question_has_capability_on($fakequestion, 'edit'));
    }

    /**
     * Test of question_categorylist function.
     *
     * @covers ::question_categorylist()
     */
    public function test_question_categorylist(): void {
        $this->resetAfterTest();

        // Create a category tree.
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        // Create a Course.
        $context = $this->create_course_and_question_bank();

        $top = question_get_top_category($context->id, true);
        $cat1 = question_get_default_category($context->id);
        $sub11 = $questiongenerator->create_question_category(['parent' => $cat1->id]);
        $sub12 = $questiongenerator->create_question_category(['parent' => $cat1->id]);
        $cat2 = $questiongenerator->create_question_category(['parent' => $top->id]);
        $sub22 = $questiongenerator->create_question_category(['parent' => $cat2->id]);

        // Test - returned array has keys and values the same.
        $this->assertEquals([$sub22->id], array_keys(question_categorylist($sub22->id)));
        $this->assertEquals([$sub22->id], array_values(question_categorylist($sub22->id)));
        $this->assertEquals([$cat1->id, $sub11->id, $sub12->id], array_keys(question_categorylist($cat1->id)));
        $this->assertEquals([$cat1->id, $sub11->id, $sub12->id], array_values(question_categorylist($cat1->id)));
        $this->assertEquals([$top->id, $cat1->id, $cat2->id, $sub11->id, $sub12->id, $sub22->id],
                array_keys(question_categorylist($top->id)));
        $this->assertEquals([$top->id, $cat1->id, $cat2->id, $sub11->id, $sub12->id, $sub22->id],
                array_values(question_categorylist($top->id)));
    }

    /**
     * Test of question_categorylist function when there is bad data, with a category pointing to a parent in another context.
     *
     * This is a situation that should never arise (parents and their children should always belong to the same context)
     * but it does, because bugs, so the code should be robust to it.
     *
     * @covers ::question_categorylist()
     */
    public function test_question_categorylist_bad_data(): void {
        $this->resetAfterTest();

        // Create a category tree.
        $course = $this->getDataGenerator()->create_course();
        $qbank1 = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $bank1context = \context_module::instance($qbank1->cmid);
        $qbank2 = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $wrongcontext = \context_module::instance($qbank2->cmid);

        $top = question_get_top_category($bank1context->id, true);
        $cat1 = question_get_default_category($bank1context->id);
        $sub11 = $questiongenerator->create_question_category(['parent' => $cat1->id]);
        $sub12 = $questiongenerator->create_question_category(['parent' => $cat1->id]);
        $cat2 = $questiongenerator->create_question_category(['parent' => $top->id, 'contextid' => $wrongcontext->id]);
        $sub22 = $questiongenerator->create_question_category(['parent' => $cat2->id]);

        // Test - returned array has keys and values the same.
        $this->assertEquals([$cat2->id, $sub22->id], array_keys(question_categorylist($cat2->id)));
        $this->assertEquals([$top->id, $cat1->id, $sub11->id, $sub12->id],
                array_keys(question_categorylist($top->id)));
    }

    /**
     * Test of question_categorylist_parents function.
     *
     * @covers ::question_categorylist_parents()
     */
    public function test_question_categorylist_parents(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $context = $this->create_course_and_question_bank();
        // Create a top category.
        $cat0 = question_get_top_category($context->id, true);
        // Add sub-categories.
        $cat1 = $questiongenerator->create_question_category(['parent' => $cat0->id]);
        $cat2 = $questiongenerator->create_question_category(['parent' => $cat1->id]);

        // Test the 'get parents' function.
        $this->assertEquals([$cat0->id, $cat1->id], question_categorylist_parents($cat2->id));
    }

    /**
     * Test question_categorylist_parents when there is bad data, with a category pointing to a parent in another context.
     *
     * This is a situation that should never arise (parents and their children should always belong to the same context)
     * but it does, because bugs, so the code should be robust to it.
     *
     * @covers ::question_categorylist_parents()
     */
    public function test_question_categorylist_parents_bad_data(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $bank1context = $this->create_course_and_question_bank();
        $coursecontext = $bank1context->get_course_context();
        $qbank2 = self::getDataGenerator()->create_module('qbank', ['course' => $coursecontext->instanceid]);
        $bank2context = \context_module::instance($qbank2->cmid);

        // Create a top category.
        $cat0 = question_get_top_category($bank1context->id, true);
        // Add sub-categories - but in a different context.
        $cat1 = $questiongenerator->create_question_category(
            ['parent' => $cat0->id, 'contextid' => $bank2context->id]);
        $cat2 = $questiongenerator->create_question_category(
            ['parent' => $cat1->id, 'contextid' => $bank2context->id]);

        // Test the 'get parents' function only returns categories in the same context.
        $this->assertEquals([$cat1->id], question_categorylist_parents($cat2->id));
    }

    /**
     * Get test cases for test_core_question_find_next_unused_idnumber.
     *
     * @return array test cases.
     */
    public static function find_next_unused_idnumber_cases(): array {
        return [
            [null, null],
            ['id', null],
            ['id1a', null],
            ['id001', 'id002'],
            ['id9', 'id10'],
            ['id009', 'id010'],
            ['id999', 'id1000'],
            ['0', '1'],
            ['-1', '-2'],
            ['01', '02'],
            ['09', '10'],
            ['1.0E+29', '1.0E+30'], // Idnumbers are strings, not floats.
            ['1.0E-29', '1.0E-30'], // By the way, this is not a sensible idnumber!
            ['10.1', '10.2'],
            ['10.9', '10.10'],

        ];
    }

    /**
     * Test core_question_find_next_unused_idnumber in the case when there are no other questions.
     *
     * @dataProvider find_next_unused_idnumber_cases
     * @param string|null $oldidnumber value to pass to core_question_find_next_unused_idnumber.
     * @param string|null $expectednewidnumber expected result.
     */
    public function test_core_question_find_next_unused_idnumber(?string $oldidnumber, ?string $expectednewidnumber): void {
        $this->assertSame($expectednewidnumber, core_question_find_next_unused_idnumber($oldidnumber, 0));
    }

    public function test_core_question_find_next_unused_idnumber_skips_used(): void {
        $this->resetAfterTest();

        /** @var core_question_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category();
        $othercategory = $generator->create_question_category();
        $generator->create_question('truefalse', null, ['category' => $category->id, 'idnumber' => 'id9']);
        $generator->create_question('truefalse', null, ['category' => $category->id, 'idnumber' => 'id10']);
        // Next one to make sure only idnumbers from the right category are ruled out.
        $generator->create_question('truefalse', null, ['category' => $othercategory->id, 'idnumber' => 'id11']);

        $this->assertSame('id11', core_question_find_next_unused_idnumber('id9', $category->id));
        $this->assertSame('id11', core_question_find_next_unused_idnumber('id8', $category->id));
    }

    /**
     * Tests for the question_move_questions_to_category function.
     *
     * @covers ::question_move_questions_to_category
     */
    public function test_question_move_questions_to_category(): void {
        $this->resetAfterTest();

        // Create the test data.
        list($category1, $course1, $quiz1, $questioncat1, $questions1) = $this->setup_quiz_and_questions();
        list($category2, $course2, $quiz2, $questioncat2, $questions2) = $this->setup_quiz_and_questions();

        $this->assertCount(2, $questions1);
        $this->assertCount(2, $questions2);
        $questionsidtomove = [];
        foreach ($questions1 as $question1) {
            $questionsidtomove[] = $question1->id;
        }

        // Move the question from quiz 1 to quiz 2.
        question_move_questions_to_category($questionsidtomove, $questioncat2->id);
        $this->assert_category_contains_questions($questioncat2->id, 4);
    }

    /**
     * Tests for the idnumber_exist_in_question_category function.
     *
     * @covers ::idnumber_exist_in_question_category
     */
    public function test_idnumber_exist_in_question_category(): void {
        global $DB;

        $this->resetAfterTest();

        // Create the test data.
        list($category1, $course1, $quiz1, $questioncat1, $questions1) = $this->setup_quiz_and_questions();
        list($category2, $course2, $quiz2, $questioncat2, $questions2) = $this->setup_quiz_and_questions();

        $questionbankentry1 = get_question_bank_entry($questions1[0]->id);
        $entry = new \stdClass();
        $entry->id = $questionbankentry1->id;
        $entry->idnumber = 1;
        $DB->update_record('question_bank_entries', $entry);

        $questionbankentry2 = get_question_bank_entry($questions2[0]->id);
        $entry2 = new \stdClass();
        $entry2->id = $questionbankentry2->id;
        $entry2->idnumber = 1;
        $DB->update_record('question_bank_entries', $entry2);

        $questionbe = $DB->get_record('question_bank_entries', ['id' => $questionbankentry1->id]);

        // Validate that a first stage of an idnumber exists (this format: xxxx_x).
        list($response, $record) = idnumber_exist_in_question_category($questionbe->idnumber, $questioncat1->id);
        $this->assertEquals([], $record);
        $this->assertEquals(true, $response);

        // Move the question to a category that has a question with the same idnumber.
        question_move_questions_to_category($questions1[0]->id, $questioncat2->id);

        // Validate that function return the last record used for the idnumber.
        list($response, $record) = idnumber_exist_in_question_category($questionbe->idnumber, $questioncat2->id);
        $record = reset($record);
        $idnumber = $record->idnumber;
        $this->assertEquals($idnumber, '1_1');
        $this->assertEquals(true, $response);
    }

    /**
     * Test method is_latest().
     *
     * @covers ::is_latest
     *
     */
    public function test_is_latest(): void {
        global $DB;
        $this->resetAfterTest();
        /** @var \core_question_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcat1 = $generator->create_question_category(['name' => 'My category', 'sortorder' => 1, 'idnumber' => 'myqcat']);
        $question = $generator->create_question('shortanswer', null, ['name' => 'q1', 'category' => $qcat1->id]);
        $record = $DB->get_record('question_versions', ['questionid' => $question->id]);
        $firstversion = $record->version;
        $questionbankentryid = $record->questionbankentryid;
        $islatest = is_latest($firstversion, $questionbankentryid);
        $this->assertTrue($islatest);
    }

    /**
     * Test question bank entry deletion.
     *
     * @covers ::delete_question_bank_entry
     */
    public function test_delete_question_bank_entry(): void {
        global $DB;
        $this->resetAfterTest();
        // Setup.
        $context = $this->create_course_and_question_bank();
        /** @var \core_question_generator $qgen */
        $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcat = $qgen->create_question_category(array('contextid' => $context->id));
        $q1 = $qgen->create_question('shortanswer', null, array('category' => $qcat->id));
        // Make sure there is an entry in the entry table.
        $sql = 'SELECT qbe.id as id,
                       qv.id as versionid
                  FROM {question_bank_entries} qbe
                  JOIN {question_versions} qv
                    ON qbe.id = qv.questionbankentryid
                  JOIN {question} q
                    ON qv.questionid = q.id
                 WHERE q.id = ?';
        $records = $DB->get_records_sql($sql, [$q1->id]);
        $this->assertCount(1, $records);
        // Delete the record.
        $record = reset($records);
        delete_question_bank_entry($record->id);
        $records = $DB->get_records('question_bank_entries', ['id' => $record->id]);
        // As the version record exists, it wont delete the data to resolve any errors.
        $this->assertCount(1, $records);
        $DB->delete_records('question_versions', ['id' => $record->versionid]);
        delete_question_bank_entry($record->id);
        $records = $DB->get_records('question_bank_entries', ['id' => $record->id]);
        $this->assertCount(0, $records);
    }

    /**
     * Test question bank entry object.
     *
     * @covers ::get_question_bank_entry
     */
    public function test_get_question_bank_entry(): void {
        global $DB;
        $this->resetAfterTest();
        // Setup.
        $context = $this->create_course_and_question_bank();
        /** @var \core_question_generator $qgen */
        $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcat = $qgen->create_question_category(array('contextid' => $context->id));
        $q1 = $qgen->create_question('shortanswer', null, array('category' => $qcat->id));
        // Make sure there is an entry in the entry table.
        $sql = 'SELECT qbe.id as id,
                       qv.id as versionid
                  FROM {question_bank_entries} qbe
                  JOIN {question_versions} qv
                    ON qbe.id = qv.questionbankentryid
                  JOIN {question} q
                    ON qv.questionid = q.id
                 WHERE q.id = ?';
        $records = $DB->get_records_sql($sql, [$q1->id]);
        $this->assertCount(1, $records);
        $record = reset($records);
        $questionbankentry = get_question_bank_entry($q1->id);
        $this->assertEquals($questionbankentry->id, $record->id);
    }

    /**
     * Test the version objects for a question.
     *
     * @covers ::get_question_version
     */
    public function test_get_question_version(): void {
        global $DB;
        $this->resetAfterTest();
        // Setup.
        $context = $this->create_course_and_question_bank();
        /** @var \core_question_generator $qgen */
        $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcat = $qgen->create_question_category(array('contextid' => $context->id));
        $q1 = $qgen->create_question('shortanswer', null, array('category' => $qcat->id));
        // Make sure there is an entry in the entry table.
        $sql = 'SELECT qbe.id as id,
                       qv.id as versionid
                  FROM {question_bank_entries} qbe
                  JOIN {question_versions} qv
                    ON qbe.id = qv.questionbankentryid
                  JOIN {question} q
                    ON qv.questionid = q.id
                 WHERE q.id = ?';
        $records = $DB->get_records_sql($sql, [$q1->id]);
        $this->assertCount(1, $records);
        $record = reset($records);
        $questionversions = get_question_version($q1->id);
        $questionversion = reset($questionversions);
        $this->assertEquals($questionversion->id, $record->versionid);
    }

    /**
     * Test get next version of a question.
     *
     * @covers ::get_next_version
     */
    public function test_get_next_version(): void {
        global $DB;
        $this->resetAfterTest();
        // Setup.
        $context = $this->create_course_and_question_bank();
        /** @var \core_question_generator $qgen */
        $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcat = $qgen->create_question_category(array('contextid' => $context->id));
        $q1 = $qgen->create_question('shortanswer', null, array('category' => $qcat->id));
        // Make sure there is an entry in the entry table.
        $sql = 'SELECT qbe.id as id,
                       qv.id as versionid,
                       qv.version
                  FROM {question_bank_entries} qbe
                  JOIN {question_versions} qv
                    ON qbe.id = qv.questionbankentryid
                  JOIN {question} q
                    ON qv.questionid = q.id
                 WHERE q.id = ?';
        $records = $DB->get_records_sql($sql, [$q1->id]);
        $this->assertCount(1, $records);
        $record = reset($records);
        $this->assertEquals(1, $record->version);
        $nextversion = get_next_version($record->id);
        $this->assertEquals(2, $nextversion);
    }

    /**
     * Test moving a question category from one context to another
     *
     * @covers ::question_move_category_to_context
     */
    public function test_question_move_category_to_context(): void {

        global $CFG, $DB;

        $this->setAdminUser();

        // Create a course.
        $course = self::getDataGenerator()->create_course();

        // Create a quiz activity to store our question in at the start.
        $quiz1 = $this->getDataGenerator()->create_module('quiz', [
            'course' => $course->id,
        ]);

        // And then create another one to move the category to.
        $quiz2 = $this->getDataGenerator()->create_module('quiz', [
            'course' => $course->id,
        ]);

        // Get the question generator and the context of the activities.
        $generator = self::getDataGenerator()->get_plugin_generator('core_question');
        $context1 = \context_module::instance($quiz1->cmid);
        $context2 = \context_module::instance($quiz2->cmid);

        // Create a question category within our first quiz activity.
        $category = $generator->create_question_category(['contextid' => $context1->id]);

        // And create a question within that.
        // We will use `truefalse` but it could be any type.
        $question = $generator->create_question('truefalse', null, ['category' => $category->id]);

        $areas = [
            'questiontext' => '1.jpg',
            'generalfeedback' => '2.jpg',
        ];

        // Add file records to each of the file areas, for our first quiz activity.
        foreach ($areas as $area => $img) {
            $fs = get_file_storage();
            $filerecord = new \stdClass();
            $filerecord->contextid = $context1->id;
            $filerecord->component = 'question';
            $filerecord->filearea = $area;
            $filerecord->itemid = $question->id;
            $filerecord->filepath = '/';
            $filerecord->filename = $img;
            $fs->create_file_from_pathname($filerecord, $CFG->dirroot .
                '/lib/tests/fixtures/' . $img);
        }

        // Firstly, confirm that the file records exist and there were no problems creating them.
        // We don't care in this test about the actual files in the data dir.
        $files = $DB->get_records('files', [
            'component' => 'question',
            'itemid' => $question->id,
            'contextid' => $context1->id,
            'mimetype' => 'image/jpeg',
        ]);

        $this->assertCount(2, $files);

        // Move the question category to another context.
        question_move_category_to_context(
            $category->id,
            $context1->id,
            $context2->id,
        );

        // Now check that the files have been moved to the new category.
        $files = $DB->get_records('files', [
            'component' => 'question',
            'itemid' => $question->id,
            'contextid' => $context2->id,
            'mimetype' => 'image/jpeg',
        ]);

        $this->assertCount(2, $files);

    }

    /**
     * Update the context for a set reference, keeping the original category.
     *
     * @covers ::move_question_set_references()
     */
    public function test_move_question_set_references_context(): void {
        $this->setAdminUser();
        // Create a course with a quiz containing a random question from a qbank context.
        $randomcourse = self::getDataGenerator()->create_course(['shortname' => 'Random']);
        $qbank1 = self::getDataGenerator()->get_plugin_generator('mod_qbank')->create_instance(['course' => $randomcourse->id]);
        $context1 = \context_module::instance($qbank1->cmid);
        $qbank2 = self::getDataGenerator()->get_plugin_generator('mod_qbank')->create_instance(['course' => $randomcourse->id]);
        $context2 = \context_module::instance($qbank2->cmid);
        $topcategory = question_get_top_category($context1->id, true);
        $randomcategory = self::getDataGenerator()->get_plugin_generator('core_question')->create_question_category(
            ['parent' => $topcategory->id],
        );
        $randomquiz = self::getDataGenerator()->get_plugin_generator('mod_quiz')->create_instance(
            [
                'course' => $randomcourse->id,
                'grade' => 100.0,
                'sumgrades' => 2,
                'layout' => '1,0',
            ],
        );

        $randomquizsettings = quiz_settings::create($randomquiz->id);
        $structure = $randomquizsettings->get_structure();

        $filtercondition = [
            'filter' => [
                'category' => [
                    'jointype' => \core_question\local\bank\condition::JOINTYPE_DEFAULT,
                    'values' => [$randomcategory->id],
                    'filteroptions' => ['includesubcategories' => true],
                ],
            ],
        ];
        $structure->add_random_questions(1, 1, $filtercondition);
        $structure = $randomquizsettings->get_structure();
        $randomquestion = $structure->get_question_in_slot(1);

        $this->assertEquals($randomquestion->contextid, $context1->id);
        $this->assertEquals($randomquestion->filtercondition['filter']['category']['values'][0], $randomcategory->id);

        move_question_set_references($randomcategory->id, $randomcategory->id, $context1->id, $context2->id);

        $structure = $randomquizsettings->get_structure();
        $randomquestion = $structure->get_question_in_slot(1);

        $this->assertEquals($randomquestion->contextid, $context2->id);
        $this->assertEquals($randomquestion->filtercondition['filter']['category']['values'][0], $randomcategory->id);
    }

    /**
     * Update the context and category for a set reference.
     *
     * @covers ::move_question_set_references()
     */
    public function test_move_question_set_references_category(): void {
        $this->setAdminUser();
        // Create a course with a quiz containing a random question from a qbank context.
        $randomcourse = self::getDataGenerator()->create_course(['shortname' => 'Random']);
        $qbank1 = self::getDataGenerator()->get_plugin_generator('mod_qbank')->create_instance(['course' => $randomcourse->id]);
        $context1 = \context_module::instance($qbank1->cmid);
        $qbank2 = self::getDataGenerator()->get_plugin_generator('mod_qbank')->create_instance(['course' => $randomcourse->id]);
        $context2 = \context_module::instance($qbank2->cmid);
        $topcategory1 = question_get_top_category($context1->id, true);
        $topcategory2 = question_get_top_category($context2->id, true);
        $randomquiz = self::getDataGenerator()->get_plugin_generator('mod_quiz')->create_instance(
            [
                'course' => $randomcourse->id,
                'grade' => 100.0,
                'sumgrades' => 2,
                'layout' => '1,0',
            ],
        );

        $randomquizsettings = quiz_settings::create($randomquiz->id);
        $structure = $randomquizsettings->get_structure();

        $filtercondition = [
            'filter' => [
                'category' => [
                    'jointype' => \core_question\local\bank\condition::JOINTYPE_DEFAULT,
                    'values' => [$topcategory1->id],
                    'filteroptions' => ['includesubcategories' => true],
                ],
            ],
        ];
        $structure->add_random_questions(1, 1, $filtercondition);
        $structure = $randomquizsettings->get_structure();
        $randomquestion = $structure->get_question_in_slot(1);

        $this->assertEquals($randomquestion->contextid, $context1->id);
        $this->assertEquals($randomquestion->filtercondition['filter']['category']['values'][0], $topcategory1->id);

        move_question_set_references($topcategory1->id, $topcategory2->id, $context1->id, $context2->id);

        $structure = $randomquizsettings->get_structure();
        $randomquestion = $structure->get_question_in_slot(1);

        $this->assertEquals($randomquestion->contextid, $context2->id);
        $this->assertEquals($randomquestion->filtercondition['filter']['category']['values'][0], $topcategory2->id);
    }
}
