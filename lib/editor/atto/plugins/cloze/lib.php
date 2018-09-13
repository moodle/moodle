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
 * Atto text editor integration version file.
 *
 * @package    atto_cloze
 * @copyright  2016 onward Daniel Thies <dthies@ccal.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Get the list of strings for this plugin.
 */
function atto_cloze_strings_for_js() {
    global $PAGE;

    $PAGE->requires->strings_for_js(array( 'pluginname' ), 'atto_cloze' );
    $PAGE->requires->strings_for_js(array( 'common:insert' ), 'editor_tinymce' );
    $PAGE->requires->strings_for_js(array( 'answer', 'chooseqtypetoadd', 'defaultmark', 'feedback', 'incorrect' ), 'question' );
    $PAGE->requires->strings_for_js(array( 'multichoice', 'numerical', 'shortanswer' ), 'mod_quiz' );
    $PAGE->requires->strings_for_js(array( 'addmoreanswerblanks', 'tolerance' ), 'qtype_calculated' );
    $PAGE->requires->strings_for_js(array( 'add', 'cancel', 'delete',
            'duplicate', 'down', 'grade', 'previous', 'up' ), 'core' );
}

/**
 * Set params for this plugin.
 *
 * @return array
 */
function atto_cloze_params_for_js() {
    global $CFG;

    $singleno = array('option' => get_string('answersingleno', 'qtype_multichoice'));
    $singleyes = array('option' => get_string('answersingleyes', 'qtype_multichoice'));
    $selectinline = array('option' => get_string('layoutselectinline', 'qtype_multianswer'));
    $horizontal = array('option' => get_string('layouthorizontal', 'qtype_multianswer'));
    $vertical = array('option' => get_string('layoutvertical', 'qtype_multianswer'));
    $qtypes = array(
        array('type' => 'MULTICHOICE', 'name' => get_string('multichoice', 'mod_quiz'),
            'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
            'options' => array($selectinline, $singleyes)
        ),
        array('type' => 'MULTICHOICE_H', 'name' => get_string('multichoice', 'mod_quiz'),
            'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
            'options' => array($horizontal, $singleyes)
        ),
        array('type' => 'MULTICHOICE_V', 'name' => get_string('multichoice', 'mod_quiz'),
            'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
            'options' => array($vertical, $singleyes)
        ),
    );
    // Check whether shuffled multichoice is supported yet.
    if ($CFG->version >= 2015111604) {
        $shuffle = array('option' => get_string('shufflewithin', 'mod_quiz'));
        $qtypes = array_merge($qtypes, array(

            array('type' => 'MULTICHOICE_S', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => array($selectinline, $shuffle, $singleyes)
            ),
            array('type' => 'MULTICHOICE_HS', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => array($horizontal, $shuffle, $singleyes)
            ),
            array('type' => 'MULTICHOICE_VS', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => array($vertical, $shuffle, $singleyes)
            ),
        ));
    }

    // Check whether shuffled multichoice is supported yet.
    if ($CFG->version >= 2016080400) {
        $multihorizontal = array('option' => get_string('layoutmultiple_horizontal', 'qtype_multianswer'));
        $multivertical = array('option' => get_string('layoutmultiple_vertical', 'qtype_multianswer'));
        $qtypes = array_merge($qtypes, array(
            array('type' => 'MULTIRESPONSE', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => array($multivertical, $singleno)
            ),
            array('type' => 'MULTIRESPONSE_H', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => array($multihorizontal, $singleno)
            ),
            array('type' => 'MULTIRESPONSE_S', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => array($multivertical, $shuffle, $singleno)
            ),
            array('type' => 'MULTIRESPONSE_HS', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => array($multihorizontal, $shuffle, $singleno)
            ),
        ));
    }
    $qtypes = array_merge($qtypes, array(
        array('type' => 'NUMERICAL', 'name' => get_string('numerical', 'mod_quiz'),
        'summary' => get_string('pluginnamesummary', 'qtype_numerical')),
        array('type' => 'SHORTANSWER', 'name' => get_string('shortanswer', 'mod_quiz'),
        'summary' => get_string('pluginnamesummary', 'qtype_shortanswer'),
        'options' => array('option' => get_string('caseno', 'mod_quiz'))),
        array('type' => 'SHORTANSWER_C', 'name' => get_string('shortanswer', 'mod_quiz'),
        'summary' => get_string('pluginnamesummary', 'qtype_shortanswer'),
        'options' => array('option' => get_string('caseyes', 'mod_quiz'))),
    ));
    return array('questiontypes' => $qtypes);
}
