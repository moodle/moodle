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

namespace quizaccess_delaybetweenattempts;

use mod_quiz\quiz_settings;
use quizaccess_delaybetweenattempts;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/accessrule/delaybetweenattempts/rule.php');


/**
 * Unit tests for the quizaccess_delaybetweenattempts plugin.
 *
 * @package    quizaccess_delaybetweenattempts
 * @category   test
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rule_test extends \basic_testcase {
    public function test_just_first_delay() {
        $quiz = new \stdClass();
        $quiz->attempts = 3;
        $quiz->timelimit = 0;
        $quiz->delay1 = 1000;
        $quiz->delay2 = 0;
        $quiz->timeclose = 0;
        $cm = new \stdClass();
        $cm->id = 0;
        $quizobj = new quiz_settings($quiz, $cm, null);
        $attempt = new \stdClass();
        $attempt->timefinish = 10000;

        $rule = new quizaccess_delaybetweenattempts($quizobj, 10000);
        $this->assertEmpty($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->end_time($attempt));
        $this->assertFalse($rule->time_left_display($attempt, 0));

        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $this->assertEquals($rule->prevent_new_attempt(1, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(11000)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $attempt->timefinish = 9000;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $attempt->timefinish = 9001;
        $this->assertEquals($rule->prevent_new_attempt(1, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(10001)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
    }

    public function test_just_second_delay() {
        $quiz = new \stdClass();
        $quiz->attempts = 5;
        $quiz->timelimit = 0;
        $quiz->delay1 = 0;
        $quiz->delay2 = 1000;
        $quiz->timeclose = 0;
        $cm = new \stdClass();
        $cm->id = 0;
        $quizobj = new quiz_settings($quiz, $cm, null);
        $attempt = new \stdClass();
        $attempt->timefinish = 10000;

        $rule = new quizaccess_delaybetweenattempts($quizobj, 10000);
        $this->assertEmpty($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->end_time($attempt));
        $this->assertFalse($rule->time_left_display($attempt, 0));

        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(5, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertEquals($rule->prevent_new_attempt(2, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(11000)));
        $this->assertEquals($rule->prevent_new_attempt(3, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(11000)));
        $attempt->timefinish = 9000;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $attempt->timefinish = 9001;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertEquals($rule->prevent_new_attempt(2, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(10001)));
        $this->assertEquals($rule->prevent_new_attempt(4, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(10001)));
    }

    public function test_just_both_delays() {
        $quiz = new \stdClass();
        $quiz->attempts = 5;
        $quiz->timelimit = 0;
        $quiz->delay1 = 2000;
        $quiz->delay2 = 1000;
        $quiz->timeclose = 0;
        $cm = new \stdClass();
        $cm->id = 0;
        $quizobj = new quiz_settings($quiz, $cm, null);
        $attempt = new \stdClass();
        $attempt->timefinish = 10000;

        $rule = new quizaccess_delaybetweenattempts($quizobj, 10000);
        $this->assertEmpty($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->end_time($attempt));
        $this->assertFalse($rule->time_left_display($attempt, 0));

        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(5, $attempt));
        $this->assertEquals($rule->prevent_new_attempt(1, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(12000)));
        $this->assertEquals($rule->prevent_new_attempt(2, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(11000)));
        $this->assertEquals($rule->prevent_new_attempt(3, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(11000)));
        $attempt->timefinish = 8000;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $attempt->timefinish = 8001;
        $this->assertEquals($rule->prevent_new_attempt(1, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(10001)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(4, $attempt));
        $attempt->timefinish = 9000;
        $this->assertEquals($rule->prevent_new_attempt(1, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(11000)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $attempt->timefinish = 9001;
        $this->assertEquals($rule->prevent_new_attempt(1, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(11001)));
        $this->assertEquals($rule->prevent_new_attempt(2, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(10001)));
        $this->assertEquals($rule->prevent_new_attempt(4, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(10001)));
    }

    public function test_with_close_date() {
        $quiz = new \stdClass();
        $quiz->attempts = 5;
        $quiz->timelimit = 0;
        $quiz->delay1 = 2000;
        $quiz->delay2 = 1000;
        $quiz->timeclose = 15000;
        $cm = new \stdClass();
        $cm->id = 0;
        $quizobj = new quiz_settings($quiz, $cm, null);
        $attempt = new \stdClass();
        $attempt->timefinish = 13000;

        $rule = new quizaccess_delaybetweenattempts($quizobj, 10000);
        $this->assertEmpty($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->end_time($attempt));
        $this->assertFalse($rule->time_left_display($attempt, 0));

        $attempt->timefinish = 13000;
        $this->assertEquals($rule->prevent_new_attempt(1, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(15000)));
        $attempt->timefinish = 13001;
        $this->assertEquals($rule->prevent_new_attempt(1, $attempt),
            get_string('youcannotwait', 'quizaccess_delaybetweenattempts'));
        $attempt->timefinish = 14000;
        $this->assertEquals($rule->prevent_new_attempt(2, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(15000)));
        $attempt->timefinish = 14001;
        $this->assertEquals($rule->prevent_new_attempt(2, $attempt),
            get_string('youcannotwait', 'quizaccess_delaybetweenattempts'));

        $rule = new quizaccess_delaybetweenattempts($quizobj, 15000);
        $attempt->timefinish = 13000;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $attempt->timefinish = 13001;
        $this->assertEquals($rule->prevent_new_attempt(1, $attempt),
            get_string('youcannotwait', 'quizaccess_delaybetweenattempts'));
        $attempt->timefinish = 14000;
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $attempt->timefinish = 14001;
        $this->assertEquals($rule->prevent_new_attempt(2, $attempt),
            get_string('youcannotwait', 'quizaccess_delaybetweenattempts'));

        $rule = new quizaccess_delaybetweenattempts($quizobj, 15001);
        $attempt->timefinish = 13000;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $attempt->timefinish = 13001;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $attempt->timefinish = 14000;
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $attempt->timefinish = 14001;
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
    }

    public function test_time_limit_and_overdue() {
        $quiz = new \stdClass();
        $quiz->attempts = 5;
        $quiz->timelimit = 100;
        $quiz->delay1 = 2000;
        $quiz->delay2 = 1000;
        $quiz->timeclose = 0;
        $cm = new \stdClass();
        $cm->id = 0;
        $quizobj = new quiz_settings($quiz, $cm, null);
        $attempt = new \stdClass();
        $attempt->timestart = 9900;
        $attempt->timefinish = 10100;

        $rule = new quizaccess_delaybetweenattempts($quizobj, 10000);
        $this->assertEmpty($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->end_time($attempt));
        $this->assertFalse($rule->time_left_display($attempt, 0));

        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(5, $attempt));
        $this->assertEquals($rule->prevent_new_attempt(1, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(12000)));
        $this->assertEquals($rule->prevent_new_attempt(2, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(11000)));
        $this->assertEquals($rule->prevent_new_attempt(3, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(11000)));
        $attempt->timestart = 7950;
        $attempt->timefinish = 8000;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $attempt->timestart = 7950;
        $attempt->timefinish = 8001;
        $this->assertEquals($rule->prevent_new_attempt(1, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(10001)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(4, $attempt));
        $attempt->timestart = 8950;
        $attempt->timefinish = 9000;
        $this->assertEquals($rule->prevent_new_attempt(1, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(11000)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $attempt->timestart = 8950;
        $attempt->timefinish = 9001;
        $this->assertEquals($rule->prevent_new_attempt(1, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(11001)));
        $this->assertEquals($rule->prevent_new_attempt(2, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(10001)));
        $this->assertEquals($rule->prevent_new_attempt(4, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(10001)));
        $attempt->timestart = 8900;
        $attempt->timefinish = 9100;
        $this->assertEquals($rule->prevent_new_attempt(1, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(11000)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $attempt->timestart = 8901;
        $attempt->timefinish = 9100;
        $this->assertEquals($rule->prevent_new_attempt(1, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(11001)));
        $this->assertEquals($rule->prevent_new_attempt(2, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(10001)));
        $this->assertEquals($rule->prevent_new_attempt(4, $attempt),
            get_string('youmustwait', 'quizaccess_delaybetweenattempts', userdate(10001)));
    }
}
