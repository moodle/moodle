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
 * Strings for component 'qtype_calculatedsimple', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   qtype_calculatedsimple
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addingcalculatedsimple'] = 'Adding a Simple Calculated question';
$string['addingcalculatedsimple_help'] = 'Simple calculated questions enable individual numerical questions to be created using wildcards that are substituted with individual values when the quiz is taken. Simple calculated questions offer the most used features of the calculated question with a simpler creation interface.';
$string['addingcalculatedsimple_link'] = 'question/type/calculatedsimple';
$string['atleastonewildcard'] = 'There must be at least one wild card <strong>{x..}</strong> present in the correct answer formulas';
$string['calculatedsimple'] = 'Calculated Simple';
$string['calculatedsimple_help'] = '<p>Calculatedsimple questions are similar to calculated questions but can be edited in a single page process, calculated needing a three pages. These question types offers a way to create individual numerical question by the use of wildcards {a},{b}that are substituted with individual values when the quiz is taken.</p>
<p>The answers are the result of formulas that can use accepted operators as +-*/ and % where % is the modulo operator. 
It is also possible to use some PHP-style mathematical function.</p>
<p>Among these there are 24 single-argument function:<b>
abs, acos, acosh, asin, asinh, atan, atanh, ceil, cos, cosh, deg2rad, exp, expm1, floor, log, log10, log1p, rad2deg, round, sin, sinh, sqrt, tan, tanh
</b>and two two-argument functions<b>
atan2, pow
</b>and the functions <b>min</b> and <b>max</b> that can take two or more arguments.</p> 
<p>It is also possible to use the function <b>pi()</b></p> 
<p>Possible usage is for example <b>sin({a}) + cos({b}) * 2</b>.</p> 
';
$string['calculatedsimplesummary'] = 'A simpler version of calculated questions which are like numerical questions but with the numbers used selected randomly from a set when the quiz is taken.';
$string['converttocalculated'] = 'Save as a new regular calculated question';
$string['editingcalculatedsimple'] = 'Editing a Simple Calculated question';
$string['findwildcards'] = 'Find the wild cards {x..} present in the correct answer formulas';
$string['generatenewitemsset'] = 'Generate';
$string['mustbenumeric'] = 'You must enter a number here.';
$string['mustnotbenumeric'] = 'This can\'t be a number.';
$string['newsetwildcardvalues'] = 'new set(s) of wild card(s) values';
$string['setno'] = 'Set {$a}';
$string['setwildcardvalues'] = 'set(s) of wild card(s) values';
$string['showitems'] = 'Display';
$string['updatewildcardvalues'] = 'Update the wild card(s) values';
$string['useadvance'] = 'Use the advance button to see the errors';
$string['wildcard'] = 'Wild card {<strong>{$a}</strong>}';
$string['wildcardparam'] = 'Wild cards parameters used to generate the values';
$string['wildcardrole'] = 'The wild cards <strong>{x..}</strong> will be substituted by a numerical value from the generated values';
$string['wildcardvalues'] = 'Wild card(s) values';
$string['wildcardvaluesgenerated'] = 'Wild card(s) values generated';
$string['willconverttocalculated'] = 'If set, the <strong>Save as new question</strong> will save as a new calculated question';
$string['youmustaddatleastonevalue'] = 'You must add at least one set of wild card(s) values before you can save this question.';
