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
 * Strings for component 'qtype_randomsamatch', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    qtype
 * @subpackage randomsamatch
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['insufficientoptions'] = 'Insufficient selection options are available for this question, therefore it is not available in  this quiz. Please inform your teacher.';
$string['nosaincategory'] = 'There are no short answer questions in the category that you chose \'{$a->catname}\'. Choose a different category, make some questions in this category.';
$string['notenoughsaincategory'] = 'There is/are only {$a->nosaquestions} short answer questions in the category that you chose \'{$a->catname}\'. Choose a different category, make some more questions in this category or reduce the amount of questions you\'ve selected.';
$string['pluginname'] = 'Random short-answer matching';
$string['pluginname_help'] = 'From the student perspective, this looks just like a matching question. The difference is that the list of names or statements (questions) for matching are drawn randomly from the short answer questions in the current category. There should be sufficient unused short answer questions in the category, otherwise an error message will be displayed.';
$string['pluginname_link'] = 'question/type/randomsamatch';
$string['pluginnameadding'] = 'Adding a Random short-answer matching question';
$string['pluginnameediting'] = 'Editing a Random short-answer matching question';
$string['pluginnamesummary'] = 'Like a Matching question, but created randomly from the short answer questions in a particular category.';
$string['privacy:metadata'] = 'The Random short-answer matching question type does not store any personal data.';
$string['randomsamatchnumber'] = 'Number of questions to select';
$string['randomsamatch'] = 'Random short-answer matching';
$string['randomsamatchintro'] = 'For each of the following questions, select the matching answer from the menu.';
$string['subcats'] = 'Include subcategories';
$string['subcats_help'] = 'If checked, questions will be choosen from subcategories too.';
