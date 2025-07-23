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
 * Strings for component 'assign', language 'en'
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['activityattachments'] = 'Assignment activity attachments';
$string['activitydate:submissionsdue'] = 'Due:';
$string['activitydate:submissionsopen'] = 'Opens:';
$string['activitydate:submissionsopened'] = 'Opened:';
$string['activityeditor'] = 'Activity instructions';
$string['activityeditor_help'] = 'The actions you would like the student to complete for this assignment. This is only shown on the submission page where a student edits and submits their assignment.';
$string['activityoverview'] = 'You have assignments that need attention';
$string['addattempt'] = 'Allow another attempt';
$string['addnewattempt'] = 'Add a new attempt';
$string['addnewattempt_help'] = 'This will create a new blank submission for you to work on.';
$string['addnewattemptfromprevious'] = 'Add a new attempt based on previous submission';
$string['addnewattemptfromprevious_help'] = 'This will copy the contents of your previous submission to a new submission for you to work on.';
$string['addnewgroupoverride'] = 'Add group override';
$string['addnewuseroverride'] = 'Add user override';
$string['addsubmission'] = 'Add submission';
$string['addsubmission_help'] = 'You have not made a submission yet.';
$string['allocatedmarker'] = 'Allocated marker';
$string['allocatedmarker_help'] = 'Marker allocated to this submission.';
$string['allowsubmissions'] = 'Allow the user to continue making submissions to this assignment.';
$string['allowsubmissionsshort'] = 'Allow submission changes';
$string['allowsubmissionsfromdate'] = 'Allow submissions from';
$string['allowsubmissionsfromdate_help'] = 'If enabled, students will not be able to submit before this date. If disabled, students will be able to start submitting right away.';
$string['alwaysshowdescription'] = 'Always show description';
$string['alwaysshowdescription_help'] = 'If disabled, the assignment description above will only become visible to students on the "Allow submissions from" date.';
$string['applytoteam'] = 'Apply grades and feedback to entire group';
$string['assign:addinstance'] = 'Add a new assignment';
$string['assign:exportownsubmission'] = 'Export own submission';
$string['assign:editothersubmission'] = 'Edit another student\'s submission';
$string['assign:grade'] = 'Grade assignment';
$string['assign:grantextension'] = 'Grant extension';
$string['assign:manageallocations'] = 'Manage markers allocated to submissions';
$string['assign:managegrades'] = 'Review and release grades';
$string['assign:manageoverrides'] = 'Manage assignment overrides';
$string['assign:receivegradernotifications'] = 'Receive grader submission notifications';
$string['assign:releasegrades'] = 'Release grades';
$string['assign:revealidentities'] = 'Reveal student identities';
$string['assign:reviewgrades'] = 'Review grades';
$string['assign:viewblinddetails'] = 'View student identities when anonymous submissions are enabled';
$string['assign:viewgrades'] = 'View grades';
$string['assign:showhiddengrader'] = 'See the identity of a hidden grader';
$string['assign:submit'] = 'Submit assignment';
$string['assign:view'] = 'View assignment';
$string['assign:viewownsubmissionsummary'] = 'View own submission summary';
$string['assignfeedback'] = 'Feedback plugin';
$string['assignfeedbackpluginname'] = 'Feedback plugin';
$string['assignmentduedigesthtml'] = '<p>Hi {$a->firstname},</p>
<p>The following assignments are due on <strong>{$a->duedate}</strong>.</p>
{$a->digest}';
$string['assignmentduedigestitem'] = '<strong>{$a->assignmentname}</strong> in course {$a->coursename}<br/>
<strong>Due: {$a->duetime}</strong><br/>
<a href="{$a->url}" aria-label="Go to {$a->assignmentname}">Go to activity</a>';
$string['assignmentduedigestsubject'] = 'You have assignments due in 7 days';
$string['assignmentduesoonhtml'] = '<p>Hi {$a->firstname},</p>
<p>The assignment <strong>{$a->assignmentname}</strong> in course {$a->coursename} is due soon.</p>
<p><strong>Due: {$a->duedate}</strong></p>
<p><a href="{$a->url}">Go to activity</a></p>';
$string['assignmentoverduehtml'] = '<p>Hi {$a->firstname},</p>
<p><strong>{$a->assignmentname}</strong> in course {$a->coursename} was due on <strong>{$a->duedate}</strong>.</p>
<p>You might still be able to submit your assignment{$a->cutoffsnippet}, but your submission will be marked as late.</p>
<p><a href="{$a->url}">Go to activity</a></p>';
$string['assignmentoverduehtmlcutoffsnippet'] = ' <strong>by {$a->cutoffdate}</strong>';
$string['assignmentduesoonsubject'] = 'Due on {$a->duedate}: {$a->assignmentname}';
$string['assignmentoverduesubject'] = 'Overdue: {$a->assignmentname}';
$string['assignmentisdue'] = 'Assignment is due';
$string['assignmentmail'] = '{$a->grader} has posted some feedback on your
assignment submission for \'{$a->assignment}\'

You can see it appended to your assignment submission:

    {$a->url}';
$string['assignmentmailhtml'] = '<p>{$a->grader} has posted some feedback on your
assignment submission for \'<i>{$a->assignment}</i>\'.</p>
<p>You can see it appended to your <a href="{$a->url}">assignment submission</a>.</p>';
$string['assignmentmailsmall'] = '{$a->grader} has posted some feedback on your
assignment submission for \'{$a->assignment}\' You can see it appended to your submission';
$string['assignmentname'] = 'Assignment name';
$string['assignmentplugins'] = 'Assignment plugins';
$string['assignmentsperpage'] = 'Assignments per page';
$string['assignsubmission'] = 'Submission plugin';
$string['assignsubmissionpluginname'] = 'Submission plugin';
$string['assigntimeleft'] = 'Time left';
$string['attemptheading'] = 'Attempt {$a->attemptnumber}: {$a->submissionsummary}';
$string['attempthistory'] = 'Previous attempts';
$string['attemptnumber'] = 'Attempt number';
$string['attemptsettings'] = 'Attempt settings';
$string['attemptreopenmethod'] = 'Grant attempts';
$string['attemptreopenmethod_automatic'] = 'Automatically';
$string['attemptreopenmethod_automatic_help'] = 'After each attempt, the next is granted automatically.';
$string['attemptreopenmethod_help'] = 'This setting controls how students are granted attempts for this assignment. For each attempt, the grade and feedback are saved, and can be viewed by the teacher and the student. The available options are:

