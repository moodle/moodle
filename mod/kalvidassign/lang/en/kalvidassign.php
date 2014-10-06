<?php
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
 * Kaltura video assignment language file.
 *
 * @package    mod_kalvidassign
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

$string['activity_not_migrated'] = 'This activity has not yet been migrated to use the new Kaltura instance.';
$string['modulenameplural'] = 'Kaltura Media Assignments';
$string['modulename'] = 'Kaltura Media Assignment';
$string['modulename_help'] = 'The Kaltura Media Assignment enables a teacher to create assignments that require students to upload and submit Kaltura videos. Teachers can grade student submissions and provide feedback.';
$string['name'] = 'Name';
$string['availabledate'] = 'Available from';
$string['duedate'] = 'Due Date';
$string['preventlate'] = 'Prevent late submissions';
$string['allowdeleting'] = 'Allow resubmitting';
$string['allowdeleting_help'] = 'If enabled, students may replace submitted videos. Whether it is possible to submit after the due date is controlled by the \'Prevent late submissions\' setting';
$string['emailteachers'] = 'Email alerts to teachers';
$string['emailteachers_help'] = 'If enabled, teachers receive email notification whenever students add or update an assignment submission. Only teachers who are able to grade the particular assignment are notified. So, for example, if the course uses separate groups, teachers restricted to particular groups won\'t receive notification about students in other groups.';
$string['invalidid'] = 'Invalid ID';
$string['invalid_launch_parameters'] = 'Invalid launch parameters';
$string['pluginadministration'] = 'Kaltura Media Assignment';
$string['addvideo'] = 'Add media submission';
$string['submitvideo'] = 'Submit media';
$string['replacevideo'] = 'Replace media';
$string['previewvideo'] = 'Preview';
$string['gradesubmission'] = 'Grade submissions';
$string['numberofsubmissions'] = 'Number of submissions: {$a}';
$string['assignmentexpired'] = 'Submission cancelled.  The assignment due date has passed';
$string['assignmentsubmitted'] = 'Success, your assignment has been submitted';
$string['emptyentryid'] = 'Video assignment was not submitted correctly.  Please try to resubmit.';
$string['deleteallsubmissions'] = 'Delete all video submissions';
$string['fullname'] = 'Name';
$string['grade'] = 'Grade';
$string['submissioncomment'] = 'Comment';
$string['timemodified'] = 'Last modified (Submission)';
$string['grademodified'] = 'Last modified (Grade)';
$string['finalgrade'] = 'Final grade';
$string['status'] = 'Status';
$string['optionalsettings'] = 'Optional settings';
$string['savepref'] = 'Save preferences';
$string['all'] = 'All';
$string['reqgrading'] = 'Require grading';
$string['submitted'] = 'Submitted';
$string['pagesize'] = 'Submissions shown per page';
$string['pagesize_help'] = 'Set the number of assignment to display per page';
$string['show'] = 'Show';
$string['show_help'] = "If filter is set to 'All' then all student submissions will be displayed; even if the student didn't submit anything.  If set to 'Require grading' only submissions that has not been graded or submissions that were updated by the student after it was graded will be shown.  If set to 'Submitted' only students who submitted a video assignment.";
$string['quickgrade'] = 'Allow quick grade';
$string['quickgrade_help'] = 'If enabled, multiple assignments can be graded on one page. Add grades and comments then click the "Save all my feedback" button to save all changes for that page.';
$string['invalidperpage'] = 'Enter a number greater than zero';
$string['savefeedback'] = 'Save feedback';
$string['submission'] = 'Submission';
$string['grades'] = 'Grades';
$string['feedback'] = 'Feedback';
$string['singlesubmissionheader'] = 'Grade submission';
$string['singlegrade'] = 'Add help text';
$string['singlegrade_help'] = 'Add help text';
$string['late'] = '{$a} late';
$string['early'] = '{$a} early';
$string['lastgrade'] = 'Last grade';
$string['savedchanges'] = 'Changed Saved';
$string['save'] = 'Save Changes';
$string['cancel'] = 'Close';
$string['checkconversionstatus'] = 'Check video conversion status';
$string['pluginname'] = 'Kaltura Media Assignment';
$string['video_converting'] = 'The video is still converting.  Please check the status of the video at a later time.';
$string['emailteachermail'] = '{$a->username} has updated their assignment submission
for \'{$a->assignment}\' at {$a->timeupdated}

It is available here:

    {$a->url}';
$string['emailteachermailhtml'] = '{$a->username} has updated their assignment submission
for <i>\'{$a->assignment}\'  at {$a->timeupdated}</i><br /><br />
It is <a href="{$a->url}">available on the web site</a>.';
$string['messageprovider:kalvidassign_updates'] = 'Kaltura Media assignment notifications';
$string['video_preview_header'] = 'Submission preview';
$string['kalvidassign:gradesubmission'] = 'Grade video submissions';
$string['kalvidassign:addinstance'] = 'Add a Kaltura Media Assignment';
$string['kalvidassign:submit'] = 'Submit videos';
$string['grade_video_not_cache'] = 'This video may still be in the process of converting...';
$string['noenrolledstudents'] = 'No students are enrolled in the course';
$string['group_filter'] = 'Group Filter';
$string['use_screen_recorder'] = 'Record screen';
$string['use_kcw'] = 'Upload media or record from webcam';
$string['scr_loading'] = 'Loading...';
$string['reviewvideo'] = 'Review submission';
$string['kalvidassign:screenrecorder'] = 'Screen recorder';
$string['checkingforjava'] = 'Checking for Java';
$string['javanotenabled'] = 'Failed to detect Java, please make sure you have the latest version of Java installed and enabled and then try again.';
$string['cannotdisplaythumbnail'] = 'Unable to display thumbnail';
$string['noassignments'] = 'No Kaltura video assignments found in the course';
$string['submitted'] = 'Submitted';
$string['nosubmission'] = 'No submission';
$string['nosubmissions'] = 'No submissions';
$string['viewsubmission'] = 'View submission';
$string['failedtoinsertsubmission'] = 'Failed to insert submission record.';
$string['video_thumbnail'] = 'Video thumbnail';