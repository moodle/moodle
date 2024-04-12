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
use backup;
use restore_controller;

/**
 * Test restoring 3.9 backups including random questions.
 *
 * @package   mod_quiz
 * @copyright 2024 Tomo Tsuyuki <tomotsuyuki@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \restore_move_module_questions_categories
 */
final class restore_39_test extends advanced_testcase {

    public function test_restore_random_question_39(): void {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // The example Moodle 3.9 backup file used in this test is an activity-level backup of a quiz.
        // So, the backup contains just the quiz-level question bank which contains:
        // | - Question category: Top
        // |   - Question category: Default for Test MDL-78902 quiz
        // |     - Question: Test MDL-78902 T/F question
        // |     - Question: Random (Default for Test MDL-78902 quiz)
        // The quiz itself contains 1 question, the random question.
        // So, during the restore, the quiz_slot needs to be updated to use a question_set_reference.
        $backupfile = 'moodle_39_quiz_with_random_question_from_mod_context';

        // Extract backup file.
        $backupid = $backupfile;
        $backuppath = make_backup_temp_directory($backupid);
        get_file_packer('application/vnd.moodle.backup')->extract_to_pathname(
            __DIR__ . "/../fixtures/$backupfile.mbz", $backuppath);

        // Restore the quiz activity in the backup from Moodle 3.9 to a new course.
        $coursecat = self::getDataGenerator()->create_category();
        $course = self::getDataGenerator()->create_course(['category' => $coursecat->id]);
        $rc = new restore_controller($backupid, $course->id, backup::INTERACTIVE_NO,
            backup::MODE_GENERAL, $USER->id, backup::TARGET_EXISTING_ADDING);
        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        // Get information about the quiz activity and confirm the references are correct.
        $modinfo = get_fast_modinfo($course->id);
        $quizzes = array_values($modinfo->get_instances_of('quiz'));
        // Get contextid for the restored quiz activity.
        $contextid = $quizzes[0]->context->id;
        $qcats = $DB->get_records('question_categories', ['contextid' => $contextid], 'parent');
        // Confirm there are 2 question categories for the restored quiz activity.
        $this->assertEquals(['top', 'Default for Test MDL-78902 quiz'], array_column($qcats, 'name'));
        // Get question_set_references records for the restored quiz activity.
        $references = $DB->get_records('question_set_references', ['usingcontextid' => $contextid]);
        foreach ($references as $reference) {
            $filtercondition = json_decode($reference->filtercondition);
            // Confirm the questionscontextid is set correctly, which is from filter question category id.
            $this->assertEquals($reference->questionscontextid,
                $qcats[$filtercondition->questioncategoryid]->contextid);
        }
    }
}