* Manually - After each attempt, you can grant the next one through the Submissions page or the Grader page.
* Automatically - After each attempt, the next is granted automatically.
* Automatically until pass - After each attempt, the next will be granted automatically, until the student achieves the passing grade.';
$string['attemptreopenmethod_manual'] = 'Manually';
$string['attemptreopenmethod_manual_help'] = 'After each attempt, you can grant the next one through the Submissions page or the Grader page.';
$string['attemptreopenmethod_untilpass'] = 'Automatically until pass';
$string['attemptreopenmethod_untilpass_help'] = 'After each attempt, the next will be granted automatically, until the student achieves the passing grade.';
$string['availability'] = 'Availability';
$string['back'] = 'Back';
$string['backtoassignment'] = 'Back to assignment';
$string['batchoperationaddattempt'] = 'Grant attempt';
$string['batchoperationsdescription'] = 'With selected...';
$string['batchoperationconfirmlock'] = 'Lock all selected submissions?';
$string['batchoperationconfirmgrantextension'] = 'Grant an extension to all selected submissions?';
$string['batchoperationconfirmunlock'] = 'Unlock all selected submissions?';
$string['batchoperationconfirmremovesubmission'] = 'Remove selected submissions?';
$string['batchoperationconfirmreverttodraft'] = 'Revert selected submissions to draft?';
$string['batchoperationconfirmaddattempt'] = 'Allow another attempt for selected submissions?';
$string['batchoperationconfirmsetmarkingworkflowstate'] = 'Set marking workflow state for all selected submissions?';
$string['batchoperationconfirmsetmarkingallocation'] = 'Set marking allocation for all selected submissions?';
$string['batchoperationconfirmdownloadselected'] = 'Download selected submissions?';
$string['batchoperationdownloadselected'] = 'Download';
$string['batchoperationgrantextension'] = 'Extend';
$string['batchoperationlock'] = 'Lock';
$string['batchoperationremovesubmission'] = 'Delete';
$string['batchoperationunlock'] = 'Unlock';
$string['batchoperationreverttodraft'] = 'Revert to draft';
$string['batchoperationsetmarkingallocation'] = 'Allocate marker';
$string['batchoperationsetmarkingworkflowstate'] = 'Change marking state';
$string['batchsetallocatedmarker'] = 'Set allocated marker for {$a} selected user(s).';
$string['batchsetmarkingworkflowstateforusers'] = 'Set marking workflow state for {$a} selected user(s).';
$string['beginassignment'] = 'Begin assignment';
$string['blindmarking'] = 'Anonymous submissions';
$string['blindmarkingenabledwarning'] = 'Anonymous submissions are enabled for this activity.';
$string['blindmarkingnogradewarning'] = 'Anonymous submissions are enabled for this activity. Grades will not be added to the gradebook until student identities are revealed via the "Actions" menu.';
$string['blindmarking_help'] = 'Anonymous submissions hide the identity of students from markers. Anonymous submission settings will be locked once a submission or grade has been made in relation to this assignment.';
$string['cachedef_overrides'] = 'User and group override information';
$string['calendardue'] = '{$a} is due';
$string['calendarextension'] = '{$a} is due (extension)';
$string['calendargradingdue'] = '{$a} is due to be graded';
$string['caneditsubmission'] = 'You can edit your submission and submit it after the time limit has expired, but it will be marked as late.';
$string['changeuser'] = 'Change user';
$string['changefilters'] = 'Change filters';
$string['choosemarker'] = 'Choose...';
$string['chooseoperation'] = 'Choose operation';
$string['clickexpandreviewpanel'] = 'Click to expand review panel';
$string['collapsegradepanel'] = 'Collapse grade panel';
$string['collapsereviewpanel'] = 'Collapse review panel';
$string['comment'] = 'Comment';
$string['completiondetail:submit'] = 'Make a submission';
$string['completionsubmit'] = 'Make a submission';
$string['conversionexception'] = 'Could not convert assignment. Exception was: {$a}.';
$string['configshowrecentsubmissions'] = 'Everyone can see notifications of submissions in recent activity reports.';
$string['confirmsubmission'] = 'Are you sure you want to submit your work for grading? You will not be able to make any more changes.';
$string['confirmsubmissionheading'] = 'Confirm submission';
$string['confirmbatchgradingoperation'] = 'Are you sure you want to {$a->operation} for {$a->count} students?';
$string['couldnotconvertgrade'] = 'Could not convert assignment grade for user {$a}.';
$string['couldnotconvertsubmission'] = 'Could not convert assignment submission for user {$a}.';
$string['couldnotcreatecoursemodule'] = 'Could not create course module.';
$string['couldnotcreatenewassignmentinstance'] = 'Could not create new assignment instance.';
$string['couldnotfindassignmenttoupgrade'] = 'Could not find old assignment instance to upgrade.';
$string['crontask'] = 'Background processing for assignment module';
$string['currentassigngrade'] = 'Current grade in assignment';
$string['currentgrade'] = 'Current grade in gradebook';
$string['currentattempt'] = 'This is attempt {$a}.';
$string['currentattemptof'] = 'This is attempt {$a->attemptnumber} ( {$a->maxattempts} attempts allowed ).';
$string['cutoffdate'] = 'Cut-off date';
$string['cutoffdatecolon'] = 'Cut-off date: {$a}';
$string['cutoffdate_help'] = 'If set, submissions will not be accepted after this date without an extension. If not set, submissions will always be accepted.';
$string['cutoffdatevalidation'] = 'Cut-off date cannot be earlier than the due date.';
$string['cutoffdatefromdatevalidation'] = 'Cut-off date cannot be earlier than the allow submissions from date.';
$string['defaultgradescale'] = 'Grading scale';
$string['defaultgradescale_help'] = 'The default grading scale for new assignments. Only standard scales are available.';
$string['defaultgradetype'] = 'Grade type';
$string['defaultgradetype_help'] = 'The default grade type for new assignments.';
$string['defaultlayout'] = 'Restore default layout';
$string['defaultsettings'] = 'Default assignment settings';
$string['defaultsettings_help'] = 'These settings define the defaults for all new assignments.';
$string['defaultteam'] = 'Default group';
$string['deleteallsubmissions'] = 'All submissions';
$string['description'] = 'Description';
$string['disabled'] = 'Disabled';
$string['downloadall'] = 'Download all submissions';
$string['download all submissions'] = 'Download all submissions in a zip file.';
$string['downloadasfolders'] = 'Download submissions in folders';
$string['downloadselectedsubmissions'] = 'Download selected submissions';
$string['prefixwithusername'] = 'Include username in filename';
$string['prefixwithusername_help'] = 'Username is included in filename as well as fullname.';
$string['duedate'] = 'Due date';
$string['duedatecolon'] = 'Due date: {$a}';
$string['duedate_help'] = 'This is when the assignment is due. Submissions will still be allowed after this date, but any assignments submitted after this date will be marked as late. Set an assignment cut-off date to prevent submissions after a certain date.';
$string['duplicateoverride'] = 'Duplicate override';
$string['submissionempty'] = 'Nothing was submitted';
$string['submissionmodified'] = 'You have existing submission data. Please leave this page and try again.';
$string['submissionmodifiedgroup'] = 'The submission has been modified by somebody else. Please leave this page and try again.';
$string['duedatereached'] = 'The due date for this assignment has now passed';
$string['duedateaftersubmissionvalidation'] = 'Due date must be after the allow submissions from date.';
$string['duedatevalidation'] = 'Due date cannot be earlier than the allow submissions from date.';
$string['editattemptfeedback'] = 'Edit the grade and feedback for attempt number {$a}.';
$string['editonline'] = 'Edit online';
$string['editingpreviousfeedbackwarning'] = 'You are editing the feedback for a previous attempt. This is attempt {$a->attemptnumber} out of {$a->totalattempts}.';
$string['editoverride'] = 'Edit override';
$string['editsubmission'] = 'Edit submission';
$string['editsubmissionother'] = 'Edit submission for {$a}';
$string['editsubmission_help'] = 'You can still make changes to your submission.';
$string['editingstatus'] = 'Editing status';
$string['enabled'] = 'Enabled';
$string['enabletimelimit'] = 'Enable timed assignments';
$string['enabletimelimit_help'] = 'If enabled, you can set a time limit on assignment settings page.';
$string['errorgradechangessaveddetail'] = 'The changes to the grade and feedback were not saved.';
$string['eventallsubmissionsdownloaded'] = 'All the submissions are being downloaded.';
$string['eventassessablesubmitted'] = 'A submission has been submitted.';
$string['eventbatchsetmarkerallocationviewed'] = 'Batch set marker allocation viewed';
$string['eventbatchsetworkflowstateviewed'] = 'Batch set workflow state viewed.';
$string['eventextensiongranted'] = 'An extension has been granted.';
$string['eventfeedbackupdated'] = 'Feedback updated';
$string['eventfeedbackviewed'] = 'Feedback viewed';
$string['eventgradingformviewed'] = 'Grading form viewed';
$string['eventgradingtableviewed'] = 'Grading table viewed';
$string['eventidentitiesrevealed'] = 'The identities have been revealed.';
$string['eventmarkerupdated'] = 'The allocated marker has been updated.';
$string['eventoverridecreated'] = 'Assignment override created';
$string['eventoverridedeleted'] = 'Assignment override deleted';
$string['eventoverrideupdated'] = 'Assignment override updated';
$string['eventremovesubmissionformviewed'] = 'Remove submission confirmation viewed.';
$string['eventrevealidentitiesconfirmationpageviewed'] = 'Reveal identities confirmation page viewed.';
$string['eventstatementaccepted'] = 'The user has accepted the statement of the submission.';
$string['eventsubmissionconfirmationformviewed'] = 'Submission confirmation form viewed.';
$string['eventsubmissioncreated'] = 'Submission created.';
$string['eventsubmissionduplicated'] = 'The user duplicated their submission.';
$string['eventsubmissionformviewed'] = 'Submission form viewed.';
$string['eventsubmissiongraded'] = 'The submission has been graded.';
$string['eventsubmissionlocked'] = 'The submissions have been locked for a user.';
$string['eventsubmissionremoved'] = 'Submission removed.';
$string['eventsubmissionstatusupdated'] = 'The status of the submission has been updated.';
$string['eventsubmissionstatusviewed'] = 'The status of the submission has been viewed.';
$string['eventsubmissionunlocked'] = 'The submissions have been unlocked for a user.';
$string['eventsubmissionupdated'] = 'Submission updated.';
$string['eventsubmissionviewed'] = 'Submission viewed.';
$string['eventworkflowstateupdated'] = 'The state of the workflow has been updated.';
$string['expandreviewpanel'] = 'Expand review panel';
$string['extensionduedate'] = 'Extension due date';
$string['extensionduedatecurrent'] = 'Current extension due date';
$string['extensionduedatenone'] = 'None';
$string['extensionduedaterange'] = 'Various between {$a->earliest} and {$a->latest}';
$string['extensionduedatewithout'] = 'Users with no current extension: {$a}';
$string['extensionnotafterduedate'] = 'Extension date must be after the due date';
$string['extensionnotafterfromdate'] = 'Extension date must be after the allow submissions from date';
$string['fixrescalednullgrades'] = 'This assignment contains some erroneous grades. You can <a href="{$a->link}">automatically fix these grades</a>. This may affect course totals.';
$string['fixrescalednullgradesconfirm'] = 'Are you sure you want to fix erroneous grades? All affected grades will be removed. This may affect course totals.';
$string['fixrescalednullgradesdone'] = 'Grades fixed.';
$string['gradeactions'] = 'Grade actions';
$string['gradecanbechanged'] = 'Grade can be changed';
$string['gradeitem:submissions'] = 'Submissions';
$string['gradersubmissionupdatedtext'] = '{$a->username} has updated their assignment submission
for \'{$a->assignment}\' at {$a->timeupdated}

