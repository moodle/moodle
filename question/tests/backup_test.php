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
 * Unit tests for question backup and restore.
 *
 * @package    core_question
 * @category   test
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Class core_question_backup_testcase
 *
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_question_backup_testcase extends advanced_testcase {

    /**
     * Makes a backup of the course.
     *
     * @param stdClass $course The course object.
     * @return string Unique identifier for this backup.
     */
    protected function backup_course($course) {
        global $CFG, $USER;

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        // Do backup with default settings. MODE_IMPORT means it will just
        // create the directory and not zip it.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id,
                backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
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
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        // Do restore to new course with default settings.
        $newcourseid = restore_dbops::create_new_course($fullname, $shortname, $categoryid);
        $rc = new restore_controller($backupid, $newcourseid,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
                backup::TARGET_NEW_COURSE);

        $precheck = $rc->execute_precheck();
        if (!$expectedprecheckwarning) {
            $this->assertTrue($precheck);
        } else {
            $precheckresults = $rc->get_precheck_results();
            $this->assertEquals(['warnings' => $expectedprecheckwarning], $precheckresults);
        }
        $rc->execute_plan();
        $rc->destroy();

        return $newcourseid;
    }

    /**
     * This function tests backup and restore of question tags and course level question tags.
     */
    public function test_backup_question_tags() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a new course category and and a new course in that.
        $category1 = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(array('category' => $category1->id));
        $courseshortname = $course->shortname;
        $coursefullname = $course->fullname;

        // Create 2 questions.
        $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');
        $context = context_coursecat::instance($category1->id);
        $qcat = $qgen->create_question_category(array('contextid' => $context->id));
        $question1 = $qgen->create_question('shortanswer', null, array('category' => $qcat->id));
        $question2 = $qgen->create_question('shortanswer', null, array('category' => $qcat->id));

        // Tag the questions with 2 question tags and 2 course level question tags.
        $qcontext = context::instance_by_id($qcat->contextid);
        $coursecontext = context_course::instance($course->id);
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $qcontext, ['qtag1', 'qtag2']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $qcontext, ['qtag3', 'qtag4']);
        core_tag_tag::set_item_tags('core_question', 'question', $question1->id, $coursecontext, ['ctag1', 'ctag2']);
        core_tag_tag::set_item_tags('core_question', 'question', $question2->id, $coursecontext, ['ctag3', 'ctag4']);

        // Create a quiz and add one of the questions to that.
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));
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
        $questions = $DB->get_records('question', array('category' => $qcat->id));
        $this->assertCount(2, $questions);

        // Retrieve tags for each question and check if they are assigned at the right context.
        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);

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
        }

        // Now, again, delete everything including the course category.
        delete_course($courseid2, false);
        foreach ($questions as $question) {
            question_delete_question($question->id);
        }
        $category1->delete_full(false);

        // Create a new course category to restore the backup file into it.
        $category2 = $this->getDataGenerator()->create_category();

        $expectedwarnings = array(
                get_string('qcategory2coursefallback', 'backup', (object) ['name' => 'top']),
                get_string('qcategory2coursefallback', 'backup', (object) ['name' => $qcat->name])
        );

        // Restore to a new course in the new course category.
        $courseid3 = $this->restore_course($backupid2, $coursefullname, $courseshortname . '_3', $category2->id, $expectedwarnings);
        $coursecontext3 = context_course::instance($courseid3);

        // The questions should have been moved to a question category that belongs to a course context.
        $questions = $DB->get_records_sql("SELECT q.*
                                             FROM {question} q
                                             JOIN {question_categories} qc ON q.category = qc.id
                                            WHERE qc.contextid = ?", array($coursecontext3->id));
        $this->assertCount(2, $questions);

        // Now, retrieve tags for each question and check if they are assigned at the right context.
        foreach ($questions as $question) {
            $tags = core_tag_tag::get_item_tags('core_question', 'question', $question->id);

            // Each question is tagged with 4 tags (all are course tags now).
            $this->assertCount(4, $tags);

            foreach ($tags as $tag) {
                $this->assertEquals($coursecontext3->id, $tag->taginstancecontextid);
            }
        }

    }
}