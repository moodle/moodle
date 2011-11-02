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

$string['saverubric'] = 'Save rubric and make it available';
$string['saverubricdraft'] = 'Save as draft';

$string['statusworkinprogress'] = 'Work in progress';
$string['statusprivate'] = 'Private';
$string['statuspublic'] = 'Public';

$string['err_nocriteria'] = 'Rubric must contain at least one criterion';
$string['err_mintwolevels'] = 'Each criterion must have at least two levels';
$string['err_nodescription'] = 'Criterion description can not be empty';
$string['err_nodefinition'] = 'Level definition can not be empty';
$string['err_scoreformat'] = 'Number of points for each level must be a valid non-negative number';
$string['err_totalscore'] = 'Maximum number of points possible when graded by the rubric must be more than zero';