It is available here:

    {$a->url}';
$string['gradersubmissionupdatedhtml'] = '{$a->username} has updated their assignment submission
for <i>\'{$a->assignment}\'  at {$a->timeupdated}</i><br /><br />
It is <a href="{$a->url}">available on the web site</a>.';
$string['gradersubmissionupdatedsmall'] = '{$a->username} has updated their submission for assignment {$a->assignment}.';
$string['gradeuser'] = 'Grade {$a}';
$string['grantextension'] = 'Grant extension';
$string['grantextensionforusers'] = 'Grant extension for {$a} students';
$string['groupsubmissionsettings'] = 'Group submission settings';
$string['errornosubmissions'] = 'There are no submissions to download';
$string['errorquickgradingvsadvancedgrading'] = 'The grades were not saved because this assignment is currently using advanced grading';
$string['errorrecordmodified'] = 'The grades were not saved because someone has modified one or more records more recently than when you loaded the page.';
$string['feedback'] = 'Feedback';
$string['feedbackavailabletext'] = '{$a->username} has posted some feedback on your
assignment submission for \'{$a->assignment}\'

You can see it appended to your assignment submission:

    {$a->url}';
$string['feedbackavailablehtml'] = '{$a->username} has posted some feedback on your
assignment submission for \'<i>{$a->assignment}</i>\'<br /><br />
You can see it appended to your <a href="{$a->url}">assignment submission</a>.';
$string['feedbackavailablesmall'] = '{$a->username} has given feedback for assignment {$a->assignment}';
$string['feedbackavailableanontext'] = 'You have new feedback on your
assignment submission for \'{$a->assignment}\'

