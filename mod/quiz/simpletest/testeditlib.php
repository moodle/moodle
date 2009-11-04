<?php
/**
 * Unit tests for (some of) mod/quiz/editlib.php.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); /// It must be included from a Moodle page.
}

require_once($CFG->dirroot . '/mod/quiz/editlib.php');

class quiz_editlib_test extends UnitTestCase {
    public static $includecoverage = array('mod/quiz/editlib.php');
    function test_quiz_move_question_up() {
        $this->assertEqual(quiz_move_question_up('0', 123), '0');
        $this->assertEqual(quiz_move_question_up('1,2,0', 1), '1,2,0');
        $this->assertEqual(quiz_move_question_up('1,2,0', 0), '1,2,0');
        $this->assertEqual(quiz_move_question_up('1,2,0', 2), '2,1,0');
        $this->assertEqual(quiz_move_question_up('1,2,0,3,4,0', 3), '1,2,3,0,4,0');
        $this->assertEqual(quiz_move_question_up('1,2,3,0,4,0', 4), '1,2,3,4,0,0');
    }

    function test_quiz_move_question_down() {
        $this->assertEqual(quiz_move_question_down('0', 123), '0');
        $this->assertEqual(quiz_move_question_down('1,2,0', 2), '1,2,0');
        $this->assertEqual(quiz_move_question_down('1,2,0', 0), '1,2,0');
        $this->assertEqual(quiz_move_question_down('1,2,0', 1), '2,1,0');
        $this->assertEqual(quiz_move_question_down('1,2,0,3,4,0', 2), '1,0,2,3,4,0');
        $this->assertEqual(quiz_move_question_down('1,0,2,3,0,4,0', 1), '0,1,2,3,0,4,0');
    }

    function test_quiz_delete_empty_page() {
        $this->assertEqual(quiz_delete_empty_page('0', 0), '0');
        $this->assertEqual(quiz_delete_empty_page('1,2,0', 2), '1,2,0');
        $this->assertEqual(quiz_delete_empty_page('0,1,2,0', -1), '1,2,0');
        $this->assertEqual(quiz_delete_empty_page('0,1,2,0', 0), '0,1,2,0');
        $this->assertEqual(quiz_delete_empty_page('1,2,0', 3), '1,2,0');
        $this->assertEqual(quiz_delete_empty_page('1,2,0', -1), '1,2,0');
        $this->assertEqual(quiz_delete_empty_page('1,2,0,0', 2), '1,2,0');
        $this->assertEqual(quiz_delete_empty_page('1,2,0,0', 1), '1,2,0,0');
        $this->assertEqual(quiz_delete_empty_page('1,2,0,0,3,4,0', 2), '1,2,0,3,4,0');
        $this->assertEqual(quiz_delete_empty_page('0,0,1,2,0', 0), '0,1,2,0');
    }

    function test_quiz_add_page_break_after() {
        $this->assertEqual(quiz_add_page_break_after('0', 1), '0');
        $this->assertEqual(quiz_add_page_break_after('1,2,0', 1), '1,0,2,0');
        $this->assertEqual(quiz_add_page_break_after('1,2,0', 2), '1,2,0,0');
        $this->assertEqual(quiz_add_page_break_after('1,2,0', 0), '1,2,0');
    }

    function test_quiz_add_page_break_at() {
        $this->assertEqual(quiz_add_page_break_at('0', 0), '0,0');
        $this->assertEqual(quiz_add_page_break_at('1,2,0', 0), '0,1,2,0');
        $this->assertEqual(quiz_add_page_break_at('1,2,0', 1), '1,0,2,0');
        $this->assertEqual(quiz_add_page_break_at('1,2,0', 2), '1,2,0,0');
        $this->assertEqual(quiz_add_page_break_at('1,2,0', 3), '1,2,0');
    }
}

