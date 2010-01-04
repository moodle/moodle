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
 * English strings for Workshop module
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string[''] = '';
$string[''] = '';
$string[''] = '';
$string[''] = '';
$string['configgradedecimals'] = 'Default number of digits that should be shown after the decimal point when displaying grades.';
$string['gradedecimals'] = 'Decimal places in grades';
$string['accesscontrol'] = 'Access control';
$string['aggregategrades'] = 'Re-calculate grades';
$string['aggregation'] = 'Grades aggregation';
$string['aggregationinfo'] = 'During the aggregation process, the grades for submission, grades for assessment and total grades are re-calculated and stored into the Workshop database. This does not modify any manual overrides nor does not push the total grade into the gradebook.';
$string['agreeassessments'] = 'Assessments must be agreed';
$string['agreeassessmentsdesc'] = 'Authors may comment assessments of their work and agree/disagree with it';
$string['allocate'] = 'Allocate submissions';
$string['allocatedetails'] = 'expected: $a->expected<br />submitted: $a->submitted<br />to allocate: $a->allocate';
$string['allocationdone'] = 'Allocation done';
$string['allocationerror'] = 'Allocation error';
$string['allocation'] = 'Submission allocation';
$string['alreadygraded'] = 'Already graded';
$string['areainstructauthors'] = 'Instructions for submitting';
$string['areasubmissionattachment'] = 'Submission attachments';
$string['areasubmissioncontent'] = 'Submission texts';
$string['assessallexamples'] = 'Assess all examples';
$string['assess'] = 'Assess';
$string['assessingsubmission'] = 'Assessing submission';
$string['assessmentcomps'] = 'Required level of assessments similarity';
$string['assessmentdeleted'] = 'Assessment deallocated';
$string['assessmentend'] = 'End of assessment phase';
$string['assessmentform'] = 'Assessment form';
$string['assessmentresult'] = 'Assessment result';
$string['assessmentsettings'] = 'Assessment settings';
$string['assessmentstart'] = 'Start of assessment phase';
$string['assignedassessments'] = 'Assigned submissions to assess';
$string['assignedassessmentsnone'] = 'You have no assigned submission to assess';
$string['backtoeditform'] = 'Back to editing form';
$string['byfullname'] = 'by <a href=\"{$a->url}\">{$a->name}</a>';
$string['comparisonhigh'] = 'High';
$string['comparisonlow'] = 'Low';
$string['comparisonnormal'] = 'Normal';
$string['comparisonveryhigh'] = 'Very high';
$string['comparisonverylow'] = 'Very low';
$string['configanonymity'] = 'Default anonymity mode in workshops';
$string['configassessmentcomps'] = 'Default value of the setting that influences the calculation of the grade for assessment.';
$string['configexamplesmode'] = 'Default mode of examples assessment in workshops';
$string['configgrade'] = 'Default maximum grade for submission in workshops';
$string['configgradinggrade'] = 'Default maximum grade for assessment in workshops';
$string['configmaxbytes'] = 'Default maximum submission file size for all workshops on the site (subject to course limits and other local settings)';
$string['confignexassessments'] = 'Default number of examples to be reviewed by a user in the example assessment phase';
$string['confignsassessments'] = 'Default number of allocated submissions to be reviewed by a user in the assessment phase';
$string['configstrategy'] = 'Default grading strategy for workshops';
$string['editassessmentform'] = 'Edit assessment form';
$string['editassessmentformstrategy'] = 'Edit assessment form ($a)';
$string['editingassessmentform'] = 'Editing assessment form';
$string['editingsubmission'] = 'Editing submission';
$string['editsubmission'] = 'Edit submission';
$string['err_removegrademappings'] = 'Unable to remove the unused grade mappings';
$string['examplesbeforeassessment'] = 'Examples are available after own submission and must be assessed before peer/self assessment phase';
$string['examplesbeforesubmission'] = 'Examples must be assessed before own submission';
$string['examplesmode'] = 'Mode of examples assessment';
$string['examplesvoluntary'] = 'Assessment of example submission is voluntary';
$string['formatpeergrade'] = '$a->grade ($a->gradinggrade)';
$string['formatpeergradeover'] = '$a->grade (<del>$a->gradinggrade</del> / <ins>$a->gradinggradeover</ins>)';
$string['givengrade'] = 'Given grade: $a';
$string['givengrades'] = 'Given grades';
$string['gradinggrade'] = 'Grade for assessment';
$string['gradinggradeof'] = 'Grade for assessment (of $a)';
$string['gradingsettings'] = 'Grading settings';
$string['chooseuser'] = 'Choose user...';
$string['iamsure'] = 'Yes, I am sure';
$string['info'] = 'Info';
$string['instructauthors'] = 'Instructions for submitting';
$string['instructreviewers'] = 'Instructions for assessing';
$string['introduction'] = 'Introduction';
$string['latesubmissionsdesc'] = 'Allow submitting the work after the deadline';
$string['latesubmissions'] = 'Late submissions';
$string['maxbytes'] = 'Maximum file size';
$string['messageclose'] = '(hide)';
$string['modulenameplural'] = 'Workshops';
$string['modulename'] = 'Workshop';
$string['mysubmission'] = 'My submission';
$string['nattachments'] = 'Maximum number of submission attachments';
$string['nexassessments'] = 'Number of required assessments of examples';
$string['nogradeyet'] = 'No grade yet';
$string['nosubmissionfound'] = 'No submission found for this user';
$string['nosubmissions'] = 'No submissions yet in this workshop';
$string['nothingtoreview'] = 'Nothing to review';
$string['noworkshops'] = 'There are no workshops in this course';
$string['noyoursubmission'] = 'You have not submitted your work yet';
$string['nsassessments'] = 'Number of required assessments of other users\' work';
$string['nullgrade'] = '-';
$string['numofreviews'] = 'Number of reviews';
$string['participant'] = 'Participant';
$string['participantrevierof'] = 'Participant is reviewer of';
$string['participantreviewedby'] = 'Participant is reviewed by';
$string['phaseassessment'] = 'Assessment phase';
$string['phaseclosed'] = 'Closed';
$string['phaseevaluation'] = 'Grading evaluation phase';
$string['phasesetup'] = 'Setup phase';
$string['phasesubmission'] = 'Submission phase';
$string['previewassessmentform'] = 'Preview';
$string['reassess'] = 'Re-assess';
$string['receivedgrades'] = 'Received grades';
$string['releasegrades'] = 'Push final grades into the gradebook';
$string['saveandclose'] = 'Save and close';
$string['saveandcontinue'] = 'Save and continue editing';
$string['saveandpreview'] = 'Save and preview';
$string['selfassessmentdisabled'] = 'Self-assessment disabled';
$string['someuserswosubmission'] = 'There are some users who have not submitted yet';
$string['strategyaccumulative'] = 'Accumulative grading';
$string['strategydummy'] = 'Dummy strategy';
$string['strategy'] = 'Grading strategy';
$string['strategyhaschanged'] = 'The workshop grading strategy has changed since the form was opened for editing.';
$string['strategynograding'] = 'No grading';
$string['strategyrubric'] = 'Rubric grading';
$string['submissionattachment'] = 'Attachment';
$string['submissioncontent'] = 'Submission content';
$string['submissionend'] = 'End of submission phase';
$string['submissiongrade'] = 'Grade for submission';
$string['submissiongradeof'] = 'Grade for submission (of $a)';
$string['submissionsettings'] = 'Submission settings';
$string['submissionstart'] = 'Start of submission phase';
$string['submission'] = 'Submission';
$string['submissiontitle'] = 'Title';
$string['switchingphase'] = 'Switching phase';
$string['switchphase'] = 'Switch phase';
$string['switchphase10info'] = 'You are going to switch the Workshop into the <strong>Setup phase</strong>. (TODO: explain what the participants and moderators will do in the new phase)';
$string['switchphase20info'] = 'You are going to switch the Workshop into the <strong>Submission phase</strong>. (TODO: explain what the participants and moderators will do in the new phase)';
$string['switchphase30info'] = 'You are going to switch the Workshop into the <strong>Assessment phase</strong>. (TODO: explain what the participants and moderators will do in the new phase)';
$string['switchphase40info'] = 'You are going to switch the Workshop into the <strong>Grading evaluation phase</strong>. (TODO: explain what the participants and moderators will do in the new phase)';
$string['switchphase50info'] = 'You are going to close the Workshop. (TODO: explain what the participants and moderators will do in the new phase)';
$string['taskassesspeers'] = 'Assess peers';
$string['taskassesspeersdetails'] = 'total: $a->total<br />pending: $a->todo';
$string['taskassessself'] = 'Assess yourself';
$string['taskinstructauthors'] = 'Provide instructions for submitting';
$string['taskinstructreviewers'] = 'Provide instructions for assessing';
$string['taskintro'] = 'Set the workshop introduction';
$string['tasksubmit'] = 'Submit your work';
$string['teacherweight'] = 'Weight of the teacher\'s assessments';
$string['totalgrade'] = 'Total grade';
$string['undersetup'] = 'The workshop is currently under setup. Please wait until it is switched to the next phase.';
$string['useexamplesdesc'] = 'Users practise evaluating on example submissions';
$string['useexamples'] = 'Use examples';
$string['usepeerassessmentdesc'] = 'Users perform peer assessment of others\' work';
$string['usepeerassessment'] = 'Use peer assessment';
$string['userdatecreated'] = 'submitted on <span>$a</span>';
$string['userdatemodified'] = 'modified on <span>$a</span>';
$string['useselfassessmentdesc'] = 'Users perform self assessment of their own work';
$string['useselfassessment'] = 'Use self assessment';
$string['withoutsubmission'] = 'Reviewer without own submission';
$string['workshopadministration'] = 'Workshop administration';
$string['workshopfeatures'] = 'Workshop features';
$string['workshopname'] = 'Workshop name';
