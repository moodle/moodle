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
 * @return bool whether the site has been upgraded.
 */
function local_qeupgradehelper_is_upgraded() {
    global $CFG, $DB;
    $dbman = $DB->get_manager();
    return is_readable($CFG->dirroot . '/question/engine/upgrade/upgradelib.php') &&
            $dbman->table_exists('question_usages');
}

/**
 * If the site has not yet been upgraded, display an error.
 */
function local_qeupgradehelper_require_upgraded() {
    if (!local_qeupgradehelper_is_upgraded()) {
        throw new moodle_exception('upgradedsiterequired', 'local_qeupgradehelper',
                local_qeupgradehelper_url('index'));
    }
}

/**
 * If the site has been upgraded, display an error.
 */
function local_qeupgradehelper_require_not_upgraded() {
    if (local_qeupgradehelper_is_upgraded()) {
        throw new moodle_exception('notupgradedsiterequired', 'local_qeupgradehelper',
                local_qeupgradehelper_url('index'));
    }
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
 *
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
 * A class to represent a list of quizzes with various information about
 * attempts that can be displayed as a table.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class local_qeupgradehelper_quiz_list {
    public $title;
    public $intro;
    public $quizacolheader;
    public $sql;
    public $quizlist = null;
    public $totalquizas = 0;
    public $totalqas = 0;

    protected function __construct($title, $intro, $quizacolheader) {
        global $DB;
        $this->title = get_string($title, 'local_qeupgradehelper');
        $this->intro = get_string($intro, 'local_qeupgradehelper');
        $this->quizacolheader = get_string($quizacolheader, 'local_qeupgradehelper');
        $this->build_sql();
        $this->quizlist = $DB->get_records_sql($this->sql);
    }

    protected function build_sql() {
        $this->sql = '
            SELECT
                quiz.id,
                quiz.name,
                c.shortname,
                c.id AS courseid,
                COUNT(1) AS attemptcount,
                SUM(qsesscounts.num) AS questionattempts

            FROM {quiz_attempts} quiza
            JOIN {quiz} quiz ON quiz.id = quiza.quiz
            JOIN {course} c ON c.id = quiz.course
            LEFT JOIN (
                SELECT attemptid, COUNT(1) AS num
                FROM {question_sessions}
                GROUP BY attemptid
            ) qsesscounts ON qsesscounts.attemptid = quiza.uniqueid

            WHERE quiza.preview = 0
                ' . $this->extra_where_clause() . '

            GROUP BY quiz.id, quiz.name, c.shortname, c.id

            ORDER BY c.shortname, quiz.name, quiz.id';
    }

    abstract protected function extra_where_clause();

    public function get_col_headings() {
        return array(
            get_string('quizid', 'local_qeupgradehelper'),
            get_string('course'),
            get_string('pluginname', 'quiz'),
            $this->quizacolheader,
            get_string('questionsessions', 'local_qeupgradehelper'),
        );
    }

    public function get_row($quizinfo) {
        $this->totalquizas += $quizinfo->attemptcount;
        $this->totalqas += $quizinfo->questionattempts;
        return array(
            $quizinfo->id,
            html_writer::link(new moodle_url('/course/view.php',
                    array('id' => $quizinfo->courseid)), format_string($quizinfo->shortname)),
            html_writer::link(new moodle_url('/mod/quiz/view.php',
                    array('id' => $quizinfo->name)), format_string($quizinfo->name)),
            $quizinfo->attemptcount,
            $quizinfo->questionattempts ? $quizinfo->questionattempts : 0,
        );
    }

    public function get_total_row() {
        return array(
            '',
            html_writer::tag('b', get_string('total')),
            '',
            html_writer::tag('b', $this->totalquizas),
            html_writer::tag('b', $this->totalqas),
        );
    }

    public function is_empty() {
        return empty($this->quizlist);
    }
}


/**
 * A list of quizzes that still need to be upgraded after the main upgrade.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_qeupgradehelper_upgradable_quiz_list extends local_qeupgradehelper_quiz_list {
    public function __construct() {
        parent::__construct('quizzeswithunconverted', 'listtodointro', 'attemptstoconvert');
    }

    protected function extra_where_clause() {
        return 'AND quiza.needsupgradetonewqe = 1';
    }

    public function get_col_headings() {
        $headings = parent::get_col_headings();
        $headings[] = get_string('action', 'local_qeupgradehelper');
        return $headings;
    }

    public function get_row($quizinfo) {
        $row = parent::get_row($quizinfo);
        $row[] = html_writer::link(local_qeupgradehelper_url('convertquiz', array('quizid' => $quizinfo->id)),
                        get_string('convertquiz', 'local_qeupgradehelper'));
        return $row;
    }
}


/**
 * A list of quizzes that still need to be upgraded after the main upgrade.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_qeupgradehelper_resettable_quiz_list extends local_qeupgradehelper_quiz_list {
    public function __construct() {
        parent::__construct('quizzesthatcanbereset', 'listupgradedintro', 'convertedattempts');
    }

    protected function extra_where_clause() {
        return 'AND quiza.needsupgradetonewqe = 0
              AND EXISTS(SELECT 1 FROM {question_states}
                    WHERE attempt = quiza.uniqueid)';
    }

    public function get_col_headings() {
        $headings = parent::get_col_headings();
        $headings[] = get_string('action', 'local_qeupgradehelper');
        return $headings;
    }

    public function get_row($quizinfo) {
        $row = parent::get_row($quizinfo);
        $row[] = html_writer::link(local_qeupgradehelper_url('resetattempts', array('quizid' => $quizinfo->id)),
                        get_string('resetattempts', 'local_qeupgradehelper'));
        return $row;
    }
}


function local_qeupgradehelper_get_pre_upgrade_quizzes() {
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

            GROUP BY quiz.id, quiz.name, c.shortname, c.id
            ORDER BY c.shortname, quiz.name, quiz.id");
}

/**
 * A list of quizzes that still need to be upgraded after the main upgrade.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_qeupgradehelper_pre_upgrade_quiz_list extends local_qeupgradehelper_quiz_list {
    public function __construct() {
        parent::__construct('quizzestobeupgraded', 'listpreupgradeintro', 'numberofattempts');
    }

    protected function extra_where_clause() {
        return '';
    }
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
 * Get the information about a quiz to be upgraded.
 * @param integer $quizid the quiz id.
 * @return object the information about that quiz, as for
 *      {@link local_qeupgradehelper_get_resettable_quizzes()}, but with extra fields
 *      totalattempts and resettableattempts.
 */
function local_qeupgradehelper_get_resettable_quiz($quizid) {
    global $DB;
    return $DB->get_record_sql("
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

            FROM {quiz_attempts} quiza
            JOIN {quiz} quiz ON quiz.id = quiza.quiz
            JOIN {course} c ON c.id = quiz.course
            LEFT JOIN (
                SELECT attempt, MAX(timestamp) AS time
                FROM {question_states}
                GROUP BY attempt
            ) AS oldtimemodified ON oldtimemodified.attempt = quiza.uniqueid
            LEFT JOIN (
                SELECT qa.questionusageid, MAX(qas.timecreated) AS time
                FROM {question_attempts} qa
                JOIN {question_attempt_steps} qas ON qas.questionattemptid = qa.id
                GROUP BY qa.questionusageid
            ) AS newtimemodified ON newtimemodified.questionusageid = quiza.uniqueid

            WHERE quiza.preview = 0
              AND quiz.id = ?

            GROUP BY quiz.id, quiz.name, c.shortname, c.id", array($quizid));
}
