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
use moodle_url;
use question_bank;

defined('MOODLE_INTERNAL') || die();

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
 */
class backup_test extends \advanced_testcase {

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
     * Restores a backup that has been made earlier.
     *
     * @param string $backupid The unique identifier of the backup.
     * @param string $fullname Full name of the new course that is going to be created.
     * @param string $shortname Short name of the new course that is going to be created.
     * @param int $categoryid The course category the backup is going to be restored in.
     * @param string[] $expectedprecheckwarning
     * @return int The new course id.
     */
    protected function restore_course($backupid, $fullname, $shortname, $categoryid, $expectedprecheckwarning = []) {
        global $CFG, $USER;

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = \backup::LOG_NONE;

        // Do restore to new course with default settings.
        $newcourseid = \restore_dbops::create_new_course($fullname, $shortname, $categoryid);
        $rc = new \restore_controller($backupid, $newcourseid,
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

        return $newcourseid;
    }

    /**
     * This function tests backup and restore of question tags and course level question tags.
     */
    public function test_backup_question_tags(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a new course category and and a new course in that.
        $category1 = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $category1->id]);
        $courseshortname = $course->shortname;
        $coursefullname = $course->fullname;

        // Create 2 questions.
        $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');
        $context = \context_coursecat::instance($category1->id);
        $qcat = $qgen->create_question_category(['contextid' => $context->id]);
        $question1 = $qgen->create_question('shortanswer', null, ['category' => $qcat->id, 'idnumber' => 'q1']);
        $question2 = $qgen->create_question('shortanswer', null, ['category' => $qcat->id, 'idnumber' => 'q2']);

        // Tag the questions with 2 question tags and 2 course level question tags.
        $qcontext = \context::instance_by_id($qcat->contextid);
        $coursecontext = context_course::instance($course->id);
        \core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['qtag1', 'qtag2']);
        \core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['qtag3', 'qtag4']);
        \core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['ctag1', 'ctag2']);
        \core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['ctag3', 'ctag4']);

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
        $courseid2 = $this->restore_course($backupid1, $coursefullname, $courseshortname . '_2', $category1->id);

        // The questions should remain in the question category they were which is
        // a question category belonging to a course category context.
        $sql = 'SELECT q.*,
                       qbe.idnumber
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                 WHERE qbe.questioncategoryid = ?
                 ORDER BY qbe.idnumber';
        $questions = $DB->get_records_sql($sql, [$qcat->id]);
        $this->assertCount(2, $questions);

        // Retrieve tags for each question and check if they are assigned at the right context.
        $qcount = 1;
        foreach ($questions as $question) {
            $tags = \core_tag_tag::get_item_tags('core_question', 'question', $question->id);

            // Each question is tagged with 4 tags (2 question tags + 2 course tags).
            $this->assertCount(4, $tags);

            foreach ($tags as $tag) {
                if (in_array($tag->name, ['ctag1', 'ctag2', 'ctag3', 'ctag4'])) {
                    $expected = context_course::instance($courseid2)->id;
                } else if (in_array($tag->name, ['qtag1', 'qtag2', 'qtag3', 'qtag4'])) {
                    $expected = $qcontext->id;
                }
                $this->assertEquals($expected, $tag->taginstancecontextid);
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

        $expectedwarnings = [
                get_string('qcategory2coursefallback', 'backup', (object) ['name' => 'top']),
                get_string('qcategory2coursefallback', 'backup', (object) ['name' => $qcat->name])
        ];

        // Restore to a new course in the new course category.
        $courseid3 = $this->restore_course($backupid2, $coursefullname, $courseshortname . '_3', $category2->id, $expectedwarnings);
        $coursecontext3 = context_course::instance($courseid3);

        // The questions should have been moved to a question category that belongs to a course context.
        $questions = $DB->get_records_sql("SELECT q.*
                                                FROM {question} q
                                                JOIN {question_versions} qv ON qv.questionid = q.id
                                                JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                                                JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
                                               WHERE qc.contextid = ?", [$coursecontext3->id]);
        $this->assertCount(2, $questions);

        // Now, retrieve tags for each question and check if they are assigned at the right context.
        foreach ($questions as $question) {
            $tags = \core_tag_tag::get_item_tags('core_question', 'question', $question->id);

            // Each question is tagged with 4 tags (all are course tags now).
            $this->assertCount(4, $tags);

            foreach ($tags as $tag) {
                $this->assertEquals($coursecontext3->id, $tag->taginstancecontextid);
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
        $category = $this->getDataGenerator()->create_category();

        // Create a question with links in all the places that should be recoded.
        $testlink = new moodle_url('/course/view.php', ['id' => $course->id]);
        $testcontent = 'Look at <a href="' . $testlink . '">the course</a>.';
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $questioncategory = $questiongenerator->create_question_category(
            ['contextid' => context_course::instance($course->id)->id]);
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
        $newcourseid = $this->restore_course($backupid, 'New course', 'C2', $category->id);

        // Get the question from the restored course - we are expecting just one, but that is not the real test here.
        $restoredquestions = $DB->get_records_sql("
                SELECT q.id, q.name
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                  JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
                 WHERE qc.contextid = ?
            ", [context_course::instance($newcourseid)->id]);
        $this->assertCount(1, $restoredquestions);
        $questionid = array_key_first($restoredquestions);
        $this->assertEquals('Test question', $restoredquestions[$questionid]->name);

        // Verify the links have been recoded.
        $restoredquestion = question_bank::load_question_data($questionid);
        $recodedlink = new moodle_url('/course/view.php', ['id' => $newcourseid]);
        $recodedcontent = 'Look at <a href="' . $recodedlink . '">the course</a>.';
        $firstanswerid = array_key_first($restoredquestion->options->answers);
        $firsthintid = array_key_first($restoredquestion->hints);

        $this->assertEquals('This is the question. ' . $recodedcontent, $restoredquestion->questiontext);
        $this->assertEquals('Why is this right? ' . $recodedcontent, $restoredquestion->generalfeedback);
        $this->assertEquals('Choose me! ' . $recodedcontent, $restoredquestion->options->answers[$firstanswerid]->answer);
        $this->assertEquals('The reason: ' . $recodedcontent, $restoredquestion->options->answers[$firstanswerid]->feedback);
        $this->assertEquals('Hint: ' . $recodedcontent, $restoredquestion->hints[$firsthintid]->hint);
    }
}
