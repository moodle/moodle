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

namespace qtype_shortanswer;

/**
 * Unit tests for restore_qtype_shortanswer_plugin
 *
 * @package   qtype_shortanswer
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \restore_qtype_shortanswer_plugin
 */
final class restore_test extends \advanced_testcase {
    /**
     * Duplicate a quiz containing a shortanswer question with no options record.
     */
    public function test_restore_quiz_with_edited_questions(): void {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and a user with editing teacher capabilities.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course();
        $qbank = $generator->get_plugin_generator('mod_qbank')->create_instance(['course' => $course1->id]);
        $context = \context_module::instance($qbank->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $initialcount = $DB->count_records('question');

        // Create a question category.
        $cat = $questiongenerator->create_question_category(['contextid' => $context->id]);

        // Create a quiz containing a multichoice question from the qbank.
        $quiz = $this->getDataGenerator()->get_plugin_generator('mod_quiz')->create_instance(['course' => $course1->id]);
        $question = $questiongenerator->create_question('shortanswer', 'frogtoad', ['category' => $cat->id]);
        quiz_add_quiz_question($question->id, $quiz);

        // Delete the multichoice_options record.
        $DB->delete_records('qtype_shortanswer_options', ['questionid' => $question->id]);

        // Confirm we have created 1 additional question.
        $this->assertEquals($initialcount + 1, $DB->count_records('question'));

        // Backup quiz.
        $bc = new \backup_controller(\backup::TYPE_1ACTIVITY, $quiz->cmid, \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO, \backup::MODE_IMPORT, $USER->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Restore the backup into the same course.
        $rc = new \restore_controller($backupid, $course1->id, \backup::INTERACTIVE_NO, \backup::MODE_IMPORT,
            $USER->id, \backup::TARGET_CURRENT_ADDING);
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        $debugging = "Failed to load question options from the table qtype_shortanswer_options for questionid {$question->id}";
        $this->assertdebuggingcalledcount(2, [$debugging, $debugging]);

        // Both quizzes should refer to the same original question.
        $quizzes = get_fast_modinfo($course1->id)->get_instances_of('quiz');
        $this->assertCount(2, $quizzes);
        foreach ($quizzes as $quiz) {
            $structure = \mod_quiz\question\bank\qbank_helper::get_question_structure($quiz->instance, $quiz->context);
            $this->assertEquals($structure[1]->questionid, $question->id);
        }

        // There should be no additional questions created during the restore.
        $this->assertEquals($initialcount + 1, $DB->count_records('question'));
    }
}
