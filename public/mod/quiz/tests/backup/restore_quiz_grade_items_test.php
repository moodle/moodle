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

namespace mod_quiz;

use core_question_generator;
use mod_quiz_generator;
use restore_date_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . "/phpunit/classes/restore_date_testcase.php");

/**
 * Test of backup and restore of quiz grade items.
 *
 * @package   mod_quiz
 * @copyright 2023 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \backup_quiz_activity_structure_step
 * @covers    \restore_quiz_activity_structure_step
 */
final class restore_quiz_grade_items_test extends restore_date_testcase {

    public function test_restore_quiz_grade_items(): void {
        global $DB;
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        /** @var mod_quiz_generator $quizgen */
        $quizgen = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a quiz with two grade items.
        $course = $generator->create_course();
        $quiz = $quizgen->create_instance(['course' => $course->id]);
        $listeninggrade = $quizgen->create_grade_item(['quizid' => $quiz->id, 'name' => 'Listening']);
        $readinggrade = $quizgen->create_grade_item(['quizid' => $quiz->id, 'name' => 'Reading']);

        // Add two questions to the quiz.
        $cat = $questiongenerator->create_question_category();
        $saq1 = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        $saq2 = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        quiz_add_quiz_question($saq1->id, $quiz, 0, 1);
        quiz_add_quiz_question($saq2->id, $quiz, 0, 1);

        // Set one of the question to use a grade item.
        $quizobj = quiz_settings::create($quiz->id);
        $structure = $quizobj->get_structure();
        $structure->update_slot_grade_item($structure->get_slot_by_number(2), $readinggrade->id);
        $quizobj->get_grade_calculator()->recompute_quiz_sumgrades();

        // Back up and restore the course.
        $newcourseid = $this->backup_and_restore($course);

        // Verify the grade items were copied over.
        $newquiz = $DB->get_record('quiz', ['course' => $newcourseid]);
        $quizobj = quiz_settings::create($newquiz->id);
        $structure = $quizobj->get_structure();
        $quizgradeitems = array_values($structure->get_grade_items());

        // Check the grade items are right in the restored quiz.
        $this->assertEquals(
            [
                (object) ['id' => reset($quizgradeitems)->id, 'quizid' => $newquiz->id, 'sortorder' => 1, 'name' => 'Listening'],
                (object) ['id' => end($quizgradeitems)->id, 'quizid' => $newquiz->id, 'sortorder' => 2, 'name' => 'Reading'],
            ],
            array_values($quizgradeitems),
        );

        // Verify that each slot uses the right grade item.
        $this->assertNull($structure->get_slot_by_number(1)->quizgradeitemid);
        $this->assertEquals(end($quizgradeitems)->id, $structure->get_slot_by_number(2)->quizgradeitemid);
    }
}
