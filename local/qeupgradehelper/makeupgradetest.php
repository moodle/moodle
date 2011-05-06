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
 * This is a simple script to facilitate testing. You need to run it before
 * you go to /admin/ to upgrade your database. It extracts all the data for
 * one particular attempt at one question, in a form that makes it easy to
 * write a unit test for upgrade logic for that particular case.
 *
 * (The theory is that if the upgrade dies with an error, you can restore the
 * database from backup, and then use this script to extract the problem case
 * as a unit test. Then you can fix that unit tests. Then you can repeat the upgrade.)
 *
 * @package moodlecore
 * @subpackage questionengine
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->libdir . '/formslib.php');

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

    $question = load_question($qsession->questionid, $quiz->id);

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

function format_var($name, $var) {
    $out = var_export($var, true);
    $out = str_replace('<', '&lt;', $out);
    $out = str_replace('ADOFetchObj::__set_state(array(', '(object) array(', $out);
    $out = str_replace('stdClass::__set_state(array(', '(object) array(', $out);
    $out = str_replace('array (', 'array(', $out);
    $out = preg_replace('/=> \n\s*/', '=> ', $out);
    $out = str_replace(')),', '),', $out);
    $out = str_replace('))', ')', $out);
    $out = preg_replace('/\n         (?! )/', "\n                        ", $out);
    $out = preg_replace('/\n       (?! )/',   "\n                        ", $out);
    $out = preg_replace('/\n      (?! )/',    "\n                    ", $out);
    $out = preg_replace('/\n     (?! )/',     "\n                ", $out);
    $out = preg_replace('/\n    (?! )/',      "\n                ", $out);
    $out = preg_replace('/\n   (?! )/',       "\n            ", $out);
    $out = preg_replace('/\n  (?! )/',        "\n            ", $out);
    $out = preg_replace('/\n(?! )/',          "\n        ", $out);
    return "        $name = $out;\n";
}

function display_convert_attempt_input($quiz, $attempt, $question, $qsession, $qstates) {
    echo format_var('$quiz', $quiz);
    echo format_var('$attempt', $attempt);
    echo format_var('$question', $question);
    echo format_var('$qsession', $qsession);
    echo format_var('$qstates', $qstates);
}

function load_question($questionid, $quizid) {
    global $CFG, $QTYPES;

    $question = get_record_sql("
        SELECT q.*, qqi.grade AS maxmark
        FROM {$CFG->prefix}question q
        JOIN {$CFG->prefix}quiz_question_instances qqi ON qqi.question = q.id
        WHERE q.id = $questionid AND qqi.quiz = $quizid");

    if (!array_key_exists($question->qtype, $QTYPES)) {
        $question->qtype = 'missingtype';
        $question->questiontext = '<p>' . get_string('warningmissingtype', 'quiz') . '</p>' . $question->questiontext;
    }

    $QTYPES[$question->qtype]->get_question_options($question);

    return$question;
}
