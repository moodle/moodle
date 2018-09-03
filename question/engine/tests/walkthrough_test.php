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
 * This file contains tests that walks a question through a whole attempt.
 *
 * @package core_question
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/..//lib.php');
require_once(__DIR__ . '/helpers.php');


/**
 * End-to-end tests of attempting a question.
 *
 * @copyright  2017 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_question_walkthrough_testcase extends qbehaviour_walkthrough_test_base {

    public function test_regrade_does_not_lose_flag() {

        // Create a true-false question with correct answer true.
        $tf = test_question_maker::make_question('truefalse', 'true');
        $this->start_attempt_at_question($tf, 'deferredfeedback', 2);

        // Process a true answer.
        $this->process_submission(array('answer' => 1));

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Flag the question.
        $this->get_question_attempt()->set_flagged(true);

        // Now change the correct answer to the question, and regrade.
        $tf->rightanswer = false;
        $this->quba->regrade_all_questions();

        // Verify the flag has not been lost.
        $this->assertTrue($this->get_question_attempt()->is_flagged());
    }
}
