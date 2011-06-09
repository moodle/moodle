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
 * @package    mod
 * @subpackage quiz
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/editlib.php');


/**
 * Unit tests for (some of) mod/quiz/editlib.php.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_editlib_test extends UnitTestCase {
    public static $includecoverage = array('mod/quiz/editlib.php');
    public function test_quiz_move_question_up() {
        $this->assertEqual(quiz_move_question_up('0', 123), '0');
        $this->assertEqual(quiz_move_question_up('1,2,0', 1), '1,2,0');
        $this->assertEqual(quiz_move_question_up('1,2,0', 0), '1,2,0');
        $this->assertEqual(quiz_move_question_up('1,2,0', 2), '2,1,0');
        $this->assertEqual(quiz_move_question_up('1,2,0,3,4,0', 3), '1,2,3,0,4,0');
        $this->assertEqual(quiz_move_question_up('1,2,3,0,4,0', 4), '1,2,3,4,0,0');
    }

    public function test_quiz_move_question_down() {
        $this->assertEqual(quiz_move_question_down('0', 123), '0');
        $this->assertEqual(quiz_move_question_down('1,2,0', 2), '1,2,0');
        $this->assertEqual(quiz_move_question_down('1,2,0', 0), '1,2,0');
        $this->assertEqual(quiz_move_question_down('1,2,0', 1), '2,1,0');
        $this->assertEqual(quiz_move_question_down('1,2,0,3,4,0', 2), '1,0,2,3,4,0');
        $this->assertEqual(quiz_move_question_down('1,0,2,3,0,4,0', 1), '0,1,2,3,0,4,0');
    }

    public function test_quiz_delete_empty_page() {
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

    public function test_quiz_add_page_break_after() {
        $this->assertEqual(quiz_add_page_break_after('0', 1), '0');
        $this->assertEqual(quiz_add_page_break_after('1,2,0', 1), '1,0,2,0');
        $this->assertEqual(quiz_add_page_break_after('1,2,0', 2), '1,2,0,0');
        $this->assertEqual(quiz_add_page_break_after('1,2,0', 0), '1,2,0');
    }

    public function test_quiz_add_page_break_at() {
        $this->assertEqual(quiz_add_page_break_at('0', 0), '0,0');
        $this->assertEqual(quiz_add_page_break_at('1,2,0', 0), '0,1,2,0');
        $this->assertEqual(quiz_add_page_break_at('1,2,0', 1), '1,0,2,0');
        $this->assertEqual(quiz_add_page_break_at('1,2,0', 2), '1,2,0,0');
        $this->assertEqual(quiz_add_page_break_at('1,2,0', 3), '1,2,0');
    }
}
