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
 * Strings for core_completion subsystem.
 *
 * @package     core_completion
 * @category    string
 * @copyright   &copy; 2008 The Open University
 * @author      Sam Marshall
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['achievinggrade'] = 'Achieving grade';
$string['achievingpassinggrade'] = 'Achieving passing grade';
$string['activities'] = 'Activities';
$string['activitieslabel'] = 'Activities / resources';
$string['activityaggregation'] = 'Condition requires';
$string['activityaggregation_all'] = 'ALL selected activities to be completed';
$string['activityaggregation_any'] = 'ANY selected activities to be completed';
$string['activitiescompleted'] = 'Activity completion';
$string['activitiescompletednote'] = 'Note: Activity completion must be set for an activity to appear in the above list.';
$string['activitycompletion'] = 'Completion conditions';
$string['activitycompletionupdated'] = 'Changes saved';
$string['activitygradetopassnotset'] = 'This activity does not have a valid grade to pass set. It may be set in the Grade section of the activity settings.';
$string['addconditions'] = 'Add conditions';
$string['affectedactivities'] = 'The changes will affect the following <b>{$a}</b> activities or resources:';
$string['aggregationmethod'] = 'Aggregation method';
$string['all'] = 'All';
$string['allconditions'] = 'Activity is completed when students do all the following:';
$string['any'] = 'Any';
$string['approval'] = 'Approval';
$string['areyousureoverridecompletion'] = 'Are you sure you want to override the current completion state of this activity for this user and mark it "{$a}"?';
$string['badautocompletion'] = 'You must select at least one condition.';
$string['badcompletiongradeitemnumber'] = 'Require grade can\'t be enabled for <b>{$a}</b> because grading by {$a} is not enabled.';
$string['bulkactivitycompletion'] = 'Bulk edit activity completion';
$string['bulkactivitydetail'] = 'Select the activities you wish to bulk edit.';
$string['bulkcompletiontracking'] = 'Completion tracking';
$string['bulkcompletiontracking_help'] = '<strong>None:</strong> Do not indicate activity completion

<strong>Manual:</strong> Students can manually mark the activity as completed

<strong>With condition(s):</strong> Show activity as complete when conditions are met';
$string['checkall'] = 'Check or uncheck all activities and resources';
$string['checkallsection'] = 'Check or uncheck all activities and resources in the following section: {$a}';
$string['checkactivity'] = 'Checkbox for activity / resource: {$a}';
$string['completed'] = 'Completed';
$string['completeactivity'] = 'Complete the activity';
$string['completedunlocked'] = 'Completion options unlocked';
$string['completedunlockedtext'] = 'When you save this form, completion data will be deleted then recalculated where possible. To avoid this, you can navigate away without saving.';
$string['completedwarning'] = 'Completion options locked';
$string['completedwarningtext'] = 'This activity is marked as done for one or more students. If you unlock the settings to make a change then save the form, completion data will be deleted then recalculated where possible. Manual completion can\'t be recalculated, so in this case the student will need to mark it as done again.';
$string['completion'] = 'Completion tracking';
$string['completion-alt-auto-enabled'] = 'The system marks this item complete according to conditions: {$a}';
$string['completion-alt-auto-fail'] = 'Completed: {$a} (did not achieve pass grade)';
$string['completion-alt-auto-n'] = 'Not completed: {$a}';
$string['completion-alt-auto-n-override'] = 'Not completed: {$a->modname} (set by {$a->overrideuser})';
$string['completion-alt-auto-pass'] = 'Completed: {$a} (achieved pass grade)';
$string['completion-alt-auto-y'] = 'Completed: {$a}';
$string['completion-alt-auto-y-override'] = 'Completed: {$a->modname} (set by {$a->overrideuser})';
$string['completion-alt-manual-enabled'] = 'Students can manually mark this item complete: {$a}';
$string['completion-alt-manual-n'] = 'Not completed: {$a}. Select to mark as complete.';
$string['completion-alt-manual-n-override'] = 'Not completed: {$a->modname} (set by {$a->overrideuser}). Select to mark as complete.';
$string['completion-alt-manual-y'] = 'Completed: {$a}. Select to mark as not complete.';
$string['completion-alt-manual-y-override'] = 'Completed: {$a->modname} (set by {$a->overrideuser}). Select to mark as not complete.';
$string['completion-fail'] = 'Completed (did not achieve pass grade)';
$string['completion-n'] = 'Not completed';
$string['completion-n-override'] = 'Not completed (set by {$a})';
$string['completion-pass'] = 'Completed (achieved pass grade)';
$string['completion-y'] = 'Completed';
$string['completion-y-override'] = 'Completed (set by {$a})';
$string['completion_automatic'] = 'Add requirements';
$string['completion_help'] = 'If enabled, activity completion is tracked, either manually or automatically, based on certain conditions. Multiple conditions may be set if desired. If so, the activity will only be considered complete when ALL conditions are met.';
$string['completion_link'] = 'activity/completion';
$string['completion_manual'] = 'Students must manually mark the activity as done';
$string['completion_none'] = 'None';
$string['completionactivitydefault'] = 'Use activity default';
$string['completionanygrade_desc'] = 'Any grade';
$string['completiondisabled'] = 'Disabled, not shown in activity settings';
$string['completionenabled'] = 'Enabled, control via completion and activity settings';
$string['completionexpected'] = 'Set reminder in Timeline';
$string['completionexpected_help'] = 'This allows you to set a reminder for students to work on this activity. It will appear in the Timeline block on their Dashboard as "{$a} requires action".';
$string['completionexpecteddesc'] = 'Completion expected on {$a}';
$string['completionexpectedfor'] = '{$a->instancename} should be completed';
$string['completionicons'] = 'Completion tick boxes';
$string['completionicons_help'] = 'A tick next to an activity name may be used to indicate when the activity is complete.