You can see it appended to your assignment submission:

    {$a->url}';
$string['feedbackavailableanonhtml'] = 'You have new feedback on your
assignment submission for \'<i>{$a->assignment}</i>\'<br /><br />
You can see it appended to your <a href="{$a->url}">assignment submission</a>.';
$string['feedbackavailableanonsmall'] = 'New feedback for assignment {$a->assignment}';
$string['feedbackplugins'] = 'Feedback plugins';
$string['feedbackpluginforgradebook'] = 'Feedback plugin that will push comments to the gradebook';
$string['feedbackpluginforgradebook_help'] = 'Only one assignment feedback plugin can push feedback into the gradebook.';
$string['feedbackplugin'] = 'Feedback plugin';
$string['feedbacksettings'] = 'Feedback settings';
$string['feedbacktypes'] = 'Feedback types';
$string['filesubmissions'] = 'File submissions';
$string['filter'] = 'Filter';
$string['filterall'] = 'All';
$string['filterdraft'] = 'Draft';
$string['filtergrantedextension'] = 'Granted extension';
$string['filternone'] = 'No filter';
$string['filternotsubmitted'] = 'Not submitted';
$string['filterrequiregrading'] = 'Requires grading';
$string['filtersubmitted'] = 'Submitted';
$string['graded'] = 'Graded';
$string['gradedby'] = 'Graded by';
$string['gradedfollowupsubmit'] = 'Graded - resubmitted';
$string['gradedon'] = 'Graded on';
$string['gradebelowzero'] = 'Grade must be greater than or equal to zero.';
$string['gradebreakdown'] = 'Grade breakdown';
$string['gradeabovemaximum'] = 'Grade must be less than or equal to {$a}.';
$string['gradelocked'] = 'This grade is locked or overridden in the gradebook.';
$string['gradeoutof'] = 'Grade out of {$a}';
$string['gradeoutofhelp'] = 'Grade';
$string['gradeoutofhelp_help'] = 'Enter the grade for the student\'s submission here. You may include decimals.';
$string['gradestudent'] = 'Grade student: (id={$a->id}, fullname={$a->fullname}). ';
$string['grading'] = 'Grading';
$string['gradingchangessaved'] = 'The grade changes were saved';
$string['gradingduedate'] = 'Remind me to grade by';
$string['gradingduedate_help'] = 'The expected date that marking of the submissions should be completed by. This date is used to prioritise dashboard notifications for teachers.';
$string['gradingdueduedatevalidation'] = 'Remind me to grade by date cannot be earlier than the due date.';
$string['gradingduefromdatevalidation'] = 'Remind me to grade by date cannot be earlier than the allow submissions from date.';
$string['gradechangessaveddetail'] = 'The changes to the grade and feedback were saved';
$string['gradingmethodpreview'] = 'Grading criteria';
$string['gradingstatus'] = 'Grading status';
$string['gradingstudent'] = 'Grading student';
$string['gradingsummary'] = 'Grading summary';
$string['groupoverrides'] = 'Group overrides';
$string['groupsnone'] = 'No groups you can access.';
$string['hidegrader'] = 'Hide grader identity from students';
$string['hidegrader_help'] = 'If enabled, the identity of any user who grades an assignment submission is not shown, so students can\'t see who marked their work.

Note that this setting has no effect on the comments box on the grading page.';
$string['hideshow'] = 'Hide/Show';
$string['hiddenuser'] = 'Participant ';
$string['inactiveoverridehelp'] = '* This override is inactive because the user\'s access to the activity is restricted. This can be due to group or role assignments, other access restrictions, or the activity being hidden.';
$string['includesuspendedparticipants'] = 'Include suspended participants';
$string['indicator:cognitivedepth'] = 'Assignment cognitive';
$string['indicator:cognitivedepth_help'] = 'This indicator is based on the cognitive depth reached by the student in an assignment activity.';
$string['indicator:cognitivedepthdef'] = 'Assignment cognitive';
$string['indicator:cognitivedepthdef_help'] = 'The participant has reached this percentage of the cognitive engagement offered by the Assignment activities during this analysis interval (Levels = No view, View, Submit, View feedback, Comment on feedback, Resubmit after viewing feedback)';
$string['indicator:cognitivedepthdef_link'] = 'Learning_analytics_indicators#Cognitive_depth';
$string['indicator:socialbreadth'] = 'Assignment social';
$string['indicator:socialbreadth_help'] = 'This indicator is based on the social breadth reached by the student in an assignment activity.';
$string['indicator:socialbreadthdef'] = 'Assignment social';
$string['indicator:socialbreadthdef_help'] = 'The participant has reached this percentage of the social engagement offered by the Assignment activities during this analysis interval (Levels = No participation, Participant alone, Participant with others)';
$string['indicator:socialbreadthdef_link'] = 'Learning_analytics_indicators#Social_breadth';
$string['instructionfiles'] = 'Instruction files';
$string['introattachments'] = 'Additional files';
$string['introattachments_help'] = 'Additional files for use in the assignment, such as answer templates, may be added.';
$string['invalidgradeforscale'] = 'The grade supplied was not valid for the current scale';
$string['invalidfloatforgrade'] = 'The grade provided could not be understood: {$a}';
$string['invalidoverrideid'] = 'Invalid override id';
$string['lastmodifiedsubmission'] = 'Last modified (submission)';
$string['lastmodifiedgrade'] = 'Last modified (grade)';
$string['latesubmissions'] = 'Late submissions';
$string['latesubmissionsaccepted'] = 'Allowed until {$a}';
$string['loading'] = 'Loading...';
$string['locksubmissionforstudent'] = 'Prevent any more submissions for student: (id={$a->id}, fullname={$a->fullname}).';
$string['locksubmissions'] = 'Lock submissions';
$string['manageassignfeedbackplugins'] = 'Manage assignment feedback plugins';
$string['manageassignsubmissionplugins'] = 'Manage assignment submission plugins';
$string['marker'] = 'Marker';
$string['markerfilter'] = 'Marker filter';
$string['markerfilternomarker'] = 'No marker';
$string['markingallocation'] = 'Use marking allocation';
$string['markingallocation_help'] = 'If enabled together with marking workflow, markers can be allocated to particular students.';
$string['markinganonymous'] = 'Allow partial release of grades while marking anonymously';
$string['markinganonymous_help'] = 'If enabled together with anonymous submissions and marking workflow, it allows partial release of grades while marking anonymously.';
$string['markingstate'] = 'Marking state';
$string['markingworkflow'] = 'Use marking workflow';
$string['markingworkflow_help'] = 'If enabled, marks will go through a series of workflow stages before being released to students. This allows for multiple rounds of marking and allows marks to be released to all students at the same time.';
$string['markingworkflowstate'] = 'Marking workflow state';
$string['markingworkflowstate_help'] = 'Possible workflow states may include (depending on your permissions):

