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

namespace qbank_comment;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot. '/comment/lib.php');

/**
 * Question comment backup and restore unit tests.
 *
 * @package    qbank_comment
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_test extends \advanced_testcase {

    /**
     * @var array Data object for generating a question.
     */
    protected $question1data;

    /**
     * @var array Data object for generating a question.
     */
    protected $question2data;

    /**
     * @var component_generator_base Question Generator.
     */
    protected $qgen;

    /**
     * @var core_course_category Course category.
     */
    protected $category;

    /**
     * @var stdClass Course object.
     */
    protected $course;

    /**
     * Set up
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();

        // Set up custom fields.
        $data = new \stdClass();
        $data->component = 'qbank_comment';
        $data->area = 'question';

        // Question initial set up.
        $this->category = $this->getDataGenerator()->create_category();
        $this->course = $this->getDataGenerator()->create_course(['category' => $this->category->id]);
        $context = \context_coursecat::instance($this->category->id);
        $this->qgen = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcat = $this->qgen->create_question_category(['contextid' => $context->id]);

        $this->question1data = ['category' => $qcat->id, 'idnumber' => 'q1'];
        $this->question2data = ['category' => $qcat->id, 'idnumber' => 'q2'];
    }

    /**
     * Makes a backup of the course.
     *
     * @param \stdClass $course The course object.
     * @return string Unique identifier for this backup.
     */
    protected function backup_course(\stdClass $course): string {
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
     * @return int The new course id.
     */
    protected function restore_course(string $backupid, string $fullname, string $shortname, int $categoryid): int {
        global $CFG, $USER;

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = \backup::LOG_NONE;

        // Do restore to new course with default settings.
        $newcourseid = \restore_dbops::create_new_course($fullname, $shortname, $categoryid);
        $rc = new \restore_controller($backupid, $newcourseid,
                \backup::INTERACTIVE_NO, \backup::MODE_GENERAL, $USER->id,
                \backup::TARGET_NEW_COURSE);

        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        return $newcourseid;
    }

    /**
     * Test comments attached to questions persist
     * across the backup and restore process.
     */
    public function test_backup_restore(): void {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/comment/lib.php');
        $this->resetAfterTest();
        $this->setAdminUser();

        $courseshortname = $this->course->shortname;
        $coursefullname = $this->course->fullname;

        // Create 2 questions.
        $question1 = $this->qgen->create_question('shortanswer', null, $this->question1data);
        $question2 = $this->qgen->create_question('shortanswer', null, $this->question2data);

        // Add comments to the questions.
        $args = new \stdClass;
        $args->context = \context_system::instance();
        $args->course = $this->course;
        $args->area = 'question';
        $args->itemid = $question1->id;
        $args->component = 'qbank_comment';
        $args->linktext = get_string('commentheader', 'qbank_comment');
        $args->notoggle = true;
        $args->autostart = true;
        $args->displaycancel = false;

        // Two comments for question 1.
        $commentobj1 = new \comment($args);
        $commentobj1->add('new \comment for question 1 _ 1');
        $comment1 = $commentobj1->add('new \comment for question 1 _ 2');

        // One comment for question 2.
        $args->itemid = $question2->id;
        $commentobj2 = new \comment($args);
        $comment2 = $commentobj2->add('new \comment for question 2');

        // Create a quiz and the questions to that.
        $quiz = $this->getDataGenerator()->create_module(
                'quiz', ['course' => $this->course->id, 'name' => 'restored_quiz']);
        quiz_add_quiz_question($question1->id, $quiz);
        quiz_add_quiz_question($question2->id, $quiz);

        // Backup the course.
        $backupid = $this->backup_course($this->course);

        // Now delete everything.
        delete_course($this->course, false);
        question_delete_question($question1->id);
        question_delete_question($question2->id);

        // Check the comment data for the questions has also gone.
        $DB->record_exists('comments', ['id' => $comment1->id]);
        $this->assertFalse($DB->record_exists('comments', ['id' => $comment1->id]));
        $this->assertFalse($DB->record_exists('comments', ['id' => $comment2->id]));

        // Restore the backup we had made earlier into a new course.
        $newcategory = $this->getDataGenerator()->create_category();
        $this->restore_course($backupid, $coursefullname, $courseshortname . '_2', $newcategory->id);

        // The questions and their associated comments should have been restored.
        $sql =
            'SELECT q.*
               FROM {question} q
               JOIN {question_versions} qv ON qv.questionid = q.id
               JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
               WHERE qbe.idnumber = ?';
        $newquestion1 = $DB->get_record_sql($sql, ['idnumber' => 'q1']);
        $args->itemid = $newquestion1->id;
        $commentobj = new \comment($args);
        $this->assertEquals($commentobj->count(), 2);

        $newquestion2 = $DB->get_record_sql($sql, ['idnumber' => 'q2']);
        $args->itemid = $newquestion2->id;
        $commentobj = new \comment($args);
        $this->assertEquals($commentobj->count(), 1);
    }
}
