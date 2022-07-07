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
 * Strings for component 'qtype_calculatedmulti', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    qtype
 * @subpackage calculatedmulti
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['answeroptions'] = 'Choice options';
$string['answeroptions_help'] = 'The suggested choice formula is ...<strong>{={x}+..}</strong>...';
$string['pluginname'] = 'Calculated multichoice';
$string['pluginname_help'] = 'Calculated multichoice questions are like multichoice questions which in their choice elements can be included numerical formula results using wildcards in curly brackets that are substituted with individual values when the quiz is taken. For example, if the question "What is the area of a rectangle of length {l} and width {w}?" one of the choice is {={l}*{w}} (where * denotes multiplication). ';
$string['pluginname_link'] = 'question/type/calculatedmulti';
$string['pluginnameadding'] = 'Adding a Calculated multichoice question';
$string['pluginnameediting'] = 'Editing a Calculated multichoice question';
$string['pluginnamesummary'] = 'Calculated multichoice questions are like multichoice questions which choice elements can include formula results from numeric values that are selected randomly from a set when the quiz is taken.';
$string['privacy:metadata'] = 'The Calculated multichoice question type plugin does not store any personal data.';