* Not marked - the marker has not yet started
* In marking - the marker has started but not yet finished
* Marking completed - the marker has finished but might need to go back for checking/corrections
* In review - the marking is now with the teacher in charge for quality checking
* Ready for release - the teacher in charge is satisfied with the marking but may wait before giving students access to the marking
* Released - the student can access the grades/feedback';
$string['markingworkflowstateinmarking'] = 'In marking';
$string['markingworkflowstateinreview'] = 'In review';
$string['markingworkflowstatenotmarked'] = 'Not marked';
$string['markingworkflowstatereadyforreview'] = 'Marking completed';
$string['markingworkflowstatereadyforrelease'] = 'Ready for release';
$string['markingworkflowstatereleased'] = 'Released';
$string['maxattempts'] = 'Allowed attempts';
$string['maxattempts_help'] = 'The maximum number of submission attempts that can be made by a student. After this number has been reached, the submission can no longer be reopened.';
$string['maxgrade'] = 'Maximum grade';
$string['maxperpage'] = 'Maximum assignments per page';
$string['maxperpage_help'] = 'The maximum number of assignments a grader can show in the assignment grading page. This setting is useful in preventing timeouts for courses with a large number of participants.';
$string['messageprovider:assign_due_digest'] = 'Assignments due in 7 days notification';
$string['messageprovider:assign_due_soon'] = 'Assignment due soon notification';
$string['messageprovider:assign_overdue'] = 'Assignment overdue notification';
$string['messageprovider:assign_notification'] = 'Assignment notifications';
$string['modulename'] = 'Assignment';
$string['modulename_help'] = 'The assignment activity module enables a teacher to communicate tasks, collect work and provide grades and feedback.

Students can submit any digital content (files), such as word-processed documents, spreadsheets, images, or audio and video clips. Alternatively, or in addition, the assignment may require students to type text directly into the text editor. An assignment can also be used to remind students of \'real-world\' assignments they need to complete offline, such as art work, and thus not require any digital content. Students can submit work individually or as a member of a group.

