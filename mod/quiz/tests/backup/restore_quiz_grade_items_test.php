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

        $course = $this->getDataGenerator()->create_course();
        /** @var mod_quiz_generator $quizgen */
        $quizgen = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgen->create_instance(['course' => $course->id]);

        $quizgen->create_grade_item(['quizid' => $quiz->id, 'name' => 'Listening']);
        $quizgen->create_grade_item(['quizid' => $quiz->id, 'name' => 'Reading']);

        // Back up and restore including group info and user info.
        $newcourseid = $this->backup_and_restore($course);

        $newquiz = $DB->get_record('quiz', ['course' => $newcourseid]);
        $quizobj = quiz_settings::create($newquiz->id);
        $structure = $quizobj->get_structure();
        $quizgradeitems = array_values($structure->get_grade_items());

        // Strip out the ids, since we don't care what those will be.
        $quizgradeitems = array_map(
            function ($quizgradeitem) {
                unset($quizgradeitem->id);
                return $quizgradeitem;
            },
            $quizgradeitems
        );

        // Check the grade items are right in the restored quiz.
        $this->assertEquals(
            [
                (object) ['quizid' => $newquiz->id, 'sortorder' => 1, 'name' => 'Listening'],
                (object) ['quizid' => $newquiz->id, 'sortorder' => 2, 'name' => 'Reading'],
            ],
            $quizgradeitems
        );

    }
}
