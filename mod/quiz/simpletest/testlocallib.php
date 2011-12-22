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
 * @package    mod
 * @subpackage quiz
 * @copyright  2008 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/locallib.php');


/**
 * Unit tests for (some of) mod/quiz/locallib.php.
 *
 * @copyright  2008 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_locallib_test extends UnitTestCase {
    public static $includecoverage = array('mod/quiz/locallib.php');
    public function test_quiz_questions_in_quiz() {
        $this->assertEqual(quiz_questions_in_quiz(''), '');
        $this->assertEqual(quiz_questions_in_quiz('0'), '');
        $this->assertEqual(quiz_questions_in_quiz('0,0'), '');
        $this->assertEqual(quiz_questions_in_quiz('0,0,0'), '');
        $this->assertEqual(quiz_questions_in_quiz('1'), '1');
        $this->assertEqual(quiz_questions_in_quiz('1,2'), '1,2');
        $this->assertEqual(quiz_questions_in_quiz('1,0,2'), '1,2');
        $this->assertEqual(quiz_questions_in_quiz('0,1,0,0,2,0'), '1,2');
    }

    public function test_quiz_number_of_pages() {
        $this->assertEqual(quiz_number_of_pages('0'), 1);
        $this->assertEqual(quiz_number_of_pages('0,0'), 2);
        $this->assertEqual(quiz_number_of_pages('0,0,0'), 3);
        $this->assertEqual(quiz_number_of_pages('1,0'), 1);
        $this->assertEqual(quiz_number_of_pages('1,2,0'), 1);
        $this->assertEqual(quiz_number_of_pages('1,0,2,0'), 2);
        $this->assertEqual(quiz_number_of_pages('1,2,3,0'), 1);
        $this->assertEqual(quiz_number_of_pages('1,2,3,0'), 1);
        $this->assertEqual(quiz_number_of_pages('0,1,0,0,2,0'), 4);
    }

    public function test_quiz_number_of_questions_in_quiz() {
        $this->assertEqual(quiz_number_of_questions_in_quiz('0'), 0);
        $this->assertEqual(quiz_number_of_questions_in_quiz('0,0'), 0);
        $this->assertEqual(quiz_number_of_questions_in_quiz('0,0,0'), 0);
        $this->assertEqual(quiz_number_of_questions_in_quiz('1,0'), 1);
        $this->assertEqual(quiz_number_of_questions_in_quiz('1,2,0'), 2);
        $this->assertEqual(quiz_number_of_questions_in_quiz('1,0,2,0'), 2);
        $this->assertEqual(quiz_number_of_questions_in_quiz('1,2,3,0'), 3);
        $this->assertEqual(quiz_number_of_questions_in_quiz('1,2,3,0'), 3);
        $this->assertEqual(quiz_number_of_questions_in_quiz('0,1,0,0,2,0'), 2);
        $this->assertEqual(quiz_number_of_questions_in_quiz('10,,0,0'), 1);
    }

    public function test_quiz_clean_layout() {
        // Without stripping empty pages.
        $this->assertEqual(quiz_clean_layout(',,1,,,2,,'), '1,2,0');
        $this->assertEqual(quiz_clean_layout(''), '0');
        $this->assertEqual(quiz_clean_layout('0'), '0');
        $this->assertEqual(quiz_clean_layout('0,0'), '0,0');
        $this->assertEqual(quiz_clean_layout('0,0,0'), '0,0,0');
        $this->assertEqual(quiz_clean_layout('1'), '1,0');
        $this->assertEqual(quiz_clean_layout('1,2'), '1,2,0');
        $this->assertEqual(quiz_clean_layout('1,0,2'), '1,0,2,0');
        $this->assertEqual(quiz_clean_layout('0,1,0,0,2,0'), '0,1,0,0,2,0');

        // With stripping empty pages.
        $this->assertEqual(quiz_clean_layout('', true), '0');
        $this->assertEqual(quiz_clean_layout('0', true), '0');
        $this->assertEqual(quiz_clean_layout('0,0', true), '0');
        $this->assertEqual(quiz_clean_layout('0,0,0', true), '0');
        $this->assertEqual(quiz_clean_layout('1', true), '1,0');
        $this->assertEqual(quiz_clean_layout('1,2', true), '1,2,0');
        $this->assertEqual(quiz_clean_layout('1,0,2', true), '1,0,2,0');
        $this->assertEqual(quiz_clean_layout('0,1,0,0,2,0', true), '1,0,2,0');
    }

    public function test_quiz_repaginate() {
        // Test starting with 1 question per page.
        $this->assertEqual(quiz_repaginate('1,0,2,0,3,0', 0), '1,2,3,0');
        $this->assertEqual(quiz_repaginate('1,0,2,0,3,0', 3), '1,2,3,0');
        $this->assertEqual(quiz_repaginate('1,0,2,0,3,0', 2), '1,2,0,3,0');
        $this->assertEqual(quiz_repaginate('1,0,2,0,3,0', 1), '1,0,2,0,3,0');

        // Test starting with all on one page page.
        $this->assertEqual(quiz_repaginate('1,2,3,0', 0), '1,2,3,0');
        $this->assertEqual(quiz_repaginate('1,2,3,0', 3), '1,2,3,0');
        $this->assertEqual(quiz_repaginate('1,2,3,0', 2), '1,2,0,3,0');
        $this->assertEqual(quiz_repaginate('1,2,3,0', 1), '1,0,2,0,3,0');

        // Test single question case.
        $this->assertEqual(quiz_repaginate('100,0', 0), '100,0');
        $this->assertEqual(quiz_repaginate('100,0', 1), '100,0');

        // No questions case.
        $this->assertEqual(quiz_repaginate('0', 0), '0');

        // Test empty pages are removed.
        $this->assertEqual(quiz_repaginate('1,2,3,0,0,0', 0), '1,2,3,0');
        $this->assertEqual(quiz_repaginate('1,0,0,0,2,3,0', 0), '1,2,3,0');
        $this->assertEqual(quiz_repaginate('0,0,0,1,2,3,0', 0), '1,2,3,0');

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
        $this->assertEqual(quiz_rescale_grade(0.12345678, $quiz, false), 0.12345678);
        $this->assertEqual(quiz_rescale_grade(0.12345678, $quiz, true), format_float(0.12, 2));
        $this->assertEqual(quiz_rescale_grade(0.12345678, $quiz, 'question'),
                format_float(0.123, 3));
        $quiz->sumgrades = 5;
        $this->assertEqual(quiz_rescale_grade(0.12345678, $quiz, false), 0.24691356);
        $this->assertEqual(quiz_rescale_grade(0.12345678, $quiz, true), format_float(0.25, 2));
        $this->assertEqual(quiz_rescale_grade(0.12345678, $quiz, 'question'),
                format_float(0.247, 3));
    }

    public function test_quiz_get_slot_for_question() {
        $quiz = new stdClass();
        $quiz->questions = '1,2,0,7,0';
        $this->assertEqual(1, quiz_get_slot_for_question($quiz, 1));
        $this->assertEqual(3, quiz_get_slot_for_question($quiz, 7));
    }
}