When reviewing assignments, teachers can leave feedback comments and upload files, such as marked-up student submissions, documents with comments or spoken audio feedback. Assignments can be graded using a numerical or custom scale or an advanced grading method such as a rubric. Final grades are recorded in the gradebook.';
$string['modulename_link'] = 'mod/assignment/view';
$string['modulenameplural'] = 'Assignments';
$string['moreusers'] = '{$a} more...';
$string['multipleteams'] = 'Member of more than one group';
$string['multipleteams_desc'] = 'The assignment requires submission in groups. You are a member of more than one group. To be able to submit you must be a member of only one group. Please contact your teacher to change your group membership.';
$string['multipleteamsgrader'] = 'Member of more than one group, so unable to make submissions.';
$string['nextuser'] = "Next user";
$string['newsubmissions'] = 'Assignments submitted';
$string['noattempt'] = 'No attempt';
$string['noclose'] = 'No close date';
$string['nofilters'] = 'No filters';
$string['nofiles'] = 'No files. ';
$string['nograde'] = 'No grade. ';
$string['nomoresubmissionsaccepted'] = 'Only allowed for participants who have been granted an extension';
$string['none'] = 'None';
$string['noonlinesubmissions'] = 'This assignment does not require you to submit anything online';
$string['noopen'] = 'No open date';
$string['nooverridedata'] = 'You must override at least one of the assignment settings.';
$string['nogroupoverrides'] = 'There are currently no group overrides.';
$string['nouseroverrides'] = 'There are currently no user overrides.';
$string['nosavebutnext'] = 'Next';
$string['nosubmission'] = 'Nothing has been submitted for this assignment';
$string['nosubmissionyet'] = 'No submissions have been made yet';
$string['noteam'] = 'Not a member of any group';
$string['noteam_desc'] = 'This assignment requires submission in groups. You are not a member of any group, so you cannot create a submission. Please contact your teacher to be added to a group.';
$string['noteamgrader'] = 'Not a member of any group, so unable to make submissions.';
$string['notgraded'] = 'Not graded';
$string['notgradedyet'] = 'Not graded yet';
$string['notifications'] = 'Notifications';
$string['notifystudents'] = 'Notify students';
$string['nousersselected'] = 'No users selected';
$string['nousers'] = 'No users';
$string['numberofdraftsubmissions'] = 'Drafts';
$string['numberofparticipants'] = 'Participants';
$string['numberofsubmittedassignments'] = 'Submitted';
$string['numberofsubmissionsneedgrading'] = 'Needs grading';
$string['numberofsubmissionsneedgradinglabel'] = 'Needs grading: {$a}';
$string['numberofteams'] = 'Groups';
$string['offline'] = 'No online submissions required';
$string['open'] = 'Open';
$string['opensubmissionexists'] = 'Open assignment submission already exists.';
$string['outof'] = '{$a->current} out of {$a->total}';
$string['overdue'] = 'Assignment is overdue by: {$a}';
$string['override'] = 'Override';
$string['overridedeletegroupsure'] = 'Are you sure you want to delete the override for group {$a}?';
$string['overridedeleteusersure'] = 'Are you sure you want to delete the override for user {$a}?';
$string['overridegroup'] = 'Override group';
$string['overridegroupeventname'] = '{$a->assign} - {$a->group}';
$string['overrides'] = 'Overrides';
$string['overrideuser'] = 'Override user';
$string['overrideusereventname'] = '{$a->assign} - Override';
$string['outlinegrade'] = 'Grade: {$a}';
$string['page-mod-assign-x'] = 'Any assignment module page';
$string['page-mod-assign-view'] = 'Assignment module main and submission page';
$string['paramtimeremaining'] = '{$a} remaining';
$string['participant'] = 'Participant';
$string['pluginadministration'] = 'Assignment administration';
$string['pluginname'] = 'Assignment';
$string['preventsubmissionnotingroup'] = 'Require group to make submission';
$string['preventsubmissionnotingroup_help'] = 'If enabled, users who are not members of a group will be unable to make submissions.';
$string['preventsubmissions'] = 'Prevent the user from making any more submissions to this assignment.';
$string['preventsubmissionsshort'] = 'Prevent submission changes';
$string['previous'] = 'Previous';
$string['previoususer'] = 'Previous user';
$string['privacy:attemptpath'] = 'attempt {$a}';
$string['privacy:blindmarkingidentifier'] = 'The identifier used for anonymous submissions';
$string['privacy:gradepath'] = 'grade';
$string['privacy:metadata:assigndownloadasfolders'] = 'A user preference for whether multiple file submissions should be downloaded into folders';
$string['privacy:metadata:assignfeedbackpluginsummary'] = 'Feedback data for the assignment.';
$string['privacy:metadata:assignfilter'] = 'Filter options such as \'Submitted\', \'Not submitted\', \'Requires grading\', and \'Granted extension\'';
$string['privacy:metadata:assigngrades'] = 'Stores user grades for the assignment';
$string['privacy:metadata:assignmarkerfilter'] = 'Filter the assign summary by the assigned marker.';
$string['privacy:metadata:assignmentid'] = 'Assignment ID';
$string['privacy:metadata:assignmessageexplanation'] = 'Messages are sent to students through the messaging system.';
$string['privacy:metadata:assignoverrides'] = 'Stores override information for the assignment';
$string['privacy:metadata:assignperpage'] = 'Number of assignments shown per page.';
$string['privacy:metadata:assignquickgrading'] = 'A preference as to whether quick grading is used or not.';
$string['privacy:metadata:assignsubmissiondetail'] = 'Stores user submission information';
$string['privacy:metadata:assignsubmissionpluginsummary'] = 'Submission data for the assignment.';
$string['privacy:metadata:assignuserflags'] = 'Stores user meta data such as extension dates';
$string['privacy:metadata:assignusermapping'] = 'The mapping for anonymous submissions';
$string['privacy:metadata:assignworkflowfilter'] = 'Filter by the different workflow stages.';
$string['privacy:metadata:grade'] = 'The numerical grade for this assignment submission. Can be determined by scales/advancedgradingforms etc but will always be converted back to a floating point number.';
$string['privacy:metadata:grader'] = 'The user ID of the person grading.';
$string['privacy:metadata:groupid'] = 'Group ID that the user is a member of.';
$string['privacy:metadata:latest'] = 'Greatly simplifies queries wanting to know information about only the latest attempt.';
$string['privacy:metadata:mailed'] = 'Has this user been mailed yet?';
$string['privacy:metadata:timecreated'] = 'Time created';
$string['privacy:metadata:timestarted'] = 'Time started';
$string['privacy:metadata:userid'] = 'ID of the user';
$string['privacy:studentpath'] = 'studentsubmissions';
$string['quickgrading'] = 'Quick grading';
$string['quickgradingresult'] = 'Quick grading';
$string['quickgradingchangessaved'] = 'The grade changes were saved';
$string['quickgrading_help'] = 'Quick grading allows you to assign grades (and outcomes) directly in the submissions table. Quick grading is not compatible with advanced grading and is not recommended when there are multiple markers.';
$string['relativedatessubmissiontimeleft'] = 'Calculated for each student';
$string['removeallgroupoverrides'] = 'All group overrides';
$string['removealluseroverrides'] = 'All user overrides';
$string['reopenuntilpassincompatiblewithblindmarking'] = 'Reopen until pass option is incompatible with anonymous submissions, because the grades are not released to the gradebook until the student identities are revealed.';
$string['requiresubmissionstatement'] = 'Require that students accept the submission statement';
$string['requiresubmissionstatement_help'] = 'Require that students accept the submission statement for all submissions to this assignment.';
$string['requireallteammemberssubmit'] = 'Require all group members submit';
$string['requireallteammemberssubmit_help'] = 'This setting must be used together with the \'Require students to click the submit button\' in Submission settings.

