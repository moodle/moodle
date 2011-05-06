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
 * Question engine upgrade helper library code.
 *
 * @package    local
 * @subpackage qeupgradehelper
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Detect whether this site has been upgraded to the new question engine yet.
 */
function local_qeupgradehelper_isupgraded() {
    global $CFG;
    return is_readable($CFG->dirroot . '/question/engine/upgrade/upgradelib.php');
}

/**
 * Get the URL of a script within this plugin.
 * @param string $script the script name, without .php. E.g. 'index'.
 * @param array $params URL parameters (optional).
 */
function local_qeupgradehelper_url($script, $params = array()) {
    return new moodle_url('/local/qeupgradehelper/' . $script . '.php', $params);
}


/**
 * Class to encapsulate one of the functionalities that this plugin offers.
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_qeupgradehelper_action {
    /** @var string the name of this action. */
    public $name;
    /** @var moodle_url the URL to launch this action. */
    public $url;
    /** @var string a description of this aciton. */
    public $description;

    /**
     * Constructor to set the fields.
     */
    protected function __construct($name, moodle_url $url, $description) {
        $this->name = $name;
        $this->url = $url;
        $this->description = $description;
    }

    /**
     * Make an action with standard values.
     * @param string $shortname internal name of the action. Used to get strings
     * and build a URL.
     * @param array $params any URL params required.
     */
    public static function make($shortname, $params = array()) {
        return new self(
                get_string($shortname, 'local_qeupgradehelper'),
                local_qeupgradehelper_url($shortname, $params),
                get_string($shortname . '_desc', 'local_qeupgradehelper'));
    }
}


/**
 * Get the information about a quizzes that can be upgraded.
 * @return array of objects with information about the quizzes that need upgrading.
 *      has fields quiz id, quiz name, course shortname, couresid and number of
 *      attempts that need converting.
 */
function local_qeupgradehelper_get_upgradable_quizzes() {
    global $DB;
    return $DB->get_records_sql("
            SELECT
                quiz.id,
                quiz.name,
                c.shortname,
                c.id AS courseid,
                COUNT(1) AS attemptcount

            FROM {quiz_attempts} quiza
            JOIN {quiz} quiz ON quiz.id = quiza.quiz
            JOIN {course} c ON c.id = quiz.course

            WHERE quiza.preview = 0
              AND quiza.needsupgradetonewqe = 1

            GROUP BY quiz.id, quiz.name, c.shortname, c.id

            ORDER BY c.shortname, quiz.name, quiz.id");
}

/**
 * Get the information about a quiz to be upgraded.
 * @param integer $quizid the quiz id.
 * @return object the information about that quiz, as for
 *      {@link local_qeupgradehelper_get_upgradable_quizzes()}.
 */
function local_qeupgradehelper_get_quiz($quizid) {
    global $DB;
    return $DB->get_record_sql("
            SELECT
                quiz.id,
                quiz.name,
                c.shortname,
                c.id AS courseid,
                COUNT(1) AS numtoconvert

            FROM {quiz_attempts} quiza
            JOIN {quiz} quiz ON quiz.id = quiza.quiz
            JOIN {course} c ON c.id = quiz.course

            WHERE quiza.preview = 0
              AND quiza.needsupgradetonewqe = 1
              AND quiz.id = ?

            GROUP BY quiz.id, quiz.name, c.shortname, c.id

            ORDER BY c.shortname, quiz.name, quiz.id", array($quizid));
}

/**
 * Get the information about quizzes that can be reset.
 * @return array of objects with information about the quizzes that need upgrading.
 *      has fields quiz id, quiz name, course shortname, couresid and number of
 *      converted attempts.
 */
function local_qeupgradehelper_get_resettable_quizzes() {
    global $DB;
    return $DB->get_records_sql("
            SELECT
                quiz.id,
                quiz.name,
                c.shortname,
                c.id AS courseid,
                COUNT(1) AS attemptcount

            FROM {quiz_attempts} quiza
            JOIN {quiz} quiz ON quiz.id = quiza.quiz
            JOIN {course} c ON c.id = quiz.course

            WHERE quiza.preview = 0
              AND quiza.needsupgradetonewqe = 0
              AND EXISTS(SELECT 1 FROM {question_states}
                    WHERE attempt = quiza.uniqueid)

            GROUP BY quiz.id, quiz.name, c.shortname, c.id
            ORDER BY c.shortname, quiz.name, quiz.id");
}

/**
 * Get the information about a quiz to be upgraded.
 * @param integer $quizid the quiz id.
 * @return object the information about that quiz, as for
 *      {@link local_qeupgradehelper_get_resettable_quizzes()}, but with extra fields
 *      totalattempts and resettableattempts.
 */
function local_qeupgradehelper_get_resettable_quiz($quizid) {
    global $CFG;
    return get_record_sql("
            SELECT
                quiz.id,
                quiz.name,
                c.shortname,
                c.id AS courseid,
                COUNT(1) AS totalattempts,
                SUM(CASE WHEN quiza.needsupgradetonewqe = 0 AND
                    oldtimemodified.time IS NOT NULL THEN 1 ELSE 0 END) AS convertedattempts,
                SUM(CASE WHEN quiza.needsupgradetonewqe = 0 AND
                    oldtimemodified.time >= newtimemodified.time THEN 1 ELSE 0 END) AS resettableattempts

            FROM {$CFG->prefix}quiz_attempts quiza
            JOIN {$CFG->prefix}quiz quiz ON quiz.id = quiza.quiz
            JOIN {$CFG->prefix}course c ON c.id = quiz.course
            LEFT JOIN (
                SELECT attempt, MAX(timestamp) AS time
                FROM {$CFG->prefix}question_states
                GROUP BY attempt
            ) AS oldtimemodified ON oldtimemodified.attempt = quiza.uniqueid
            LEFT JOIN (
                SELECT qa.questionusageid, MAX(qas.timecreated) AS time
                FROM {$CFG->prefix}question_attempts qa
                JOIN {$CFG->prefix}question_attempt_steps qas ON qas.questionattemptid = qa.id
                GROUP BY qa.questionusageid
            ) AS newtimemodified ON newtimemodified.questionusageid = quiza.uniqueid

            WHERE quiza.preview = 0
              AND quiz.id = {$quizid}

            GROUP BY quiz.id, quiz.name, c.shortname, c.id");
}


