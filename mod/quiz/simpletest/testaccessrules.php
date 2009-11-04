<?php
/**
 * Unit tests for (some of) mod/quiz/accessrules.php.
 *
 * @copyright &copy; 2008 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); /// It must be included from a Moodle page.
}

require_once($CFG->dirroot . '/mod/quiz/locallib.php');

class simple_rules_test extends UnitTestCase {
    public static $includecoverage = array('mod/quiz/locallib.php');
    function test_num_attempts_access_rule() {
        $quiz = new stdClass;
        $quiz->attempts = 3;
        $quiz->questions = '';
        $cm = new stdClass;
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $rule = new num_attempts_access_rule($quizobj, 0);
        $attempt = new stdClass;

        $this->assertEqual($rule->description(), get_string('attemptsallowedn', 'quiz', 3));

        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertEqual($rule->prevent_new_attempt(3, $attempt), get_string('nomoreattempts', 'quiz'));
        $this->assertEqual($rule->prevent_new_attempt(666, $attempt), get_string('nomoreattempts', 'quiz'));

        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->is_finished(2, $attempt));
        $this->assertTrue($rule->is_finished(3, $attempt));
        $this->assertTrue($rule->is_finished(666, $attempt));

        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->time_left($attempt, 1));
    }

    function test_ipaddress_access_rule() {
        $quiz = new stdClass;
        $attempt = new stdClass;
        $cm = new stdClass;
        $cm->id = 0;

        // Test the allowed case by getting the user's IP address. However, this
        // does not always work, for example using the mac install package on my laptop.
        $quiz->subnet = getremoteaddr();
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

    function test_time_limit_access_rule() {
        $quiz = new stdClass;
        $quiz->timelimit = 3600;
        $quiz->questions = '';
        $cm = new stdClass;
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $rule = new time_limit_access_rule($quizobj, 10000);
        $attempt = new stdClass;

        $this->assertEqual($rule->description(), get_string('quiztimelimit', 'quiz', format_time(3600)));

        $attempt->timestart = 10000;
        $this->assertEqual($rule->time_left($attempt, 10000), 3600);
        $this->assertEqual($rule->time_left($attempt, 12000), 1600);
        $this->assertEqual($rule->time_left($attempt, 14000), -400);

        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
    }
}

class open_close_date_access_rule_test extends UnitTestCase {
    function test_no_dates() {
        $quiz = new stdClass;
        $quiz->timeopen = 0;
        $quiz->timeclose = 0;
        $quiz->questions = '';
        $cm = new stdClass;
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass;
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

    function test_start_date() {
        $quiz = new stdClass;
        $quiz->timeopen = 10000;
        $quiz->timeclose = 0;
        $quiz->questions = '';
        $cm = new stdClass;
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass;
        $attempt->preview = 0;

        $rule = new open_close_date_access_rule($quizobj, 9999);
        $this->assertEqual($rule->description(), array(get_string('quiznotavailable', 'quiz', userdate(10000))));
        $this->assertEqual($rule->prevent_access(), get_string('notavailable', 'quiz'));
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 0));

        $rule = new open_close_date_access_rule($quizobj, 10000);
        $this->assertEqual($rule->description(), array(get_string('quizopenedon', 'quiz', userdate(10000))));
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 0));
    }

    function test_close_date() {
        $quiz = new stdClass;
        $quiz->timeopen = 0;
        $quiz->timeclose = 20000;
        $quiz->questions = '';
        $cm = new stdClass;
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass;
        $attempt->preview = 0;

        $rule = new open_close_date_access_rule($quizobj, 20000);
        $this->assertEqual($rule->description(), array(get_string('quizcloseson', 'quiz', userdate(20000))));
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 20000 - QUIZ_SHOW_TIME_BEFORE_DEADLINE));
        $this->assertEqual($rule->time_left($attempt, 19900), 100);
        $this->assertEqual($rule->time_left($attempt, 20000), 0);
        $this->assertEqual($rule->time_left($attempt, 20100), -100);

        $rule = new open_close_date_access_rule($quizobj, 20001);
        $this->assertEqual($rule->description(), array(get_string('quizclosed', 'quiz', userdate(20000))));
        $this->assertEqual($rule->prevent_access(), get_string('notavailable', 'quiz'));
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertTrue($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 20000 - QUIZ_SHOW_TIME_BEFORE_DEADLINE));
        $this->assertEqual($rule->time_left($attempt, 19900), 100);
        $this->assertEqual($rule->time_left($attempt, 20000), 0);
        $this->assertEqual($rule->time_left($attempt, 20100), -100);
    }

    function test_both_dates() {
        $quiz = new stdClass;
        $quiz->timeopen = 10000;
        $quiz->timeclose = 20000;
        $quiz->questions = '';
        $cm = new stdClass;
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass;
        $attempt->preview = 0;

        $rule = new open_close_date_access_rule($quizobj, 9999);
        $this->assertEqual($rule->description(), array(get_string('quiznotavailable', 'quiz', userdate(10000))));
        $this->assertEqual($rule->prevent_access(), get_string('notavailable', 'quiz'));
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));

        $rule = new open_close_date_access_rule($quizobj, 10000);
        $this->assertEqual($rule->description(), array(get_string('quizopenedon', 'quiz', userdate(10000)),
                get_string('quizcloseson', 'quiz', userdate(20000))));
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));

        $rule = new open_close_date_access_rule($quizobj, 20000);
        $this->assertEqual($rule->description(), array(get_string('quizopenedon', 'quiz', userdate(10000)),
                get_string('quizcloseson', 'quiz', userdate(20000))));
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));

        $rule = new open_close_date_access_rule($quizobj, 20001);
        $this->assertEqual($rule->description(), array(get_string('quizclosed', 'quiz', userdate(20000))));
        $this->assertEqual($rule->prevent_access(), get_string('notavailable', 'quiz'));
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertTrue($rule->is_finished(0, $attempt));

        $this->assertFalse($rule->time_left($attempt, 20000 - QUIZ_SHOW_TIME_BEFORE_DEADLINE));
        $this->assertEqual($rule->time_left($attempt, 19900), 100);
        $this->assertEqual($rule->time_left($attempt, 20000), 0);
        $this->assertEqual($rule->time_left($attempt, 20100), -100);
    }
}

class inter_attempt_delay_access_rule_test extends UnitTestCase {
    function test_just_first_delay() {
        $quiz = new stdClass;
        $quiz->attempts = 3;
        $quiz->delay1 = 1000;
        $quiz->delay2 = 0;
        $quiz->timeclose = 0;
        $quiz->questions = '';
        $cm = new stdClass;
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass;
        $attempt->timefinish = 10000;

        $rule = new inter_attempt_delay_access_rule($quizobj, 10000);
        $this->assertFalse($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 0));

        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt), get_string('youmustwait', 'quiz', userdate(11000)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $attempt->timefinish = 9000;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $attempt->timefinish = 9001;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt), get_string('youmustwait', 'quiz', userdate(10001)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
    }

    function test_just_second_delay() {
        $quiz = new stdClass;
        $quiz->attempts = 5;
        $quiz->delay1 = 0;
        $quiz->delay2 = 1000;
        $quiz->timeclose = 0;
        $quiz->questions = '';
        $cm = new stdClass;
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass;
        $attempt->timefinish = 10000;

        $rule = new inter_attempt_delay_access_rule($quizobj, 10000);
        $this->assertFalse($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 0));

        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(5, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt), get_string('youmustwait', 'quiz', userdate(11000)));
        $this->assertEqual($rule->prevent_new_attempt(3, $attempt), get_string('youmustwait', 'quiz', userdate(11000)));
        $attempt->timefinish = 9000;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $attempt->timefinish = 9001;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt), get_string('youmustwait', 'quiz', userdate(10001)));
        $this->assertEqual($rule->prevent_new_attempt(4, $attempt), get_string('youmustwait', 'quiz', userdate(10001)));
    }

    function test_just_both_delays() {
        $quiz = new stdClass;
        $quiz->attempts = 5;
        $quiz->delay1 = 2000;
        $quiz->delay2 = 1000;
        $quiz->timeclose = 0;
        $quiz->questions = '';
        $cm = new stdClass;
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass;
        $attempt->timefinish = 10000;

        $rule = new inter_attempt_delay_access_rule($quizobj, 10000);
        $this->assertFalse($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 0));

        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(5, $attempt));
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt), get_string('youmustwait', 'quiz', userdate(12000)));
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt), get_string('youmustwait', 'quiz', userdate(11000)));
        $this->assertEqual($rule->prevent_new_attempt(3, $attempt), get_string('youmustwait', 'quiz', userdate(11000)));
        $attempt->timefinish = 8000;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $attempt->timefinish = 8001;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt), get_string('youmustwait', 'quiz', userdate(10001)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(4, $attempt));
        $attempt->timefinish = 9000;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt), get_string('youmustwait', 'quiz', userdate(11000)));
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $this->assertFalse($rule->prevent_new_attempt(3, $attempt));
        $attempt->timefinish = 9001;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt), get_string('youmustwait', 'quiz', userdate(11001)));
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt), get_string('youmustwait', 'quiz', userdate(10001)));
        $this->assertEqual($rule->prevent_new_attempt(4, $attempt), get_string('youmustwait', 'quiz', userdate(10001)));
    }

    function test_with_close_date() {
        $quiz = new stdClass;
        $quiz->attempts = 5;
        $quiz->delay1 = 2000;
        $quiz->delay2 = 1000;
        $quiz->timeclose = 15000;
        $quiz->questions = '';
        $cm = new stdClass;
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $attempt = new stdClass;
        $attempt->timefinish = 13000;

        $rule = new inter_attempt_delay_access_rule($quizobj, 10000);
        $this->assertFalse($rule->description());
        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 0));

        $attempt->timefinish = 13000;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt), get_string('youmustwait', 'quiz', userdate(15000)));
        $attempt->timefinish = 13001;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt), get_string('youcannotwait', 'quiz'));
        $attempt->timefinish = 14000;
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt), get_string('youmustwait', 'quiz', userdate(15000)));
        $attempt->timefinish = 14001;
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt), get_string('youcannotwait', 'quiz'));

        $rule = new inter_attempt_delay_access_rule($quizobj, 15000);
        $attempt->timefinish = 13000;
        $this->assertFalse($rule->prevent_new_attempt(1, $attempt));
        $attempt->timefinish = 13001;
        $this->assertEqual($rule->prevent_new_attempt(1, $attempt), get_string('youcannotwait', 'quiz'));
        $attempt->timefinish = 14000;
        $this->assertFalse($rule->prevent_new_attempt(2, $attempt));
        $attempt->timefinish = 14001;
        $this->assertEqual($rule->prevent_new_attempt(2, $attempt), get_string('youcannotwait', 'quiz'));

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
}

class password_access_rule_test extends UnitTestCase {
    function test_password_access_rule() {
        $quiz = new stdClass;
        $quiz->password = 'frog';
        $quiz->questions = '';
        $cm = new stdClass;
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $rule = new password_access_rule($quizobj, 0);
        $attempt = new stdClass;

        $this->assertFalse($rule->prevent_access());
        $this->assertEqual($rule->description(), get_string('requirepasswordmessage', 'quiz'));
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 1));
    }
}

class securewindow_access_rule_test extends UnitTestCase {
    // Nothing very testable in this class, just test that it obeys the general access rule contact.

    function test_securewindow_access_rule() {
        $quiz = new stdClass;
        $quiz->popup = 1;
        $quiz->questions = '';
        $cm = new stdClass;
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $rule = new securewindow_access_rule($quizobj, 0);
        $attempt = new stdClass;

        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->description());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->time_left($attempt, 1));
    }
}


