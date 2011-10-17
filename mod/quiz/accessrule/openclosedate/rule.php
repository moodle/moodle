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
 * Implementaton of the quizaccess_openclosedate plugin.
 *
 * @package    quizaccess
 * @subpackage openclosedate
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/accessrule/accessrulebase.php');


/**
 * A rule enforcing open and close dates.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_openclosedate extends quiz_access_rule_base {

    public static function make(quiz $quizobj, $timenow, $canignoretimelimits) {
        // This rule is always used, even if the quiz has no open or close date.
        return new self($quizobj, $timenow);
    }

    public function description() {
        $result = array();
        if ($this->timenow < $this->quiz->timeopen) {
            $result[] = get_string('quiznotavailable', 'quizaccess_openclosedate',
                    userdate($this->quiz->timeopen));

        } else if ($this->quiz->timeclose && $this->timenow > $this->quiz->timeclose) {
            $result[] = get_string('quizclosed', 'quiz', userdate($this->quiz->timeclose));

        } else {
            if ($this->quiz->timeopen) {
                $result[] = get_string('quizopenedon', 'quiz', userdate($this->quiz->timeopen));
            }
            if ($this->quiz->timeclose) {
                $result[] = get_string('quizcloseson', 'quiz', userdate($this->quiz->timeclose));
            }
        }

        return $result;
    }

    public function prevent_access() {
        if ($this->timenow < $this->quiz->timeopen ||
        ($this->quiz->timeclose && $this->timenow > $this->quiz->timeclose)) {
            return get_string('notavailable', 'quizaccess_openclosedate');
        }
        return false;
    }

    public function is_finished($numprevattempts, $lastattempt) {
        return $this->quiz->timeclose && $this->timenow > $this->quiz->timeclose;
    }

    public function time_left($attempt, $timenow) {
        // If this is a teacher preview after the close date, do not show
        // the time.
        if ($attempt->preview && $timenow > $this->quiz->timeclose) {
            return false;
        }

        // Otherwise, return to the time left until the close date, providing
        // that is less than QUIZ_SHOW_TIME_BEFORE_DEADLINE
        if ($this->quiz->timeclose) {
            $timeleft = $this->quiz->timeclose - $timenow;
            if ($timeleft < QUIZ_SHOW_TIME_BEFORE_DEADLINE) {
                return $timeleft;
            }
        }
        return false;
    }
}