If a box with a dotted border is shown, a tick will appear automatically when you have completed the activity according to conditions set by the teacher.

If a box with a solid border is shown, you can click it to tick the box when you think you have completed the activity. (Clicking it again removes the tick if you change your mind.)';
$string['completionmenuitem'] = 'Completion';
$string['completionnotenabled'] = 'Completion is not enabled';
$string['completionnotenabledforcourse'] = 'Completion is not enabled for this course';
$string['completionnotenabledforsite'] = 'Completion is not enabled for this site';
$string['completionondate'] = 'Date';
$string['completionondatevalue'] = 'Date when course will be marked as complete';
$string['completionduration'] = 'Enrolment';
$string['completionsettingslocked'] = 'Completion settings locked';
$string['completionpassgrade'] = 'Require passing grade';
$string['completionpassgrade_desc'] = 'Passing grade';
$string['completionusegrade'] = 'Require grade';
$string['completionusegrade_desc'] = 'Receive a grade';
$string['completionupdated'] = 'Updated completion for activity <b>{$a}</b>';
$string['completionview_desc'] = 'View the activity';
$string['configenablecompletion'] = 'If enabled, course and activity completion conditions may be set. Setting activity completion conditions is recommended so that meaningful data is displayed for users in their course overview on the Dashboard.';
$string['confirmselfcompletion'] = 'Confirm self completion';
$string['courseaggregation'] = 'Condition requires';
$string['courseaggregation_all'] = 'ALL selected courses to be completed';
$string['courseaggregation_any'] = 'ANY selected courses to be completed';
$string['coursealreadycompleted'] = 'You have already completed this course';
$string['coursecomplete'] = 'Course complete';
$string['coursecompleted'] = 'Course completed';
$string['coursecompletedmessage'] = '<p>Congratulations!</p><p>You have completed the course <a href="{$a->courselink}">{$a->coursename}</a>.</p>';
$string['coursecompletion'] = 'Course completion';
$string['coursecompletioncondition'] = 'Condition: {$a}';
$string['coursecompletionnavigation'] = 'Course completion tertiary navigation';
$string['coursecompletionsettings'] = 'Course completion settings';
$string['coursegrade'] = 'Course grade';
$string['coursesavailable'] = 'Courses available';
$string['coursesavailableexplaination'] = 'Note: Course completion conditions must be set for a course to appear in the above list.';
$string['criteria'] = 'Criteria';
$string['criteriagroup'] = 'Criteria group';
$string['criteriarequiredall'] = 'All criteria below are required';
$string['criteriarequiredany'] = 'Any criteria below are required';
$string['criteriasummary'] = '{$a->type}: {$a->summary}';
$string['csvdownload'] = 'Download in spreadsheet format (UTF-8 .csv)';
$string['datepassed'] = 'Date passed';
$string['days'] = 'Days';
$string['daysoftotal'] = '{$a->days} of {$a->total}';
$string['daysuntilcompletion'] = 'Days until completion';
$string['defaultactivitycompletionsite'] = 'These are the default completion conditions for activities in all courses.';
$string['defaultactivitycompletioncourse'] = 'These are the default completion conditions for activities in this course.';
$string['defaultcompletion'] = 'Default activity completion';
$string['defaultcompletionupdated'] = 'Changes saved';
$string['deletecompletiondata'] = 'Delete completion data';
$string['dependencies'] = 'Dependencies';
$string['dependenciescompleted'] = 'Completion of other courses';
$string['detail_desc:receivegrade'] = 'Receive a grade';
$string['detail_desc:receivepassgrade'] = 'Receive a passing grade';
$string['detail_desc:view'] = 'View';
$string['done'] = 'Done';
$string['hiddenrules'] = 'Some settings specific to <b>{$a}</b> have been hidden. To view unselect other activities';
$string['editconditions'] = 'Edit conditions';
$string['enablecompletion'] = 'Enable completion tracking';
$string['enablecompletion_help'] = 'If enabled, you can set conditions for activity completion or course completion.';
$string['enrolmentduration'] = 'Enrolment duration';
$string['enrolmentdurationlength'] = 'User must remain enrolled for';
$string['emptyconditionsinfo'] = 'There are no completion conditions set for this activity.';
$string['emptyconditionswarning'] = 'You have to add at least one completion condition.';
$string['err_noactivities'] = 'Completion information is not enabled for any activity, so none can be displayed. You can enable completion information by editing the settings for an activity.';
$string['err_nocourses'] = 'Course completion is not enabled for any other courses, so none can be displayed. You can enable course completion in the course settings.';
$string['err_nograde'] = 'A course pass grade has not been set for this course. To enable this criteria type you must create a pass grade for this course.';
$string['err_noroles'] = 'There are no roles with the capability moodle/course:markcomplete in this course.';
$string['err_nousers'] = 'There are no students in this course or group for whom completion information is displayed. (Completion information is displayed only for users with the capability \'Be shown on completion reports\'. The capability is allowed for the default role of student only, so if there are no students, you will see this message.)';
$string['err_settingslocked'] = 'One or more students have already completed a criterion so the settings have been locked. Unlocking the settings will delete user completion data and may cause confusion.';
$string['err_system'] = 'An internal error occurred in the completion system. (System administrators can enable debugging information to see more detail.)';
$string['eventcoursecompleted'] = 'Course completed';
$string['eventcoursecompletionupdated'] = 'Course completion updated';
$string['eventcoursemodulecompletionupdated'] = 'Course activity completion updated';
$string['eventdefaultcompletionupdated'] = 'Default for course activity completion updated';
$string['excelcsvdownload'] = 'Download in Excel-compatible format (.csv)';
$string['failed'] = 'Failed';
$string['fraction'] = 'Fraction';
$string['graderequired'] = 'Required course grade';
$string['gradexrequired'] = '{$a} required';
$string['inprogress'] = 'In progress';
$string['manual'] = 'Manual';
$string['manualcompletionby'] = 'Manual completion by others';
$string['manualcompletionbynote'] = 'Note: The capability moodle/course:markcomplete must be allowed for a role to appear in the list.';
$string['manualselfcompletion'] = 'Manual self completion';
$string['manualselfcompletionnote'] = 'Note: The self completion block should be added to the course if manual self completion is enabled.';
$string['markcomplete'] = 'Mark complete';
$string['markedcompleteby'] = 'Marked complete by {$a}';
$string['markingyourselfcomplete'] = 'Marking yourself complete';
$string['modifybulkactions'] = 'Modify the actions you wish to bulk edit';
$string['moredetails'] = 'More details';
$string['nocriteriaset'] = 'No completion criteria set for this course';
$string['nogradeitem'] = 'Require grade can\'t be enabled for <b>{$a}</b> because the activity is not graded.';
$string['notcompleted'] = 'Not completed';
$string['notenroled'] = 'You are not enrolled in this course';
$string['nottracked'] = 'You are currently not being tracked by completion in this course';
$string['notyetstarted'] = 'Not yet started';
$string['overallaggregation'] = 'Completion requirements';
$string['overallaggregation_all'] = 'Course is complete when ALL conditions are met';
$string['overallaggregation_any'] = 'Course is complete when ANY of the conditions are met';
$string['pending'] = 'Pending';
$string['periodpostenrolment'] = 'Period post enrolment';
$string['privacy:metadata:completionstate'] = 'If the activity has been completed';
$string['privacy:metadata:course'] = 'A course identifier';
$string['privacy:metadata:coursecompletedsummary'] = 'Stores information about users who have completed criteria in a course';
$string['privacy:metadata:coursemoduleid'] = 'The activity ID';
$string['privacy:metadata:coursemodulesummary'] = 'Stores activity completion data for a user';
$string['privacy:metadata:coursesummary'] = 'Stores the course completion data for a user.';
$string['privacy:metadata:gradefinal'] = 'Final grade received for course completion';
$string['privacy:metadata:overrideby'] = 'The user ID of the person who overrode the activity completion';
$string['privacy:metadata:reaggregate'] = 'If the course completion was reaggregated.';
$string['privacy:metadata:timecompleted'] = 'The time that the course was completed.';
$string['privacy:metadata:timeenrolled'] = 'The time that the user was enrolled in the course';
$string['privacy:metadata:timemodified'] = 'The time that the activity completion was modified';
$string['privacy:metadata:timestarted'] = 'The time the course was started.';
$string['privacy:metadata:timecreated'] = 'The time that the activity completion was created';
$string['privacy:metadata:viewed'] = 'If the activity was viewed';
$string['privacy:metadata:userid'] = 'The user ID of the person with course and activity completion data';
$string['privacy:metadata:unenroled'] = 'If the user has been unenrolled from the course';
$string['progress'] = 'Student progress';
$string['progress-title'] = '{$a->user}, {$a->activity}: {$a->state} {$a->date}';
$string['progresstotal'] = 'Progress: {$a->complete} / {$a->total}';
$string['recognitionofpriorlearning'] = 'Recognition of prior learning';
$string['remainingenroledfortime'] = 'Remaining enrolled for a specified period of time';
$string['remainingenroleduntildate'] = 'Remaining enrolled until a specified date';
$string['reportpage'] = 'Showing users {$a->from} to {$a->to} of {$a->total}.';
$string['requiredcriteria'] = 'Required criteria';
$string['resetactivities'] = 'Clear all checked activities and resources';
$string['restoringcompletiondata'] = 'Writing completion data';
$string['roleaggregation'] = 'Condition requires';
$string['roleaggregation_all'] = 'ALL selected roles to mark when the condition is met';
$string['roleaggregation_any'] = 'ANY selected roles to mark when the condition is met';
$string['roleidnotfound'] = 'Role ID {$a} not found';
$string['saved'] = 'Saved';
$string['seedetails'] = 'See details';
$string['select'] = 'Select';
$string['self'] = 'Self';
$string['selfcompletion'] = 'Self completion';
$string['showcompletionconditions'] = 'Show activity completion conditions';
$string['showcompletionconditions_help'] = 'Show activity completion conditions on the course page.';
$string['showinguser'] = 'Showing user';
$string['studentsmust'] = 'Students must';
$string['timecompleted'] = 'Time completed';
$string['todo'] = 'To do';
$string['unenrolingfromcourse'] = 'Unenrolling from course';
$string['unenrolment'] = 'Unenrolment';
$string['unit'] = 'Unit';
$string['unlockcompletion'] = 'Unlock completion settings';
$string['unlockcompletiondelete'] = 'Unlock completion settings and delete user completion data';
$string['updateactivities'] = 'Update completion status of checked activities';
$string['usealternateselector'] = 'Use the alternate course selector';
$string['usernotenroled'] = 'User is not enrolled in this course';
$string['viewcoursereport'] = 'View course report';
$string['viewingactivity'] = 'Viewing the {$a}';
$string['withconditions'] = 'With conditions';
$string['writingcompletiondata'] = 'Writing completion data';
$string['xdays'] = '{$a} days';
$string['youmust'] = 'You must';

// Deprecated since Moodle 4.0.
$string['yourprogress'] = 'Your progress';

// Deprecated since Moodle 4.3.
$string['editcoursecompletionsettings'] = 'Edit course completion settings';
$string['completiondefault'] = 'Default completion tracking';
$string['configcompletiondefault'] = 'The default setting for completion tracking when creating new activities.';
$string['completionview'] = 'Require view';
$string['activitygradenotrequired'] = 'Grade not required';
$string['completionpassgrade_help'] = 'If enabled, the activity is considered complete when a student receives a passing grade.';
$string['completionusegrade_help'] = 'If enabled, the activity is considered complete when a student receives a grade. If a pass grade for the activity is set, then pass and fail icons are displayed in the activity completion report.';
