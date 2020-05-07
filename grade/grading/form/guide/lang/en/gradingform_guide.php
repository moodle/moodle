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
 * Strings for the marking guide advanced grading plugin
 *
 * @package    gradingform_guide
 * @copyright  2012 Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcomment'] = 'Add frequently used comment';
$string['additionalcomments'] = 'Additional comments';
$string['additionalcommentsforcriterion'] = 'Additional comments for criterion, {$a}';
$string['addcriterion'] = 'Add criterion';
$string['alwaysshowdefinition'] = 'Show guide definition to students';
$string['backtoediting'] = 'Back to editing';
$string['clicktocopy'] = 'Click to copy this text into the criteria feedback';
$string['clicktoedit'] = 'Click to edit';
$string['clicktoeditname'] = 'Click to edit criterion name';
$string['comment'] = 'Comment';
$string['commentpickerforcriterion'] = 'Frequently used comments picker for {$a} additional comments';
$string['comments'] = 'Frequently used comments';
$string['commentsdelete'] = 'Delete comment';
$string['commentsempty'] = 'Click to edit comment';
$string['commentsmovedown'] = 'Move down';
$string['commentsmoveup'] = 'Move up';
$string['confirmdeletecriterion'] = 'Are you sure you want to delete this item?';
$string['confirmdeletelevel'] = 'Are you sure you want to delete this level?';
$string['criterion'] = 'Criterion name';
$string['criteriondelete'] = 'Delete criterion';
$string['criterionempty'] = 'Click to edit criterion';
$string['criterionmovedown'] = 'Move down';
$string['criterionmoveup'] = 'Move up';
$string['criterionname'] = 'Criterion name';
$string['criterionremark'] = '{$a} criterion remark';
$string['definemarkingguide'] = 'Define marking guide';
$string['description'] = 'Description';
$string['descriptionmarkers'] = 'Description for Markers';
$string['descriptionstudents'] = 'Description for students';
$string['err_maxscoreisnegative'] = 'The max score is not valid, negative values are not allowed';
$string['err_maxscorenotnumeric'] = 'Criterion max score must be numeric';
$string['err_nocomment'] = 'Comment can not be empty';
$string['err_nodescription'] = 'Student description can not be empty';
$string['err_nodescriptionmarkers'] = 'Marker description can not be empty';
$string['err_nomaxscore'] = 'Criterion max score can not be empty';
$string['err_noshortname'] = 'Criterion name can not be empty';
$string['err_shortnametoolong'] = 'Criterion name must be less than 256 characters';
$string['err_scoreinvalid'] = 'The score given to \'{$a->criterianame}\' is not valid, the max score is: {$a->maxscore}';
$string['err_scoreisnegative'] = 'The score given to \'{$a->criterianame}\' is not valid, negative values are not allowed';
$string['gradingof'] = '{$a} grading';
$string['guide'] = 'Marking guide';
$string['guidemappingexplained'] = 'WARNING: Your marking guide has a maximum grade of <b>{$a->maxscore} points</b> but the maximum grade set in your activity is {$a->modulegrade}  The maximum score set in your marking guide will be scaled to the maximum grade in the module.<br />
    Intermediate scores will be converted respectively and rounded to the nearest available grade.';
$string['guidenotcompleted'] = 'Please provide a valid grade for each criterion';
$string['guideoptions'] = 'Marking guide options';
$string['guidestatus'] = 'Current marking guide status';
$string['hidemarkerdesc'] = 'Hide marker criterion descriptions';
$string['hidestudentdesc'] = 'Hide student criterion descriptions';
$string['informationforcriterion'] = '{$a} information';
$string['insertcomment'] = 'Insert frequently used comment';
$string['maxscore'] = 'Maximum score';
$string['name'] = 'Name';
$string['needregrademessage'] = 'The marking guide definition was changed after this student had been graded. The student can not see this marking guide until you check the marking guide and update the grade.';
$string['outof'] = 'Score out of {$a}';
$string['pluginname'] = 'Marking guide';
$string['previewmarkingguide'] = 'Preview marking guide';
$string['privacy:metadata:criterionid'] = 'An identifier to a criterion for advanced marking.';
$string['privacy:metadata:fillingssummary'] = 'Stores information about a user\'s grade and feedback for the marking guide.';
$string['privacy:metadata:instanceid'] = 'An identifier to a grade used by an activity.';
$string['privacy:metadata:preference:showmarkerdesc'] = 'Whether to show marker criterion descriptions';
$string['privacy:metadata:preference:showstudentdesc'] = 'Whether to show student criterion descriptions';
$string['privacy:metadata:remark'] = 'Remarks related to this grade criterion.';
$string['privacy:metadata:score'] = 'A score for this grade criterion.';
$string['regrademessage1'] = 'You are about to save changes to a marking guide that has already been used for grading. Please indicate if existing grades need to be reviewed. If you set this then the marking guide will be hidden from students until their item is regraded.';
$string['regrademessage5'] = 'You are about to save significant changes to a marking guide that has already been used for grading. The gradebook value will be unchanged, but the marking guide will be hidden from students until their item is regraded.';
$string['regradeoption0'] = 'Do not mark for regrade';
$string['regradeoption1'] = 'Mark for regrade';
$string['remark_help'] = 'Enter any additional comments about this criterion. You may also pick from the list of frequently used comments using the frequently used comments picker button.';
$string['restoredfromdraft'] = 'NOTE: The last attempt to grade this person was not saved properly so draft grades have been restored. If you want to cancel these changes use the \'Cancel\' button below.';
$string['save'] = 'Save';
$string['saveguide'] = 'Save marking guide and make it ready';
$string['saveguidedraft'] = 'Save as draft';
$string['score'] = 'score';
$string['scoreforcriterion'] = '{$a} score';
$string['score_help'] = 'Enter a score for {$a->criterion} between 0 and {$a->maxscore}.';
$string['showmarkerdesc'] = 'Show marker criterion descriptions';
$string['showmarkspercriterionstudents'] = 'Show marks per criterion to students';
$string['showstudentdesc'] = 'Show student criterion descriptions';
