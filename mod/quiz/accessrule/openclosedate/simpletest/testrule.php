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
 * Unit tests for the quizaccess_openclosedate plugin.
 *
 * @package    quizaccess
 * @subpackage openclosedate
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/accessrule/openclosedate/rule.php');


/**
 * Unit tests for the quizaccess_openclosedate plugin.
 *
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_openclosedate_test extends UnitTestCase {
    public static $includecoverage = array('mod/quiz/accessrule/openclosedate/rule.php');

    public function test_no_dates() {
        $quiz = new stdClass();
        $quiz->timeopen = 0;
        $quiz->timeclose = 0;
        $quiz->questions = '';
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass();
        $attempt->preview = 0;

        $rule = new quizaccess_openclosedate($quizobj, 10000);
        $this->assertFalse($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 10000));
        $this->assertFalse($rule->time_left($attempt, 0));

        $rule = new quizaccess_openclosedate($quizobj, 0);
        $this->assertFalse($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 0));
    }

    public function test_start_date() {
        $quiz = new stdClass();
        $quiz->timeopen = 10000;
        $quiz->timeclose = 0;
        $quiz->questions = '';
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass();
        $attempt->preview = 0;

        $rule = new quizaccess_openclosedate($quizobj, 9999);
        $this->assertEqual($rule->description(),
                array(get_string('quiznotavailable', 'quizaccess_openclosedate', userdate(10000))));
        $this->assertEqual($rule->prevent_access(),
                get_string('notavailable', 'quizaccess_openclosedate'));
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 0));

        $rule = new quizaccess_openclosedate($quizobj, 10000);
        $this->assertEqual($rule->description(),
                array(get_string('quizopenedon', 'quiz', userdate(10000))));
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 0));
    }

    public function test_close_date() {
        $quiz = new stdClass();
        $quiz->timeopen = 0;
        $quiz->timeclose = 20000;
        $quiz->questions = '';
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass();
        $attempt->preview = 0;

        $rule = new quizaccess_openclosedate($quizobj, 20000);
        $this->assertEqual($rule->description(),
                array(get_string('quizcloseson', 'quiz', userdate(20000))));
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 20000 - QUIZ_SHOW_TIME_BEFORE_DEADLINE));
        $this->assertEqual($rule->time_left($attempt, 19900), 100);
        $this->assertEqual($rule->time_left($attempt, 20000), 0);
        $this->assertEqual($rule->time_left($attempt, 20100), -100);

        $rule = new quizaccess_openclosedate($quizobj, 20001);
        $this->assertEqual($rule->description(),
                array(get_string('quizclosed', 'quiz', userdate(20000))));
        $this->assertEqual($rule->prevent_access(),
                get_string('notavailable', 'quizaccess_openclosedate'));
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertTrue($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 20000 - QUIZ_SHOW_TIME_BEFORE_DEADLINE));
        $this->assertEqual($rule->time_left($attempt, 19900), 100);
        $this->assertEqual($rule->time_left($attempt, 20000), 0);
        $this->assertEqual($rule->time_left($attempt, 20100), -100);
    }

    public function test_both_dates() {
        $quiz = new stdClass();
        $quiz->timeopen = 10000;
        $quiz->timeclose = 20000;
        $quiz->questions = '';
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass();
        $attempt->preview = 0;

        $rule = new quizaccess_openclosedate($quizobj, 9999);
        $this->assertEqual($rule->description(),
                array(get_string('quiznotavailable', 'quizaccess_openclosedate', userdate(10000))));
        $this->assertEqual($rule->prevent_access(),
                get_string('notavailable', 'quizaccess_openclosedate'));
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));

        $rule = new quizaccess_openclosedate($quizobj, 10000);
        $this->assertEqual($rule->description(),
                array(get_string('quizopenedon', 'quiz', userdate(10000)),
                get_string('quizcloseson', 'quiz', userdate(20000))));
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));

        $rule = new quizaccess_openclosedate($quizobj, 20000);
        $this->assertEqual($rule->description(),
                array(get_string('quizopenedon', 'quiz', userdate(10000)),
                get_string('quizcloseson', 'quiz', userdate(20000))));
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));

        $rule = new quizaccess_openclosedate($quizobj, 20001);
        $this->assertEqual($rule->description(),
                array(get_string('quizclosed', 'quiz', userdate(20000))));
        $this->assertEqual($rule->prevent_access(),
                get_string('notavailable', 'quizaccess_openclosedate'));
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertTrue($rule->is_finished(0, $attempt));

        $this->assertFalse($rule->time_left($attempt, 20000 - QUIZ_SHOW_TIME_BEFORE_DEADLINE));
        $this->assertEqual($rule->time_left($attempt, 19900), 100);
        $this->assertEqual($rule->time_left($attempt, 20000), 0);
        $this->assertEqual($rule->time_left($attempt, 20100), -100);
    }
}
