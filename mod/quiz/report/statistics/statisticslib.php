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
 * Common functions for the quiz statistics report.
 *
 * @package    quiz_statistics
 * @copyright  2013 The Open University
 * @author     James Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function quiz_statistics_attempts_sql($quizid, $currentgroup, $groupstudents,
                                      $allattempts = true, $includeungraded = false) {
    global $DB;

    $fromqa = '{quiz_attempts} quiza ';

    $whereqa = 'quiza.quiz = :quizid AND quiza.preview = 0 AND quiza.state = :quizstatefinished';
    $qaparams = array('quizid' => (int)$quizid, 'quizstatefinished' => quiz_attempt::FINISHED);

    if (!empty($currentgroup) && $groupstudents) {
        list($grpsql, $grpparams) = $DB->get_in_or_equal(array_keys($groupstudents),
                                                         SQL_PARAMS_NAMED, 'u');
        $whereqa .= " AND quiza.userid $grpsql";
        $qaparams += $grpparams;
    }

    if (!$allattempts) {
        $whereqa .= ' AND quiza.attempt = 1';
    }

    if (!$includeungraded) {
        $whereqa .= ' AND quiza.sumgrades IS NOT NULL';
    }

    return array($fromqa, $whereqa, $qaparams);
}

/**
 * Return a {@link qubaid_condition} from the values returned by {@link quiz_statistics_attempts_sql}.
 *
 * @param int     $quizid
 * @param int     $currentgroup
 * @param array   $groupstudents
 * @param bool    $allattempts
 * @param bool    $includeungraded
 * @return        \qubaid_join
 */
function quiz_statistics_qubaids_condition($quizid, $currentgroup, $groupstudents,
                                           $allattempts = true, $includeungraded = false) {
    list($fromqa, $whereqa, $qaparams) = quiz_statistics_attempts_sql($quizid, $currentgroup,
                                                                      $groupstudents, $allattempts, $includeungraded);
    return new qubaid_join($fromqa, 'quiza.uniqueid', $whereqa, $qaparams);
}

/**
 * This helper function returns a sequence of colours each time it is called.
 * Used for choosing colours for graph data series.
 * @return string colour name.
 */
function quiz_statistics_graph_get_new_colour() {
    static $colourindex = -1;
    $colours = array('red', 'green', 'yellow', 'orange', 'purple', 'black',
        'maroon', 'blue', 'ltgreen', 'navy', 'ltred', 'ltltgreen', 'ltltorange',
        'olive', 'gray', 'ltltred', 'ltorange', 'lime', 'ltblue', 'ltltblue');

    $colourindex = ($colourindex + 1) % count($colours);

    return $colours[$colourindex];
}
