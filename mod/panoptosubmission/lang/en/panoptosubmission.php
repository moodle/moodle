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
 * Panopto Student Submission language file
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['modulenameplural'] = 'Panopto Student Submissions';
$string['modulename'] = 'Panopto Student Submission';
$string['modulename_help'] = 'The Panopto Student Submission activity is a gradeable assignment that require students to upload and submit Panopto videos. Teachers can also provide feedback.';
$string['name'] = 'Name';
$string['availabledate'] = 'Allow submissions from';
$string['availabledate_help'] = 'If enabled, students will not be able to submit before this date. If disabled, students will be able to start submitting right away.';
$string['cutoffdate'] = 'Cut-off date';
$string['cutoffdate_help'] = 'If set, the assignment will not accept submissions after this date.';
$string['cutoffdatevalidation'] = 'Cut-off date cannot be earlier than the due date.';
$string['cutoffdatefromdatevalidation'] = 'Cut-off date cannot be earlier than the allow submissions from date.';
$string['duedate'] = 'Due Date';
$string['duedate_help'] = 'This is when the assignment is due. Submissions will still be allowed after this date, but any assignments submitted after this date will be marked as late. Set an assignment cut-off date to prevent submissions after a certain date.';
$string['duedatevalidation'] = 'Due date cannot be earlier than the allow submissions from date.';
$string['preventlate'] = 'Prevent late submissions';
$string['preventlate_help'] = 'If enabled, this will prevent students from submitting the assignment after the due date.';
$string['allowdeleting'] = 'Allow resubmitting';
$string['allowdeleting_help'] = 'If enabled, students may replace submitted videos. Whether it is possible to submit after the due date is controlled by the \'Prevent late submissions\' setting';
$string['emailteachers_help'] = 'If enabled, teachers receive email notification whenever students add or update an assignment submission. Only teachers who are able to grade the particular assignment are notified. So, for example, if the course uses separate groups, teachers restricted to particular groups won\'t receive notification about students in other groups.';
$string['invalidid'] = 'Invalid ID';
$string['invalid_launch_parameters'] = 'Invalid launch parameters';
$string['pluginadministration'] = 'Panopto Student Submission';
$string['addvideo'] = 'Add Panopto submission';
$string['submitvideo'] = 'Submit';
$string['replacevideo'] = 'Replace';
$string['gradesubmission'] = 'Grade';
$string['numberofsubmissions'] = 'Number of submissions: {$a}';
$string['assignmentexpired'] = 'Submission cancelled. The assignment cut off date has passed.';
$string['assignmentpastdue'] = 'Submission cancelled.  The assignment due date has passed';
$string['notallowedtoreplacemedia'] = 'You are not allowed to replace the media.';
$string['assignmentsubmitted'] = 'Success, your assignment has been submitted';
$string['deleteallsubmissions'] = 'Delete all video submissions';
$string['fullname'] = 'Name';
$string['gradeverb'] = 'Grade';
$string['gradenoun'] = 'Grade';
$string['gradedon'] = 'Graded on';
$string['gradedby'] = 'Graded by';
$string['gradingsummary'] = 'Grading summary';
$string['numberofparticipants'] = 'Participants';
$string['numberofsubmittedassignments'] = 'Submitted';
$string['numberofsubmissionsneedgrading'] = 'Needs grading';
$string['timeremaining'] = 'Time remaining';
$string['hiddenfromstudents'] = 'Hidden from students';
$string['yes'] = 'Yes';
$string['no'] = 'No';
$string['submissionisdue'] = 'Submission is due';
$string['relativedatessubmissiontimeleft'] = 'Calculated for each student';
$string['latesubmissions'] = 'Late submissions';
$string['latesubmissionsaccepted'] = 'Allowed until {$a}';
$string['submissioncomment'] = 'Comment';
$string['submissioncommentfeedback'] = 'Comment Feedback';
$string['timemodified'] = 'Last modified (Submission)';
$string['grademodified'] = 'Last modified (Grade)';
$string['finalgrade'] = 'Final grade';
$string['status'] = 'Status';
$string['optionalsettings'] = 'Optional settings';
$string['savepref'] = 'Save preferences';
$string['all'] = 'All';
$string['reqgrading'] = 'Require grading';
$string['submitted'] = 'Submitted';
$string['not_submitted'] = 'Not submitted';
$string['pagesize'] = 'Submissions shown per page';
$string['pagesize_help'] = 'Set the number of assignment to display per page';
$string['show'] = 'Show';
$string['show_help'] = "If filter is set to 'All' then all student submissions will be displayed; even if the student didn't submit anything.  If set to 'Require grading' only submissions that has not been graded or submissions that were updated by the student after it was graded will be shown.  If set to 'Submitted' only students who submitted a video assignment.";
$string['invalidperpage'] = 'Enter a number greater than zero';
$string['savefeedback'] = 'Save feedback';
$string['submission'] = 'Submission';
$string['submissions'] = 'Submissions';
$string['gradeitem:submissions'] = 'Submissions';
$string['feedback'] = 'Feedback';
$string['feedbackavailabletext'] = '{$a->username} has posted some feedback on your
assignment submission for \'{$a->assignment}\'

