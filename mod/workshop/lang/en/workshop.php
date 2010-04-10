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

$string['accesscontrol'] = 'Access control';
$string['aggregategrades'] = 'Re-calculate grades';
$string['aggregation'] = 'Grades aggregation';
$string['allocate'] = 'Allocate submissions';
$string['allocatedetails'] = 'expected: $a->expected<br />submitted: $a->submitted<br />to allocate: $a->allocate';
$string['allocationdone'] = 'Allocation done';
$string['allocationerror'] = 'Allocation error';
$string['allocation'] = 'Submission allocation';
$string['allsubmissions'] = 'All submissions';
$string['alreadygraded'] = 'Already graded';
$string['areainstructauthors'] = 'Instructions for submitting';
$string['areasubmissionattachment'] = 'Submission attachments';
$string['areasubmissioncontent'] = 'Submission texts';
$string['assess'] = 'Assess';
$string['assessedexample'] = 'Assessed example submission';
$string['assessedsubmission'] = 'Assessed submission';
$string['assessingexample'] = 'Assessing example submission';
$string['assessingsubmission'] = 'Assessing submission';
$string['assessmentbyknown'] = 'Assessment by $a';
$string['assessmentbyunknown'] = 'Assessment';
$string['assessmentbyyourself'] = 'Assessment by yourself';
$string['assessmentdeleted'] = 'Assessment deallocated';
$string['assessmentend'] = 'Assessing not allowed after';
$string['assessmentform'] = 'Assessment form';
$string['assessmentreferenceneeded'] = 'You have to assess this example submission to provide a reference assessment. Click \'Continue\' button to assess the submission.';
$string['assessmentreference'] = 'Reference assessment';
$string['assessmentsettings'] = 'Assessment settings';
$string['assessmentstart'] = 'Assessing not allowed before';
$string['assignedassessments'] = 'Assigned submissions to assess';
$string['assignedassessmentsnone'] = 'You have no assigned submission to assess';
$string['backtoeditform'] = 'Back to editing form';
$string['byfullname'] = 'by <a href=\"{$a->url}\">{$a->name}</a>';
$string['calculategradinggrades'] = 'Calculate assessment grades';
$string['calculategradinggradesdetails'] = 'expected: $a->expected<br />calculated: $a->calculated';
$string['calculatesubmissiongrades'] = 'Calculate submission grades';
$string['calculatesubmissiongradesdetails'] = 'expected: $a->expected<br />calculated: $a->calculated';
$string['configexamplesmode'] = 'Default mode of examples assessment in workshops';
$string['configgradedecimals'] = 'Default number of digits that should be shown after the decimal point when displaying grades.';
$string['configgrade'] = 'Default maximum grade for submission in workshops';
$string['configgradinggrade'] = 'Default maximum grade for assessment in workshops';
$string['configmaxbytes'] = 'Default maximum submission file size for all workshops on the site (subject to course limits and other local settings)';
$string['configstrategy'] = 'Default grading strategy for workshops';
$string['editassessmentform'] = 'Edit assessment form';
$string['editassessmentformstrategy'] = 'Edit assessment form ($a)';
$string['editingassessmentform'] = 'Editing assessment form';
$string['editingsubmission'] = 'Editing submission';
$string['editsubmission'] = 'Edit submission';
$string['err_removegrademappings'] = 'Unable to remove the unused grade mappings';
$string['evaluategradeswait'] = 'Please wait until the assessments are evaluated and the grades are calculated';
$string['evaluation'] = 'Grading evaluation';
$string['evaluationmethod'] = 'Grading evaluation method';
$string['exampleadd'] = 'Add example submission';
$string['exampleassess'] = 'Assess example submission';
$string['exampleassessments'] = 'Example submissions to assess';
$string['examplecomparing'] = 'Comparing assessments of example submission';
$string['exampledeleteconfirm'] = 'Are you sure you want to delete the following example submission? Click \'Continue\' button to delete the submission.';
$string['exampledelete'] = 'Delete example';
$string['exampleedit'] = 'Edit example';
$string['exampleediting'] = 'Editing example';
$string['example'] = 'Example submission';
$string['examplegrade'] = 'Grade: {$a->received} of {$a->max}';
$string['examplesbeforeassessment'] = 'Examples are available after own submission and must be assessed before assessment phase';
$string['examplesbeforesubmission'] = 'Examples must be assessed before own submission';
$string['examplesmode'] = 'Mode of examples assessment';
$string['examplesubmissions'] = 'Example submissions';
$string['examplesvoluntary'] = 'Assessment of example submission is voluntary';
$string['feedbackauthor'] = 'Feedback for the author';
$string['feedbackreviewer'] = 'Feedback for the reviewer';
$string['formataggregatedgrade'] = '$a->grade';
$string['formataggregatedgradeover'] = '<del>$a->grade</del><br /><ins>$a->over</ins>';
$string['formatpeergradeover'] = '<span class=\"grade\">$a->grade</span> <span class=\"gradinggrade\">(<del>$a->gradinggrade</del> / <ins>$a->gradinggradeover</ins>)</span>';
$string['formatpeergradeoverweighted'] = '<span class=\"grade\">$a->grade</span> <span class=\"gradinggrade\">(<del>$a->gradinggrade</del> / <ins>$a->gradinggradeover</ins>)</span> @ <span class=\"weight\">{$a->weight}</span>';
$string['formatpeergrade'] = '<span class=\"grade\">$a->grade</span> <span class=\"gradinggrade\">({$a->gradinggrade})</span>';
$string['formatpeergradeweighted'] = '<span class=\"grade\">$a->grade</span> <span class=\"gradinggrade\">({$a->gradinggrade})</span> @ <span class=\"weight\">{$a->weight}</span>';
$string['givengrades'] = 'Given grades';
$string['gradecalculated'] = 'Calculated grade for submission';
$string['gradedecimals'] = 'Decimal places in grades';
$string['gradegivento'] = ' &gt; ';
$string['gradeitemassessment'] = '$a->workshopname (assessment)';
$string['gradeitemsubmission'] = '$a->workshopname (submission)';
$string['gradeover'] = 'Override grade for submission';
$string['gradereceivedfrom'] = ' &lt; ';
$string['gradinggradecalculated'] = 'Calculated grade for assessment';
$string['gradinggrade'] = 'Grade for assessment';
$string['gradinggradeof'] = 'Grade for assessment (of $a)';
$string['gradinggradeover'] = 'Override grade for assessment';
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
$string['messageclose'] = '(X)';
$string['modulenameplural'] = 'Workshops';
$string['modulename'] = 'Workshop';
$string['mysubmission'] = 'My submission';
$string['nattachments'] = 'Maximum number of submission attachments';
$string['noexamplesformready'] = 'You must define the assessment form before providing example submissions';
$string['noexamples'] = 'No examples yet in this workshop';
$string['nogradeyet'] = 'No grade yet';
$string['nosubmissionfound'] = 'No submission found for this user';
$string['nosubmissions'] = 'No submissions yet in this workshop';
$string['nothingtoreview'] = 'Nothing to review';
$string['notoverridden'] = 'Not overriden';
$string['noworkshops'] = 'There are no workshops in this course';
$string['noyoursubmission'] = 'You have not submitted your work yet';
$string['nullgrade'] = '-';
$string['participant'] = 'Participant';
$string['participantrevierof'] = 'Participant is reviewer of';
$string['participantreviewedby'] = 'Participant is reviewed by';
$string['phaseassessment'] = 'Assessment phase';
$string['phaseclosed'] = 'Closed';
$string['phaseevaluation'] = 'Grading evaluation phase';
$string['phasesetup'] = 'Setup phase';
$string['phasesubmission'] = 'Submission phase';
$string['prepareexamples'] = 'Prepare example submissions';
$string['previewassessmentform'] = 'Preview';
$string['reassess'] = 'Re-assess';
$string['receivedgrades'] = 'Received grades';
$string['saveandclose'] = 'Save and close';
$string['saveandcontinue'] = 'Save and continue editing';
$string['saveandpreview'] = 'Save and preview';
$string['selfassessmentdisabled'] = 'Self-assessment disabled';
$string['someuserswosubmission'] = 'There are some users who have not submitted yet';
$string['sortasc'] = 'Ascending sort';
$string['sortdesc'] = 'Descending sort';
$string['strategyaccumulative'] = 'Accumulative grading';
$string['strategy'] = 'Grading strategy';
$string['strategyhaschanged'] = 'The workshop grading strategy has changed since the form was opened for editing.';
$string['strategynograding'] = 'No grading';
$string['strategyrubric'] = 'Rubric grading';
$string['submissionattachment'] = 'Attachment';
$string['submissioncontent'] = 'Submission content';
$string['submissionend'] = 'Submitting not allowed after';
$string['submissiongrade'] = 'Grade for submission';
$string['submissiongradeof'] = 'Grade for submission (of $a)';
$string['submissionsettings'] = 'Submission settings';
$string['submissionstart'] = 'Submitting not allowed before';
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
$string['workshop:allocate'] = 'Allocate submissions for review';
$string['workshop:editdimensions'] = 'Edit assessment forms';
$string['workshopfeatures'] = 'Workshop features';
$string['workshop:manageexamples'] = 'Manage example submissions';
$string['workshopname'] = 'Workshop name';
$string['workshop:overridegrades'] = 'Override calculated grades';
$string['workshop:peerassess'] = 'Peer assess';
$string['workshop:publishsubmissions'] = 'Publish submissions';
$string['workshop:submit'] = 'Submit';
$string['workshop:switchphase'] = 'Switch phase';
$string['workshop:viewallassessments'] = 'View all assessments';
$string['workshop:viewallsubmissions'] = 'View all submissions';
$string['workshop:viewauthornames'] = 'View author names';
$string['workshop:viewpublishedsubmissions'] = 'View published submissions';
$string['workshop:viewreviewernames'] = 'View reviewer names';
$string['workshop:view'] = 'View workshop';
$string['yoursubmission'] = 'Your submission';
