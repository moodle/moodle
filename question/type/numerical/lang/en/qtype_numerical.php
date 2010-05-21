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
 * @package   qtype_numerical
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addingnumerical'] = 'Adding a Numerical question';
$string['addmoreanswerblanks'] = 'Blanks for {no} More Answers';
$string['addmoreunitblanks'] = 'Blanks for {no} More Units';
$string['answermustbenumberorstar'] = 'The answer must be a number, or \'*\'.';
$string['answerno'] = 'Answer {$a}';
$string['decfractionof'] = 'AS' ;
$string['decfractionofquestiongrade'] = 'as decimal fraction (0-1) of question grade' ;
$string['decfractionofresponsegrade'] = 'as decimal fraction (0-1) of response grade' ;
$string['editableunittext'] = 'Text input element' ;
$string['editingnumerical'] = 'Editing a Numerical question';
$string['errornomultiplier'] = 'You must specify a multiplier for this unit.';
$string['errorrepeatedunit'] = 'You cannot have two units with the same name.';
$string['instructions'] = 'Instructions ' ;
$string['leftexample'] = 'LEFT as $1.00' ;
$string['noneditableunittext'] = 'NON editable text of Unit No1' ;
$string['notenoughanswers'] = 'You must enter at least one answer.';
$string['nounitdisplay'] = 'No unit grading' ;
$string['numerical'] = 'Numerical';
$string['numerical_help'] = 'From the student perspective, a numerical question looks just
like a short-answer question.
<p>
The difference is that numerical answers are allowed
to have an accepted error. This allows a continuous range of answers
to be set.
</p>
For example, if the answer is 30 with an accepted error of 5,
then any number between 25 and 35 will be accepted as correct.
Like with short answer questions, different answers, or the same
answer with different precisions can be given.'; 
$string['numericalsummary'] = 'Allows a numerical response, possibly with units, that is graded by comparing against various model answers, possibly with tolerances.';
$string['numericalinstructions'] = 'Instructions'; 
$string['numericalinstructions_help'] = 'Specific instructions related to the question as

* examples of number formats

* complex units. 

'; 
$string['numericalmultiplier'] = 'Multiplier'; 
$string['numericalmultiplier_help'] = 'The Correct numerical response will be multiplied by the multiplier related to a specific unit to obtain the Correct response in this unit.<br/>
 <p> The first unit (Unit1) has a default multiplier of 1. </p>
 <p> So, if your Correct numerical response is 5500 and you set W as unit at Unit1 which has 1 as default multiplier, the Correct response is 5500 W.</p> 
 <p> If you add the unit kW with a multiplier of 0.001, this will add a Correct response of 5.5 kW. </p><p>This means that the answers 5500W or 5.5kW would be marked correct.</p><p> Note that the accepted error is also multiplied, so an allowed error of 100W would become an error of 0.1kW.</p>'; 
$string['onlynumerical'] = 'Only NUMERICAL ANSWER will be graded' ;
$string['rightexample'] = 'RIGHT as 1.00cm' ;
$string['selectunits'] = 'Select units' ; 
$string['studentunitanswer'] = 'UNIT ANSWER displayed as a ' ;
$string['unitchoice'] = 'Multichoice (radio elements)' ;
$string['unitdisplay'] = 'Display Unit1' ; 
$string['unitedit'] = 'Edit unit' ;
$string['unitgraded'] = ' NUMERICAL  ANSWER and UNIT ANSWER will be graded ' ;
$string['unitgraded1'] = '<STRONG>UNIT GRADED</STRONG>' ;
$string['unithdr'] = 'Unit {$a}';
$string['unitnotgraded'] = '<STRONG>UNIT NOT GRADED</STRONG>' ;
$string['unitnotvalid'] = ' Unit not valid with this numerical value';
$string['unitunknown'] = ' Undefined unit ';
$string['unitpenalty'] = 'Penalty for bad unit' ;
$string['unitappliedpenalty'] = 'This submission attracted a penalty of {$a} for bad unit.' ;
$string['unitposition'] = 'Unit position' ;
$string['unitshandling'] = 'Units handling' ;
$string['validnumberformats'] = 'Valid number formats';
$string['validnumbers'] = ' 13500.67 : 13 500.67 : 13500,67: 13 500,67 : 1.350067 E4 : 1.350067 E04 ';

