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
 * Unit tests for the quizaccess_safebrowser plugin.
 *
 * @package    quizaccess
 * @subpackage safebrowser
 * @category   phpunit
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/accessrule/safebrowser/rule.php');


/**
 * Unit tests for the quizaccess_safebrowser plugin.
 *
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_safebrowser_testcase extends basic_testcase {
    // Nothing very testable in this class, just test that it obeys the general access rule contact.
    public function test_safebrowser_access_rule() {
        $quiz = new stdClass();
        $quiz->browsersecurity = 'safebrowser';
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $rule = new quizaccess_safebrowser($quizobj, 0);
        $attempt = new stdClass();

        // This next test assumes the unit tests are not being run using Safe Exam Browser!
        $_SERVER['HTTP_USER_AGENT'] = 'unknonw browser';
        $this->assertEquals(get_string('safebrowsererror', 'quizaccess_safebrowser'),
            $rule->prevent_access());

        $this->assertEquals(get_string('safebrowsernotice', 'quizaccess_safebrowser'),
            $rule->description());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->end_time($attempt));
        $this->assertFalse($rule->time_left_display($attempt, 0));
    }
}
