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
 * This script is a bit of a hack. It is like makeupgradetest.php but has been
 * hacked so you can extract test cases from a different databse.
 *
 * To make this work, you need to fill in the details below, and add
 *
 * if (defined('NASTY_HACK_IGNORE_CONFIGPHP')) {
 *     return;
 * }
 *
 * to the very top of your config.php file.
 *
 * @package moodlecore
 * @subpackage questionengine
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


error_reporting(E_ALL);
ini_set('display_errors', 1);
define('NASTY_HACK_IGNORE_CONFIGPHP', true);

// Clone config.php to point at the learnacct DB read-only.
unset($CFG);  // Ignore this line
$CFG = new stdClass();

$CFG->debug = 6143; 
$CFG->debugdisplay = 1;

// The following block points this site at learnacct database, read-only.
$CFG->dbtype    = 'postgres7';
$CFG->dbhost    = ''; // TODO to use this script, complete this section
$CFG->dbname    = ''; // with details of the database you want to
$CFG->dbuser    = ''; // connect to.
$CFG->dbpass    = '';
$CFG->prefix    = '';

$CFG->wwwroot   = ''; // TODO to use this script, complete this section
$CFG->dirroot   = ''; // with data copied from this Moodle's config.php
$CFG->dataroot  = '';
$CFG->directorypermissions = 02777;

$CFG->admin = 'admin';

require_once($CFG->dirroot . '/local/ouflags/ouflags.class.php');
$OUFLAGS = new ouflags('vle','dev');

require_once($CFG->dirroot . '/lib/setup.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/engine/upgradefromoldqe/upgrade.php');

$CFG->querylog = '';

// =============================================================
// Settings form
class grab_settings_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $behaviour = array(
            0 => 'Deferred feedback',
            1 => 'Interactive',
        );

        $qtypes = array(
            'ddwtos' => 'Drag-drop',
            'description' => 'Description',
            'essay' => 'Essay',
            'match' => 'Matching',
            'multichoice' => 'Multiple choice',
            'numerical' => 'Numerical',
            'opaque' => 'OpenMark',
            'oumultiresponse' => 'OU multiple-response',
            'random' => 'Random',
            'shortanswer' => 'Short-answer',
            'truefalse' => 'True/false',
        );

        $mform->addElement('header', 'h1', 'Either extract a specific question_session');
        $mform->addElement('text', 'qsid', 'Question session id', array('size' => '10'));
        $mform->addElement('header', 'h2', 'Or find and extract an example by type');
        $mform->addElement('select', 'behaviour', 'Behaviour', $behaviour);
        $mform->addElement('text', 'statehistory', 'State history', array('size' => '10'));
        $mform->addElement('select', 'qtype', 'Question type', $qtypes);
        $mform->addElement('text', 'extratests', 'Extra conditions', array('size' => '50'));
        $this->add_action_buttons(false, 'Create test case');
    }
}

class grabber_question_engine_attempt_upgrader extends question_engine_attempt_upgrader {
    public function __construct() {
        $this->questionloader = new question_engine_upgrade_question_loader(null);
    }
}

if ($sesskey = optional_param('sesskey', '', PARAM_RAW)) {
    $USER->sesskey = $sesskey;
}

print_header('Question engine upgrade test case extractor');

$mform = new grab_settings_form($CFG->wwwroot . '/question/engine/upgradefromoldqe/grabexample.php', null, 'get');
if ($fromform = $mform->get_data()) {
    if (!empty($fromform->qsid)) {
        generate_unit_test($fromform->qsid, 'qsession' . $fromform->qsid);
    } else {
        notify('Searching ...', 'notifysuccess');
        flush();
        $qsid = find_test_case($fromform->behaviour, $fromform->statehistory,
                $fromform->qtype, $fromform->extratests);
        if ($qsid) {
            generate_unit_test($qsid, 'history' . $fromform->statehistory);
        } else {
            notify('No suitable attempts found.');
        }
    }
}

$mform->display();
print_footer('empty');

/**
 * Identify the question session id of a question attempt matching certain
 * requirements.
 * @param integer $behaviour 0 = deferred feedback, 1 = interactive.
 * @param string $statehistory of states, last first. E.g. 620.
 * @param string $qtype question type.
 * @return integer question_session.id.
 */
function find_test_case($behaviour, $statehistory, $qtype, $extratests) {
    global $CFG;
    $possibleids = get_records_sql_menu("
            SELECT
                qsess.id,
                1

            FROM {$CFG->prefix}question_sessions qsess
            JOIN {$CFG->prefix}question_states qst ON qst.attempt = qsess.attemptid
                    AND qst.question = qsess.questionid
            JOIN {$CFG->prefix}quiz_attempts quiza ON quiza.uniqueid = qsess.attemptid
            JOIN {$CFG->prefix}quiz quiz ON quiz.id = quiza.quiz
            JOIN {$CFG->prefix}question q ON q.id = qsess.questionid

            WHERE q.qtype = '{$qtype}'
            AND quiz.optionflags = {$behaviour}

            GROUP BY
                qsess.id

            HAVING SUM(
                (CASE WHEN qst.event = 10 THEN 1 ELSE qst.event END) *
                POWER(10, CAST(qst.seq_number AS NUMERIC(110,0)))
            ) = {$statehistory}
            {$extratests}", 0, 100);

    if (!$possibleids) {
        return null;
    }

    return array_rand($possibleids);
}

/**
 * Grab all the data that upgrade will need for upgrading one
 * attempt at one question from the old DB.
 */
function generate_unit_test($questionsessionid, $namesuffix) {
    $qsession = get_record('question_sessions', 'id', $questionsessionid);
    $attempt = get_record('quiz_attempts', 'uniqueid', $qsession->attemptid);
    $quiz = get_record('quiz', 'id', $attempt->quiz);
    $qstates = get_records_select('question_states',
            "attempt = {$qsession->attemptid} AND question = {$qsession->questionid}",
            'seq_number, id');

    $upgrader = new grabber_question_engine_attempt_upgrader();

    $question = $upgrader->load_question($qsession->questionid, $quiz->id);

    if ($quiz->optionflags) {
        $quiz->preferredbehaviour = 'interactive';
    } else {
        $quiz->preferredbehaviour = 'deferredfeedback';
    }
    echo "<pre>
    public function test_{$question->qtype}_{$quiz->preferredbehaviour}_{$namesuffix}() {
";
    $upgrader->display_convert_attempt_input($quiz, $attempt,
            $question, $qsession, $qstates);

    if ($question->qtype == 'random') {
        list($randombit, $realanswer) = explode('-', reset($qstates)->answer, 2);
        $newquestionid = substr($randombit, 6);
        $newquestion = $upgrader->load_question($newquestionid);
        $newquestion->maxmark = $question->maxmark;

        echo $upgrader->format_var('$realquestion', $newquestion);
        echo '        $this->loader->put_question_in_cache($realquestion);
';
    }

    echo '
        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(';
    echo "
            'behaviour' => '{$quiz->preferredbehaviour}',
            'questionid' => {$question->id},
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

        $this->assertEqual($expectedqa, $qa);
    }
</pre>';
}

