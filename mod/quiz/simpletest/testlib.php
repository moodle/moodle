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

require_once($CFG->dirroot . '/mod/quiz/lib.php');

class quiz_lib_test extends UnitTestCase {
    public static $includecoverage = array('mod/quiz/lib.php');
    function test_quiz_has_grades() {
        $quiz = new stdClass;
        $quiz->grade = '100.0000';
        $quiz->sumgrades = '100.0000';
        $this->assertTrue(quiz_has_grades($quiz));
        $quiz->sumgrades = '0.0000';
        $this->assertFalse(quiz_has_grades($quiz));
        $quiz->grade = '0.0000';
        $this->assertFalse(quiz_has_grades($quiz));
        $quiz->sumgrades = '100.0000';
        $this->assertFalse(quiz_has_grades($quiz));
    }

    function test_quiz_format_grade() {
        $quiz = new stdClass;
        $quiz->decimalpoints = 2;
        $this->assertEqual(quiz_format_grade($quiz, 0.12345678), format_float(0.12, 2));
        $this->assertEqual(quiz_format_grade($quiz, 0), format_float(0, 2));
        $this->assertEqual(quiz_format_grade($quiz, 1.000000000000), format_float(1, 2));
        $quiz->decimalpoints = 0;
        $this->assertEqual(quiz_format_grade($quiz, 0.12345678), '0');
    }

    function test_quiz_format_question_grade() {
        $quiz = new stdClass;
        $quiz->decimalpoints = 2;
        $quiz->questiondecimalpoints = 2;
        $this->assertEqual(quiz_format_question_grade($quiz, 0.12345678), format_float(0.12, 2));
        $this->assertEqual(quiz_format_question_grade($quiz, 0), format_float(0, 2));
        $this->assertEqual(quiz_format_question_grade($quiz, 1.000000000000), format_float(1, 2));
        $quiz->decimalpoints = 3;
        $quiz->questiondecimalpoints = -1;
        $this->assertEqual(quiz_format_question_grade($quiz, 0.12345678), format_float(0.123, 3));
        $this->assertEqual(quiz_format_question_grade($quiz, 0), format_float(0, 3));
        $this->assertEqual(quiz_format_question_grade($quiz, 1.000000000000), format_float(1, 3));
        $quiz->questiondecimalpoints = 4;
        $this->assertEqual(quiz_format_question_grade($quiz, 0.12345678), format_float(0.1235, 4));
        $this->assertEqual(quiz_format_question_grade($quiz, 0), format_float(0, 4));
        $this->assertEqual(quiz_format_question_grade($quiz, 1.000000000000), format_float(1, 4));
    }
}
