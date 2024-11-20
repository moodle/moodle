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
        $coursecontext = \context_course::instance($course1->id);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a question category.
        $cat = $questiongenerator->create_question_category(['contextid' => $coursecontext->id]);

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
}
