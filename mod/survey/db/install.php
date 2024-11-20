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
 * Survey module installation.
 *
 * @package    mod_survey
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Perform the post-install procedures.
 */
function xmldb_survey_install() {
    global $DB;

    // Disable the survey activity module on new installs by default.
    $DB->set_field('modules', 'visible', 0, ['name' => 'survey']);

    // Insert survey data.
    $records = array(
        array_combine(array('course', 'template', 'days', 'timecreated', 'timemodified', 'name', 'intro', 'questions'), array(0, 0, 0, 985017600, 985017600, 'collesaname', 'collesaintro', '25,26,27,28,29,30,43,44')),
        array_combine(array('course', 'template', 'days', 'timecreated', 'timemodified', 'name', 'intro', 'questions'), array(0, 0, 0, 985017600, 985017600, 'collespname', 'collespintro', '31,32,33,34,35,36,43,44')),
        array_combine(array('course', 'template', 'days', 'timecreated', 'timemodified', 'name', 'intro', 'questions'), array(0, 0, 0, 985017600, 985017600, 'collesapname', 'collesapintro', '37,38,39,40,41,42,43,44')),
        array_combine(array('course', 'template', 'days', 'timecreated', 'timemodified', 'name', 'intro', 'questions'), array(0, 0, 0, 985017600, 985017600, 'attlsname', 'attlsintro', '65,67,68')),
        array_combine(array('course', 'template', 'days', 'timecreated', 'timemodified', 'name', 'intro', 'questions'), array(0, 0, 0, 985017600, 985017600, 'ciqname', 'ciqintro', '69,70,71,72,73')),
    );
    foreach ($records as $record) {
        $DB->insert_record('survey', $record, false);
    }

    $records = array(
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles1', 'colles1short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles2', 'colles2short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles3', 'colles3short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles4', 'colles4short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles5', 'colles5short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles6', 'colles6short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles7', 'colles7short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles8', 'colles8short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles9', 'colles9short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles10', 'colles10short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles11', 'colles11short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles12', 'colles12short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles13', 'colles13short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles14', 'colles14short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles15', 'colles15short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles16', 'colles16short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles17', 'colles17short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles18', 'colles18short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles19', 'colles19short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles20', 'colles20short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles21', 'colles21short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles22', 'colles22short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles23', 'colles23short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('colles24', 'colles24short', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm1', 'collesm1short', '1,2,3,4', 'collesmintro', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm2', 'collesm2short', '5,6,7,8', 'collesmintro', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm3', 'collesm3short', '9,10,11,12', 'collesmintro', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm4', 'collesm4short', '13,14,15,16', 'collesmintro', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm5', 'collesm5short', '17,18,19,20', 'collesmintro', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm6', 'collesm6short', '21,22,23,24', 'collesmintro', 1, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm1', 'collesm1short', '1,2,3,4', 'collesmintro', 2, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm2', 'collesm2short', '5,6,7,8', 'collesmintro', 2, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm3', 'collesm3short', '9,10,11,12', 'collesmintro', 2, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm4', 'collesm4short', '13,14,15,16', 'collesmintro', 2, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm5', 'collesm5short', '17,18,19,20', 'collesmintro', 2, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm6', 'collesm6short', '21,22,23,24', 'collesmintro', 2, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm1', 'collesm1short', '1,2,3,4', 'collesmintro', 3, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm2', 'collesm2short', '5,6,7,8', 'collesmintro', 3, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm3', 'collesm3short', '9,10,11,12', 'collesmintro', 3, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm4', 'collesm4short', '13,14,15,16', 'collesmintro', 3, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm5', 'collesm5short', '17,18,19,20', 'collesmintro', 3, 'scaletimes5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('collesm6', 'collesm6short', '21,22,23,24', 'collesmintro', 3, 'scaletimes5')),
        array_combine(array('text', 'type', 'options'), array('howlong', 1, 'howlongoptions')),
        array_combine(array('text', 'type'), array('othercomments', 0)),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls1', 'attls1short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls2', 'attls2short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls3', 'attls3short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls4', 'attls4short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls5', 'attls5short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls6', 'attls6short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls7', 'attls7short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls8', 'attls8short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls9', 'attls9short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls10', 'attls10short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls11', 'attls11short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls12', 'attls12short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls13', 'attls13short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls14', 'attls14short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls15', 'attls15short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls16', 'attls16short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls17', 'attls17short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls18', 'attls18short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls19', 'attls19short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type', 'options'), array('attls20', 'attls20short', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('attlsm1', 'attlsm1', '45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64', 'attlsmintro', 1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('-', '-', '-', '-', 0, '-')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('attlsm2', 'attlsm2', '63,62,59,57,55,49,52,50,48,47', 'attlsmintro', -1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'multi', 'intro', 'type', 'options'), array('attlsm3', 'attlsm3', '46,54,45,51,60,53,56,58,61,64', 'attlsmintro', -1, 'scaleagree5')),
        array_combine(array('text', 'shorttext', 'type'), array('ciq1', 'ciq1short', 0)),
        array_combine(array('text', 'shorttext', 'type'), array('ciq2', 'ciq2short', 0)),
        array_combine(array('text', 'shorttext', 'type'), array('ciq3', 'ciq3short', 0)),
        array_combine(array('text', 'shorttext', 'type'), array('ciq4', 'ciq4short', 0)),
        array_combine(array('text', 'shorttext', 'type'), array('ciq5', 'ciq5short', 0)),
    );
    foreach ($records as $record) {
        $DB->insert_record('survey_questions', $record, false);
    }

}