If enabled, all group members must click the submit button for the group submission to be considered as submitted. If disabled, any group member can click the submit button.';
$string['recordid'] = 'Identifier';
$string['removesubmission'] = 'Remove submission';
$string['removesubmissionforstudent'] = 'Remove submission for student: (id={$a->id}, fullname={$a->fullname}).';
$string['removesubmissionconfirm'] = 'Are you sure you want to remove your submission?';
$string['removesubmissionconfirmforstudent'] = 'Are you sure you want to remove the submission for {$a}?';
$string['removesubmissionconfirmforstudentwithtimelimit'] = 'Are you sure you want to remove the submission for {$a}? Please note that this will not reset the student\'s time limit. You can give more time by adding a time limit user override.';
$string['removesubmissionconfirmwithtimelimit'] = 'Are you sure you want to remove your submission? Please note that this will not reset your time limit.';
$string['revealidentities'] = 'Reveal student identities';
$string['revealidentitiesconfirm'] = 'Are you sure you want to reveal student identities for this assignment? This operation cannot be undone. Once the student identities have been revealed, the marks will be released to the gradebook.';
$string['reverttodefaults'] = 'Revert to assignment defaults';
$string['reverttodraftforgroup'] = 'Revert submission to draft for group {$a}.';
$string['reverttodraftforstudent'] = 'Revert submission to draft for student: (id={$a->id}, fullname={$a->fullname}).';
$string['reverttodraft'] = 'Revert the submission to draft status';
$string['reverttodraftshort'] = 'Revert the submission to draft';
$string['reviewed'] = 'Reviewed';
$string['save'] = 'Save';
$string['saveandcontinue'] = 'Save and continue';
$string['savechanges'] = 'Save changes';
$string['savegradingresult'] = 'Grade';
$string['savenext'] = 'Save and show next';
$string['savingchanges'] = 'Saving changes...';
$string['saveoverrideandstay'] = 'Save and enter another override';
$string['scale'] = 'Scale';
$string['search:activity'] = 'Assignment - activity information';
$string['sendstudentnotificationsdefault'] = 'Default for \'Notify student\'';
$string['sendstudentnotificationsdefault_help'] = 'When grading each student, should \'Notify student\' be ticked by default?';
$string['sendstudentnotifications'] = 'Notify student';
$string['sendstudentnotifications_help'] = 'Tick this box to send a notification about the updated grade or feedback. If the assignment uses a marking workflow, or the grades are hidden in the grader report, then the notification will not be sent until the grade is released.';
$string['sendnotifications'] = 'Notify graders about submissions';
$string['sendnotifications_help'] = 'If enabled, graders (usually teachers) receive a message whenever a student submits an assignment, early, on time and late. Message methods are configurable.';
$string['selectlink'] = 'Select...';
$string['selectuser'] = 'Select {$a}';
$string['sendlatenotifications'] = 'Notify graders about late submissions';
$string['sendlatenotifications_help'] = 'If enabled, graders (usually teachers) receive a message whenever a student submits an assignment late. Message methods are configurable.';
$string['sendnotificationduedatesoon'] = 'Notify user of an approaching assignment due date';
$string['sendnotificationoverdue'] = 'Notify user of an assignment that is overdue';
$string['sendnotificationduedigest'] = 'Notify user of assignments due in 7 days';
$string['sendsubmissionreceipts'] = 'Send submission receipt to students';
$string['sendsubmissionreceipts_help'] = 'This switch enables submission receipts for students. Students will receive a notification every time they successfully submit an assignment.';
$string['setmarkingallocation'] = 'Set allocated marker';
$string['setmarkingworkflowstate'] = 'Set marking workflow state';
$string['selectedusers'] = 'Selected users';
$string['setmarkingworkflowstateforlog'] = 'Set marking workflow state : (id={$a->id}, fullname={$a->fullname}, state={$a->state}). ';
$string['setmarkerallocationforlog'] = 'Set marking allocation : (id={$a->id}, fullname={$a->fullname}, marker={$a->marker}). ';
$string['settings'] = 'Assignment settings';
$string['showrecentsubmissions'] = 'Show recent submissions';
$string['status'] = 'Status';
$string['studentnotificationworkflowstateerror'] = 'Marking workflow state must be \'Released\' to notify students.';
$string['submissionactions'] = 'Submission actions';
$string['submissionattachments'] = 'Only show files during submission';
$string['submissionattachments_help'] = 'Tick the box to only show files on the submission page. Otherwise, files will be shown on both the assignment and submission pages.';
$string['confirmstart'] = 'You have {$a} to complete this assignment. When you begin, the timer will start to count down and can\'t be paused.';
$string['submissioncopiedtext'] = 'You have made a copy of your previous
assignment submission for \'{$a->assignment}\'

You can see the status of your assignment submission:

    {$a->url}';
$string['submissioncopiedhtml'] = '<p>You have made a copy of your previous
assignment submission for \'<i>{$a->assignment}</i>\'.</p>
<p>You can see the status of your <a href="{$a->url}">assignment submission</a>.</p>';
$string['submissioncopiedsmall'] = 'You have copied your previous assignment submission for {$a->assignment}';
$string['submissiondrafts'] = 'Require students to click the submit button';
$string['submissiondrafts_help'] = 'Should students have a submit button to indicate when they have finished making changes to their submission and it is ready for grading?';
$string['submissioneditable'] = 'Student can edit this submission';
$string['submissionlog'] = 'Student: {$a->fullname}, Status: {$a->status}';
$string['submissionnotcopiedinvalidstatus'] = 'The submission was not copied because it has been edited since it was reopened.';
$string['submissionnoteditable'] = 'Student cannot edit this submission';
$string['submissionnotopen'] = 'This assignment is not open for submissions';
$string['submissionnotready'] = 'This assignment is not ready to submit:';
$string['privacy:submissionpath'] = 'submission';
$string['submissionplugins'] = 'Submission plugins';
$string['submissionreceipts'] = 'Send submission receipts';
$string['submissionreceiptothertext'] = 'Your assignment submission for
\'{$a->assignment}\' has been submitted.

You can see the status of your assignment submission:

    {$a->url}';
$string['submissionreceiptotherhtml'] = 'Your assignment submission for
\'<i>{$a->assignment}</i>\' has been submitted.<br /><br />
You can see the status of your <a href="{$a->url}">assignment submission</a>.';
$string['submissionreceiptothersmall'] = 'Your assignment submission for {$a->assignment} has been submitted.';
$string['submissionreceipttext'] = 'You have submitted an
assignment submission for \'{$a->assignment}\'

You can see the status of your assignment submission:

    {$a->url}';
