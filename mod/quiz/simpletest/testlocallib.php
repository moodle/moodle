<?php
/**
 * Unit tests for (some of) mod/quiz/locallib.php.
 *
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); /// It must be included from a Moodle page.
}

require_once($CFG->dirroot . '/mod/quiz/locallib.php');

class quiz_locallib_test extends UnitTestCase {
    public static $includecoverage = array('mod/quiz/locallib.php');
    function test_quiz_questions_in_quiz() {
        $this->assertEqual(quiz_questions_in_quiz(''), '');
        $this->assertEqual(quiz_questions_in_quiz('0'), '');
        $this->assertEqual(quiz_questions_in_quiz('0,0'), '');
        $this->assertEqual(quiz_questions_in_quiz('0,0,0'), '');
        $this->assertEqual(quiz_questions_in_quiz('1'), '1');
        $this->assertEqual(quiz_questions_in_quiz('1,2'), '1,2');
        $this->assertEqual(quiz_questions_in_quiz('1,0,2'), '1,2');
        $this->assertEqual(quiz_questions_in_quiz('0,1,0,0,2,0'), '1,2');
    }

    function test_quiz_number_of_pages() {
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

    function test_quiz_number_of_questions_in_quiz() {
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

    function test_quiz_clean_layout() {
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

    function test_quiz_rescale_grade() {
        $quiz = new stdClass;
        $quiz->decimalpoints = 2;
        $quiz->questiondecimalpoints = 3;
        $quiz->grade = 10;
        $quiz->sumgrades = 10;
        $this->assertEqual(quiz_rescale_grade(0.12345678, $quiz, false), 0.12345678);
        $this->assertEqual(quiz_rescale_grade(0.12345678, $quiz, true), format_float(0.12, 2));
        $this->assertEqual(quiz_rescale_grade(0.12345678, $quiz, 'question'), format_float(0.123, 3));
        $quiz->sumgrades = 5;
        $this->assertEqual(quiz_rescale_grade(0.12345678, $quiz, false), 0.24691356);
        $this->assertEqual(quiz_rescale_grade(0.12345678, $quiz, true), format_float(0.25, 2));
        $this->assertEqual(quiz_rescale_grade(0.12345678, $quiz, 'question'), format_float(0.247, 3));
    }
}
