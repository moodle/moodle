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
$string['completedunlockedtext'] = 'When you save changes, completion state for all students will be erased. If you change your mind about this, do not save the form.';
$string['completedwarning'] = 'Completion options locked';
$string['completedwarningtext'] = 'One or more students ({$a}) has already marked this activity as completed. Changing completion options will erase their completion state and may cause confusion. Thus the options have been locked and should not be unlocked unless absolutely necessary.';
$string['completion'] = 'Completion tracking';
$string['completion_help'] = 'If enabled, activity completion is tracked, either manually or automatically, based on certain conditions. Multiple conditions may be set if desired. If so, the activity will only be considered complete when ALL conditions are met.

A tick next to the activity name on the course page indicates when the activity is complete.';
$string['completion_link'] = 'activity/completion';
$string['completion-alt-auto-enabled'] = 'The system marks this item complete according to conditions';
$string['completion-alt-auto-fail'] = 'Completed (did not achieve pass grade)';
$string['completion-alt-auto-n'] = 'Not completed: {$a}';
$string['completion-alt-auto-pass'] = 'Completed (achieved pass grade)';
$string['completion-alt-auto-y'] = 'Completed: {$a}';
$string['completion-alt-manual-enabled'] = 'Students can manually mark this item complete';
$string['completion-alt-manual-n'] = 'Not completed: {$a}. Select to mark as complete.';
$string['completion-alt-manual-y'] = 'Completed: {$a}. Select to mark as not complete.';
$string['completion_automatic'] = 'Show activity as complete when conditions are met';
$string['completiondisabled'] = 'Disabled, not shown in activity settings';
$string['completionexpected'] = 'Expect completed on';
$string['completionexpected_help']='This setting specifies the date when the activity is expected to be completed. The date is not shown to students and is only displayed in the activity completion report.';
$string['completionicons'] = 'Completion tick boxes';
$string['completionicons_help'] = 'A tick next an activity name may be used to indicate when the activity is complete.

If a dotted tick is shown, you can click it to tick the box when you think you have completed the activity. (Clicking it again removes the tick if you change your mind.) The tick is optional and is simply a way of tracking your progress through the course.

