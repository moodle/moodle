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
 * @package    gradingform
 * @subpackage rubric
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['definerubric'] = 'Define rubric';
$string['pluginname'] = 'Rubric';

$string['confirmdeletecriterion'] = 'Are you sure you want to delete this criterion?';
$string['confirmdeletelevel'] = 'Are you sure you want to delete this level?';
$string['description'] = 'Description';
$string['name'] = 'Name';

$string['addcriterion'] = 'Add criterion';
$string['criterionmoveup'] = 'Move up';
$string['criteriondelete'] = 'Delete criterion';
$string['criterionmovedown'] = 'Move down';
$string['criterionaddlevel'] = 'Add level';
$string['scorepostfix'] = ' points';
$string['leveldelete'] = 'Delete level';

$string['criterionempty'] = 'Click to edit criterion';
$string['levelempty'] = 'Click to edit level';

$string['rubric'] = 'Rubric';
$string['rubricoptions'] = 'Rubric options';

$string['sortlevelsasc'] = 'Sort order for levels:';
$string['sortlevelsasc1'] = 'Ascending by number of points';
$string['sortlevelsasc0'] = 'Descending by number of points';
$string['showdescriptionteacher'] = 'Display rubric description during evaluation';
$string['showdescriptionstudent'] = 'Display rubric description to those being graded';
$string['showscoreteacher'] = 'Display points for each level during evaluation';
$string['showscorestudent'] = 'Display points for each level to those being graded';
$string['enableremarks'] = 'Allow grader to add text remarks for each criteria';
$string['showremarksstudent'] = 'Show remarks to those being graded';

$string['saverubric'] = 'Save rubric and make it ready';
$string['saverubricdraft'] = 'Save as draft';

$string['rubricstatus'] = 'Current rubric status';
$string['statusdraft'] = 'Draft';
$string['statusready'] = 'Ready';

$string['err_nocriteria'] = 'Rubric must contain at least one criterion';
$string['err_mintwolevels'] = 'Each criterion must have at least two levels';
$string['err_nodescription'] = 'Criterion description can not be empty';
$string['err_nodefinition'] = 'Level definition can not be empty';
$string['err_scoreformat'] = 'Number of points for each level must be a valid non-negative number';
$string['err_totalscore'] = 'Maximum number of points possible when graded by the rubric must be more than zero';

$string['regrademessage1'] = 'You are about to save changes to the rubric that has already been used for grading. Please indicate whether your changes
                are significant and students grades need to be reviewed.
                If students already graded are marked for re-grading their
                current grades remain in gradebook but the students will not see the rubric grading before teacher updates it.';
$string['regrademessage5'] = 'You are about to save significant changes to the rubric that has already been used for grading. Please note that all students already graded will be marked for re-grading.
                The
                current grades remain in gradebook but the students will not see the rubric grading before teacher updates it.';
$string['regradeoption0'] = 'Do not mark for regrade';
$string['regradeoption1'] = 'Mark for regrade';

$string['needregrademessage'] = 'Rubric definition was changed after this student had been graded. You must update the grade otherwise it will not be shown to student.';
$string['rubricnotcompleted'] = 'Please choose something for each criterion';

$string['backtoediting'] = 'Back to editing';