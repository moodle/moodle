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

use context_course;
use mod_quiz\quiz_settings;
use moodle_url;
use question_bank;

defined('MOODLE_INTERNAL') || die();

use backup;
use core_question\local\bank\question_bank_helper;
use restore_controller;
use restore_dbops;

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Class core_question_backup_testcase
 *
 * @package    core_question
 * @category   test
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \restore_qtype_plugin
 * @covers     \restore_create_categories_and_questions
 * @covers     \restore_move_module_questions_categories
 */
final class backup_test extends \advanced_testcase {

    /**
     * Makes a backup of the course.
     *
     * @param \stdClass $course The course object.
     * @return string Unique identifier for this backup.
     */
    protected function backup_course($course) {
        global $CFG, $USER;

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = \backup::LOG_NONE;

        // Do backup with default settings. MODE_IMPORT means it will just
        // create the directory and not zip it.
        $bc = new \backup_controller(\backup::TYPE_1COURSE, $course->id,
                \backup::FORMAT_MOODLE, \backup::INTERACTIVE_NO, \backup::MODE_IMPORT,
                $USER->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        return $backupid;
    }

    /**
     * Makes a backup of a course module.
     *
     * @param int $modid The course_module id.
     * @return string Unique identifier for this backup.
     */
    protected function backup_course_module(int $modid) {
        global $CFG, $USER;

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = \backup::LOG_NONE;

        // Do backup with default settings. MODE_IMPORT means it will just
        // create the directory and not zip it.
        $bc = new \backup_controller(\backup::TYPE_1ACTIVITY, $modid,
            \backup::FORMAT_MOODLE, \backup::INTERACTIVE_NO, \backup::MODE_IMPORT,
            $USER->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        return $backupid;
    }

    /**
     * Restores a backup that has been made earlier.
     *
     * @param string $backupid The unique identifier of the backup.
     * @param int $courseid Course id of where the restore is happening.
     * @param string[] $expectedprecheckwarning
     */
    protected function restore_to_course(string $backupid, int $courseid, array $expectedprecheckwarning = []): void {
        global $CFG, $USER;

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = \backup::LOG_NONE;

        $rc = new \restore_controller($backupid, $courseid,
                \backup::INTERACTIVE_NO, \backup::MODE_GENERAL, $USER->id,
                \backup::TARGET_NEW_COURSE);

        $precheck = $rc->execute_precheck();
        if (!$expectedprecheckwarning) {
            $this->assertTrue($precheck);
        } else {
            $precheckresults = $rc->get_precheck_results();
            $this->assertEqualsCanonicalizing($expectedprecheckwarning, $precheckresults['warnings']);
            $this->assertCount(1, $precheckresults);
        }
        $rc->execute_plan();
        $rc->destroy();
    }

    /**
     * This function tests backup and restore of question tags.
     */
    public function test_backup_question_tags(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a new course category and a new course in that.
        $category1 = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $category1->id]);
        $courseshortname = $course->shortname;
        $coursefullname = $course->fullname;

        // Create 2 questions.
        $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qbank = $this->getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $context = \context_module::instance($qbank->cmid);
        $qcat = $qgen->create_question_category(['contextid' => $context->id]);
        $question1 = $qgen->create_question('shortanswer', null, ['category' => $qcat->id, 'idnumber' => 'q1']);
        $question2 = $qgen->create_question('shortanswer', null, ['category' => $qcat->id, 'idnumber' => 'q2']);

        // Tag the questions with 2 question tags.
        $qcontext = \context::instance_by_id($qcat->contextid);
        $coursecontext = context_course::instance($course->id);
        \core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['qtag1', 'qtag2']);
        \core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['qtag3', 'qtag4']);

        // Create a quiz and add one of the questions to that.
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        quiz_add_quiz_question($question1->id, $quiz);

        // Backup the course twice for future use.
        $backupid1 = $this->backup_course($course);
        $backupid2 = $this->backup_course($course);

        // Now delete almost everything.
        delete_course($course, false);
        question_delete_question($question1->id);
        question_delete_question($question2->id);

        // Restore the backup we had made earlier into a new course.
        // Do restore to new course with default settings.
        $courseid2 = \restore_dbops::create_new_course($coursefullname, $courseshortname . '_2', $category1->id);
        $this->restore_to_course($backupid1, $courseid2);
        $modinfo = get_fast_modinfo($courseid2);
        $qbanks = $modinfo->get_instances_of('qbank');
        $qbanks = array_filter($qbanks, static fn($qbank) => $qbank->get_name() === 'Question bank 1');
        $this->assertCount(1, $qbanks);
        $qbank = reset($qbanks);
        $qbankcontext = \context_module::instance($qbank->id);
        $cats = $DB->get_records_select('question_categories' , 'parent <> 0', ['contextid' => $qbankcontext->id]);
        $this->assertCount(1, $cats);
        $cat = reset($cats);

        // The questions should be restored to a mod_qbank context in the new course.
        $sql = 'SELECT q.*,
                       qbe.idnumber
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                 WHERE qbe.questioncategoryid = ?
                 ORDER BY qbe.idnumber';
        $questions = $DB->get_records_sql($sql, [$cat->id]);
        $this->assertCount(2, $questions);

        // Retrieve tags for each question and check if they are assigned at the right context.
        $qcount = 1;
        foreach ($questions as $question) {
            $tags = \core_tag_tag::get_item_tags('core_question', 'question', $question->id);

            // Each question is tagged with 4 tags (2 question tags + 2 course tags).
            $this->assertCount(2, $tags);

            foreach ($tags as $tag) {
                $this->assertEquals($qbankcontext->id, $tag->taginstancecontextid);
            }

            // Also check idnumbers have been backed up and restored.
            $this->assertEquals('q' . $qcount, $question->idnumber);
            $qcount++;
        }

        // Now, again, delete everything including the course category.
        delete_course($courseid2, false);
        foreach ($questions as $question) {
            question_delete_question($question->id);
        }
        $category1->delete_full(false);

        // Create a new course category to restore the backup file into it.
        $category2 = $this->getDataGenerator()->create_category();

        // Restore to a new course in the new course category.
        $courseid3 = \restore_dbops::create_new_course($coursefullname, $courseshortname . '_3', $category2->id);
        $this->restore_to_course($backupid2, $courseid3);
        $modinfo = get_fast_modinfo($courseid3);
        $qbanks = $modinfo->get_instances_of('qbank');
        $qbanks = array_filter($qbanks, static fn($qbank) => $qbank->get_name() === 'Question bank 1');
        $this->assertCount(1, $qbanks);
        $qbank = reset($qbanks);
        $context = \context_module::instance($qbank->id);

        // The questions should have been moved to a question category that belongs to a course context.
        $questions = $DB->get_records_sql("SELECT q.*
                                                FROM {question} q
                                                JOIN {question_versions} qv ON qv.questionid = q.id
                                                JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                                                JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
                                               WHERE qc.contextid = ?", [$context->id]);
        $this->assertCount(2, $questions);

        // Now, retrieve tags for each question and check if they are assigned at the right context.
        foreach ($questions as $question) {
            $tags = \core_tag_tag::get_item_tags('core_question', 'question', $question->id);

            // Each question is tagged with 2 tags (all are question context tags now).
            $this->assertCount(2, $tags);

            foreach ($tags as $tag) {
                $this->assertEquals($context->id, $tag->taginstancecontextid);
            }
        }

    }

    /**
     * Test that the question author is retained when they are enrolled in to the course.
     */
    public function test_backup_question_author_retained_when_enrolled(): void {
        global $DB, $USER, $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course, a category and a user.
        $course = $this->getDataGenerator()->create_course();
        $category = $this->getDataGenerator()->create_category();
        $user = $this->getDataGenerator()->create_user();

        // Create a question.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $questioncategory = $questiongenerator->create_question_category();
        $overrides = ['name' => 'Test question', 'category' => $questioncategory->id,
                'createdby' => $user->id, 'modifiedby' => $user->id];
        $question = $questiongenerator->create_question('truefalse', null, $overrides);

        // Create a quiz and a questions.
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));
        quiz_add_quiz_question($question->id, $quiz);

        // Enrol user with a teacher role.
        $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher']);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $teacherrole->id, 'manual');

        // Backup the course.
        $bc = new \backup_controller(\backup::TYPE_1COURSE, $course->id, \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO, \backup::MODE_GENERAL, $USER->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];
        $fp = get_file_packer('application/vnd.moodle.backup');
        $filepath = $CFG->dataroot . '/temp/backup/' . $backupid;
        $file->extract_to_pathname($fp, $filepath);
        $bc->destroy();

        // Delete the original course and related question.
        delete_course($course, false);
        question_delete_question($question->id);

        // Restore the course.
        $restoredcourseid = \restore_dbops::create_new_course($course->fullname, $course->shortname . '_1', $category->id);
        $rc = new \restore_controller($backupid, $restoredcourseid, \backup::INTERACTIVE_NO,
            \backup::MODE_GENERAL, $USER->id, \backup::TARGET_NEW_COURSE);
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        // Test the question author.
        $questions = $DB->get_records('question', ['name' => 'Test question']);
        $this->assertCount(1, $questions);
        $question3 = array_shift($questions);
        $this->assertEquals($user->id, $question3->createdby);
        $this->assertEquals($user->id, $question3->modifiedby);
    }

    /**
     * Test that the question author is retained when they are not enrolled in to the course,
     * but we are restoring the backup at the same site.
     */
    public function test_backup_question_author_retained_when_not_enrolled(): void {
        global $DB, $USER, $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course, a category and a user.
        $course = $this->getDataGenerator()->create_course();
        $category = $this->getDataGenerator()->create_category();
        $user = $this->getDataGenerator()->create_user();

        // Create a question.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $questioncategory = $questiongenerator->create_question_category();
        $overrides = ['name' => 'Test question', 'category' => $questioncategory->id,
                'createdby' => $user->id, 'modifiedby' => $user->id];
        $question = $questiongenerator->create_question('truefalse', null, $overrides);

        // Create a quiz and a questions.
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));
        quiz_add_quiz_question($question->id, $quiz);

        // Backup the course.
        $bc = new \backup_controller(\backup::TYPE_1COURSE, $course->id, \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO, \backup::MODE_GENERAL, $USER->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];
        $fp = get_file_packer('application/vnd.moodle.backup');
        $filepath = $CFG->dataroot . '/temp/backup/' . $backupid;
        $file->extract_to_pathname($fp, $filepath);
        $bc->destroy();

        // Delete the original course and related question.
        delete_course($course, false);
        question_delete_question($question->id);

        // Restore the course.
        $restoredcourseid = \restore_dbops::create_new_course($course->fullname, $course->shortname . '_1', $category->id);
        $rc = new \restore_controller($backupid, $restoredcourseid, \backup::INTERACTIVE_NO,
            \backup::MODE_GENERAL, $USER->id, \backup::TARGET_NEW_COURSE);
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        // Test the question author.
        $questions = $DB->get_records('question', ['name' => 'Test question']);
        $this->assertCount(1, $questions);
        $question = array_shift($questions);
        $this->assertEquals($user->id, $question->createdby);
        $this->assertEquals($user->id, $question->modifiedby);
    }

    /**
     * Test that the current user is set as a question author when we are restoring the backup
     * at the another site and the question author is not enrolled in to the course.
     */
    public function test_backup_question_author_reset(): void {
        global $DB, $USER, $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course, a category and a user.
        $course = $this->getDataGenerator()->create_course();
        $category = $this->getDataGenerator()->create_category();
        $user = $this->getDataGenerator()->create_user();

        // Create a question.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $questioncategory = $questiongenerator->create_question_category();
        $overrides = ['name' => 'Test question', 'category' => $questioncategory->id,
                'createdby' => $user->id, 'modifiedby' => $user->id];
        $question = $questiongenerator->create_question('truefalse', null, $overrides);

        // Create a quiz and a questions.
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));
        quiz_add_quiz_question($question->id, $quiz);

        // Backup the course.
        $bc = new \backup_controller(\backup::TYPE_1COURSE, $course->id, \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO, \backup::MODE_SAMESITE, $USER->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];
        $fp = get_file_packer('application/vnd.moodle.backup');
        $filepath = $CFG->dataroot . '/temp/backup/' . $backupid;
        $file->extract_to_pathname($fp, $filepath);
        $bc->destroy();

        // Delete the original course and related question.
        delete_course($course, false);
        question_delete_question($question->id);

        // Emulate restoring to a different site.
        set_config('siteidentifier', random_string(32) . 'not the same site');

        // Restore the course.
        $restoredcourseid = \restore_dbops::create_new_course($course->fullname, $course->shortname . '_1', $category->id);
        $rc = new \restore_controller($backupid, $restoredcourseid, \backup::INTERACTIVE_NO,
            \backup::MODE_SAMESITE, $USER->id, \backup::TARGET_NEW_COURSE);
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        // Test the question author.
        $questions = $DB->get_records('question', ['name' => 'Test question']);
        $this->assertCount(1, $questions);
        $question = array_shift($questions);
        $this->assertEquals($USER->id, $question->createdby);
        $this->assertEquals($USER->id, $question->modifiedby);
    }

    public function test_backup_and_restore_recodes_links_in_questions(): void {
        global $DB, $USER, $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and a category.
        $course = $this->getDataGenerator()->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $category = $this->getDataGenerator()->create_category();

        // Create a question with links in all the places that should be recoded.
        $testlink = new moodle_url('/course/view.php', ['id' => $course->id]);
        $testcontent = 'Look at <a href="' . $testlink . '">the course</a>.';
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $questioncategory = $questiongenerator->create_question_category(
            ['contextid' => \context_module::instance($qbank->cmid)->id]);
        $question = $questiongenerator->create_question('multichoice', null, [
            'name' => 'Test question',
            'category' => $questioncategory->id,
            'questiontext' => ['text' => 'This is the question. ' . $testcontent],
            'generalfeedback' => ['text' => 'Why is this right? ' . $testcontent],
            'answer' => [
                '0' => ['text' => 'Choose me! ' . $testcontent],
            ],
            'feedback' => [
                '0' => ['text' => 'The reason: ' . $testcontent],
            ],
            'hint' => [
                '0' => ['text' => 'Hint: ' . $testcontent],
            ],
        ]);

        // Create a quiz and add the question.
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        quiz_add_quiz_question($question->id, $quiz);

        // Backup and restore the course.
        $backupid = $this->backup_course($course);
        $newcourse = $this->getDataGenerator()->create_course();
        $this->restore_to_course($backupid, $newcourse->id);
        $modinfo = get_fast_modinfo($newcourse);
        $qbanks = $modinfo->get_instances_of('qbank');
        $qbank = reset($qbanks);

        // Get the question from the restored course - we are expecting just one, but that is not the real test here.
        $restoredquestions = $DB->get_records_sql("
                SELECT q.id, q.name
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                  JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
                 WHERE qc.contextid = ?
            ", [\context_module::instance($qbank->id)->id]);
        $this->assertCount(1, $restoredquestions);
        $questionid = array_key_first($restoredquestions);
        $this->assertEquals('Test question', $restoredquestions[$questionid]->name);

        // Verify the links have been recoded.
        $restoredquestion = question_bank::load_question_data($questionid);
        $recodedlink = new moodle_url('/course/view.php', ['id' => $newcourse->id]);
        $recodedcontent = 'Look at <a href="' . $recodedlink . '">the course</a>.';
        $firstanswerid = array_key_first($restoredquestion->options->answers);
        $firsthintid = array_key_first($restoredquestion->hints);

        $this->assertEquals('This is the question. ' . $recodedcontent, $restoredquestion->questiontext);
        $this->assertEquals('Why is this right? ' . $recodedcontent, $restoredquestion->generalfeedback);
        $this->assertEquals('Choose me! ' . $recodedcontent, $restoredquestion->options->answers[$firstanswerid]->answer);
        $this->assertEquals('The reason: ' . $recodedcontent, $restoredquestion->options->answers[$firstanswerid]->feedback);
        $this->assertEquals('Hint: ' . $recodedcontent, $restoredquestion->hints[$firsthintid]->hint);
    }

    /**
     * Boilerplate setup for the tests. Creates a course, a quiz, and a qbank module. It adds a category to each module context
     * and adds a question to each category. Finally, it adds the 2 questions to the quiz.
     *
     * @return \stdClass
     */
    private function add_course_quiz_and_qbank() {
        $qgen = self::getDataGenerator()->get_plugin_generator('core_question');

        // Create a new course.
        $course = self::getDataGenerator()->create_course();

        // Create a question bank module instance, a category for that module, and a question for that category.
        $qbank = self::getDataGenerator()->create_module(
            'qbank',
            ['type' => question_bank_helper::TYPE_STANDARD, 'course' => $course->id]
        );
        $qbankcontext = \context_module::instance($qbank->cmid);
        $bankqcat = $qgen->create_question_category(['contextid' => $qbankcontext->id]);
        $bankquestion = $qgen->create_question('shortanswer',
            null,
            ['name' => 'bank question', 'category' => $bankqcat->id, 'idnumber' => 'bankq1']
        );

        // Create a quiz module instance, a category for that module, and a question for that category.
        $quiz = self::getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $quizcontext = \context_module::instance($quiz->cmid);
        $quizqcat = $qgen->create_question_category(['contextid' => $quizcontext->id]);
        $quizquestion = $qgen->create_question('shortanswer',
            null,
            ['name' => 'quiz question', 'category' => $quizqcat->id, 'idnumber' => 'quizq1']
        );

        quiz_add_quiz_question($bankquestion->id, $quiz);
        quiz_add_quiz_question($quizquestion->id, $quiz);

        $data = new \stdClass();
        $data->course = $course;
        $data->qbank = $qbank;
        $data->qbankcategory = $bankqcat;
        $data->qbankquestion = $bankquestion;
        $data->quiz = $quiz;
        $data->quizcategory = $quizqcat;
        $data->quizquestion = $quizquestion;

        return $data;
    }

    /**
     * If the backup contains ONLY a quiz but that quiz uses questions from a qbank module and itself,
     * and the original course does not exist on the target system,
     * then the non-quiz context categories and questions should restore to a default qbank module on the new course
     * if the old qbank no longer exists.
     */
    public function test_quiz_activity_restore_to_new_course(): void {
        global $DB;

        $this->resetAfterTest();
        self::setAdminUser();

        // Create a course to make a backup.
        $data = $this->add_course_quiz_and_qbank();
        $oldquiz = $data->quiz;

        // Backup ONLY the quiz module.
        $backupid = $this->backup_course_module($oldquiz->cmid);

        // Create a new course to restore to.
        $newcourse = self::getDataGenerator()->create_course();
        delete_course($data->course->id, false);

        $this->restore_to_course($backupid, $newcourse->id);
        $modinfo = get_fast_modinfo($newcourse);

        // Assert we have our quiz including the category and question.
        $newquizzes = $modinfo->get_instances_of('quiz');
        $this->assertCount(1, $newquizzes);
        $newquiz = reset($newquizzes);
        $newquizcontext = \context_module::instance($newquiz->id);

        $quizcats = $DB->get_records_select('question_categories',
            'parent <> 0 AND contextid = :contextid',
            ['contextid' => $newquizcontext->id]
        );
        $this->assertCount(1, $quizcats);
        $quizcat = reset($quizcats);
        $quizcatqs = get_questions_category($quizcat, false);
        $this->assertCount(1, $quizcatqs);
        $quizq = reset($quizcatqs);
        $this->assertEquals('quiz question', $quizq->name);

        // The backup did not contain the qbank that held the categories, but it is dependant.
        // So make sure the categories and questions got restored to a 'system' type default qbank module on the course.
        $defaultbanks = $modinfo->get_instances_of('qbank');
        $this->assertCount(1, $defaultbanks);
        $defaultbank = reset($defaultbanks);
        $defaultbankcontext = \context_module::instance($defaultbank->id);
        $bankcats = $DB->get_records_select('question_categories',
            'parent <> 0 AND contextid = :contextid',
            ['contextid' => $defaultbankcontext->id]
        );
        $bankcat = reset($bankcats);
        $bankqs = get_questions_category($bankcat, false);
        $this->assertCount(1, $bankqs);
        $bankq = reset($bankqs);
        $this->assertEquals('bank question', $bankq->name);
    }

    /**
     * If the backup contains ONLY a quiz but that quiz uses questions from a qbank module and itself,
     * and the original course does exist on the target system but you dont have permission to view the original qbank,
     * then the non-quiz context categories and questions should restore to a default qbank module on the new course
     * if the old qbank no longer exists.
     */
    public function test_quiz_activity_restore_to_new_course_no_permission(): void {
        global $DB;

        $this->resetAfterTest();
        self::setAdminUser();

        // Create a course to make a backup.
        $data = $this->add_course_quiz_and_qbank();
        $oldquiz = $data->quiz;

        // Backup ONLY the quiz module.
        $backupid = $this->backup_course_module($oldquiz->cmid);

        // Create a new course to restore to.
        $newcourse = self::getDataGenerator()->create_course();
        $restoreuser = self::getDataGenerator()->create_user();
        self::getDataGenerator()->enrol_user($restoreuser->id, $newcourse->id, 'manager');
        $this->setUser($restoreuser);

        $this->restore_to_course($backupid, $newcourse->id);
        $modinfo = get_fast_modinfo($newcourse);

        // Assert we have our quiz including the category and question.
        $newquizzes = $modinfo->get_instances_of('quiz');
        $this->assertCount(1, $newquizzes);
        $newquiz = reset($newquizzes);
        $newquizcontext = \context_module::instance($newquiz->id);

        $quizcats = $DB->get_records_select('question_categories',
            'parent <> 0 AND contextid = :contextid',
            ['contextid' => $newquizcontext->id]
        );
        $this->assertCount(1, $quizcats);
        $quizcat = reset($quizcats);
        $quizcatqs = get_questions_category($quizcat, false);
        $this->assertCount(1, $quizcatqs);
        $quizq = reset($quizcatqs);
        $this->assertEquals('quiz question', $quizq->name);

        // The backup did not contain the qbank that held the categories, but it is dependant.
        // So make sure the categories and questions got restored to a qbank module on the course.
        $defaultbanks = $modinfo->get_instances_of('qbank');
        $this->assertCount(1, $defaultbanks);
        $defaultbank = reset($defaultbanks);
        $defaultbankcontext = \context_module::instance($defaultbank->id);
        $bankcats = $DB->get_records_select('question_categories',
            'parent <> 0 AND contextid = :contextid',
            ['contextid' => $defaultbankcontext->id]
        );
        $bankcat = reset($bankcats);
        $bankqs = get_questions_category($bankcat, false);
        $this->assertCount(1, $bankqs);
        $bankq = reset($bankqs);
        $this->assertEquals('bank question', $bankq->name);
    }

    /**
     * If the backup contains ONLY a quiz but that quiz uses questions from a qbank module and itself,
     * and that qbank still exists on the system, and the restoring user can access that qbank, then
     * the quiz should be restored with a copy of the quiz question, and a reference to the original qbank question.
     */
    public function test_quiz_activity_restore_to_new_course_by_reference(): void {
        global $DB;

        $this->resetAfterTest();
        self::setAdminUser();

        // Create a course to make a backup.
        $data = $this->add_course_quiz_and_qbank();
        $oldquiz = $data->quiz;

        // Backup ONLY the quiz module.
        $backupid = $this->backup_course_module($oldquiz->cmid);

        // Create a new course to restore to.
        $newcourse = self::getDataGenerator()->create_course();

        $this->restore_to_course($backupid, $newcourse->id);
        $modinfo = get_fast_modinfo($newcourse);

        // Assert we have our new quiz with the expected questions.
        $newquizzes = $modinfo->get_instances_of('quiz');
        $this->assertCount(1, $newquizzes);
        /** @var \cm_info $newquiz */
        $newquiz = reset($newquizzes);
        $quiz = $DB->get_record('quiz', ['id' => $newquiz->instance], '*', MUST_EXIST);
        [$course, $cm] = get_course_and_cm_from_instance($quiz, 'quiz');
        $newquizsettings = new quiz_settings($quiz, $cm, $course);
        $newq1 = $newquizsettings->get_structure()->get_question_in_slot(1);
        $newq2 = $newquizsettings->get_structure()->get_question_in_slot(2);

        $newquizcontext = \context_module::instance($newquiz->id);
        $qbankcontext = \context_module::instance($data->qbank->cmid);

        // Check we've got a copy of the quiz question in the new context.
        $this->assertEquals($data->quizquestion->name, $newq2->name);
        $this->assertEquals($newquizcontext->id, $newq2->contextid);
        // Check we've got a reference to the qbank question in the original context.
        $this->assertEquals($data->qbankquestion->name, $newq1->name);
        $this->assertEquals($qbankcontext->id, $newq1->contextid);
        // Check we have the expected restored categories.
        $this->assertEquals(2, $DB->count_records('question_categories', ['stamp' => $data->quizcategory->stamp]));
        $this->assertEquals(1, $DB->count_records('question_categories', ['stamp' => $data->qbankcategory->stamp]));
    }

    /**
     * If the backup contains BOTH a quiz and a qbank module and the quiz uses questions from the qbank module and itself,
     * then we need to restore the categories and questions to the qbank and quiz modules included in the backup on the new course.
     *
     * @return void
     * @covers \restore_controller::execute_plan()
     */
    public function test_bank_and_quiz_activity_restore_to_new_course(): void {
        // Create a new course.
        global $DB;

        $this->resetAfterTest();
        self::setAdminUser();

        // Create a course to make a backup from.
        $data = $this->add_course_quiz_and_qbank();
        $oldcourse = $data->course;

        // Backup the course.
        $backupid = $this->backup_course($oldcourse);

        // Create a new course to restore to.
        $newcourse = self::getDataGenerator()->create_course();

        // Restore it.
        $this->restore_to_course($backupid, $newcourse->id);

        // Assert the quiz got its question catregories restored.
        $modinfo = get_fast_modinfo($newcourse);
        $newquizzes = $modinfo->get_instances_of('quiz');
        $this->assertCount(1, $newquizzes);
        $newquiz = reset($newquizzes);
        $newquizcontext = \context_module::instance($newquiz->id);
        $quizcats = $DB->get_records_select('question_categories',
            'parent <> 0 AND contextid = :contextid',
            ['contextid' => $newquizcontext->id]
        );
        $quizcat = reset($quizcats);
        $quizcatqs = get_questions_category($quizcat, false);
        $this->assertCount(1, $quizcatqs);
        $quizcatq = reset($quizcatqs);
        $this->assertEquals('quiz question', $quizcatq->name);

        // Assert the qbank got its questions restored to the module in the backup.
        $qbanks = $modinfo->get_instances_of('qbank');
        $qbanks = array_filter($qbanks, static function($bank) {
            global $DB;
            $modrecord = $DB->get_record('qbank', ['id' => $bank->instance]);
            return $modrecord->type === question_bank_helper::TYPE_STANDARD;
        });
        $this->assertCount(1, $qbanks);
        $qbank = reset($qbanks);
        $bankcats = $DB->get_records_select('question_categories',
            'parent <> 0 AND contextid = :contextid',
            ['contextid' => \context_module::instance($qbank->id)->id]
        );
        $bankcat = reset($bankcats);
        $bankqs = get_questions_category($bankcat, false);
        $this->assertCount(1, $bankqs);
        $bankq = reset($bankqs);
        $this->assertEquals('bank question', $bankq->name);
    }

    /**
     * The course backup file contains question banks and a quiz module.
     * There is 1 question bank category per deprecated context level i.e. CONTEXT_SYSTEM, CONTEXT_COURSECAT, and CONTEXT_COURSE.
     * The quiz included in the backup uses a question in each category.
     *
     * @return void
     * @covers \restore_controller::execute_plan()
     */
    public function test_pre_46_course_restore_to_new_course(): void {
        global $DB, $USER;
        self::setAdminUser();
        $this->resetAfterTest();

        $backupid = 'question_category_45_format';
        $backuppath = make_backup_temp_directory($backupid);
        get_file_packer('application/vnd.moodle.backup')->extract_to_pathname(
            __DIR__ . "/fixtures/{$backupid}.mbz",
            $backuppath
        );

        // Do restore to new course with default settings.
        $categoryid = $DB->get_field_sql("SELECT MIN(id) FROM {course_categories}");
        $newcourseid = restore_dbops::create_new_course('Test fullname', 'Test shortname', $categoryid);
        $rc = new restore_controller($backupid, $newcourseid,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
            backup::TARGET_NEW_COURSE
        );

        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        $modinfo = get_fast_modinfo($newcourseid);

        $qbanks = $modinfo->get_instances_of('qbank');
        $qbanks = array_filter($qbanks, static function($bank) {
            global $DB;
            $modrecord = $DB->get_record('qbank', ['id' => $bank->instance]);
            return $modrecord->type === question_bank_helper::TYPE_SYSTEM;
        });
        $this->assertCount(1, $qbanks);
        $qbank = reset($qbanks);
        $qbankcontext = \context_module::instance($qbank->id);
        $bankcats = $DB->get_records_select('question_categories',
            'parent <> 0 AND contextid = :contextid',
            ['contextid' => $qbankcontext->id],
            'name ASC'
        );
        // The categories and questions in the 3 deprecated contexts
        // all got moved to the new default qbank module instance on the new course.
        $this->assertCount(3, $bankcats);
        $expectedidentifiers = [
            'Default for Category 1',
            'Default for System',
            'Default for Test Course 1',
            'Default for Quiz',
        ];
        $i = 0;

        foreach ($bankcats as $bankcat) {
            $identifer = $expectedidentifiers[$i];
            $this->assertEquals($identifer, $bankcat->name);
            $bankcatqs = get_questions_category($bankcat, false);
            $this->assertCount(1, $bankcatqs);
            $bankcatq = reset($bankcatqs);
            $this->assertEquals($identifer, $bankcatq->name);
            $i++;
        }

        // The question category and question attached to the quiz got restored to its own context correctly.
        $newquizzes = $modinfo->get_instances_of('quiz');
        $this->assertCount(1, $newquizzes);
        $newquiz = reset($newquizzes);
        $newquizcontext = \context_module::instance($newquiz->id);
        $quizcats = $DB->get_records_select('question_categories',
            'parent <> 0 AND contextid = :contextid',
            ['contextid' => $newquizcontext->id]
        );
        $quizcat = reset($quizcats);
        $quizcatqs = get_questions_category($quizcat, false);
        $this->assertCount(1, $quizcatqs);
        $quizcatq = reset($quizcatqs);
        $this->assertEquals($expectedidentifiers[$i], $quizcatq->name);
    }
}
