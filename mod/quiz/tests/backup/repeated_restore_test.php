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

namespace mod_quiz\backup;

use advanced_testcase;
use backup_controller;
use restore_controller;
use quiz_question_helper_test_trait;
use backup;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/mod/quiz/tests/quiz_question_helper_test_trait.php');

/**
 * Test repeatedly restoring a quiz into another course.
 *
 * @package    mod_quiz
 * @category   test
 * @copyright  Julien Rädler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \restore_questions_parser_processor
 * @covers \restore_create_categories_and_questions
 */
final class repeated_restore_test extends advanced_testcase {
    use quiz_question_helper_test_trait;

    /**
     * Restore a quiz twice into the same target course, and verify the quiz uses the restored questions both times.
     */
    public function test_restore_quiz_into_other_course_twice(): void {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Step 1: Create two courses and a user with editing teacher capabilities.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course();
        $course2 = $generator->create_course();
        $teacher = $USER;
        $generator->enrol_user($teacher->id, $course1->id, 'editingteacher');
        $generator->enrol_user($teacher->id, $course2->id, 'editingteacher');

        // Create a quiz with questions in the first course.
        $quiz = $this->create_test_quiz($course1);
        $qbank = $generator->get_plugin_generator('mod_qbank')->create_instance(['course' => $course1->id]);
        $context = \context_module::instance($qbank->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a question category.
        $cat = $questiongenerator->create_question_category(['contextid' => $context->id]);

        // Create a short answer question.
        $saq = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        // Update the question to simulate editing.
        $questiongenerator->update_question($saq);
        // Add question to quiz.
        quiz_add_quiz_question($saq->id, $quiz);

        // Create a numerical question.
        $numq = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);
        // Update the question to simulate multiple versions.
        $questiongenerator->update_question($numq);
        $questiongenerator->update_question($numq);
        // Add question to quiz.
        quiz_add_quiz_question($numq->id, $quiz);

        // Create a true false question.
        $tfq = $questiongenerator->create_question('truefalse', null, ['category' => $cat->id]);
        // Update the question to simulate multiple versions.
        $questiongenerator->update_question($tfq);
        $questiongenerator->update_question($tfq);
        // Add question to quiz.
        quiz_add_quiz_question($tfq->id, $quiz);

        // Capture original question IDs for verification after import.
        $modules1 = get_fast_modinfo($course1->id)->get_instances_of('quiz');
        $module1 = reset($modules1);
        $questionscourse1 = \mod_quiz\question\bank\qbank_helper::get_question_structure(
            $module1->instance, $module1->context);

        $originalquestionids = [];
        foreach ($questionscourse1 as $slot) {
            array_push($originalquestionids, intval($slot->questionid));
        }

        // Step 2: Backup the first course.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course1->id, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $teacher->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Step 3: Import the backup into the second course.
        $rc = new restore_controller($backupid, $course2->id, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
            $teacher->id, backup::TARGET_CURRENT_ADDING);
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        // Verify the question ids from the quiz in the original course are different
        // from the question ids in the duplicated quiz in the second course.
        $modules2 = get_fast_modinfo($course2->id)->get_instances_of('quiz');
        $module2 = reset($modules2);
        $questionscourse2firstimport = \mod_quiz\question\bank\qbank_helper::get_question_structure(
            $module2->instance, $module2->context);

        foreach ($questionscourse2firstimport as $slot) {
            $this->assertNotContains(intval($slot->questionid), $originalquestionids,
                "Question ID $slot->questionid should not be in the original course's question IDs.");
        }

        // Repeat the backup and import process to simulate a second import.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course1->id, backup::FORMAT_MOODLE,
                            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $teacher->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        $rc = new restore_controller($backupid, $course2->id, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
            $teacher->id, backup::TARGET_CURRENT_ADDING);
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        // Verify that the second restore has used the same new questions that were created by the first restore.
        $modules3 = get_fast_modinfo($course2->id)->get_instances_of('quiz');
        $module3 = end($modules3);
        $questionscourse2secondimport = \mod_quiz\question\bank\qbank_helper::get_question_structure(
                $module3->instance, $module3->context);

        foreach ($questionscourse2secondimport as $slot) {
            $this->assertEquals($questionscourse2firstimport[$slot->slot]->questionid, $slot->questionid);
        }
    }

