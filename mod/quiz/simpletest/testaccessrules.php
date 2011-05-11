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
 * Unit tests for (some of) mod/quiz/accessrules.php.
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/locallib.php');


/**
 * Unit tests for (some of) mod/quiz/accessrules.php.
 *
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class simple_rules_test extends UnitTestCase {
    public static $includecoverage = array('mod/quiz/locallib.php');
    public function test_num_attempts_access_rule() {
        $quiz = new stdClass();
        $quiz->attempts = 3;
        $quiz->questions = '';
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $rule = new num_attempts_access_rule($quizobj, 0);
        $attempt = new stdClass();

        $this->assertEqual($rule->description(), get_string('attemptsallowedn', 'quiz', 3));

        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertEqual($rule->prevent_new_attempt(3, $attempt),
                get_string('nomoreattempts', 'quiz'));
        $this->assertEqual($rule->prevent_new_attempt(666, $attempt),
                get_string('nomoreattempts', 'quiz'));

        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->is_finished(2, $attempt));
        $this->assertTrue($rule->is_finished(3, $attempt));
        $this->assertTrue($rule->is_finished(666, $attempt));

        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->time_left($attempt, 1));
    }

    public function test_ipaddress_access_rule() {
        $quiz = new stdClass();
        $attempt = new stdClass();
        $cm = new stdClass();
        $cm->id = 0;

        // Test the allowed case by getting the user's IP address. However, this
        // does not always work, for example using the mac install package on my laptop.
        $quiz->subnet = getremoteaddr(null);
        if (!empty($quiz->subnet)) {
            $quiz->questions = '';
            $quizobj = new quiz($quiz, $cm, null);
            $rule = new ipaddress_access_rule($quizobj, 0);
            $this->assertFalse($rule->prevent_access());
            $this->assertFalse($rule->description());
            $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
            $this->assertFalse($rule->is_finished(0, $attempt));
            $this->assertFalse($rule->time_left($attempt, 1));
        }

        $quiz->subnet = '0.0.0.0';
        $quiz->questions = '';
        $quizobj = new quiz($quiz, $cm, null);
        $rule = new ipaddress_access_rule($quizobj, 0);
        $this->assertTrue($rule->prevent_access());
        $this->assertFalse($rule->description());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 1));
    }

    public function test_time_limit_access_rule() {
        $quiz = new stdClass();
        $quiz->timelimit = 3600;
        $quiz->questions = '';
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $rule = new time_limit_access_rule($quizobj, 10000);
        $attempt = new stdClass();

        $this->assertEqual($rule->description(),
                get_string('quiztimelimit', 'quiz', format_time(3600)));

        $attempt->timestart = 10000;
        $this->assertEqual($rule->time_left($attempt, 10000), 3600);
        $this->assertEqual($rule->time_left($attempt, 12000), 1600);
        $this->assertEqual($rule->time_left($attempt, 14000), -400);

        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
    }
}


/**
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class open_close_date_access_rule_test extends UnitTestCase {
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

        $rule = new open_close_date_access_rule($quizobj, 10000);
        $this->assertFalse($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 10000));
        $this->assertFalse($rule->time_left($attempt, 0));

        $rule = new open_close_date_access_rule($quizobj, 0);
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

        $rule = new open_close_date_access_rule($quizobj, 9999);
        $this->assertEqual($rule->description(),
                array(get_string('quiznotavailable', 'quiz', userdate(10000))));
        $this->assertEqual($rule->prevent_access(),
                get_string('notavailable', 'quiz'));
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 0));

        $rule = new open_close_date_access_rule($quizobj, 10000);
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

        $rule = new open_close_date_access_rule($quizobj, 20000);
        $this->assertEqual($rule->description(),
                array(get_string('quizcloseson', 'quiz', userdate(20000))));
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 20000 - QUIZ_SHOW_TIME_BEFORE_DEADLINE));
        $this->assertEqual($rule->time_left($attempt, 19900), 100);
        $this->assertEqual($rule->time_left($attempt, 20000), 0);
        $this->assertEqual($rule->time_left($attempt, 20100), -100);

        $rule = new open_close_date_access_rule($quizobj, 20001);
        $this->assertEqual($rule->description(),
                array(get_string('quizclosed', 'quiz', userdate(20000))));
        $this->assertEqual($rule->prevent_access(),
                get_string('notavailable', 'quiz'));
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

        $rule = new open_close_date_access_rule($quizobj, 9999);
        $this->assertEqual($rule->description(),
                array(get_string('quiznotavailable', 'quiz', userdate(10000))));
        $this->assertEqual($rule->prevent_access(),
                get_string('notavailable', 'quiz'));
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));

        $rule = new open_close_date_access_rule($quizobj, 10000);
        $this->assertEqual($rule->description(),
                array(get_string('quizopenedon', 'quiz', userdate(10000)),
                get_string('quizcloseson', 'quiz', userdate(20000))));
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));

        $rule = new open_close_date_access_rule($quizobj, 20000);
        $this->assertEqual($rule->description(),
                array(get_string('quizopenedon', 'quiz', userdate(10000)),
                get_string('quizcloseson', 'quiz', userdate(20000))));
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));

        $rule = new open_close_date_access_rule($quizobj, 20001);
        $this->assertEqual($rule->description(),
                array(get_string('quizclosed', 'quiz', userdate(20000))));
        $this->assertEqual($rule->prevent_access(),
                get_string('notavailable', 'quiz'));
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertTrue($rule->is_finished(0, $attempt));

        $this->assertFalse($rule->time_left($attempt, 20000 - QUIZ_SHOW_TIME_BEFORE_DEADLINE));
        $this->assertEqual($rule->time_left($attempt, 19900), 100);
        $this->assertEqual($rule->time_left($attempt, 20000), 0);
        $this->assertEqual($rule->time_left($attempt, 20100), -100);
    }
}


/**
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class inter_attempt_delay_access_rule_test extends UnitTestCase {
    public function test_just_first_delay() {
        $quiz = new stdClass();
        $quiz->attempts = 3;
        $quiz->timelimit = 0;
        $quiz->delay1 = 1000;
        $quiz->delay2 = 0;
        $quiz->timeclose = 0;
        $quiz->questions = '';
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass();
        $attempt->timefinish = 10000;

        $rule = new inter_attempt_delay_access_rule($quizobj, 10000);
        $this->assertFalse($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 0));

        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt),
                get_string('youmustwait', 'quiz', userdate(11000)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $attempt->timefinish = 9000;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $attempt->timefinish = 9001;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt),
                get_string('youmustwait', 'quiz', userdate(10001)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
    }

    public function test_just_second_delay() {
        $quiz = new stdClass();
        $quiz->attempts = 5;
        $quiz->timelimit = 0;
        $quiz->delay1 = 0;
        $quiz->delay2 = 1000;
        $quiz->timeclose = 0;
        $quiz->questions = '';
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass();
        $attempt->timefinish = 10000;

        $rule = new inter_attempt_delay_access_rule($quizobj, 10000);
        $this->assertFalse($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 0));

        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(5, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt),
                get_string('youmustwait', 'quiz', userdate(11000)));
        $this->assertEqual($rule->prevent_new_attempt(3, $attempt),
                get_string('youmustwait', 'quiz', userdate(11000)));
        $attempt->timefinish = 9000;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $attempt->timefinish = 9001;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt),
                get_string('youmustwait', 'quiz', userdate(10001)));
        $this->assertEqual($rule->prevent_new_attempt(4, $attempt),
                get_string('youmustwait', 'quiz', userdate(10001)));
    }

    public function test_just_both_delays() {
        $quiz = new stdClass();
        $quiz->attempts = 5;
        $quiz->timelimit = 0;
        $quiz->delay1 = 2000;
        $quiz->delay2 = 1000;
        $quiz->timeclose = 0;
        $quiz->questions = '';
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass();
        $attempt->timefinish = 10000;

        $rule = new inter_attempt_delay_access_rule($quizobj, 10000);
        $this->assertFalse($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 0));

        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(5, $attempt));
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt),
                get_string('youmustwait', 'quiz', userdate(12000)));
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt),
                get_string('youmustwait', 'quiz', userdate(11000)));
        $this->assertEqual($rule->prevent_new_attempt(3, $attempt),
                get_string('youmustwait', 'quiz', userdate(11000)));
        $attempt->timefinish = 8000;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $attempt->timefinish = 8001;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt),
                get_string('youmustwait', 'quiz', userdate(10001)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(4, $attempt));
        $attempt->timefinish = 9000;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt),
                get_string('youmustwait', 'quiz', userdate(11000)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $attempt->timefinish = 9001;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt),
                get_string('youmustwait', 'quiz', userdate(11001)));
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt),
                get_string('youmustwait', 'quiz', userdate(10001)));
        $this->assertEqual($rule->prevent_new_attempt(4, $attempt),
                get_string('youmustwait', 'quiz', userdate(10001)));
    }

    public function test_with_close_date() {
        $quiz = new stdClass();
        $quiz->attempts = 5;
        $quiz->timelimit = 0;
        $quiz->delay1 = 2000;
        $quiz->delay2 = 1000;
        $quiz->timeclose = 15000;
        $quiz->questions = '';
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass();
        $attempt->timefinish = 13000;

        $rule = new inter_attempt_delay_access_rule($quizobj, 10000);
        $this->assertFalse($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 0));

        $attempt->timefinish = 13000;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt),
                get_string('youmustwait', 'quiz', userdate(15000)));
        $attempt->timefinish = 13001;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt),
                get_string('youcannotwait', 'quiz'));
        $attempt->timefinish = 14000;
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt),
                get_string('youmustwait', 'quiz', userdate(15000)));
        $attempt->timefinish = 14001;
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt),
                get_string('youcannotwait', 'quiz'));

        $rule = new inter_attempt_delay_access_rule($quizobj, 15000);
        $attempt->timefinish = 13000;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $attempt->timefinish = 13001;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt),
                get_string('youcannotwait', 'quiz'));
        $attempt->timefinish = 14000;
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $attempt->timefinish = 14001;
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt),
                get_string('youcannotwait', 'quiz'));

        $rule = new inter_attempt_delay_access_rule($quizobj, 15001);
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
        $quiz = new stdClass();
        $quiz->attempts = 5;
        $quiz->timelimit = 100;
        $quiz->delay1 = 2000;
        $quiz->delay2 = 1000;
        $quiz->timeclose = 0;
        $quiz->questions = '';
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass();
        $attempt->timestart = 9900;
        $attempt->timefinish = 10100;

        $rule = new inter_attempt_delay_access_rule($quizobj, 10000);
        $this->assertFalse($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 0));

        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(5, $attempt));
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt),
                get_string('youmustwait', 'quiz', userdate(12000)));
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt),
                get_string('youmustwait', 'quiz', userdate(11000)));
        $this->assertEqual($rule->prevent_new_attempt(3, $attempt),
                get_string('youmustwait', 'quiz', userdate(11000)));
        $attempt->timestart = 7950;
        $attempt->timefinish = 8000;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $attempt->timestart = 7950;
        $attempt->timefinish = 8001;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt),
                get_string('youmustwait', 'quiz', userdate(10001)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(4, $attempt));
        $attempt->timestart = 8950;
        $attempt->timefinish = 9000;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt),
                get_string('youmustwait', 'quiz', userdate(11000)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $attempt->timestart = 8950;
        $attempt->timefinish = 9001;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt),
                get_string('youmustwait', 'quiz', userdate(11001)));
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt),
                get_string('youmustwait', 'quiz', userdate(10001)));
        $this->assertEqual($rule->prevent_new_attempt(4, $attempt),
                get_string('youmustwait', 'quiz', userdate(10001)));
        $attempt->timestart = 8900;
        $attempt->timefinish = 9100;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt),
                get_string('youmustwait', 'quiz', userdate(11000)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $attempt->timestart = 8901;
        $attempt->timefinish = 9100;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt),
                get_string('youmustwait', 'quiz', userdate(11001)));
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt),
                get_string('youmustwait', 'quiz', userdate(10001)));
        $this->assertEqual($rule->prevent_new_attempt(4, $attempt),
                get_string('youmustwait', 'quiz', userdate(10001)));
    }
}


/**
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class password_access_rule_test extends UnitTestCase {
    public function test_password_access_rule() {
        $quiz = new stdClass();
        $quiz->password = 'frog';
        $quiz->questions = '';
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $rule = new password_access_rule($quizobj, 0);
        $attempt = new stdClass();

        $this->assertFalse($rule->prevent_access());
        $this->assertEqual($rule->description(), get_string('requirepasswordmessage', 'quiz'));
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 1));
    }
}


/**
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class securewindow_access_rule_test extends UnitTestCase {
    // Nothing very testable in this class, just test that it obeys the general access rule contact.

    public function test_securewindow_access_rule() {
        $quiz = new stdClass();
        $quiz->popup = 1;
        $quiz->questions = '';
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $rule = new securewindow_access_rule($quizobj, 0);
        $attempt = new stdClass();

        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->description());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 1));
    }
}


/**
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_access_manager_test extends UnitTestCase {
    public function test_cannot_review_message() {
        $quiz = new stdClass();
        $quiz->reviewattempt = 0x10010;
        $quiz->timeclose = 0;
        $quiz->attempts = 0;
        $quiz->questions = '1,2,0,3,4,0';

        $cm = new stdClass();
        $cm->id = 123;

        $quizobj = new quiz($quiz, $cm, new stdClass(), false);

        $am = new quiz_access_manager($quizobj, time(), false);

        $this->assertEqual('',
                $am->cannot_review_message(mod_quiz_display_options::DURING));
        $this->assertEqual('',
                $am->cannot_review_message(mod_quiz_display_options::IMMEDIATELY_AFTER));
        $this->assertEqual(get_string('noreview', 'quiz'),
                $am->cannot_review_message(mod_quiz_display_options::LATER_WHILE_OPEN));
        $this->assertEqual(get_string('noreview', 'quiz'),
                $am->cannot_review_message(mod_quiz_display_options::AFTER_CLOSE));

        $closetime = time() + 10000;
        $quiz->timeclose = $closetime;
        $quizobj = new quiz($quiz, $cm, new stdClass(), false);
        $am = new quiz_access_manager($quizobj, time(), false);

        $this->assertEqual(get_string('noreviewuntil', 'quiz', userdate($closetime)),
                $am->cannot_review_message(mod_quiz_display_options::LATER_WHILE_OPEN));
    }
}
