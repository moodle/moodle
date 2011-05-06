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
 * This script is a bit of a hack. It connects to another database and tries to
 * run most of the question engine upgrade. That is, it loads the old data, and
 * tries to convert it to the new structure, but it does not try to output it
 * at all.
 *
 * The idea is that this should find most of the logic errors, since the code to
 * save the new data to the DB is quite simple.
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
 * @copyright 2010 The Open University
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
$CFG->rcache = false;
raise_memory_limit('1024M');

// =============================================================

class pretend_question_engine_attempt_upgrader extends question_engine_attempt_upgrader {
    public $fromquiz = 0;
    public $toquiz = 1000000;
    public $qsdone = 0;

    protected function get_quiz_ids() {
        return get_records_select_menu('quiz',
                "id >= {$this->fromquiz} AND id < {$this->toquiz}", 'id', 'id,1');
    }

    public function get_attemtps_where($quizid) {
        return "quiz = {$quizid} AND preview = 0";
    }

    protected function set_quba_preferred_behaviour($qubaid, $preferredbehaviour) {
        return true;
    }

    protected function set_quiz_attempt_layout($qubaid, $layout) {
        return true;
    }

    protected function delete_quiz_attempt($qubaid) {
        return true;
    }

    protected function insert_record($table, $record, $saveid = true) {
        if ($table == 'question_attempts') {
            if ($this->toquiz - $this->fromquiz <= 10) {
                echo "saving qa from {$record->_fromqsession} ";
            } else {
                echo 'S';
            }
        }
        $this->escape_fields($record);
        if ($table == 'question_attempt_steps' && is_null($record->sequencenumber)) {
            notify('Null sequencenumber found.');
        }
        if ($saveid) {
            $record->id = 666;
        }
        return true;
    }

    protected function convert_quiz_attempt($quiz, $attempt, $questionsessionsrs, $questionsstatesrs) {
        if (empty($quiz->preferredbehaviour)) {
            if ($quiz->optionflags == 0) {
                $quiz->preferredbehaviour = 'deferredfeedback';
            } else {
                $quiz->preferredbehaviour = 'interactive';
            }
        }
        return parent::convert_quiz_attempt($quiz, $attempt, $questionsessionsrs, $questionsstatesrs);
    }

    public function convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates) {
        if ($this->toquiz - $this->fromquiz <= 10) {
            if ($this->qsdone % 10 == 0) {
                echo '<br />';
            }
            echo "qs {$qsession->id} ";
        } else {
            if ($this->qsdone % 100 == 0) {
                echo '<br />';
            }
            echo "C";
        }
        $qa = parent::convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);
        $qa->_fromqsession = $qsession->id;
        $this->qsdone++;
        return $qa;
    }

    public function supply_missing_question_attempt($quiz, $attempt, $question) {
        if ($this->toquiz - $this->fromquiz <= 10) {
            if ($this->qsdone % 10 == 0) {
                echo '<br />';
            }
            echo "missing {$question->id} ";
        } else {
            if ($this->qsdone % 100 == 0) {
                echo '<br />';
            }
            echo "M";
        }
        $qa = parent::supply_missing_question_attempt($quiz, $attempt, $question);
        $qa->_fromqsession = 'missing';
        return $qa;
    }

    protected function print_progress($done, $outof, $quizid) {
        echo "</div>\n\n<h2>Quiz {$done}/{$outof} ({$quizid})</h2>\n\n<div>";
        gc_collect_cycles();
        echo '<p>Current memory usage: ' . memory_get_usage() . '/' . memory_get_peak_usage() . '</p>';
    }
}

// =============================================================

$fromquiz = required_param('from', PARAM_INT);
$toquiz = optional_param('to', $fromquiz + 1, PARAM_INT);

print_header('Question engine upgrade tester');
echo "\n\n<h1>Starting pretend upgrade of database '$CFG->dbhost', prefix '$CFG->prefix' on host '$CFG->dbhost'.</h1>\n\n<div>";

$timestart = time();
$qsconverted = do_pretend_upgrade($fromquiz, $toquiz);
$totaltime = time() - $timestart;
echo "</div>\n\n<p>{$qsconverted} question sessions converted in {$totaltime} seconds.</p>\n\n";

if ($qsconverted > 0) {
    echo "<p>Estimate for 5 million: " . format_time(5000000 / $qsconverted * $totaltime) . "</p>\n\n";
}

$number = $toquiz - $fromquiz;
echo "</div>\n\n<p>";
if (record_exists_select('quiz', "id < {$fromquiz}")) {
    $newfrom = $fromquiz - $number;
    echo "<a href='pretendupgrade.php?from={$newfrom}&amp;to={$fromquiz}'>Previous {$number} quizzes</a> ";
}
if (record_exists_select('quiz', "id >= {$toquiz}")) {
    $newto = $toquiz + $number;
    echo "<a href='pretendupgrade.php?from={$toquiz}&amp;to={$newto}'>Next {$number} quizzes</a> ";
}
echo "<p>\n\n";

print_footer('empty');

function do_pretend_upgrade($fromquiz, $toquiz) {
    $upgrader = new pretend_question_engine_attempt_upgrader();
    $upgrader->fromquiz = $fromquiz;
    $upgrader->toquiz = $toquiz;

//    xhprof_enable(XHPROF_FLAGS_MEMORY + XHPROF_FLAGS_NO_BUILTINS);

    $upgrader->convert_all_quiz_attempts();

//    $xhprof_data = xhprof_disable();
//    print_object($xhprof_data);

    return $upgrader->qsdone;
}
