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
 * Tests of the scheduled task for cleaning up random questions.
 *
 * @package    qtype_random
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/locallib.php');


/**
 * Tests of the scheduled task for cleaning up random questions.
 *
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_random_cleanup_task_testcase extends advanced_testcase {

    public function test_cleanup_task_removes_unused_question() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $quizgenerator = $generator->get_plugin_generator('mod_quiz');
        $cat = $questiongenerator->create_question_category();
        $quiz = $quizgenerator->create_instance(['course' => SITEID]);

        // Add two random questions.
        quiz_add_random_questions($quiz, 0, $cat->id, 2, false);
        $quizslots = $DB->get_records('quiz_slots', ['quizid' => $quiz->id],
                'slot', 'slot, id, questionid');

        // Now remove the second from the quiz. (Do it manually,
        // because the API cleans up the random question, but we are trying to
        // create an orphaned random question.)
        $DB->delete_records('quiz_slots', array('id' => $quizslots[2]->id));

        // Run the scheduled task.
        $task = new \qtype_random\task\remove_unused_questions();
        $this->expectOutputString("Cleaned up 1 unused random questions.\n");
        $task->execute();

        // Verify.
        $this->assertTrue($DB->record_exists('question', ['id' => $quizslots[1]->questionid]));
        $this->assertFalse($DB->record_exists('question', ['id' => $quizslots[2]->questionid]));
    }
}
