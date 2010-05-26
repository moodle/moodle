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
 * Strings for component 'qtype_calculated', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   qtype_calculated
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addingcalculated'] = 'Adding a calculated question';
$string['additem'] = 'Add Item';
$string['addmoreanswerblanks'] = 'Add another answer blank.';
$string['addmoreunitblanks'] = 'Blanks for {$a} More Units';
$string['answerhdr'] = 'Answer';
$string['answerstoleranceparam'] = 'Answers tolerance parameters';
$string['atleastoneanswer'] = 'You need to provide at least one answer.';
$string['atleastonerealdataset']='There should be at least one real dataset in question text';
$string['atleastonewildcard']='There should be at least one wild card in answer formula or question text';
$string['calcdistribution'] = 'Distribution';
$string['calclength'] = 'Decimal places';
$string['calcmax'] = 'Maximum';
$string['calcmin'] = 'Minimum';
$string['calculated'] = 'Calculated';
$string['calculated_help'] = 'Calculated questions enable individual numerical questions to be created using wildcards in curly brackets that are substituted with individual values when the quiz is taken. For example, the question "What is the area of a rectangle of length {l} and width {w}?" would have correct answer formula "{l}*{w}" (where * denotes multiplication).';
$string['calculated_link'] = 'question/type/calculated';
$string['calculatedsummary'] = 'Calculated questions are like numerical questions but with the numbers used selected randomly from a set when the quiz is taken.';
$string['choosedatasetproperties'] = 'Choose wildcards dataset properties';
$string['choosedatasetproperties_help'] = 'A dataset is a set of values inserted in place of a wildcard. You can create a private dataset for a specific question, or a shared dataset that can be used for other calculated questions within the category.';
$string['correctanswershows'] = 'Correct answer shows';
$string['correctanswershowsformat'] = 'Format';
$string['dataitemdefined']='with $a numerical values already defined is available';
$string['datasetrole']= ' The wild cards <strong>{x..}</strong> will be substituted by a numerical value from their dataset';
$string['deleteitem'] = 'Delete Item';
$string['deletelastitem'] = 'Delete Last Item';
$string['editingcalculated'] = 'Editing a Calculated question';
$string['editdatasets'] = 'Edit the wildcards datasets';
$string['editdatasets_help'] = 'Wildcard values may be created by entering a number in each wild card field then clicking the add button. To automatically generate 10 or more values, select the number of values required before clicking the add button. A uniform distribution means any value between the limits is equally likely to be generated; a loguniform distribution means that values towards the lower limit are more likely.';
$string['editdatasets_link'] = 'question/type/calculated';
$string['existingcategory1'] = 'will use an already existing shared dataset';
$string['existingcategory2'] = 'a file from an already existing set of files that are also used by other questions in this category';
$string['existingcategory3'] = 'a link from an already existing set of links that are also used by other questions in this category';
$string['forceregeneration'] = 'force regeneration';
$string['forceregenerationall'] = 'forceregeneration of all wildcards';
$string['forceregenerationshared'] = 'forceregeneration of only non-shared wildcards';
$string['getnextnow'] = 'Get New \'Item to Add\' Now';
$string['hexanotallowed'] = 'Dataset <strong>{$a->name}</strong> hexadecimal format value $a->value is not allowed' ;
$string['item(s)'] = 'item(s)';
$string['itemno'] = 'Item $a';
$string['itemscount']='Items<br />Count';
$string['itemtoadd'] = 'Item To Add';
$string['keptcategory1'] = 'will use the same existing shared dataset as before';
$string['keptcategory2'] = 'a file from the same category reusable set of files as before';
$string['keptcategory3'] = 'a link from the same category reusable set of links as before';
$string['keptlocal1'] = 'will use the same existing private dataset as before';
$string['keptlocal2'] = 'a file from the same question private set of files as before';
$string['keptlocal3'] = 'a link from the same question private set of links as before';
$string['lastitem(s)'] = 'last items(s)';
$string['loguniform'] = 'Loguniform';
$string['makecopynextpage'] = 'Next Page (new question)';
$string['mandatoryhdr'] = 'Mandatory wild cards present in answers';
$string['minmax'] = 'Range of Values';
$string['mustbenumeric'] = 'You must enter a number here.';
$string['mustnotbenumeric'] = 'This can\'t be a number.';
$string['newcategory1'] = 'will use a new shared dataset';
$string['newcategory2'] = 'a file from a new set of files that may also be used by other questions in this category';
$string['newcategory3'] = 'a link from a new set of links that may also be used by other questions in this category';
$string['newlocal1'] = 'will use a new private dataset';
$string['newlocal2'] = 'a file  from a new set of files that will only be used by this question';
$string['newlocal3'] = 'a link from a new set of links that will only be used by this question';
$string['nextitemtoadd'] = 'Next \'Item to Add\'';
$string['nextpage'] = 'Next Page';
$string['nocommaallowed'] = 'The , cannot be used, use . as in 0.013 or 1.3e-2' ;
$string['nodataset'] = 'nothing - it is not a wild card';
$string['nosharedwildcard'] = 'No shared wild card in this category';
$string['notvalidnumber'] = 'Wild card value is not a valid number ' ;
$string['oneanswertrueansweroutsidelimits'] = 'At least one correct answer outside the true value limits.<br />Modify the answers tolerance settings available as Advanced parameters';   
$string['param'] = 'Param {<strong>$a</strong>}';
$string['possiblehdr'] = 'Possible wild cards present only in the question text';
$string['questiondatasets'] = 'Question datasets';
$string['questiondatasets_help'] = 'Question datasets of wild cards that will be used in each individual question';
$string['replacewithrandom'] = 'Replace with a random value';
$string['reuseifpossible'] = 'reuse previous value if available';
$string['sharedwildcard']='Shared wild card';
$string['sharedwildcards']='Shared wild cards';
$string['synchronize']='Synchronize the data from shared datasets with other questions in a quiz';
$string['tolerance'] = 'Tolerance &plusmn;';
$string['trueanswerinsidelimits'] = 'Correct answer : {$a->correct} inside limits of true value {$a->true}';
$string['trueansweroutsidelimits'] = '<span class="error">ERROR Correct answer : {$a->correct} outside limits of true value {$a->true}</span>';
$string['uniform'] = 'Uniform';
$string['updatecategory'] = 'Update the category';
$string['updatedatasetparam'] = 'Update the datasets parameters';
$string['updatetolerancesparam'] = 'Update the answers tolerance parameters';
$string['usedinquestion'] = 'Used in Question';
$string['youmustaddatleastoneitem'] = 'You must add at least one dataset item before you can save this question.';
$string['youmustenteramultiplierhere'] = 'You must enter a multiplier here.';