$string['submissionreceipthtml'] = '<p>You have submitted an assignment submission for \'<i>{$a->assignment}</i>\'.</p>
<p>You can see the status of your <a href="{$a->url}">assignment submission</a>.</p>';
$string['submissionreceiptsmall'] = 'You have submitted your assignment submission for {$a->assignment}';
$string['submissionslocked'] = 'This assignment is not accepting submissions';
$string['submissionslockedshort'] = 'Submission changes not allowed';
$string['submissionsclosed'] = 'Submissions closed';
$string['submissionsettings'] = 'Submission settings';
$string['submissionstatement'] = 'Submission statement';
$string['submissionstatementdefault'] = 'This submission is my own work, except where I have acknowledged the use of the works of other people.';
$string['submissionstatement_help'] = 'Statement that each student must accept in order to submit their work.';
$string['submissionstatementrequired'] = 'You are required to agree to this statement before you can submit.';
$string['submissionstatementteamsubmission'] = 'Group submission statement';
$string['submissionstatementteamsubmissiondefault'] = 'This submission is the work of my group, except where we have acknowledged the use of the works of other people.';
$string['submissionstatementteamsubmission_help'] = 'Statement that each student must accept in order to submit the work of their group.';
$string['submissionstatementteamsubmissionallsubmit'] = 'Group submission statement where all group members submit';
$string['submissionstatementteamsubmissionallsubmitdefault'] = 'This submission is my own work as a group member, except where I have acknowledged the use of the works of other people.';
$string['submissionstatementteamsubmissionallsubmit_help'] = 'Statement that each student must accept in order to submit their work as a group member.';
$string['submissionstatementacceptedlog'] = 'Submission statement accepted by user {$a}';
$string['submissionstatus_draft'] = 'Draft (not submitted)';
$string['submissionstatusheading'] = 'Submission status';
$string['submissionstatus_marked'] = 'Graded';
$string['submissionstatus_new'] = 'No submission';
$string['submissionstatus_reopened'] = 'Reopened';
$string['submissionstatus_'] = 'No submission';
$string['submissionstatus'] = 'Submission status';
$string['submissionstatus_submitted'] = 'Submitted for grading';
$string['submissionsummary'] = '{$a->status}. Last modified on {$a->timemodified}';
$string['submissionteam'] = 'Group';
$string['submissiontypes'] = 'Submission types';
$string['submission'] = 'Submission';
$string['submitaction'] = 'Submit';
$string['submitforgrading'] = 'Submit for grading';
$string['submitassignment_help'] = 'Once this assignment is submitted you will not be able to make any more changes.';
$string['submitassignment'] = 'Submit assignment';
$string['submittedearly'] = 'Assignment was submitted {$a} early';
$string['submittedlate'] = 'Assignment was submitted {$a} late';
$string['submittedovertime'] = 'Assignment was submitted {$a} over the time limit';
$string['submittedundertime'] = 'Assignment was submitted {$a} under the time limit';
$string['submittedlateshort'] = '{$a} late';
$string['submitted'] = 'Submitted';
$string['subpagetitle'] = '{$a->contextname} - {$a->subpage}';
$string['subplugintype_assignsubmission'] = 'Submission plugin';
$string['subplugintype_assignsubmission_plural'] = 'Submission plugins';
$string['subplugintype_assignfeedback'] = 'Feedback plugin';
$string['subplugintype_assignfeedback_plural'] = 'Feedback plugins';
$string['teamname'] = 'Group: {$a}';
$string['teamsubmission'] = 'Students submit in groups';
$string['teamsubmission_help'] = 'If enabled, students will be divided into groups based on the default set of groups or a custom grouping. A group submission will be shared among group members and all members of the group will see each others\' changes to the submission. Note: You cannot change this setting if there are submissions already.';
$string['teamsubmissiongroupingid'] = 'Grouping for student groups';
$string['teamsubmissiongroupingid_help'] = 'This is the grouping that the assignment will use to find groups for student groups. If not set, the default set of groups will be used.';
$string['textinstructions'] = 'Assignment instructions';
$string['timelimit'] = 'Time limit';
$string['timelimit_help'] = 'If enabled, the time limit is stated on the assignment page and a countdown timer is displayed during the assignment.';
$string['timelimitnotenabled'] = 'Time limit is not enabled for assignment.';
$string['timelimitpassed'] = 'The time limit has expired';
$string['timemodified'] = 'Last modified';
$string['timeremaining'] = 'Time remaining';
$string['timeremainingcolon'] = 'Time remaining: {$a}';
$string['togglezoom'] = 'Zoom in/out of region';
$string['ungroupedusers'] = 'The setting \'Require group to make submission\' is enabled and some users are either not a member of any group, or are a member of more than one group, so are unable to make submissions.';
$string['ungroupedusersoptional'] = 'The setting \'Students submit in groups\' is enabled and some users are either not a member of any group, or are a member of more than one group. Please be aware that these students will submit as members of the \'Default group\'.';
$string['unlocksubmissionforstudent'] = 'Allow submissions for student: (id={$a->id}, fullname={$a->fullname}).';
$string['unlocksubmissions'] = 'Unlock submissions';
$string['unlimitedattempts'] = 'Unlimited';
$string['unlimitedattemptsallowed'] = 'Unlimited attempts allowed.';
$string['unlimitedpages'] = 'Unlimited';
$string['unsavedchanges'] = 'Unsaved changes';
$string['unsavedchangesquestion'] = 'There are unsaved changes to grades or feedback. Do you want to save the changes and continue?';
$string['updatetable'] = 'Save and update table';
$string['upgradenotimplemented'] = 'Upgrade not implemented in plugin ({$a->type} {$a->subtype})';
$string['userextensiondate'] = 'Extension granted until: {$a}';
$string['userassignmentdefaults'] = 'User assignment defaults';
$string['useridlistnotcached'] = 'The grade changes were NOT saved, as it was not possible to determine which submission they were for.';
$string['useroverrides'] = 'User overrides';
$string['usersubmissioncannotberemoved'] = 'The submission of {$a} cannot be removed.';
$string['usersnone'] = 'No students have access to this assignment.';
$string['userswhoneedtosubmit'] = 'Users who need to submit: {$a}';
$string['usergrade'] = 'User grade';
$string['validmarkingworkflowstates'] = 'Valid marking workflow states';
$string['viewadifferentattempt'] = 'View a different attempt';
$string['viewbatchsetmarkingworkflowstate'] = 'View batch set marking workflow state page.';
$string['viewbatchmarkingallocation'] = 'View batch set marking allocation page.';
$string['viewfeedback'] = 'View feedback';
$string['viewfeedbackforuser'] = 'View feedback for user: {$a}';
$string['viewfullgradingpage'] = 'Open the full grading page to provide feedback';
$string['viewgradebook'] = 'View gradebook';
$string['viewgradingformforstudent'] = 'View grading page for student: (id={$a->id}, fullname={$a->fullname}).';
$string['viewgrading'] = 'View all submissions';
$string['viewownsubmissionform'] = 'View own submit assignment page.';
$string['viewownsubmissionstatus'] = 'View own submission status page.';
$string['viewsubmissionforuser'] = 'View submission for user: {$a}';
$string['viewsubmission'] = 'View submission';
$string['viewfull'] = 'View full';
$string['viewsummary'] = 'View summary';
$string['viewsubmissiongradingtable'] = 'View submission grading table.';
$string['viewrevealidentitiesconfirm'] = 'View reveal student identities confirmation page.';
$string['workflowfilter'] = 'Workflow filter';
$string['xofy'] = '{$a->x} of {$a->y}';

// Deprecated since Moodle 4.5.
$string['attemptreopenmethod_none'] = 'Never';
$string['choosegradingaction'] = 'Grading action';
$string['downloadasfolders_help'] = 'Assignment submissions may be downloaded in folders. Each submission is then put in a separate folder, with the folder structure kept for any subfolders, and files are not renamed.';
$string['editaction'] = 'Actions...';
$string['gradingoptions'] = 'Options';
$string['groupoverridesdeleted'] = 'Group overrides deleted';
$string['saveallquickgradingchanges'] = 'Save all quick grading changes';
$string['updategrade'] = 'Update grade';
$string['useroverridesdeleted'] = 'User overrides deleted';
$string['viewgrader'] = 'View Grader';