If a blank tick box is shown, a tick will appear automatically when you have completed the activity according to conditions set by the teacher.';
$string['completion_manual'] = 'Students can manually mark the activity as completed';
$string['completion_none'] = 'Do not indicate activity completion';
$string['completion-title-manual-n'] = 'Mark as complete: {$a}';
$string['completion-title-manual-y'] = 'Mark as not complete: {$a}';
$string['completionnotenabled'] = 'Completion is not enabled';
$string['completionnotenabledforcourse'] = 'Completion is not enabled for this course';
$string['completionnotenabledforsite'] = 'Completion is not enabled for this site';
$string['completionusegrade'] = 'Require grade';
$string['completionusegrade_help'] = 'If enabled, the activity is considered complete when a student receives a grade. Pass and fail icons may be displayed if a pass grade for the activity has been set.';
$string['completionusegrade_desc'] = 'Student must receive a grade to complete this activity';
$string['completionview'] = 'Require view';
$string['completionview_desc'] = 'Student must view this activity to complete it';
$string['configenablecompletion'] = 'When enabled, this lets you turn on completion tracking (progress) features at course level.';
$string['csvdownload'] = 'Download in spreadsheet format (UTF-8 .csv)';
$string['deletecoursecompletiondata'] = 'Delete course completion data';
$string['enablecompletion'] = 'Enable completion tracking';
$string['err_noactivities'] = 'Completion information is not enabled for any activity, so none can be displayed. You can enable completion information by editing the settings for an activity.';
$string['err_nousers'] = 'There are no students on this course or group for whom completion information is displayed. (By default, completion information is displayed only for students, so if there are no students, you will see this error. Administrators can alter this option via the admin screens.)';
$string['err_system'] = 'An internal error occurred in the completion system. (System administrators can enable debugging information to see more detail.)';
$string['excelcsvdownload'] = 'Download in Excel-compatible format (.csv)';
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
$string['addcourseprerequisite']='Add course prerequisite';
$string['afterspecifieddate']='After specified date';
$string['aggregationmethod']='Aggregation method';
$string['all']='All';
$string['any']='Any';
$string['approval']='Approval';
$string['completionenabled']='Enabled, control via completion and activity settings';
$string['completionmenuitem']='Completion';
$string['completiononunenrolment']='Completion on unenrolment';
$string['completionsettingslocked']='Completion settings locked';
$string['completionstartonenrol']='Completion tracking begins on enrolment';
$string['completionstartonenrolhelp']='Begin tracking a student\'s progress in course completion after course enrolment';
$string['confirmselfcompletion']='Confirm self completion';
$string['coursealreadycompleted']='You have already completed this course';
$string['coursecomplete']='Course complete';
$string['coursecompleted']='Course completed';
$string['coursegrade']='Course grade';
$string['courseprerequisites']='Course prerequisites';
$string['coursesavailable']='Courses available';
$string['coursesavailableexplaination']='<i>Course completion criteria must be set for a course to appear in this list</i>';
$string['criteria']='Criteria';
$string['criteriagroup']='Criteria group';
$string['criteriarequiredall']='All criteria below are required';
$string['criteriarequiredany']='Any criteria below are required';
$string['days']='Days';
$string['editcoursecompletionsettings']='Edit course completion settings';
$string['enrolmentduration']='Days left';
$string['err_nocourses']='Course completion is not enabled for any other courses, so none can be displayed. You can enable course completion in the course settings.';
$string['err_nograde']='A course pass grade has not been set for this course. To enable this criteria type you must create a pass grade for this course.';
$string['err_noroles']='There are no roles with the capability \'moodle/course:markcomplete\' in this course. You can enable this criteria type by adding this capability to role(s).';
$string['err_settingslocked']='One or more students have already completed a criteria so the settings have been locked. Unlocking the completion criteria settings will delete any existing user data and may cause confusion.';
$string['datepassed']='Date passed';
$string['daysafterenrolment']='Days after enrolment';
$string['durationafterenrolment']='Duration after enrolment';
$string['fraction']='Fraction';
$string['inprogress']='In progress';
$string['manualcompletionby']='Manual completion by';
$string['manualselfcompletion']='Manual self completion';
$string['markcomplete']='Mark complete';
$string['markedcompleteby']='Marked complete by {$a}';
$string['markingyourselfcomplete']='Marking yourself complete';
$string['moredetails']='More details';
$string['nocriteriaset']='No completion criteria set for this course';
$string['notenroled']='You are not enroled in this course';
$string['notyetstarted']='Not yet started';
$string['overallcriteriaaggregation']='Overall criteria type aggregation';
$string['passinggrade']='Passing grade';
$string['pending']='Pending';
$string['periodpostenrolment']='Period post enrolment';
$string['prerequisites']='Prerequisites';
$string['prerequisitescompleted']='Prerequisites completed';
$string['recognitionofpriorlearning']='Recognition of prior learning';
$string['remainingenroledfortime']='Remaining enrolled for a specified period of time';
$string['remainingenroleduntildate']='Remaining enrolled until a specified date';
$string['requiredcriteria']='Required criteria';
$string['seedetails']='See details';
$string['self']='Self';
$string['selfcompletion']='Self completion';
$string['showinguser']='Showing user';
$string['unit']='Unit';
$string['unenrolingfromcourse']='Unenroling from course';
$string['unenrolment']='Unenrolment';
$string['unlockcompletiondelete']='Unlock completion options and delete user completion data';
$string['usealternateselector']='Use the alternate course selector';
$string['usernotenroled']='User is not enroled in this course';
$string['viewcoursereport']='View course report';
$string['viewingactivity']='Viewing the {$a}';
$string['xdays']='{$a} days';
