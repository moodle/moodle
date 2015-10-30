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
 * Unit tests for (some of) mod/quiz/editlib.php.
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/editlib.php');


/**
 * Unit tests for (some of) mod/quiz/editlib.php.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_editlib_testcase extends advanced_testcase {
    public function test_quiz_question_tostring() {
        $question = new stdClass();
        $question->qtype = 'multichoice';
        $question->name = 'The question name';
        $question->questiontext = '<p>What sort of <b>inequality</b> is x &lt; y<img alt="?" src="..."></p>';
        $question->questiontextformat = FORMAT_HTML;

        $summary = quiz_question_tostring($question);
        $this->assertEquals('<span class="questionname">The question name</span>' .
                '<span class="questiontext">What sort of INEQUALITY is x &lt; y[?]</span>', $summary);
    }

    /**
     * Test removing slots from a quiz.
     */
    public function test_quiz_remove_slot() {
        global $SITE, $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Setup a quiz with 1 standard and 1 random question.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(array('course' => $SITE->id, 'questionsperpage' => 3, 'grade' => 100.0));

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $standardq = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));

        quiz_add_quiz_question($standardq->id, $quiz);
        quiz_add_random_questions($quiz, 0, $cat->id, 1, false);

        // Get the random question.
        $randomq = $DB->get_record('question', array('qtype' => 'random'));

        $slotssql = "SELECT qs.*, q.qtype AS qtype
                       FROM {quiz_slots} qs
                       JOIN {question} q ON qs.questionid = q.id
                      WHERE qs.quizid = ?
                   ORDER BY qs.slot";
        $slots = $DB->get_records_sql($slotssql, array($quiz->id));

        // Check that the setup looks right.
        $this->assertEquals(2, count($slots));
        $slot = array_shift($slots);
        $this->assertEquals($standardq->id, $slot->questionid);
        $slot = array_shift($slots);
        $this->assertEquals($randomq->id, $slot->questionid);
        $this->assertEquals(2, $slot->slot);

        // Remove the standard question.
        quiz_remove_slot($quiz, 1);

        $slots = $DB->get_records_sql($slotssql, array($quiz->id));

        // Check the new ordering, and that the slot number was updated.
        $this->assertEquals(1, count($slots));
        $slot = array_shift($slots);
        $this->assertEquals($randomq->id, $slot->questionid);
        $this->assertEquals(1, $slot->slot);

        // Check the the standard question was not deleted.
        $count = $DB->count_records('question', array('id' => $standardq->id));
        $this->assertEquals(1, $count);

        // Remove the random question.
        quiz_remove_slot($quiz, 1);

        $slots = $DB->get_records_sql($slotssql, array($quiz->id));

        // Check that new ordering.
        $this->assertEquals(0, count($slots));

        // Check that the random question was deleted.
        $count = $DB->count_records('question', array('id' => $randomq->id));
        $this->assertEquals(0, $count);
    }
}
