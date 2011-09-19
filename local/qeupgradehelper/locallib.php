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

    public function get_row_class($quizinfo) {
        return null;
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
        $row[] = html_writer::link(local_qeupgradehelper_url('resetquiz', array('quizid' => $quizinfo->id)),
                        get_string('resetquiz', 'local_qeupgradehelper'));
        return $row;
    }
}


/**
 * A list of quizzes that will be upgraded during the main upgrade.
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
 * A list of quizzes that will be upgraded during the main upgrade, when the
 * partialupgrade.php script is being used.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_qeupgradehelper_pre_upgrade_quiz_list_restricted extends local_qeupgradehelper_pre_upgrade_quiz_list {
    protected $quizids;
    protected $restrictedtotalquizas = 0;
    protected $restrictedtotalqas = 0;

    public function __construct($quizids) {
        parent::__construct();
        $this->quizids = $quizids;
    }

    public function get_row_class($quizinfo) {
        if (!in_array($quizinfo->id, $this->quizids)) {
            return 'dimmed';
        } else {
            return parent::get_row_class($quizinfo);
        }
    }

    public function get_col_headings() {
        $headings = parent::get_col_headings();
        $headings[] = get_string('includedintheupgrade', 'local_qeupgradehelper');
        return $headings;
    }

    public function get_row($quizinfo) {
        $row = parent::get_row($quizinfo);
        if (in_array($quizinfo->id, $this->quizids)) {
            $this->restrictedtotalquizas += $quizinfo->attemptcount;
            $this->restrictedtotalqas += $quizinfo->questionattempts;
            $row[] = get_string('yes');
        } else {
            $row[] = get_string('no');
        }
        return $row;
    }

    protected function out_of($restrictedtotal, $fulltotal) {
        $a = new stdClass();
        $a->some = $a->some = html_writer::tag('b', $restrictedtotal);
        $a->total = $fulltotal;
        return get_string('outof', 'local_qeupgradehelper', $a);
    }

    public function get_total_row() {
        return array(
            '',
            html_writer::tag('b', get_string('total')),
            '',
            $this->out_of($this->restrictedtotalquizas, $this->totalquizas),
            $this->out_of($this->restrictedtotalqas, $this->totalqas),
        );
    }
}


/**
 * List the number of quiz attempts that were never upgraded from 1.4 -> 1.5.
 * @return int the number of such attempts.
 */
