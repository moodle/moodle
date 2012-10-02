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
 * Unit tests for (some of) mod/quiz/locallib.php.
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2008 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/locallib.php');


/**
 * Unit tests for (some of) mod/quiz/locallib.php.
 *
 * @copyright  2008 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_locallib_testcase extends basic_testcase {
    public function test_quiz_questions_in_quiz() {
        $this->assertEquals(quiz_questions_in_quiz(''), '');
        $this->assertEquals(quiz_questions_in_quiz('0'), '');
        $this->assertEquals(quiz_questions_in_quiz('0,0'), '');
        $this->assertEquals(quiz_questions_in_quiz('0,0,0'), '');
        $this->assertEquals(quiz_questions_in_quiz('1'), '1');
        $this->assertEquals(quiz_questions_in_quiz('1,2'), '1,2');
        $this->assertEquals(quiz_questions_in_quiz('1,0,2'), '1,2');
        $this->assertEquals(quiz_questions_in_quiz('0,1,0,0,2,0'), '1,2');
    }

    public function test_quiz_number_of_pages() {
        $this->assertEquals(quiz_number_of_pages('0'), 1);
        $this->assertEquals(quiz_number_of_pages('0,0'), 2);
        $this->assertEquals(quiz_number_of_pages('0,0,0'), 3);
        $this->assertEquals(quiz_number_of_pages('1,0'), 1);
        $this->assertEquals(quiz_number_of_pages('1,2,0'), 1);
        $this->assertEquals(quiz_number_of_pages('1,0,2,0'), 2);
        $this->assertEquals(quiz_number_of_pages('1,2,3,0'), 1);
        $this->assertEquals(quiz_number_of_pages('1,2,3,0'), 1);
        $this->assertEquals(quiz_number_of_pages('0,1,0,0,2,0'), 4);
    }

    public function test_quiz_number_of_questions_in_quiz() {
        $this->assertEquals(quiz_number_of_questions_in_quiz('0'), 0);
        $this->assertEquals(quiz_number_of_questions_in_quiz('0,0'), 0);
        $this->assertEquals(quiz_number_of_questions_in_quiz('0,0,0'), 0);
        $this->assertEquals(quiz_number_of_questions_in_quiz('1,0'), 1);
        $this->assertEquals(quiz_number_of_questions_in_quiz('1,2,0'), 2);
        $this->assertEquals(quiz_number_of_questions_in_quiz('1,0,2,0'), 2);
        $this->assertEquals(quiz_number_of_questions_in_quiz('1,2,3,0'), 3);
        $this->assertEquals(quiz_number_of_questions_in_quiz('1,2,3,0'), 3);
        $this->assertEquals(quiz_number_of_questions_in_quiz('0,1,0,0,2,0'), 2);
        $this->assertEquals(quiz_number_of_questions_in_quiz('10,,0,0'), 1);
    }

    public function test_quiz_clean_layout() {
        // Without stripping empty pages.
        $this->assertEquals(quiz_clean_layout(',,1,,,2,,'), '1,2,0');
        $this->assertEquals(quiz_clean_layout(''), '0');
        $this->assertEquals(quiz_clean_layout('0'), '0');
        $this->assertEquals(quiz_clean_layout('0,0'), '0,0');
        $this->assertEquals(quiz_clean_layout('0,0,0'), '0,0,0');
        $this->assertEquals(quiz_clean_layout('1'), '1,0');
        $this->assertEquals(quiz_clean_layout('1,2'), '1,2,0');
        $this->assertEquals(quiz_clean_layout('1,0,2'), '1,0,2,0');
        $this->assertEquals(quiz_clean_layout('0,1,0,0,2,0'), '0,1,0,0,2,0');

        // With stripping empty pages.
        $this->assertEquals(quiz_clean_layout('', true), '0');
        $this->assertEquals(quiz_clean_layout('0', true), '0');
        $this->assertEquals(quiz_clean_layout('0,0', true), '0');
        $this->assertEquals(quiz_clean_layout('0,0,0', true), '0');
        $this->assertEquals(quiz_clean_layout('1', true), '1,0');
        $this->assertEquals(quiz_clean_layout('1,2', true), '1,2,0');
        $this->assertEquals(quiz_clean_layout('1,0,2', true), '1,0,2,0');
        $this->assertEquals(quiz_clean_layout('0,1,0,0,2,0', true), '1,0,2,0');
    }

    public function test_quiz_repaginate() {
        // Test starting with 1 question per page.
        $this->assertEquals(quiz_repaginate('1,0,2,0,3,0', 0), '1,2,3,0');
        $this->assertEquals(quiz_repaginate('1,0,2,0,3,0', 3), '1,2,3,0');
        $this->assertEquals(quiz_repaginate('1,0,2,0,3,0', 2), '1,2,0,3,0');
        $this->assertEquals(quiz_repaginate('1,0,2,0,3,0', 1), '1,0,2,0,3,0');

        // Test starting with all on one page page.
        $this->assertEquals(quiz_repaginate('1,2,3,0', 0), '1,2,3,0');
        $this->assertEquals(quiz_repaginate('1,2,3,0', 3), '1,2,3,0');
        $this->assertEquals(quiz_repaginate('1,2,3,0', 2), '1,2,0,3,0');
        $this->assertEquals(quiz_repaginate('1,2,3,0', 1), '1,0,2,0,3,0');

        // Test single question case.
        $this->assertEquals(quiz_repaginate('100,0', 0), '100,0');
        $this->assertEquals(quiz_repaginate('100,0', 1), '100,0');

        // No questions case.
        $this->assertEquals(quiz_repaginate('0', 0), '0');

        // Test empty pages are removed.
        $this->assertEquals(quiz_repaginate('1,2,3,0,0,0', 0), '1,2,3,0');
        $this->assertEquals(quiz_repaginate('1,0,0,0,2,3,0', 0), '1,2,3,0');
        $this->assertEquals(quiz_repaginate('0,0,0,1,2,3,0', 0), '1,2,3,0');

        // Test shuffle option.
        $this->assertTrue(in_array(quiz_repaginate('1,2,0', 0, true),
            array('1,2,0', '2,1,0')));
        $this->assertTrue(in_array(quiz_repaginate('1,2,0', 1, true),
            array('1,0,2,0', '2,0,1,0')));
    }

    public function test_quiz_rescale_grade() {
        $quiz = new stdClass();
        $quiz->decimalpoints = 2;
        $quiz->questiondecimalpoints = 3;
        $quiz->grade = 10;
        $quiz->sumgrades = 10;
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, false), 0.12345678);
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, true), format_float(0.12, 2));
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, 'question'),
            format_float(0.123, 3));
        $quiz->sumgrades = 5;
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, false), 0.24691356);
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, true), format_float(0.25, 2));
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, 'question'),
            format_float(0.247, 3));
    }

    public function test_quiz_get_slot_for_question() {
        $quiz = new stdClass();
        $quiz->questions = '1,2,0,7,0';
        $this->assertEquals(1, quiz_get_slot_for_question($quiz, 1));
        $this->assertEquals(3, quiz_get_slot_for_question($quiz, 7));
    }

    public function test_quiz_attempt_state_in_progress() {
        $attempt = new stdClass();
        $attempt->state = quiz_attempt::IN_PROGRESS;
        $attempt->timefinish = 0;

        $quiz = new stdClass();
        $quiz->timeclose = 0;

        $this->assertEquals(mod_quiz_display_options::DURING, quiz_attempt_state($quiz, $attempt));
    }

    public function test_quiz_attempt_state_recently_submitted() {
        $attempt = new stdClass();
        $attempt->state = quiz_attempt::FINISHED;
        $attempt->timefinish = time() - 10;

        $quiz = new stdClass();
        $quiz->timeclose = 0;

        $this->assertEquals(mod_quiz_display_options::IMMEDIATELY_AFTER, quiz_attempt_state($quiz, $attempt));
    }

    public function test_quiz_attempt_state_sumitted_quiz_never_closes() {
        $attempt = new stdClass();
        $attempt->state = quiz_attempt::FINISHED;
        $attempt->timefinish = time() - 7200;

        $quiz = new stdClass();
        $quiz->timeclose = 0;

        $this->assertEquals(mod_quiz_display_options::LATER_WHILE_OPEN, quiz_attempt_state($quiz, $attempt));
    }

    public function test_quiz_attempt_state_sumitted_quiz_closes_later() {
        $attempt = new stdClass();
        $attempt->state = quiz_attempt::FINISHED;
        $attempt->timefinish = time() - 7200;

        $quiz = new stdClass();
        $quiz->timeclose = time() + 3600;

        $this->assertEquals(mod_quiz_display_options::LATER_WHILE_OPEN, quiz_attempt_state($quiz, $attempt));
    }

    public function test_quiz_attempt_state_sumitted_quiz_closed() {
        $attempt = new stdClass();
        $attempt->state = quiz_attempt::FINISHED;
        $attempt->timefinish = time() - 7200;

        $quiz = new stdClass();
        $quiz->timeclose = time() - 3600;

        $this->assertEquals(mod_quiz_display_options::AFTER_CLOSE, quiz_attempt_state($quiz, $attempt));
    }

    public function test_quiz_attempt_state_never_sumitted_quiz_never_closes() {
        $attempt = new stdClass();
        $attempt->state = quiz_attempt::ABANDONED;
        $attempt->timefinish = 1000; // A very long time ago!

        $quiz = new stdClass();
        $quiz->timeclose = 0;

        $this->assertEquals(mod_quiz_display_options::LATER_WHILE_OPEN, quiz_attempt_state($quiz, $attempt));
    }

    public function test_quiz_attempt_state_never_sumitted_quiz_closes_later() {
        $attempt = new stdClass();
        $attempt->state = quiz_attempt::ABANDONED;
        $attempt->timefinish = time() - 7200;

        $quiz = new stdClass();
        $quiz->timeclose = time() + 3600;

        $this->assertEquals(mod_quiz_display_options::LATER_WHILE_OPEN, quiz_attempt_state($quiz, $attempt));
    }

    public function test_quiz_attempt_state_never_sumitted_quiz_closed() {
        $attempt = new stdClass();
        $attempt->state = quiz_attempt::ABANDONED;
        $attempt->timefinish = time() - 7200;

        $quiz = new stdClass();
        $quiz->timeclose = time() - 3600;

        $this->assertEquals(mod_quiz_display_options::AFTER_CLOSE, quiz_attempt_state($quiz, $attempt));
    }
}