You can see it appended to your assignment submission:

    {$a->url}';
$string['feedbackavailablehtml'] = '{$a->username} has posted some feedback on your
assignment submission for \'<i>{$a->assignment}</i>\'<br /><br />
You can see it appended to your <a href="{$a->url}">assignment submission</a>.';
$string['feedbackavailablesmall'] = '{$a->username} has given feedback for assignment {$a->assignment}';
$string['singlesubmissionheader'] = 'Grade submission';
$string['singlegrade'] = 'Add help text';
$string['singlegrade_help'] = 'Add help text';
$string['late'] = '{$a} late';
$string['early'] = '{$a} early';
$string['savedchanges'] = 'Changed Saved';
$string['save'] = 'Save Changes';
$string['cancel'] = 'Close';
$string['pluginname'] = 'Panopto Student Submission';
$string['gradersubmissionupdatedtext'] = '{$a->username} has updated their assignment submission
for \'{$a->assignment}\' at {$a->timeupdated}

View the submission here:

    {$a->url}';
$string['gradersubmissionupdatedhtml'] = '{$a->username} has updated their assignment submission
for <i>\'{$a->assignment}\'  at {$a->timeupdated}</i><br /><br />
It is <a href="{$a->url}">available on the web site</a>.';
$string['gradersubmissionupdatedsmall'] = '{$a->username} has updated their submission for assignment {$a->assignment}.';
$string['submissionreceipttext'] = 'You have submitted an
assignment submission for \'{$a->assignment}\'

You can see the status of your assignment submission:

    {$a->url}';
