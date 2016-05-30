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
        $course = $this->getDataGenerator()->create_course(array('numsections' => 5));

        $options = array(
            'course' => $course->id,
            'duedate' => time(),
        );

        // Generate an assignment with due date (will generate a course event).
        $quiz = $this->getDataGenerator()->create_module('quiz', $options);

        $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');

        if ('course' == $type) {
            $context = context_course::instance($course->id);
        } else if ('category' == $type) {
            $context = context_coursecat::instance($category->id);
        } else {
            $context = context_module::instance($quiz->cmid);
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
        $restoredcategory = $DB->get_record('question_categories', array('contextid' => context_course::instance($course2->id)->id),
            '*', MUST_EXIST);

        // Check that there are two questions in the restored to course's context.
        $this->assertEquals(2, $DB->count_records('question', array('category' => $restoredcategory->id)));

        $rc->destroy();
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
}
