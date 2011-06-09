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
 * This defines the states a question can be in.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * An enumeration representing the states a question can be in after a
 * {@link question_attempt_step}.
 *
 * There are also some useful methods for testing and manipulating states.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_state {
    /**#@+
     * Specific question_state instances.
     */
    public static $notstarted;
    public static $unprocessed;
    public static $todo;
    public static $invalid;
    public static $complete;
    public static $needsgrading;
    public static $finished;
    public static $gaveup;
    public static $gradedwrong;
    public static $gradedpartial;
    public static $gradedright;
    public static $manfinished;
    public static $mangaveup;
    public static $mangrwrong;
    public static $mangrpartial;
    public static $mangrright;
    /**#@+-*/

    protected function __construct() {
    }

    public static function init() {
        $us = new ReflectionClass('question_state');
        foreach ($us->getStaticProperties() as $name => $notused) {
            $class = 'question_state_' . $name;
            $states[$name] = new $class();
            self::$$name = $states[$name];
        }
    }

    /**
     * Get all the states in an array.
     * @return of question_state objects.
     */
    public static function get_all() {
        $states = array();
        $us = new ReflectionClass('question_state');
        foreach ($us->getStaticProperties() as $name => $notused) {
            $states[] = self::$$name;
        }
        return $states;
    }

    /**
     * Get all the states in an array.
     * @param string $summarystate one of the four summary states
     * inprogress, needsgrading, manuallygraded or autograded.
     * @return arrau of the corresponding states.
     */
    public static function get_all_for_summary_state($summarystate) {
        $states = array();
        foreach (self::get_all() as $state) {
            if ($state->get_summary_state() == $summarystate) {
                $states[] = $state;
            }
        }
        if (empty($states)) {
            throw new coding_exception('unknown summary state ' . $summarystate);
        }
        return $states;
    }

    /**
     * @return string convert this state to a string.
     */
    public function __toString() {
        return substr(get_class($this), 15);
    }

    /**
     * @param string $name a state name.
     * @return question_state the state with that name.
     */
    public static function get($name) {
        return self::$$name;
    }

    /**
     * Is this state one of the ones that mean the question attempt is in progress?
     * That is, started, but no finished.
     * @return bool
     */
    public function is_active() {
        return false;
    }

    /**
     * Is this state one of the ones that mean the question attempt is finished?
     * That is, no further interaction possible, apart from manual grading.
     * @return bool
     */
    public function is_finished() {
        return true;
    }

    /**
     * Is this state one of the ones that mean the question attempt has been graded?
     * @return bool
     */
    public function is_graded() {
        return false;
    }

    /**
     * Is this state one of the ones that mean the question attempt has been graded?
     * @return bool
     */
    public function is_correct() {
        return false;
    }

    /**
     * Is this state one of the ones that mean the question attempt has been graded?
     * @return bool
     */
    public function is_partially_correct() {
        return false;
    }

    /**
     * Is this state one of the ones that mean the question attempt has been graded?
     * @return bool
     */
    public function is_incorrect() {
        return false;
    }

    /**
     * Is this state one of the ones that mean the question attempt has been graded?
     * @return bool
     */
    public function is_gave_up() {
        return false;
    }

    /**
     * Is this state one of the ones that mean the question attempt has had a manual comment added?
     * @return bool
     */
    public function is_commented() {
        return false;
    }

    /**
     * Each state can be categorised into one of four categories:
     * inprogress, needsgrading, manuallygraded or autograded.
     * @return string which category this state falls into.
     */
    public function get_summary_state() {
        if (!$this->is_finished()) {
            return 'inprogress';
        } else if ($this == self::$needsgrading) {
            return 'needsgrading';
        } else if ($this->is_commented()) {
            return 'manuallygraded';
        } else {
            return 'autograded';
        }
    }

    /**
     * Return the appropriate graded state based on a fraction. That is 0 or less
     * is $graded_incorrect, 1 is $graded_correct, otherwise it is $graded_partcorrect.
     * Appropriate allowance is made for rounding float values.
     *
     * @param number $fraction the grade, on the fraction scale.
     * @return int one of the state constants.
     */
    public static function graded_state_for_fraction($fraction) {
        if ($fraction < 0.000001) {
            return self::$gradedwrong;
        } else if ($fraction > 0.999999) {
            return self::$gradedright;
        } else {
            return self::$gradedpartial;
        }
    }

    /**
     * Return the appropriate manually graded state based on a fraction. That is 0 or less
     * is $manually_graded_incorrect, 1 is $manually_graded_correct, otherwise it is
     * $manually_graded_partcorrect. Appropriate allowance is made for rounding float values.
     *
     * @param number $fraction the grade, on the fraction scale.
     * @return int one of the state constants.
     */
    public static function manually_graded_state_for_fraction($fraction) {
        if (is_null($fraction)) {
            return self::$needsgrading;
        } else if ($fraction < 0.000001) {
            return self::$mangrwrong;
        } else if ($fraction > 0.999999) {
            return self::$mangrright;
        } else {
            return self::$mangrpartial;
        }
    }

    /**
     * Compute an appropriate state to move to after a manual comment has been
     * added to this state.
     * @param number $fraction the manual grade (if any) on the fraction scale.
     * @return int the new state.
     */
    public function corresponding_commented_state($fraction) {
        throw new coding_exception('Unexpected question state.');
    }

    /**
     * Return an appropriate CSS class name ''/'correct'/'partiallycorrect'/'incorrect',
     * for a state.
     * @return string
     */
    public function get_feedback_class() {
        return '';
    }

    /**
     * Return the name of an appropriate string to look up in the question
     * language pack for a state. This is used, for example, by
     * {@link question_behaviour::get_state_string()}. However, behaviours
     * sometimes change this default string for soemthing more specific.
     *
     * @param bool $showcorrectness Whether right/partial/wrong states should
     * be distinguised, or just treated as 'complete'.
     * @return string the name of a string that can be looked up in the 'question'
     *      lang pack, or used as a CSS class name, etc.
     */
    public abstract function get_state_class($showcorrectness);

    /**
     * The result of doing get_string on the result of {@link get_state_class()}.
     *
     * @param bool $showcorrectness Whether right/partial/wrong states should
     * be distinguised.
     * @return string a string from the lang pack that can be used in the UI.
     */
    public function default_string($showcorrectness) {
        return get_string($this->get_state_class($showcorrectness), 'question');
    }
}