$string['submissionreceipthtml'] = '<p>You have submitted an assignment submission for \'<i>{$a->assignment}</i>\'.</p>
<p>You can see the status of your <a href="{$a->url}">assignment submission</a>.</p>';
$string['submissionreceiptsmall'] = 'You have submitted your assignment submission for {$a->assignment}';
$string['messageprovider:panoptosubmission_updates'] = 'Panopto Student Submission notifications';
$string['video_preview_header'] = 'Submission preview';
$string['noenrolledstudents'] = 'No students are enrolled in the course';
$string['group_filter'] = 'Group Filter';
$string['noassignments'] = 'No Panopto Student Submission activities found in the course';
$string['submitted'] = 'Submitted';
$string['has_grade'] = 'Graded';
$string['needs_grade'] = 'Needs grade';
$string['nosubmission'] = 'No submission';
$string['nosubmissions'] = 'No submissions';
$string['viewsubmission'] = 'View submission';
$string['failedtoinsertsubmission'] = 'Failed to insert submission record.';
$string['feedbackfromteacher'] = 'Feedback From Teacher';
$string['currentgrade'] = 'Current grade in gradebook';
$string['eventgrade_submissions_page_viewed'] = 'Grade submissions page viewed';
$string['eventsingle_submission_page_viewed'] = 'Single submission page viewed';
$string['eventgrades_updated'] = 'Assignment grades updated';
$string['eventassignment_submitted'] = 'Assignment submitted';
$string['eventassignment_details_viewed'] = 'Assignment details viewed';
$string['nosubmissionsforgrading'] = 'There are no submissions available for grading';
$string['select_submission'] = 'Select Panopto submission';
$string['sessionpreview_show'] = 'Show video preview';
$string['sessionpreview_hide'] = 'Hide video preview';
$string['quickgrade'] = 'Enable quick grading';
$string['userpicture'] = 'User Picture';
$string['useremail'] = 'Email';
$string['grade_out_of'] = 'Grade out of {$a}: ';
$string['quickgrade_help'] = 'If enabled, multiple assignments can be graded at the same time. Update grades and feedback and then click "Save all feedback".';
$string['no_existing_lti_tools'] = 'A preconfigured Panopto LTI tool with the custom parameter "panopto_student_submission_tool" must exist to be able to use the Panopto Student Submission activity. Please see setup documentation for more information.';
$string['no_automatic_operation_target_server'] = 'Please set Automatic Operation Target Server in the settings, so course can be provisioned.';
$string['notifications'] = 'Notifications';
$string['sendstudentnotificationsdefault'] = 'Default for \'Notify student\'';
$string['sendstudentnotificationsdefault_help'] = 'When grading each student, should \'Notify student\' be ticked by default?';
$string['sendstudentnotifications'] = 'Notify student';
$string['sendstudentnotifications_help'] = 'Tick this box to send a notification about the updated grade or feedback. If the assignment uses a marking workflow, or the grades are hidden in the grader report, then the notification will not be sent until the grade is released.';
$string['sendnotifications'] = 'Notify graders about submissions';
$string['sendnotifications_help'] = 'If enabled, graders (usually teachers) receive a message whenever a student submits an assignment, early, on time and late.';
$string['sendlatenotifications'] = 'Notify graders about late submissions';
$string['sendlatenotifications_help'] = 'If enabled, graders (usually teachers) receive a message whenever a student submits an assignment late.';
$string['privacy:metadata:emailteachersexplanation'] = 'Messages are sent to teachers through the messaging system.';
$string['privacy:metadata:panoptosubmission_submission'] = 'Panopto Student Submission submissions';
$string['privacy:metadata:panoptosubmission_submission:email'] = 'Your email is sent to Panopto to allow use of Panopto\'s email features.';
$string['privacy:metadata:panoptosubmission_submission:username'] = 'Your username is sent to Panopto to allow use of LTI features.';
$string['privacy:metadata:panoptosubmission_submission:userid'] = 'Moodle user id';
$string['privacy:metadata:panoptosubmission_submission:source'] = 'The LTI link that opens the submitted content';
$string['privacy:metadata:panoptosubmission_submission:grade'] = 'Grade score for the submission';
$string['privacy:metadata:panoptosubmission_submission:submissioncomment'] = 'Submission teacher comment';
$string['privacy:metadata:panoptosubmission_submission:teacher'] = 'Moodle userId of the teacher who marked the submission';
$string['privacy:metadata:panoptosubmission_submission:mailed'] = 'Whether the assignment submission notification has been emailed to the teacher';
$string['privacy:metadata:panoptosubmission_submission:timemarked'] = 'Time the assignment submission was marked';
$string['privacy:metadata:panoptosubmission_submission:timecreated'] = 'Time the submission record was created';
$string['privacy:metadata:panoptosubmission_submission:timemodified'] = 'Time the assignment submission was modified';
$string['privacy:metadata:panoptosubmissionfilter'] = 'Filter preference of assignment submissions.';
$string['privacy:metadata:panoptosubmissiongroupfilter'] = 'Group filter preference of assignment submissions.';
$string['privacy:metadata:panoptosubmissionperpage'] = 'Number of assignment submissions shown per page preference.';
$string['privacy:metadata:panoptosubmissionquickgrade'] = 'Quick grading preference for Panopto Submission.';
$string['privacy:markedsubmissionspath'] = 'markedsubmissions';
$string['privacy:submissionpath'] = 'submission';
$string['panoptosubmission:gradesubmission'] = 'Grade video submissions';
$string['panoptosubmission:addinstance'] = 'Add a Panopto Student Submission activity';
$string['panoptosubmission:submit'] = 'Submit';
$string['panoptosubmission:receivegradernotifications'] = 'Receive grader submission notifications';
