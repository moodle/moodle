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
 * Strings for component 'qtype_numerical', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    qtype
 * @subpackage numerical
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['acceptederror'] = 'Accepted error';
$string['addingnumerical'] = 'Adding a Numerical question';
$string['addmoreanswerblanks'] = 'Blanks for {no} More Answers';
$string['addmoreunitblanks'] = 'Blanks for {no} More Units';
$string['answermustbenumberorstar'] = 'The answer must be a number, or \'*\'.';
$string['answerno'] = 'Answer {$a}';
$string['decfractionofquestiongrade'] = 'as decimal fraction (0-1) of question grade';
$string['decfractionofresponsegrade'] = 'as decimal fraction (0-1) of response grade';
$string['decimalformat'] = 'decimals';
$string['editableunittext'] = 'Text input element';
$string['editingnumerical'] = 'Editing a Numerical question';
$string['errornomultiplier'] = 'You must specify a multiplier for this unit.';
$string['errorrepeatedunit'] = 'You cannot have two units with the same name.';
$string['geometric'] = 'Geometric';
$string['instructions'] = 'Instructions ';
$string['invalidnumericanswer'] = 'One of the answers you entered was not a valid number.';
$string['invalidnumerictolerance'] = 'One of the tolerances you entered was not a valid number.';
$string['leftexample'] = 'LEFT as $1.00';
$string['multiplier'] = 'Multiplier';
$string['noneditableunittext'] = 'NON editable text of Unit No1';
$string['nonvalidcharactersinnumber'] = 'NON valid characters in number';
$string['notenoughanswers'] = 'You must enter at least one answer.';
$string['nounitdisplay'] = 'No unit grading';
$string['numerical'] = 'Numerical';
$string['numerical_help'] = 'From the student perspective, a numerical question looks just like a short-answer question. The difference is that numerical answers are allowed to have an accepted error. This allows a fixed range of answers to be evaluated as one answer. For example, if the answer is 10 with an accepted error of 2, then any number between 8 and 12 will be accepted as correct. ';
$string['numerical_link'] = 'question/type/numerical';
$string['numericalsummary'] = 'Allows a numerical response, possibly with units, that is graded by comparing against various model answers, possibly with tolerances.';
$string['numericalinstructions'] = 'Instructions';
$string['numericalinstructions_help'] = 'Specific instructions related to the question as

* Examples of number formats
* Complex units';
$string['numericalmultiplier'] = 'Multiplier';
$string['numericalmultiplier_help'] = 'The multiplier is the factor by which the correct numerical response will be multiplied.

The first unit (Unit 1) has a default multiplier of 1. Thus if the correct numerical response is 5500 and you set W as unit at Unit 1 which has 1 as default multiplier, the correct response is 5500 W.

If you add the unit kW with a multiplier of 0.001, this will add a correct response of 5.5 kW. This means that the answers 5500W or 5.5kW would be marked correct.

Note that the accepted error is also multiplied, so an allowed error of 100W would become an error of 0.1kW.';
$string['manynumerical'] = 'Only the NUMERICAL ANSWER will be graded using optional units ';
$string['nominal'] = 'Nominal';
$string['onlynumerical'] = 'Only the NUMERICAL ANSWER will be graded, no units allowed';
$string['oneunitshown'] = 'Only the NUMERICAL ANSWER will be graded, Unit1 will be shown';
$string['pleaseenterananswer'] = 'Please enter an answer.';
$string['relative'] = 'Relative';
$string['rightexample'] = 'RIGHT as 1.00cm';
$string['selectunits'] = 'Select units';
$string['selectunit'] = 'Select one unit';
$string['studentunitanswer'] = 'UNIT ANSWER displayed as a ';
$string['tolerancetype'] = 'Tolerance type';
$string['unit'] = 'Unit';
$string['unitchoice'] = 'Multichoice (radio elements)';
$string['unitdisplay'] = '<STRONG>Unit 1 displayed </STRONG>';
$string['unitedit'] = 'Edit unit';
$string['unitgraded'] = ' NUMERICAL ANSWER and UNIT ANSWER will be graded ';
$string['unitgraded1'] = '<STRONG>UNIT GRADED</STRONG>';
$string['unitsused'] = '<STRONG>UNIT USED</STRONG>';
$string['unithdr'] = 'Unit {$a}';
$string['unitmandatory'] = 'Mandatory';
$string['unitmandatory_help'] = '

* The response will be graded using the unit written.

* The unit penalty will be applied if the unit field is empty

';
$string['unitnotgraded'] = '<STRONG>UNIT NOT GRADED</STRONG>';
$string['unitnotused'] = '<STRONG>UNIT NOT USED</STRONG>';
$string['unitoptional'] = 'Optional unit';
$string['unitoptional_help'] = '
* If the unit field is not empty, the response will be graded using this unit.

* If the unit is badly written or unknown, the response will be considered as non valid.
';
$string['unitused'] = '<STRONG>UNIT USED</STRONG>';
$string['unituses'] = 'Unit uses';
$string['unituses_help'] = 'The unit(s) are uses as in pre 2,0 Moodle version

* The student can answer using units predefined by the teacher
* in which case the constant will be applied to the student value.
* If the student does not add any unit, his numerical answer is used as is.';
$string['unitnotvalid'] = ' Unit not valid with this numerical value';
$string['unitunknown'] = ' Undefined unit ';
$string['unitpenalty'] = 'Unit penalty';
$string['unitpenalty_help'] = 'The penalty is applied if

* An undefined unit name is put in the Unit answer element or
* An unit name is put in the Number answer element ';
$string['unitappliedpenalty'] = 'These marks include a penalty of {$a} for bad unit.';
$string['unitposition'] = 'Unit position';
$string['unitnotselected'] = 'No unit selected';
$string['unitshandling'] = 'Units handling';
$string['validnumberformats'] = 'Valid number formats';
$string['validnumberformats_help'] = '
* regular numbers  13500.67 : 13 500.67 : 13500,67: 13 500,67

* if you use , as thousand separator *always* put the decimal . as in
 13,500.67 : 13,500.

* for exponent form, say 1.350067 * 10<sup>4</sup>, use
 1.350067 E4 : 1.350067 E04 ';

$string['validnumbers'] = ' 13500.67 : 13 500.67 : 13,500.67 : 13500,67: 13 500,67 : 1.350067 E4 : 1.350067 E04 ';