    /**
     * Restore a copy of a quiz to the same course, using questions that include line breaks in the text.
     */
    public function test_restore_question_with_linebreaks(): void {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Step 1: Create two courses and a user with editing teacher capabilities.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course();
        $course2 = $generator->create_course();
        $teacher = $USER;
        $generator->enrol_user($teacher->id, $course1->id, 'editingteacher');
        $generator->enrol_user($teacher->id, $course2->id, 'editingteacher');

        // Create a quiz with questions in the first course.
        $quiz = $this->create_test_quiz($course1);
        $qbank = $generator->get_plugin_generator('mod_qbank')->create_instance(['course' => $course1->id]);
        $context = \context_module::instance($qbank->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a question category.
        $cat = $questiongenerator->create_question_category(['contextid' => $context->id]);

        // Create questions and add to the quiz.
        $q1 = $questiongenerator->create_question('truefalse', null, [
            'category' => $cat->id,
            'questiontext' => ['text' => "<p>Question</p>\r\n<p>One</p>", 'format' => FORMAT_MOODLE]
        ]);
        $q2 = $questiongenerator->create_question('truefalse', null, [
            'category' => $cat->id,
            'questiontext' => ['text' => "<p>Question</p>\n<p>Two</p>", 'format' => FORMAT_MOODLE]
        ]);
        // Add question to quiz.
        quiz_add_quiz_question($q1->id, $quiz);
        quiz_add_quiz_question($q2->id, $quiz);

        // Capture original question IDs for verification after import.
        $modules1 = get_fast_modinfo($course1->id)->get_instances_of('quiz');
        $module1 = reset($modules1);
        $originalslots = \mod_quiz\question\bank\qbank_helper::get_question_structure(
            $module1->instance, $module1->context);

        $originalquestionids = [];
        foreach ($originalslots as $slot) {
            array_push($originalquestionids, intval($slot->questionid));
        }

        $this->assertCount(2, get_questions_category($cat, false));

        // Step 2: Backup the quiz
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $quiz->cmid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $teacher->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Step 3: Import the backup into the same course.
        $rc = new restore_controller($backupid, $course1->id, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
            $teacher->id, backup::TARGET_CURRENT_ADDING);
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        // Verify the question ids from the new quiz match the first.
        $modules2 = get_fast_modinfo($course1->id)->get_instances_of('quiz');
        $this->assertCount(2, $modules2);
        $module2 = end($modules2);
        $copyslots = \mod_quiz\question\bank\qbank_helper::get_question_structure(
            $module2->instance, $module2->context);

        foreach ($copyslots as $slot) {
            $this->assertContains(intval($slot->questionid), $originalquestionids);
        }

        // The category should still only contain 2 question, neither question should be duplicated.
        $this->assertCount(2, get_questions_category($cat, false));
    }

    /**
     * Return a list of qtypes with valid generators in their helper class.
     *
     * This will check all installed qtypes for a test helper class, then find a defined test question which has a corresponding
     * form_data method and return it. If the helper doesn't have a form_data method for any test question, it will return a
     * null test question name for that qtype.
     *
     * @return array
     */
    public static function get_qtype_generators(): array {
        global $CFG;
        $generators = [];
        foreach (\core\plugin_manager::instance()->get_plugins_of_type('qtype') as $qtype) {
            if ($qtype->name == 'random') {
                continue;
            }
            $helperpath = "{$CFG->dirroot}/question/type/{$qtype->name}/tests/helper.php";
            if (!file_exists($helperpath)) {
                continue;
            }
            require_once($helperpath);
            $helperclass = "qtype_{$qtype->name}_test_helper";
            if (!class_exists($helperclass)) {
                continue;
            }
            $helper = new $helperclass();
            $testquestion = null;
            foreach ($helper->get_test_questions() as $question) {
                if (method_exists($helper, "get_{$qtype->name}_question_form_data_{$question}")) {
                    $testquestion = $question;
                    break;
                }
            }
            $generators[$qtype->name] = [
                'qtype' => $qtype->name,
                'testquestion' => $testquestion,
            ];
        }
        return $generators;
    }

    /**
     * Restore a quiz with questions of same stamp into the same course, but different answers.
     *
     * @dataProvider get_qtype_generators
     * @param string $qtype The name of the qtype plugin to test
     * @param ?string $testquestion The test question to generate for the plugin. If null, the plugin will be skipped
     *      with a message.
     */
    public function test_restore_quiz_with_same_stamp_questions(string $qtype, ?string $testquestion): void {
        global $DB, $USER;
        if (is_null($testquestion)) {
            $this->markTestSkipped(
                "Cannot test qtype_{$qtype} as there is no test question with a form_data method in the " .
                "test helper class."
            );
        }
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and a user with editing teacher capabilities.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course();
        $teacher = $USER;
        $generator->enrol_user($teacher->id, $course1->id, 'editingteacher');
        $qbank = $generator->get_plugin_generator('mod_qbank')->create_instance(['course' => $course1->id]);

        $context = \context_module::instance($qbank->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a question category.
        $cat = $questiongenerator->create_question_category(['contextid' => $context->id]);

        // Create 2 quizzes with 2 questions multichoice.
        $quiz1 = $this->create_test_quiz($course1);
        $question1 = $questiongenerator->create_question($qtype, $testquestion, ['category' => $cat->id]);
        quiz_add_quiz_question($question1->id, $quiz1, 0);

        $question2 = $questiongenerator->create_question($qtype, $testquestion, ['category' => $cat->id]);
        quiz_add_quiz_question($question2->id, $quiz1, 0);

        // Update question2 to have the same stamp as question1.
        $DB->set_field('question', 'stamp', $question1->stamp, ['id' => $question2->id]);

        // Change the answers of the question2 to be different to question1.
        $question2data = \question_bank::load_question_data($question2->id);
        if (!isset($question2data->options->answers) || empty($question2data->options->answers)) {
            $this->markTestSkipped(
                "Cannot test edited answers for qtype_{$qtype} as it does not use answers.",
            );
        }
        foreach ($question2data->options->answers as $answer) {
            $DB->set_field('question_answers', 'answer', 'edited', ['id' => $answer->id]);
        }

        // Backup quiz1.
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $quiz1->cmid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $teacher->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Restore the backup into the same course.
        $rc = new restore_controller($backupid, $course1->id, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
            $teacher->id, backup::TARGET_CURRENT_ADDING);
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        // Verify that the newly-restored quiz uses the same question as quiz2.
        $modules = get_fast_modinfo($course1->id)->get_instances_of('quiz');
        $this->assertCount(2, $modules);
        $quiz2structure = \mod_quiz\question\bank\qbank_helper::get_question_structure(
            $quiz1->id,
            \context_module::instance($quiz1->cmid),
        );
        $quiz2 = end($modules);
        $quiz2structure = \mod_quiz\question\bank\qbank_helper::get_question_structure($quiz2->instance, $quiz2->context);
        $this->assertEquals($quiz2structure[1]->questionid, $quiz2structure[1]->questionid);
        $this->assertEquals($quiz2structure[2]->questionid, $quiz2structure[2]->questionid);
    }

    /**
     * Restore a quiz with duplicate questions (same stamp and questions) into the same course.
     *
     * This is a contrived case, but this test serves as a control for the other tests in this class, proving that the hashing
     * process will match an identical question.
     *
     * @dataProvider get_qtype_generators
     * @param string $qtype The name of the qtype plugin to test
     * @param ?string $testquestion The test question to generate for the plugin. If null, the plugin will be skipped
     *       with a message.
     */
    public function test_restore_quiz_with_duplicate_questions(string $qtype, ?string $testquestion): void {
        global $DB, $USER;
        if (is_null($testquestion)) {
            $this->markTestSkipped(
                "Cannot test qtype_{$qtype} as there is no test question with a form_data method in the " .
                "test helper class."
            );
        }
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and a user with editing teacher capabilities.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course();
        $teacher = $USER;
        $generator->enrol_user($teacher->id, $course1->id, 'editingteacher');
        $qbank = $generator->get_plugin_generator('mod_qbank')->create_instance(['course' => $course1->id]);
        $context = \context_module::instance($qbank->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a question category.
        $cat = $questiongenerator->create_question_category(['contextid' => $context->id]);

        // Create a quiz with 2 identical but separate questions.
        $quiz1 = $this->create_test_quiz($course1);
        $question1 = $questiongenerator->create_question($qtype, $testquestion, ['category' => $cat->id]);
        quiz_add_quiz_question($question1->id, $quiz1, 0);
        $question2 = $questiongenerator->create_question($qtype, $testquestion, ['category' => $cat->id]);
        quiz_add_quiz_question($question2->id, $quiz1, 0);

        // Update question2 to have the same times and stamp as question1.
        $DB->update_record('question', [
            'id' => $question2->id,
            'stamp' => $question1->stamp,
            'timecreated' => $question1->timecreated,
            'timemodified' => $question1->timemodified,
        ]);

        // Backup quiz.
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $quiz1->cmid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $teacher->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Restore the backup into the same course.
        $rc = new restore_controller($backupid, $course1->id, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
            $teacher->id, backup::TARGET_CURRENT_ADDING);
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        // Expect that the restored quiz will have the second question in both its slots
        // by virtue of identical stamp, version, and hash of question answer texts.
        $modules = get_fast_modinfo($course1->id)->get_instances_of('quiz');
        $this->assertCount(2, $modules);
        $quiz2 = end($modules);
        $quiz2structure = \mod_quiz\question\bank\qbank_helper::get_question_structure($quiz2->instance, $quiz2->context);
        $this->assertEquals($quiz2structure[1]->questionid, $quiz2structure[2]->questionid);
    }

    /**
     * Restore a quiz with questions that have the same stamp but different text.
     *
     * @dataProvider get_qtype_generators
     * @param string $qtype The name of the qtype plugin to test
     * @param ?string $testquestion The test question to generate for the plugin. If null, the plugin will be skipped
     *       with a message.
     */
    public function test_restore_quiz_with_edited_questions(string $qtype, ?string $testquestion): void {
        global $DB, $USER;
        if (is_null($testquestion)) {
            $this->markTestSkipped(
                "Cannot test qtype_{$qtype} as there is no test question with a form_data method in the " .
                    "test helper class."
            );
        }
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and a user with editing teacher capabilities.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course();
        $teacher = $USER;
        $generator->enrol_user($teacher->id, $course1->id, 'editingteacher');
        $qbank = $generator->get_plugin_generator('mod_qbank')->create_instance(['course' => $course1->id]);
        $context = \context_module::instance($qbank->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a question category.
        $cat = $questiongenerator->create_question_category(['contextid' => $context->id]);

        // Create a quiz with 2 identical but separate questions.
        $quiz1 = $this->create_test_quiz($course1);
        $question1 = $questiongenerator->create_question($qtype, $testquestion, ['category' => $cat->id]);
        quiz_add_quiz_question($question1->id, $quiz1);
        $question2 = $questiongenerator->create_question($qtype, $testquestion, ['category' => $cat->id]);
        // Edit question 2 to have the same stamp and times as question1, but different text.
        $DB->update_record('question', [
            'id' => $question2->id,
            'questiontext' => 'edited',
            'stamp' => $question1->stamp,
            'timecreated' => $question1->timecreated,
            'timemodified' => $question1->timemodified,
        ]);
        quiz_add_quiz_question($question2->id, $quiz1);

        // Backup quiz.
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $quiz1->cmid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $teacher->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Restore the backup into the same course.
        $rc = new restore_controller($backupid, $course1->id, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
            $teacher->id, backup::TARGET_CURRENT_ADDING);
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        // The quiz should contain both questions, as they have different text.
        $modules = get_fast_modinfo($course1->id)->get_instances_of('quiz');
        $this->assertCount(2, $modules);
        $quiz2 = end($modules);
        $quiz2structure = \mod_quiz\question\bank\qbank_helper::get_question_structure($quiz2->instance, $quiz2->context);
        $this->assertEquals($quiz2structure[1]->questionid, $question1->id);
        $this->assertEquals($quiz2structure[2]->questionid, $question2->id);
    }

    /**
     * Restore a course to another course having questions with the same stamp in a shared question bank context category.
     *
     * @dataProvider get_qtype_generators
     * @param string $qtype The name of the qtype plugin to test
     * @param ?string $testquestion The test question to generate for the plugin. If null, the plugin will be skipped
     *      with a message.
     */
    public function test_restore_course_with_same_stamp_questions(string $qtype, ?string $testquestion): void {
        global $DB, $USER;
        if (is_null($testquestion)) {
            $this->markTestSkipped(
                "Cannot test qtype_{$qtype} as there is no test question with a form_data method in the " .
                "test helper class."
            );
        }
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create two courses and a user with editing teacher capabilities.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course();
        $course2 = $generator->create_course();
        $qbank = $generator->get_plugin_generator('mod_qbank')->create_instance(['course' => $course2->id]);
        $teacher = $USER;
        $generator->enrol_user($teacher->id, $course1->id, 'editingteacher');
        $generator->enrol_user($teacher->id, $course2->id, 'editingteacher');

        $context = \context_module::instance($qbank->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a question category.
        $cat = $questiongenerator->create_question_category(['contextid' => $context->id]);

        // Create quiz with question.
        $quiz1 = $this->create_test_quiz($course1);
        $question1 = $questiongenerator->create_question($qtype, $testquestion, ['category' => $cat->id]);
        quiz_add_quiz_question($question1->id, $quiz1, 0);

        $quiz2 = $this->create_test_quiz($course1);
        $question2 = $questiongenerator->create_question($qtype, $testquestion, ['category' => $cat->id]);
        quiz_add_quiz_question($question2->id, $quiz2, 0);

        // Update question2 to have the same stamp as question1.
        $DB->set_field('question', 'stamp', $question1->stamp, ['id' => $question2->id]);

        // Change the answers of the question2 to be different to question1.
        $question2data = \question_bank::load_question_data($question2->id);
        if (!isset($question2data->options->answers) || empty($question2data->options->answers)) {
            $this->markTestSkipped(
                "Cannot test edited answers for qtype_{$qtype} as it does not use answers.",
            );
        }
        foreach ($question2data->options->answers as $answer) {
            $answer->answer = 'New answer ' . $answer->id;
            $DB->update_record('question_answers', $answer);
        }

        $course1q1structure = \mod_quiz\question\bank\qbank_helper::get_question_structure(
            $quiz1->id, \context_module::instance($quiz1->cmid));
        $this->assertEquals($question1->id, $course1q1structure[1]->questionid);
        $course1q2structure = \mod_quiz\question\bank\qbank_helper::get_question_structure(
            $quiz2->id, \context_module::instance($quiz2->cmid));
        $this->assertEquals($question2->id, $course1q2structure[1]->questionid);

        // Backup course1.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course1->id, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $teacher->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Restore the backup, adding to course2.
        $rc = new restore_controller($backupid, $course2->id, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
            $teacher->id, backup::TARGET_CURRENT_ADDING);
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        // Verify that the newly-restored course's quizzes use the same questions as their counterparts of course1.
        $modules = get_fast_modinfo($course2->id)->get_instances_of('quiz');
        $course1q1structure = \mod_quiz\question\bank\qbank_helper::get_question_structure(
                $quiz1->id, \context_module::instance($quiz1->cmid));
        $course2quiz1 = array_shift($modules);
        $course2q1structure = \mod_quiz\question\bank\qbank_helper::get_question_structure(
                $course2quiz1->instance, $course2quiz1->context);
        $this->assertEquals($question1->id, $course1q1structure[1]->questionid);
        $this->assertEquals($question1->id, $course2q1structure[1]->questionid);

        $course1q2structure = \mod_quiz\question\bank\qbank_helper::get_question_structure(
                $quiz2->id, \context_module::instance($quiz2->cmid));
        $course2quiz2 = array_shift($modules);
        $course2q2structure = \mod_quiz\question\bank\qbank_helper::get_question_structure(
                $course2quiz2->instance, $course2quiz2->context);
        $this->assertEquals($question2->id, $course1q2structure[1]->questionid);
        $this->assertEquals($question2->id, $course2q2structure[1]->questionid);
    }

    /**
     * Restore a quiz with questions of same stamp into the same course, but different hints.
     *
     * @dataProvider get_qtype_generators
     * @param string $qtype The name of the qtype plugin to test
     * @param ?string $testquestion The test question to generate for the plugin. If null, the plugin will be skipped
     *     with a message.
     */
    public function test_restore_quiz_with_same_stamp_questions_edited_hints(string $qtype, ?string $testquestion): void {
        global $DB, $USER;
        if (is_null($testquestion)) {
            $this->markTestSkipped(
                "Cannot test qtype_{$qtype} as there is no test question with a form_data method in the " .
                    "test helper class."
            );
        }
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and a user with editing teacher capabilities.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course();
        $teacher = $USER;
        $generator->enrol_user($teacher->id, $course1->id, 'editingteacher');
        $qbank = $generator->get_plugin_generator('mod_qbank')->create_instance(['course' => $course1->id]);
        $context = \context_module::instance($qbank->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a question category.
        $cat = $questiongenerator->create_question_category(['contextid' => $context->id]);

        // Create 2 questions multichoice.
        $quiz1 = $this->create_test_quiz($course1);
        $question1 = $questiongenerator->create_question($qtype, $testquestion, ['category' => $cat->id]);
        quiz_add_quiz_question($question1->id, $quiz1, 0);

        $question2 = $questiongenerator->create_question($qtype, $testquestion, ['category' => $cat->id]);
        quiz_add_quiz_question($question2->id, $quiz1, 0);

        // Update question2 to have the same stamp as question1.
        $DB->set_field('question', 'stamp', $question1->stamp, ['id' => $question2->id]);

        // Change the hints of the question2 to be different to question1.
        $hints = $DB->get_records('question_hints', ['questionid' => $question2->id]);
        if (empty($hints)) {
            $this->markTestSkipped(
                "Cannot test edited hints for qtype_{$qtype} as test question {$testquestion} does not use hints.",
            );
        }
        foreach ($hints as $hint) {
            $DB->set_field('question_hints', 'hint', "{$hint->hint} edited", ['id' => $hint->id]);
        }

        // Backup quiz1.
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $quiz1->cmid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $teacher->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Restore the backup into the same course.
        $rc = new restore_controller($backupid, $course1->id, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
            $teacher->id, backup::TARGET_CURRENT_ADDING);
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        // Verify that the newly-restored quiz uses the same question as quiz2.
        $modules = get_fast_modinfo($course1->id)->get_instances_of('quiz');
        $this->assertCount(2, $modules);
        $quiz1structure = \mod_quiz\question\bank\qbank_helper::get_question_structure(
            $quiz1->id,
            \context_module::instance($quiz1->cmid),
        );
        $quiz2 = end($modules);
        $quiz2structure = \mod_quiz\question\bank\qbank_helper::get_question_structure($quiz2->instance, $quiz2->context);
        $this->assertEquals($quiz1structure[1]->questionid, $quiz2structure[1]->questionid);
        $this->assertEquals($quiz1structure[2]->questionid, $quiz2structure[2]->questionid);

    }

    /**
     * Return a set of options fields and new values.
     *
     * @return array
     */
    public static function get_edited_option_fields(): array {
        return [
            'single' => [
                'single',
                '0',
            ],
            'shuffleanswers' => [
                'shuffleanswers',
                '0',
            ],
            'answernumbering' => [
                'answernumbering',
                'ABCD',
            ],
            'shownumcorrect' => [
                'shownumcorrect',
                '0',
            ],
            'showstandardinstruction' => [
                'showstandardinstruction',
                '1',
            ],
            'correctfeedback' => [
                'correctfeedback',
                'edited',
            ],
            'partiallycorrectfeedback' => [
                'partiallycorrectfeedback',
                'edited',
            ],
            'incorrectfeedback' => [
                'incorrectfeedback',
                'edited',
            ],
        ];
    }

    /**
     * Restore a quiz with questions of same stamp into the same course, but different qtype-specific options.
     *
     * @dataProvider get_edited_option_fields
     * @param string $field The answer field to edit
     * @param string $value The value to set
     */
    public function test_restore_quiz_with_same_stamp_questions_edited_options(string $field, string $value): void {
        global $DB, $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and a user with editing teacher capabilities.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course();
        $teacher = $USER;
        $generator->enrol_user($teacher->id, $course1->id, 'editingteacher');
        $qbank = $generator->get_plugin_generator('mod_qbank')->create_instance(['course' => $course1->id]);
        $context = \context_module::instance($qbank->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a question category.
        $cat = $questiongenerator->create_question_category(['contextid' => $context->id]);

        // A quiz with 2 multichoice questions.
        $quiz1 = $this->create_test_quiz($course1);
        $question1 = $questiongenerator->create_question('multichoice', 'one_of_four', ['category' => $cat->id]);
        quiz_add_quiz_question($question1->id, $quiz1, 0);

        $question2 = $questiongenerator->create_question('multichoice', 'one_of_four', ['category' => $cat->id]);
        quiz_add_quiz_question($question2->id, $quiz1, 0);

        // Update question2 to have the same stamp as question1.
        $DB->set_field('question', 'stamp', $question1->stamp, ['id' => $question2->id]);

        // Change the options of question2 to be different to question1.
        $DB->set_field('qtype_multichoice_options', $field, $value, ['questionid' => $question2->id]);

        // Backup quiz.
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $quiz1->cmid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $teacher->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Restore the backup into the same course.
        $rc = new restore_controller($backupid, $course1->id, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
            $teacher->id, backup::TARGET_CURRENT_ADDING);
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        // Verify that the newly-restored quiz questions match their quiz1 counterparts.
        $modules = get_fast_modinfo($course1->id)->get_instances_of('quiz');
        $this->assertCount(2, $modules);
        $quiz1structure = \mod_quiz\question\bank\qbank_helper::get_question_structure(
            $quiz1->id,
            \context_module::instance($quiz1->cmid),
        );
        $quiz2 = end($modules);
        $quiz2structure = \mod_quiz\question\bank\qbank_helper::get_question_structure($quiz2->instance, $quiz2->context);
        $this->assertEquals($quiz1structure[1]->questionid, $quiz2structure[1]->questionid);
        $this->assertEquals($quiz1structure[2]->questionid, $quiz2structure[2]->questionid);
    }
}
