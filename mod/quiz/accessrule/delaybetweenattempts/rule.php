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
 * Implementaton of the quizaccess_delaybetweenattempts plugin.
 *
 * @package    quizaccess
 * @subpackage delaybetweenattempts
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/accessrule/accessrulebase.php');


/**
* A rule imposing the delay between attempts settings.
*
* @copyright  2009 Tim Hunt
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
class quizaccess_delaybetweenattempts extends quiz_access_rule_base {
    public function prevent_new_attempt($numprevattempts, $lastattempt) {
        if ($this->_quiz->attempts > 0 && $numprevattempts >= $this->_quiz->attempts) {
            // No more attempts allowed anyway.
            return false;
        }
        if ($this->_quiz->timeclose != 0 && $this->_timenow > $this->_quiz->timeclose) {
            // No more attempts allowed anyway.
            return false;
        }
        $nextstarttime = $this->compute_next_start_time($numprevattempts, $lastattempt);
        if ($this->_timenow < $nextstarttime) {
            if ($this->_quiz->timeclose == 0 || $nextstarttime <= $this->_quiz->timeclose) {
                return get_string('youmustwait', 'quiz', userdate($nextstarttime));
            } else {
                return get_string('youcannotwait', 'quiz');
            }
        }
        return false;
    }

    /**
     * Compute the next time a student would be allowed to start an attempt,
     * according to this rule.
     * @param int $numprevattempts number of previous attempts.
     * @param object $lastattempt information about the previous attempt.
     * @return number the time.
     */
    protected function compute_next_start_time($numprevattempts, $lastattempt) {
        if ($numprevattempts == 0) {
            return 0;
        }

        $lastattemptfinish = $lastattempt->timefinish;
        if ($this->_quiz->timelimit > 0) {
            $lastattemptfinish = min($lastattemptfinish,
            $lastattempt->timestart + $this->_quiz->timelimit);
        }

        if ($numprevattempts == 1 && $this->_quiz->delay1) {
            return $lastattemptfinish + $this->_quiz->delay1;
        } else if ($numprevattempts > 1 && $this->_quiz->delay2) {
            return $lastattemptfinish + $this->_quiz->delay2;
        }
        return 0;
    }

    public function is_finished($numprevattempts, $lastattempt) {
        $nextstarttime = $this->compute_next_start_time($numprevattempts, $lastattempt);
        return $this->_timenow <= $nextstarttime &&
        $this->_quiz->timeclose != 0 && $nextstarttime >= $this->_quiz->timeclose;
    }
}
