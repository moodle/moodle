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
 * Strings for component 'completion', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   completion
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['activitycompletion'] = 'Activity completion';
$string['badautocompletion'] = 'When you select automatic completion, you must also enable at least one requirement (below).';
$string['completedunlocked'] = 'Completion options unlocked';
$string['completedunlockedtext'] = 'When you save changes, completion state for all users will be erased. If you change your mind about this, do not save the form.';
$string['completedwarning'] = 'Completion options locked';
$string['completedwarningtext'] = 'One or more users ({$a}) has already marked this activity completed. Changing completion options will erase their completion state and may cause confusion. The options have been locked and we recommend that you do not unlock them unless absolutely necessary.';
$string['completion'] = 'Completion tracking';
$string['completion-alt-auto-enabled'] = 'The system marks this item complete according to conditions';
$string['completion-alt-auto-fail'] = 'Completed (did not achieve pass grade)';
$string['completion-alt-auto-n'] = 'Not completed';
$string['completion-alt-auto-pass'] = 'Completed (achieved pass grade)';
$string['completion-alt-auto-y'] = 'Completed';
$string['completion-alt-manual-enabled'] = 'Users can manually mark this item complete';
$string['completion-alt-manual-n'] = 'Not completed; select to mark as complete';
$string['completion-alt-manual-y'] = 'Completed; select to mark as not complete';
$string['completion_automatic'] = 'Show activity as complete when conditions are met';
$string['completiondisabled'] = 'Disabled, not shown in activity settings';
$string['completionexpected'] = 'Expect completed on';
$string['completionexpected_help']='This is the date that completion of this activity is expected. The value is used when displaying student progress.';
$string['completionicons'] = 'progress tick boxes';
$string['completion_manual'] = 'Users can manually mark the activity as completed';
$string['completion_none'] = 'Do not indicate activity completion';
$string['completionreport'] = 'Completion progress report';
$string['completion-title-manual-n'] = 'Mark as complete';
$string['completion-title-manual-y'] = 'Mark as not complete';
$string['completionusegrade'] = 'Require grade';
$string['completionusegrade_desc'] = 'User must receive a grade to complete this activity';
$string['completionview'] = 'Require view';
$string['completionview_desc'] = 'Users must view this activity to complete it';
$string['configenablecompletion'] = 'When enabled, this lets you turn on completion tracking (progress) features at course level.';
$string['configprogresstrackedroles'] = 'Roles that are displayed in the progress-tracking screen. (Usually includes just students and equivalent roles.)';
$string['csvdownload'] = 'Download in spreadsheet format (UTF-8 .csv)';
$string['enablecompletion'] = 'Enable completion tracking';
$string['err_noactivities'] = 'Completion information is not enabled for any activity, so none can be displayed. You can enable completion information by editing the settings for an activity.';
$string['err_nousers'] = 'There are no users on this course or group for whom completion information is displayed. (By default, completion information is displayed only for students, so if there are no students, you will see this error. Administrators can alter this option via the admin screens.)';
$string['err_system'] = 'An internal error occurred in the completion system. (System administrators can enable debugging information to see more detail.)';
$string['excelcsvdownload'] = 'Download in Excel-compatible format (.csv)';
$string['completionlocked_help'] = 'Completion options are locked because some users have already completed this activity.';
$string['progress'] = 'Student progress';
$string['progress-title'] = '{$a->user}, {$a->activity}: {$a->state} {$a->date}';
$string['reportpage'] = 'Showing users {$a->from} to {$a->to} of {$a->total}.';
$string['restoringcompletiondata'] = 'Writing completion data';
$string['saved'] = 'Saved';
$string['unlockcompletion'] = 'Unlock completion options';
$string['writingcompletiondata'] = 'Writing completion data';
$string['yourprogress'] = 'Your progress';

$string['achievinggrade']='Achieving grade';
$string['activities']='Activities';
$string['activitiescompleted']='Activities completed';
$string['activitycompletionreport']='Activity completion progress report';
$string['addcourseprerequisite']='Add course prerequisite';
$string['afterspecifieddate']='After specified date';
$string['aggregationmethod']='Aggregation method';
$string['all']='All';
$string['any']='Any';
$string['approval']='Approval';
$string['completionenabled']='Enabled, control via completion and activity settings';
$string['completionmenuitem']='Completion';
$string['completionsettingslocked']='Completion settings locked';
$string['completionstartonenrol']='Completion tracking begins on enrolment';
$string['completionstartonenrolhelp']='Begin tracking a user\'s progress in course completion after course enrolment';
$string['confirmselfcompletion']='Confirm self completion';
$string['coursecomplete']='Course Complete';
$string['coursecompleted']='Course Completed';
$string['coursecompletionreport']='Course completion progress report';
$string['coursegrade']='Course grade';
$string['courseprerequisites']='Course prerequisites';
$string['coursesavailable']='Courses available';
$string['coursesavailableexplaination']='<i>Course completion criteria must be set for a course to appear in this list</i>';
$string['criteria']='Criteria';
$string['criteriagroup']='Criteria group';
$string['criteriarequiredall']='All criteria below are required';
$string['criteriarequiredany']='Any criteria below are required';
$string['days']='Days';
$string['editcoursecompletionsettings']='Edit Course Completion Settings';
$string['enrolmentduration']='Days left';
$string['err_nocourses']='Course completion is not enabled for any other courses, so none can be displayed. You can enable course completion in the course settings.';
$string['err_nograde']='A course pass grade has not been set for this course. To enable this criteria type you must create a pass grade for this course.';
$string['err_noroles']='There are no roles with the capability \'moodle/course:markcomplete\' in this course. You can enable this criteria type by adding this capability to role(s).';
$string['err_settingslocked']='One or more users have already completed a criteria so the settings have been locked. Unlocking the completion criteria settings will delete any existing user data and may cause confusion.';
$string['datepassed']='Date passed';
$string['daysafterenrolment']='Days after enrolment';
$string['durationafterenrolment']='Duration after enrolment';
$string['fraction']='Fraction';
$string['inprogress']='In progress';
$string['manualcompletionby']='Manual completion by';
$string['manualselfcompletion']='Manual self completion';
$string['markcomplete']='Mark complete';
$string['markedcompleteby']='Marked complete by $a';
$string['markingyourselfcomplete']='Marking yourself complete';
$string['notenroled']='You are not enroled as a student in this course';
$string['notyetstarted']='Not yet started';
$string['overallcriteriaaggregation']='Overall critieria type aggregation';
$string['passinggrade']='Passing grade';
$string['pending']='Pending';
$string['periodpostenrolment']='Period post enrolment';
$string['prerequisites']='Prerequisites';
$string['prerequisitescompleted']='Prerequisites completed';
$string['progresstrackedroles']='Progress-tracked roles';
$string['recognitionofpriorlearning']='Recognition of prior learning';
$string['remainingenroledfortime']='Remaining enroled for a specified period of time';
$string['remainingenroleduntildate']='Remaining enroled until a specified date';
$string['requiredcriteria']='Required Criteria';
$string['seedetails']='See details';
$string['self']='Self';
$string['selfcompletion']='Self completion';
$string['showinguser']='Showing user';
$string['unit']='Unit';
$string['unenrolingfromcourse']='Unenroling from course';
$string['unenrolment']='Unenrolment';
$string['unlockcompletiondelete']='Unlock completion options and delete user completion data';
$string['usealternateselector']='Use the alternate course selector';
$string['viewcoursereport']='View course report';
$string['viewingactivity']='Viewing the $a';
$string['xdays']='$a days';