/**#@+
 * Specific question_state subclasses.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_state_notstarted extends question_state {
    public function is_finished() {
        return false;
    }
    public function get_state_class($showcorrectness) {
        throw new coding_exception('Unexpected question state.');
    }
}
class question_state_unprocessed extends question_state {
    public function is_finished() {
        return false;
    }
    public function get_state_class($showcorrectness) {
        throw new coding_exception('Unexpected question state.');
    }
}
class question_state_todo extends question_state {
    public function is_active() {
        return true;
    }
    public function is_finished() {
        return false;
    }
    public function get_state_class($showcorrectness) {
        return 'notyetanswered';
    }
}
class question_state_invalid extends question_state {
    public function is_active() {
        return true;
    }
    public function is_finished() {
        return false;
    }
    public function get_state_class($showcorrectness) {
        return 'invalidanswer';
    }
}
class question_state_complete extends question_state {
    public function is_active() {
        return true;
    }
    public function is_finished() {
        return false;
    }
    public function get_state_class($showcorrectness) {
        return 'answersaved';
    }
}
class question_state_needsgrading extends question_state {
    public function get_state_class($showcorrectness) {
        if ($showcorrectness) {
            return 'requiresgrading';
        } else {
            return 'complete';
        }
    }
    public function corresponding_commented_state($fraction) {
        return self::manually_graded_state_for_fraction($fraction);
    }
}
class question_state_finished extends question_state {
    public function get_state_class($showcorrectness) {
        return 'complete';
    }
    public function corresponding_commented_state($fraction) {
        return self::$manfinished;
    }
}
class question_state_gaveup extends question_state {
    public function is_gave_up() {
        return true;
    }
    public function get_feedback_class() {
        return 'incorrect';
    }
    public function get_state_class($showcorrectness) {
        return 'notanswered';
    }
    public function corresponding_commented_state($fraction) {
        if (is_null($fraction)) {
            return self::$mangaveup;
        } else {
            return self::manually_graded_state_for_fraction($fraction);
        }
    }
}
abstract class question_state_graded extends question_state {
    public function is_graded() {
        return true;
    }
    public function get_state_class($showcorrectness) {
        if ($showcorrectness) {
            return $this->get_feedback_class();
        } else {
            return 'complete';
        }
    }
    public function corresponding_commented_state($fraction) {
        return self::manually_graded_state_for_fraction($fraction);
    }
}
class question_state_gradedwrong extends question_state_graded {
    public function is_incorrect() {
        return true;
    }
    public function get_feedback_class() {
        return 'incorrect';
    }
}
class question_state_gradedpartial extends question_state_graded {
    public function is_graded() {
        return true;
    }
    public function is_partially_correct() {
        return true;
    }
    public function get_feedback_class() {
        return 'partiallycorrect';
    }
}
class question_state_gradedright extends question_state_graded {
    public function is_graded() {
        return true;
    }
    public function is_correct() {
        return true;
    }
    public function get_feedback_class() {
        return 'correct';
    }
}
class question_state_manfinished extends question_state_finished {
    public function is_commented() {
        return true;
    }
}
class question_state_mangaveup extends question_state_gaveup {
    public function is_commented() {
        return true;
    }
}
abstract class question_state_manuallygraded extends question_state_graded {
    public function is_commented() {
        return true;
    }
}
class question_state_mangrwrong extends question_state_manuallygraded {
    public function is_incorrect() {
        return false;
    }
    public function get_feedback_class() {
        return 'incorrect';
    }
}
class question_state_mangrpartial extends question_state_manuallygraded {
    public function is_partially_correct() {
        return true;
    }
    public function get_feedback_class() {
        return 'partiallycorrect';
    }
}
class question_state_mangrright extends question_state_manuallygraded {
    public function is_correct() {
        return true;
    }
    public function get_feedback_class() {
        return 'correct';
    }
}
/**#@-*/
question_state::init();
