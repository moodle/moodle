<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'qtype_preg', language 'en_us', version '4.1'.
 *
 * @package     qtype_preg
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['langselect_help'] = 'For next lexeme hint you should choose a language, which is used to break answers down to lexemes. Each language has it own rules for lexemes. Languages are defined using \'Formal languages block\'';
$string['pluginnamesummary'] = 'Enter a string response from student that can be matched against several regular expressions. Shows to the student the correct part of his response. Using behaviors with multiple tries can give a hint by telling a next correct character or lexem.<br/>You can use it without knowing regular expression to get hinting by using the \'Moodle shortanswer\' notation.';
$string['unrecognized_pqh_node_error'] = 'Unrecognized character after (? or (?-';
$string['unrecognized_pqlt_node_error'] = 'Unrecognized character after (? or (?-';
$string['unrecognized_pqp_node_error'] = 'Unrecognized character after (? or (?-';
$string['usecharhint_help'] = 'In behaviors which allow multiple tries (e.g. adaptive or interactive) show students the \'Hint next character\' button that allows to get a one-character hint with applying the \'Hint next character penalty\'. Not all matching engines support hinting.';
$string['usehint_help'] = 'In behaviors which allow multiple tries (e.g. adaptive or interactive) show students the \'Hint next character\' button that allows to get a one-character hint with applying the \'Hint next character penalty\'. Not all matching engines support hinting.';
$string['uselexemhint_help'] = '<p>In behaviors which allow multiple tries (e.g. adaptive or interactive) show students the \'Hint next word\' button that allows to get a hint either completing current lexem or showing next one if lexem is complete with applying the \'Hint next lexem penalty\'. Not all matching engines support hinting.</p><p><b>Lexem</b> is an atomic part of the language: a word, number, punctuation mark, operator etc.</p>';
