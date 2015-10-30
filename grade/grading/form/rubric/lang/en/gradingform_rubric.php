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
 * Language file for plugin gradingform_rubric
 *
 * @package    gradingform_rubric
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcriterion'] = 'Add criterion';
$string['alwaysshowdefinition'] = 'Allow users to preview rubric used in the module (otherwise rubric will only become visible after grading)';
$string['backtoediting'] = 'Back to editing';
$string['confirmdeletecriterion'] = 'Are you sure you want to delete this criterion?';
$string['confirmdeletelevel'] = 'Are you sure you want to delete this level?';
$string['criterionaddlevel'] = 'Add level';
$string['criteriondelete'] = 'Delete criterion';
$string['criterionempty'] = 'Click to edit criterion';
$string['criterionmovedown'] = 'Move down';
$string['criterionmoveup'] = 'Move up';
$string['definerubric'] = 'Define rubric';
$string['description'] = 'Description';
$string['enableremarks'] = 'Allow grader to add text remarks for each criterion';
$string['err_mintwolevels'] = 'Each criterion must have at least two levels';
$string['err_nocriteria'] = 'Rubric must contain at least one criterion';
$string['err_nodefinition'] = 'Level definition can not be empty';
$string['err_nodescription'] = 'Criterion description can not be empty';
$string['err_scoreformat'] = 'Number of points for each level must be a valid non-negative number';
$string['err_totalscore'] = 'Maximum number of points possible when graded by the rubric must be more than zero';
$string['gradingof'] = '{$a} grading';
$string['leveldelete'] = 'Delete level';
$string['levelempty'] = 'Click to edit level';
$string['name'] = 'Name';
$string['needregrademessage'] = 'The rubric definition was changed after this student had been graded. The student can not see this rubric until you check the rubric and update the grade.';
$string['pluginname'] = 'Rubric';
$string['previewrubric'] = 'Preview rubric';
$string['regrademessage1'] = 'You are about to save changes to a rubric that has already been used for grading. Please indicate if existing grades need to be reviewed. If you set this then the rubric will be hidden from students until their item is regraded.';
$string['regrademessage5'] = 'You are about to save significant changes to a rubric that has already been used for grading. The gradebook value will be unchanged, but the rubric will be hidden from students until their item is regraded.';
$string['regradeoption0'] = 'Do not mark for regrade';
$string['regradeoption1'] = 'Mark for regrade';
$string['restoredfromdraft'] = 'NOTE: The last attempt to grade this person was not saved properly so draft grades have been restored. If you want to cancel these changes use the \'Cancel\' button below.';
$string['rubric'] = 'Rubric';
$string['rubricmapping'] = 'Score to grade mapping rules';
$string['rubricmappingexplained'] = 'The minimum possible score for this rubric is <b>{$a->minscore} points</b> and it will be converted to the minimum grade available in this module (which is zero unless the scale is used).
    The maximum score <b>{$a->maxscore} points</b> will be converted to the maximum grade.<br />
    Intermediate scores will be converted respectively and rounded to the nearest available grade.<br />
    If a scale is used instead of a grade, the score will be converted to the scale elements as if they were consecutive integers.';
$string['rubricnotcompleted'] = 'Please choose something for each criterion';
$string['rubricoptions'] = 'Rubric options';
$string['rubricstatus'] = 'Current rubric status';
$string['save'] = 'Save';
$string['saverubric'] = 'Save rubric and make it ready';
$string['saverubricdraft'] = 'Save as draft';
$string['scorepostfix'] = '{$a}points';
$string['showdescriptionstudent'] = 'Display rubric description to those being graded';
$string['showdescriptionteacher'] = 'Display rubric description during evaluation';
$string['showremarksstudent'] = 'Show remarks to those being graded';
$string['showscorestudent'] = 'Display points for each level to those being graded';
$string['showscoreteacher'] = 'Display points for each level during evaluation';
$string['sortlevelsasc'] = 'Sort order for levels:';
$string['sortlevelsasc0'] = 'Descending by number of points';
$string['sortlevelsasc1'] = 'Ascending by number of points';