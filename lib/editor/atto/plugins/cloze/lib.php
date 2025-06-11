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
 * @copyright  2016 onward Daniel Thies <dethies@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Get the list of strings for this plugin.
 */
function atto_cloze_strings_for_js() {
    global $PAGE;

    $PAGE->requires->strings_for_js([ 'pluginname' ], 'atto_cloze' );
    $PAGE->requires->strings_for_js([ 'privacy:request:historyactioninsert' ], 'core_grades' );
    $PAGE->requires->strings_for_js([ 'answer', 'chooseqtypetoadd', 'defaultmark', 'feedback', 'incorrect' ], 'question' );
    $PAGE->requires->strings_for_js([ 'multichoice', 'numerical', 'shortanswer' ], 'mod_quiz' );
    $PAGE->requires->strings_for_js([ 'addmoreanswerblanks', 'tolerance' ], 'qtype_calculated' );
    $PAGE->requires->strings_for_js([ 'add', 'cancel', 'delete',
            'duplicate', 'down', 'gradenoun', 'previous', 'up' ], 'core' );
}

/**
 * Set params for this plugin.
 *
 * @return array
 */
function atto_cloze_params_for_js() {
    global $CFG;

    $singleno = ['option' => get_string('answersingleno', 'qtype_multichoice')];
    $singleyes = ['option' => get_string('answersingleyes', 'qtype_multichoice')];
    $selectinline = ['option' => get_string('layoutselectinline', 'qtype_multianswer')];
    $horizontal = ['option' => get_string('layouthorizontal', 'qtype_multianswer')];
    $vertical = ['option' => get_string('layoutvertical', 'qtype_multianswer')];
    $qtypes = [
        ['type' => 'MULTICHOICE', 'name' => get_string('multichoice', 'mod_quiz'),
            'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
            'options' => [$selectinline, $singleyes],
        ],
        ['type' => 'MULTICHOICE_H', 'name' => get_string('multichoice', 'mod_quiz'),
            'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
            'options' => [$horizontal, $singleyes],
        ],
        ['type' => 'MULTICHOICE_V', 'name' => get_string('multichoice', 'mod_quiz'),
            'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
            'options' => [$vertical, $singleyes],
        ],
    ];
    // Check whether shuffled multichoice is supported yet.
    if ($CFG->version >= 2015111604) {
        $shuffle = ['option' => get_string('shufflewithin', 'mod_quiz')];
        $qtypes = array_merge($qtypes, [

            ['type' => 'MULTICHOICE_S', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => [$selectinline, $shuffle, $singleyes],
            ],
            ['type' => 'MULTICHOICE_HS', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => [$horizontal, $shuffle, $singleyes],
            ],
            ['type' => 'MULTICHOICE_VS', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => [$vertical, $shuffle, $singleyes],
            ],
        ]);
    }

    // Check whether shuffled multichoice is supported yet.
    if ($CFG->version >= 2016080400) {
        $multihorizontal = ['option' => get_string('layoutmultiple_horizontal', 'qtype_multianswer')];
        $multivertical = ['option' => get_string('layoutmultiple_vertical', 'qtype_multianswer')];
        $qtypes = array_merge($qtypes, [
            ['type' => 'MULTIRESPONSE', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => [$multivertical, $singleno],
            ],
            ['type' => 'MULTIRESPONSE_H', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => [$multihorizontal, $singleno],
            ],
            ['type' => 'MULTIRESPONSE_S', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => [$multivertical, $shuffle, $singleno],
            ],
            ['type' => 'MULTIRESPONSE_HS', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => [$multihorizontal, $shuffle, $singleno],
            ],
        ]);
    }
    $qtypes = array_merge($qtypes, [
        ['type' => 'NUMERICAL', 'name' => get_string('numerical', 'mod_quiz'),
        'summary' => get_string('pluginnamesummary', 'qtype_numerical')],
        ['type' => 'SHORTANSWER', 'name' => get_string('shortanswer', 'mod_quiz'),
        'summary' => get_string('pluginnamesummary', 'qtype_shortanswer'),
        'options' => ['option' => get_string('caseno', 'mod_quiz')]],
        ['type' => 'SHORTANSWER_C', 'name' => get_string('shortanswer', 'mod_quiz'),
        'summary' => get_string('pluginnamesummary', 'qtype_shortanswer'),
        'options' => ['option' => get_string('caseyes', 'mod_quiz')]],
    ]);
    return ['questiontypes' => $qtypes];
}