function local_qeupgradehelper_get_num_very_old_attempts() {
    global $DB;
    return $DB->count_records_sql('
            SELECT COUNT(1)
              FROM {quiz_attempts} quiza
             WHERE uniqueid IN (
                SELECT DISTINCT qst.attempt
                  FROM {question_states} qst
                  LEFT JOIN {question_sessions} qsess ON
                        qst.question = qsess.questionid AND qst.attempt = qsess.attemptid
                 WHERE qsess.id IS NULL)');
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
                    newtimemodified.time IS NULL OR oldtimemodified.time >= newtimemodified.time
                            THEN 1 ELSE 0 END) AS resettableattempts

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

/**
 * Get a question session id form a quiz attempt id and a question id.
 * @param int $attemptid a quiz attempt id.
 * @param int $questionid a question id.
 * @return int the question session id.
 */
function local_qeupgradehelper_get_session_id($attemptid, $questionid) {
    global $DB;
    $attempt = $DB->get_record('quiz_attempts', array('id' => $attemptid));
    if (!$attempt) {
        return null;
    }
    return $DB->get_field('question_sessions', 'id',
            array('attemptid' => $attempt->uniqueid, 'questionid' => $questionid));
}

/**
 * Identify the question session id of a question attempt matching certain
 * requirements.
 * @param integer $behaviour 0 = deferred feedback, 1 = interactive.
 * @param string $statehistory of states, last first. E.g. 620.
 * @param string $qtype question type.
 * @return integer question_session.id.
 */
function local_qeupgradehelper_find_test_case($behaviour, $statehistory, $qtype, $extratests) {
    global $DB;

    $params = array(
        'qtype' => $qtype,
        'statehistory' => $statehistory
    );

    if ($behaviour == 'deferredfeedback') {
        $extrawhere = '';
        $params['optionflags'] = 0;

    } else if ($behaviour == 'adaptive') {
        $extrawhere = 'AND penaltyscheme = :penaltyscheme';
        $params['optionflags'] = 0;
        $params['penaltyscheme'] = 0;

    } else {
        $extrawhere = 'AND penaltyscheme = :penaltyscheme';
        $params['optionflags'] = 0;
        $params['penaltyscheme'] = 1;
    }

    $possibleids = $DB->get_records_sql_menu('
            SELECT
                qsess.id,
                1

            FROM {question_sessions} qsess
            JOIN {question_states} qst ON qst.attempt = qsess.attemptid
                    AND qst.question = qsess.questionid
            JOIN {quiz_attempts} quiza ON quiza.uniqueid = qsess.attemptid
            JOIN {quiz} quiz ON quiz.id = quiza.quiz
            JOIN {question} q ON q.id = qsess.questionid

            WHERE q.qtype = :qtype
            AND quiz.optionflags = :optionflags
            ' . $extrawhere . '

            GROUP BY
                qsess.id

            HAVING SUM(
                (CASE WHEN qst.event = 10 THEN 1 ELSE qst.event END) *
                POWER(10, CAST(qst.seq_number AS NUMERIC(110,0)))
            ) = :statehistory' . $extratests, $params, 0, 100);

    if (!$possibleids) {
        return null;
    }

    return array_rand($possibleids);
}

/**
 * Grab all the data that upgrade will need for upgrading one
 * attempt at one question from the old DB.
 */
function local_qeupgradehelper_generate_unit_test($questionsessionid, $namesuffix) {
    global $DB;

    $qsession = $DB->get_record('question_sessions', array('id' => $questionsessionid));
    $attempt = $DB->get_record('quiz_attempts', array('uniqueid' => $qsession->attemptid));
    $quiz = $DB->get_record('quiz', array('id' => $attempt->quiz));
    $qstates = $DB->get_records('question_states',
            array('attempt' => $qsession->attemptid, 'question' => $qsession->questionid),
            'seq_number, id');

    $question = local_qeupgradehelper_load_question($qsession->questionid, $quiz->id);

    if (!local_qeupgradehelper_is_upgraded()) {
        if (!$quiz->optionflags) {
            $quiz->preferredbehaviour = 'deferredfeedback';
        } else if ($quiz->penaltyscheme) {
            $quiz->preferredbehaviour = 'adaptive';
        } else {
            $quiz->preferredbehaviour = 'adaptivenopenalty';
        }
        unset($quiz->optionflags);
        unset($quiz->penaltyscheme);

        $question->defaultmark = $question->defaultgrade;
        unset($question->defaultgrade);
    }

    $attempt->needsupgradetonewqe = 1;

    echo '<textarea readonly="readonly" rows="80" cols="120" >' . "
    public function test_{$question->qtype}_{$quiz->preferredbehaviour}_{$namesuffix}() {
";
    local_qeupgradehelper_display_convert_attempt_input($quiz, $attempt,
            $question, $qsession, $qstates);

    if ($question->qtype == 'random') {
        list($randombit, $realanswer) = explode('-', reset($qstates)->answer, 2);
        $newquestionid = substr($randombit, 6);
        $newquestion = local_qeupgradehelper_load_question($newquestionid);
        $newquestion->maxmark = $question->maxmark;

        echo local_qeupgradehelper_format_var('$realquestion', $newquestion);
        echo '        $this->loader->put_question_in_cache($realquestion);
';
    }

    echo '
        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(';
    echo "
            'behaviour' => '{$quiz->preferredbehaviour}',
            'questionid' => {$question->id},
            'variant' => 1,
            'maxmark' => {$question->maxmark},
            'minfraction' => 0,
            'flagged' => 0,
            'questionsummary' => '',
            'rightanswer' => '',
            'responsesummary' => '',
            'timemodified' => 0,
            'steps' => array(";
    foreach ($qstates as $state) {
        echo "
                {$state->seq_number} => (object) array(
                    'sequencenumber' => {$state->seq_number},
                    'state' => '',
                    'fraction' => null,
                    'timecreated' => {$state->timestamp},
                    'userid' => {$attempt->userid},
                    'data' => array(),
                ),";
    }
    echo '
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }
</textarea>';
}

function local_qeupgradehelper_format_var($name, $var) {
    $out = var_export($var, true);
    $out = str_replace('<', '&lt;', $out);
    $out = str_replace('ADOFetchObj::__set_state(array(', '(object) array(', $out);
    $out = str_replace('stdClass::__set_state(array(', '(object) array(', $out);
    $out = str_replace('array (', 'array(', $out);
    $out = preg_replace('/=> \n\s*/', '=> ', $out);
    $out = str_replace(')),', '),', $out);
    $out = str_replace('))', ')', $out);
    $out = preg_replace('/\n               (?! )/', "\n                                    ", $out);
    $out = preg_replace('/\n            (?! )/',    "\n                                ", $out);
    $out = preg_replace('/\n           (?! )/',     "\n                            ", $out);
    $out = preg_replace('/\n          (?! )/',      "\n                            ", $out);
    $out = preg_replace('/\n         (?! )/',       "\n                        ", $out);
    $out = preg_replace('/\n        (?! )/',        "\n                        ", $out);
    $out = preg_replace('/\n       (?! )/',         "\n                        ", $out);
    $out = preg_replace('/\n      (?! )/',          "\n                    ", $out);
    $out = preg_replace('/\n     (?! )/',           "\n                ", $out);
    $out = preg_replace('/\n    (?! )/',            "\n                ", $out);
    $out = preg_replace('/\n   (?! )/',             "\n            ", $out);
    $out = preg_replace('/\n  (?! )/',              "\n            ", $out);
    $out = preg_replace('/\n(?! )/',                "\n        ", $out);
    $out = preg_replace('/\bNULL\b/', 'null', $out);
    return "        $name = $out;\n";
}

function local_qeupgradehelper_display_convert_attempt_input($quiz, $attempt,
        $question, $qsession, $qstates) {
    echo local_qeupgradehelper_format_var('$quiz', $quiz);
    echo local_qeupgradehelper_format_var('$attempt', $attempt);
    echo local_qeupgradehelper_format_var('$question', $question);
    echo local_qeupgradehelper_format_var('$qsession', $qsession);
    echo local_qeupgradehelper_format_var('$qstates', $qstates);
}

function local_qeupgradehelper_load_question($questionid, $quizid) {
    global $CFG, $DB;

    $question = $DB->get_record_sql('
            SELECT q.*, qqi.grade AS maxmark
            FROM {question} q
            JOIN {quiz_question_instances} qqi ON qqi.question = q.id
            WHERE q.id = :questionid AND qqi.quiz = :quizid',
            array('questionid' => $questionid, 'quizid' => $quizid));

    if (local_qeupgradehelper_is_upgraded()) {
        require_once($CFG->dirroot . '/question/engine/bank.php');
        $qtype = question_bank::get_qtype($question->qtype, false);
    } else {
        global $QTYPES;
        if (!array_key_exists($question->qtype, $QTYPES)) {
            $question->qtype = 'missingtype';
            $question->questiontext = '<p>' . get_string('warningmissingtype', 'quiz') . '</p>' . $question->questiontext;
        }
        $qtype = $QTYPES[$question->qtype];
    }

    $qtype->get_question_options($question);

    return $question;
}

function local_qeupgradehelper_get_quiz_for_upgrade() {
    global $DB;

    return $DB->get_record_sql("SELECT quiz.id
            FROM {quiz_attempts} quiza
            JOIN {quiz} quiz ON quiz.id = quiza.quiz
            JOIN {course} c ON c.id = quiz.course
            WHERE quiza.preview = 0 AND quiza.needsupgradetonewqe = 1
            GROUP BY quiz.id, quiz.name, c.shortname, c.id
            ORDER BY quiza.timemodified DESC", array(), IGNORE_MULTIPLE);
}
