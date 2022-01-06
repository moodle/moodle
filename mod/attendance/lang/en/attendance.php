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
 * Strings for component 'attendance', language 'en'
 *
 * @package   mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['Aacronym'] = 'A';
$string['Afull'] = 'Absent';
$string['Eacronym'] = 'E';
$string['Efull'] = 'Excused';
$string['Lacronym'] = 'L';
$string['Lfull'] = 'Late';
$string['Pacronym'] = 'P';
$string['Pfull'] = 'Present';
$string['absenteereport'] = 'Absentee report';
$string['acronym'] = 'Acronym';
$string['add'] = 'Add';
$string['addedrecip'] = 'Added {$a} new recipient';
$string['addedrecips'] = 'Added {$a} new recipients';
$string['addmultiplesessions'] = 'Multiple sessions';
$string['addsession'] = 'Add session';
$string['adduser'] = 'Add user';
$string['addwarning'] = 'Add warning';
$string['all'] = 'All';
$string['allcourses'] = 'All courses';
$string['allpast'] = 'All past';
$string['allsessions'] = 'All sessions';
$string['allsessionstotals'] = 'Totals for selected sessions';
$string['attendance:addinstance'] = 'Add a new attendance activity';
$string['attendance:canbelisted'] = 'Appears in the roster';
$string['attendance:changeattendances'] = 'Changing Attendances';
$string['attendance:changepreferences'] = 'Changing Preferences';
$string['attendance:export'] = 'Export Reports';
$string['attendance:manageattendances'] = 'Manage Attendances';
$string['attendance:managetemporaryusers'] = 'Manage temporary users';
$string['attendance:takeattendances'] = 'Taking Attendances';
$string['attendance:view'] = 'Viewing Attendances';
$string['attendance:viewreports'] = 'Viewing Reports';
$string['attendance:viewsummaryreports'] = 'View course summary reports';
$string['attendance:warningemails'] = 'Can be subscribed to emails with absentee users';
$string['attendance_already_submitted'] = 'Your attendance has already been set.';
$string['attendance_no_status'] = 'No valid status was available - you may be too late to record attendance.';
$string['attendancedata'] = 'Attendance data';
$string['attendancefile'] = 'Attendance file (csv format)';
$string['attendancefile_help'] = 'The file must be a CSV file with a header row and fields for identifying the user and the time attendance was recorded eg (email,scantime) or (username,time)';
$string['attendanceforthecourse'] = 'Attendance for the course';
$string['attendancegrade'] = 'Attendance grade';
$string['attendancenotset'] = 'You must set your attendance';
$string['attendancenotstarted'] = 'Attendance has not started yet for this course';
$string['attendancepercent'] = 'Attendance percent';
$string['attendancereport'] = 'Attendance report';
$string['attendanceslogged'] = 'Attendances logged';
$string['attendancestaken'] = 'Attendances taken';
$string['attendancesuccess'] = 'Attendance has been successfully taken';
$string['attendanceupdated'] = 'Attendance successfully updated';
$string['attforblockdirstillexists'] = 'old mod/attforblock directory still exists - you must delete this directory on your server before running this upgrade.';
$string['attrecords'] = 'Attendances records';
$string['autoassignstatus'] = 'Automatically select highest status available';
$string['autoassignstatus_help'] = 'If this is selected, students will automatically be assigned the highest available grade.';
$string['automark'] = 'Automatic marking';
$string['automark_help'] = 'Allows marking to be completed automatically.
If "Yes" students will be automatically marked depending on their first access to the course.
If "Set unmarked at end of session" any students who have not marked their attendance will be set to the unmarked status selected.';
$string['automarkall'] = 'Yes';
$string['automarkclose'] = 'Set unmarked at end of session';
$string['automarktask'] = 'Check for attendance sessions that require auto marking';
$string['autorecorded'] = 'system auto recorded';
$string['averageattendance'] = 'Average attendance';
$string['averageattendancegraded'] = 'Average attendance';
$string['backtoparticipants'] = 'Back to participants list';
$string['below'] = 'Below {$a}%';
$string['calclose'] = 'Close';
$string['calendarevent'] = 'Create calendar event for session';
$string['calendarevent_help'] = 'If enabled, a calendar event will be created for this session.
If disabled, any existing calendar event for this session will be deleted.';
$string['caleventcreated'] = 'Calendar event for session successfully created';
$string['caleventdeleted'] = 'Calendar event for session successfully deleted';
$string['calmonths'] = 'January,February,March,April,May,June,July,August,September,October,November,December';
$string['calshow'] = 'Choose date';
$string['caltoday'] = 'Today';
$string['calweekdays'] = 'Su,Mo,Tu,We,Th,Fr,Sa';
$string['cannottakeforgroup'] = 'You can\'t take attendance for group "{$a}"';
$string['cantaddstatus'] = 'You must set an acronym and description when adding a new status.';
$string['categoryreport'] = 'Course category report';
$string['changeattendance'] = 'Change attendance';
$string['changeduration'] = 'Change duration';
$string['changesession'] = 'Change session';
$string['checkweekdays'] = 'Select weekdays that fall within your selected session date range.';
$string['closed'] = 'This session is not currently available for self-marking';
$string['column'] = 'column';
$string['columnmap'] = 'Column mapping';
$string['columnmap_help'] = 'For each of the fields presented, select the corresponding column in the csv file.';
$string['columns'] = 'columns';
$string['commonsession'] = 'All students';
$string['commonsessions'] = 'All students';
$string['confirm'] = 'Confirm';
$string['confirmcolumnmappings'] = 'Confirm column mappings';
$string['confirmdeletehiddensessions'] = 'Are you sure you want to delete {$a->count} sessions scheduled before the course start date ({$a->date})?';
$string['confirmdeleteuser'] = 'Are you sure you want to delete user \'{$a->fullname}\' ({$a->email})?<br/>All of their attendance records will be permanently deleted.';
$string['copyfrom'] = 'Copy attendance data from';
$string['countofselected'] = 'Count of selected';
$string['course'] = 'Course';
$string['coursemessage'] = 'Message course users';
$string['courseshortname'] = 'Course shortname';
$string['coursesummary'] = 'Course summary report';
$string['createmultiplesessions'] = 'Create multiple sessions';
$string['createmultiplesessions_help'] = 'This function allows you to create multiple sessions in one simple step.
The sessions begin on the date of the base session and continue until the \'repeat until\' date.

  * <strong>Repeat on</strong>: Select the days of the week when your class will meet (for example, Monday/Wednesday/Friday).
  * <strong>Repeat every</strong>: This allows for a frequency setting. If your class will meet every week, select 1; if it will meet every other week, select 2; every 3rd week, select 3, etc.
  * <strong>Repeat until</strong>: Select the last day of class (the last day you want to take attendance).
';
$string['createonesession'] = 'Create one session for the course';
$string['csvdelimiter'] = 'CSV delimiter';
$string['currentlyselectedusers'] = 'Currently selected users';
$string['customexportfields'] = 'Export custom user profile fields';
$string['customexportfields_help'] = 'Extra custom user profile fields to expose in the export report.';
$string['date'] = 'Date';
$string['days'] = 'Days';
$string['defaultdisplaymode'] = 'Default display mode';
$string['defaults'] = 'Defaults';
$string['defaultsessionsettings'] = 'Default session settings';
$string['defaultsessionsettings_help'] = 'These settings define the defaults for all new sessions';
$string['defaultsettings'] = 'Default attendance settings';
$string['defaultsettings_help'] = 'These settings define the defaults for all new attendances';
$string['defaultstatus'] = 'Default status set';
$string['defaultsubnet'] = 'Default network address';
$string['defaultsubnet_help'] = 'Attendance recording may be restricted to particular subnets by specifying a comma-separated list of partial or full IP addresses. This is the default value used when creating new sessions.';
$string['defaultview'] = 'Default view on login';
$string['defaultview_desc'] = 'This is the default view shown to teachers on first login.';
$string['defaultwarnings'] = 'Default warning set';
$string['defaultwarningsettings'] = 'Default warning settings';
$string['defaultwarningsettings_help'] = 'These settings define the defaults for all new warnings';
$string['delete'] = 'Delete';
$string['deletecheckfull'] = 'Are you absolutely sure you want to completely delete the {$a}, including all user data?';
$string['deletedgroup'] = 'The group associated with this session has been deleted';
$string['deletehiddensessions'] = 'Delete all hidden sessions';
$string['deletelogs'] = 'Delete attendance data';
$string['deleteselected'] = 'Delete selected';
$string['deletesession'] = 'Delete session';
$string['deletesessions'] = 'Delete all sessions';
$string['deleteuser'] = 'Delete user';
$string['deletewarningconfirm'] = 'Are you sure you want to delete this warning?';
$string['deletingsession'] = 'Deleting session for the course';
$string['deletingstatus'] = 'Deleting status for the course';
$string['description'] = 'Description';
$string['display'] = 'Display';
$string['displaymode'] = 'Display mode';
$string['donotusepaging'] = 'Do not use paging';
$string['downloadexcel'] = 'Download in Excel format';
$string['downloadooo'] = 'Download in OpenOffice format';
$string['downloadtext'] = 'Download in text format';
$string['duration'] = 'Duration';
$string['editsession'] = 'Edit Session';
$string['edituser'] = 'Edit user';
$string['emailcontent'] = 'Email content';
$string['emailcontent_default'] = 'Hi %userfirstname%,
Your attendance in %coursename% %attendancename% has dropped below %warningpercent% and is currently %percent% - we hope you are ok!

To get the most out of this course you should improve your attendance, please get in touch if you require any further support.';
$string['emailcontent_help'] = 'When a warning is sent to a student, it takes the email content from this field. The following wildcards can be used:
<ul>
<li>%coursename%</li>
<li>%userfirstname%</li>
<li>%userlastname%</li>
<li>%userid%</li>
<li>%warningpercent%</li>
<li>%attendancename%</li>
<li>%cmid%</li>
<li>%numtakensessions%</li>
<li>%points%</li>
<li>%maxpoints%</li>
<li>%percent%</li>
</ul>';
$string['emailsubject'] = 'Email subject';
$string['emailsubject_default'] = 'Attendance warning';
$string['emailsubject_help'] = 'When a warning is sent to a student, it takes the email subject from this field.';
$string['emailuser'] = 'Email user';
$string['emailuser_help'] = 'If checked, a warning will be sent to the student.';
$string['emptyacronym'] = 'Empty acronyms are not allowed. Status record not updated.';
$string['emptydescription'] = 'Empty descriptions are not allowed. Status record not updated.';
$string['enablecalendar'] = 'Create calendar events';
$string['enablecalendar_desc'] = 'If enabled, a calendar event will be created for each attendance session. After changing this setting you should run the reset calendar report.';
$string['enablewarnings'] = 'Enable warnings';
$string['enablewarnings_desc'] = 'This allows a warning set to be defined for an attendance and email notifications to users when attendance drops below the configured threshold. <br/><strong>WARNING: This is a new feature and has not been tested extensively. Please use at your own-risk and provide feeback in the moodle forums if you find it works well.</strong>';
$string['encoding'] = 'Encoding';
$string['encoding_help'] = 'This refers to the type of barcode encoding used on the students\' id card. Typical types of barcode encoding schemes include Code-39, Code-128 and UPC-A.';
$string['endofperiod'] = 'End of period';
$string['endtime'] = 'Session end time';
$string['enrolmentend'] = 'User enrolment ends {$a}';
$string['enrolmentstart'] = 'User enrolment starts {$a}';
$string['enrolmentsuspended'] = 'Enrolment suspended';
$string['enterpassword'] = 'Enter password';
$string['error:coursehasnoattendance'] = 'The course with the short name {$a} has no attendance activities.';
$string['error:coursenotfound'] = 'A course with the short name {$a} can not be found.';
$string['error:qrcode'] = 'Allow students to record own attendance must be enabled to use QR code! Skipping.';
$string['error:sessioncourseinvalid'] = 'A session course is invalid! Skipping.';
$string['error:sessiondateinvalid'] = 'A session date is invalid! Skipping.';
$string['error:sessionendinvalid'] = 'A session end time is invalid! Skipping.';
$string['error:sessionstartinvalid'] = 'A session start time is invalid! Skipping.';
$string['error:statusnotfound'] = 'User: {$a->extuser} has a status value that could not be found: {$a->status}';
$string['error:timenotreadable'] = 'User: {$a->extuser} has a scantime that could not be converted by strtotime: {$a->scantime}';
$string['error:userduplicate'] = 'User {$a} was found twice in the import. please only include one record per user.';
$string['error:usernotfound'] = 'A user with the {$a->userfield} set to {$a->extuser} could not be found';
$string['errorgroupsnotselected'] = 'Select one or more groups';
$string['errorinaddingsession'] = 'Error in adding session';
$string['erroringeneratingsessions'] = 'Error in generating sessions ';
$string['eventdurationupdated'] = 'Session duration updated';
$string['eventreportviewed'] = 'Attendance report viewed';
$string['eventscreated'] = 'Calendar events created';
$string['eventsdeleted'] = 'Calendar events deleted';
$string['eventsessionadded'] = 'Session added';
$string['eventsessiondeleted'] = 'Session deleted';
$string['eventsessionipshared'] = 'Attendance self-marking IP conflict';
$string['eventsessionsimported'] = 'Sessions imported';
$string['eventsessionupdated'] = 'Session updated';
$string['eventstatusadded'] = 'Status added';
$string['eventstatusupdated'] = 'Status updated';
$string['eventstudentattendancesessionsviewed'] = 'Session report viewed';
$string['eventstudentattendancesessionsupdated'] = 'Session report updated';
$string['eventtaken'] = 'Attendance taken';
$string['eventtakenbystudent'] = 'Attendance taken by student';
$string['export'] = 'Export';
$string['extrarestrictions'] = 'Extra restrictions';
$string['formattexttype'] = 'Formatting';
$string['from'] = 'from:';
$string['gradebookexplanation'] = 'Grade in gradebook';
$string['gradebookexplanation_help'] = 'The Attendance module displays your current attendance grade based on the number of points you have earned to date and the number of points that could have been earned to date; it does not include class periods in the future. In the gradebook, your attendance grade is based on your current attendance percentage and the number of points that can be earned over the entire duration of the course, including future class periods. As such, your attendance grades displayed in the Attendance module and in the gradebook may not be the same number of points but they are the same percentage.

For example, if you have earned 8 of 10 points to date (80% attendance) and attendance for the entire course is worth 50 points, the Attendance module will display 8/10 and the gradebook will display 40/50. You have not yet earned 40 points but 40 is the equivalent point value to your current attendance percentage of 80%. The point value you have earned in the Attendance module can never decrease, as it is based only on attendance to date; however, the attendance point value shown in the gradebook may increase or decrease depending on your future attendance, as it is based on attendance for the entire course.';
$string['graded'] = 'Graded sessions';
$string['gridcolumns'] = 'Grid columns';
$string['group'] = 'Group';
$string['groups'] = 'Groups';
$string['groupsession'] = 'Group of students';
$string['groupsessionsby'] = 'Group sessions by';
$string['hiddensessions'] = 'Hidden sessions';
$string['hiddensessions_help'] = 'Sessions are hidden if they are scheduled before the course start date.

You can use this feature to hide older sessions instead of deleting them. Only visible sessions will appear in the Gradebook.';
$string['hiddensessionsdeleted'] = 'All hidden sessions were delete';
$string['hideextrauserdetails'] = 'Hide extra user details';
$string['hidensessiondetails'] = 'Hide session details';
$string['identifyby'] = 'Identify student by';
$string['import'] = 'Import';
$string['importfile'] = 'Import file';
$string['importfile_help'] = 'Import file';
$string['importsessions'] = 'Import Sessions';
$string['importstatus'] = 'Status field';
$string['importstatus_help'] = 'This allows a status value to be included in the import - eg values like P, L, or A';
$string['includeabsentee'] = 'Include session when calculating absentee report';
$string['includeabsentee_help'] = 'If checked this session will be included in the absentee report calculations.';
$string['includeall'] = 'Select all sessions';
$string['includedescription'] = 'Include session description';
$string['includenottaken'] = 'Include not taken sessions';
$string['includeqrcode'] = 'Include QR code';
$string['includeremarks'] = 'Include remarks';
$string['incorrectpassword'] = 'You have entered an incorrect password and your attendance has not been recorded, please enter the correct password.';
$string['incorrectpasswordshort'] = 'Incorrect password, attendance not recorded.';
$string['indetail'] = 'In detail...';
$string['indicator:cognitivedepth'] = 'Attendance cognitive';
$string['indicator:cognitivedepth_help'] = 'This indicator is based on the cognitive depth reached by the student in an Attendance activity.';
$string['indicator:cognitivedepthdef'] = 'Attendance cognitive';
$string['indicator:cognitivedepthdef_help'] = 'The participant has reached this percentage of the cognitive engagement offered by the Attendance during this analysis interval (Levels = No view, View)';
$string['indicator:cognitivedepthdef_link'] = 'Learning_analytics_indicators#Cognitive_depth';
$string['indicator:socialbreadth'] = 'Attendance social';
$string['indicator:socialbreadth_help'] = 'This indicator is based on the social breadth reached by the student in an Attendance activity.';
$string['indicator:socialbreadthdef'] = 'Attendance social';
$string['indicator:socialbreadthdef_help'] = 'The participant has reached this percentage of the social engagement offered by the Attendance during this analysis interval (Levels = No participation, Participant alone)';
$string['indicator:socialbreadthdef_link'] = 'Learning_analytics_indicators#Social_breadth';
$string['invalidaction'] = 'You must select an action';
$string['invalidemails'] = 'You must specify addresses of existing user accounts, could not find: {$a}';
$string['invalidimportfile'] = 'File format is invalid.';
$string['invalidsessionenddate'] = 'This date can not be earlier than the session date';
$string['invalidsessionendtime'] = 'The end time must be greater than start time';
$string['invalidstatus'] = 'You have selected an invalid status, please try again';
$string['iptimemissing'] = 'Invalid minutes to release';
$string['jumpto'] = 'Jump to';
$string['keepsearching'] = 'Keep searching';
$string['marksessionimportcsvhelp'] = 'This form allows you to upload a csv file containing a user identifier and a status - the status field can be the status acronym or the time that attendance was recorded for that user. If a time value is passed then it will try to assign the status value with the highest grade available at that time.';
$string['maxpossible'] = 'Maximum possible';
$string['maxpossible_help'] = 'Shows the score each user can reach if they receive the maximum points in each session not yet taken (past and future):
    <ul>
    <li><strong>Points</strong>: maximum points each user can reach over all sessions.</li>
    <li><strong>Percentage</strong>: maximum percentage each user can reach over all sessions.</li>
    </ul>';
$string['maxpossiblepercentage'] = 'Maximum possible percentage';
$string['maxpossiblepoints'] = 'Maximum possible points';
$string['maxwarn'] = 'Maximum number of e-mail warnings';
$string['maxwarn_help'] = 'The maximum number of times a warning should be sent (only one warning per session is sent)';
$string['mergeuser'] = 'Merge user';
$string['mobilesessionfrom'] = 'Show sessions older than the last';
$string['mobilesessionfrom_help'] = 'Allows the list of sessions to be restricted when marking in the app - only shows sessions that started since this value';
$string['mobilesessionto'] = 'Show future sessions';
$string['mobilesessionto_help'] = 'Allows the list of sessions to be restricted to only show a small number of future sessions.';
$string['mobilesettings'] = 'Mobile app settings';
$string['mobilesettings_help'] = 'These settings control Moodle mobile app behaviour';
$string['modulename'] = 'Attendance';
$string['modulename_help'] = 'The attendance activity module enables a teacher to take attendance during class and students to view their own attendance record.

The teacher can create multiple sessions and can mark the attendance status as "Present", "Absent", "Late", or "Excused" or modify the statuses to suit their needs.

Reports are available for the entire class or individual students.';
$string['modulenameplural'] = 'Attendances';
$string['months'] = 'Months';
$string['moreattendance'] = 'Attendance has been successfully taken for this page';
$string['moveleft'] = 'Move left';
$string['moveright'] = 'Move right';
$string['multisessionexpanded'] = 'Multiple sessions expanded';
$string['multisessionexpanded_desc'] = 'Show the "Multiple sessions" settings as expanded by default when creating new sessions.';
$string['mustselectusers'] = 'Must select users to export';
$string['newdate'] = 'New date';
$string['newduration'] = 'New duration';
$string['newstatusset'] = 'New set of statuses';
$string['noabsentstatusset'] = 'The status set in use does not have a status to use when not marked.';
$string['noattendanceusers'] = 'It is not possible to export any data as there are no students enrolled in the course.';
$string['noattforuser'] = 'No attendance records exist for the user';
$string['noautomark'] = 'Disabled';
$string['nocapabilitytotakethisattendance'] = 'You tried to change the attendance of a session with the cmid: {$a} that you do not have permission to modify.';
$string['nodescription'] = 'Regular class session';
$string['noeventstoreset'] = 'There are no calendar events that require an update.';
$string['nogroups'] = 'This activity has been set to use groups, but no groups exist in the course.';
$string['noguest'] = 'Guest can\'t see attendance';
$string['noofdaysabsent'] = 'No of days absent';
$string['noofdaysexcused'] = 'No of days excused';
$string['noofdayslate'] = 'No of days late';
$string['noofdayspresent'] = 'No of days present';
$string['nosessiondayselected'] = 'No Session day selected';
$string['nosessionexists'] = 'No Session exists for this course';
$string['nosessionsselected'] = 'No sessions selected';
$string['notfound'] = 'Attendance activity not found in this course!';
$string['notifytask'] = 'Send warnings to users';
$string['notmember'] = 'not&nbsp;member';
$string['notset'] = 'not set';
$string['noupgradefromthisversion'] = 'The Attendance module cannot upgrade from the version of attforblock you have installed. - please delete attforblock or upgrade it to the latest version before isntalling the new attendance module';
$string['numsessions'] = 'Number of sessions';
$string['olddate'] = 'Old date';
$string['onlyselectedusers'] = 'Export specific users';
$string['overallsessions'] = 'Over all sessions';
$string['overallsessions_help'] = 'Shows statistics for all sessions including those not yet taken (past and future):
    <ul>
    <li><strong>Sessions</strong>: total number of sessions.</li>
    <li><strong>Points</strong>: points awarded based on the taken sessions.</li>
    <li><strong>Percentage</strong>: percentage of points awarded over the maxium possible points for all sessions.</li>
    </ul>';
$string['oversessionstaken'] = 'Over taken sessions';
$string['oversessionstaken_help'] = 'Shows statistics for sessions where attendance has been taken:
    <ul>
    <li><strong>Sessions</strong>: number of already taken sessions.</li>
    <li><strong>Points</strong>: points awarded based on the taken sessions.</li>
    <li><strong>Percentage</strong>: percentage of points awarded over the maxium possible points of the taken sessions.</li>
    </ul>';
$string['pageof'] = 'Page {$a->page} of {$a->numpages}';
$string['participant'] = 'Participant';
$string['password'] = 'Password';
$string['passwordgrp'] = 'Student password';
$string['passwordgrp_help'] = 'If set students will be required to enter this password before they can set their own attendance status for the session. If empty, no password is required.';
$string['passwordrequired'] = 'You must enter the session password before you can submit your attendance';
$string['percentage'] = 'Percentage';
$string['percentageallsessions'] = 'Percentage over all sessions';
$string['percentagesessionscompleted'] = 'Percentage over taken sessions';
$string['pluginadministration'] = 'Attendance administration';
$string['pluginname'] = 'Attendance';
$string['points'] = 'Points';
$string['pointsallsessions'] = 'Points over all sessions';
$string['pointssessionscompleted'] = 'Points over taken sessions';
$string['preferences_desc'] = 'Changes to status sets will affect existing attendance sessions and may affect grading.';
$string['preventsharederror'] = 'Self-marking has been disabled for a session because this device appears to have been used to record attendance for another student.';
$string['preventsharedip'] = 'Prevent students sharing IP address';
$string['preventsharedip_help'] = 'Prevent students from using the same device (identified using IP address) to take attendance for other students.';
$string['preventsharediptime'] = 'Time to allow re-use of IP address (minutes)';
$string['preventsharediptime_help'] = 'Allow an IP address to be re-used for taking attendance in this session after this time has elapsed.';
$string['preview'] = 'File preview';
$string['previewhtml'] = 'HTML format preview';
$string['priorto'] = 'The session date is prior to the course start date ({$a}) so that the new sessions scheduled before this date will be hidden (not accessible). You can change the course start date at any time (see course settings) in order to have access to earlier sessions.<br><br>Please change the session date or just click the "Add session" button again to confirm?';
$string['privacy:metadata:attendancelog'] = 'Log of user attendances recorded.';
$string['privacy:metadata:attendancesessions'] = 'Sessions to which attendance will be recorded.';
$string['privacy:metadata:attendancewarningdone'] = 'Log of warnings sent to users over their attendance record.';
$string['privacy:metadata:duration'] = 'Session duration in seconds';
$string['privacy:metadata:groupid'] = 'Group ID associated with session.';
$string['privacy:metadata:ipaddress'] = 'IP address attendance was marked from.';
$string['privacy:metadata:lasttaken'] = 'Timestamp of when session attendance was last taken.';
$string['privacy:metadata:lasttakenby'] = 'User ID of the last user to take attendance in this session';
$string['privacy:metadata:notifyid'] = 'ID of attendance session warning is associated with.';
$string['privacy:metadata:remarks'] = 'Comments about the user\'s attendance.';
$string['privacy:metadata:sessdate'] = 'Timestamp of when session starts.';
$string['privacy:metadata:sessionid'] = 'Attendance session ID.';
$string['privacy:metadata:statusid'] = 'ID of student\'s attendance status.';
$string['privacy:metadata:statusset'] = 'Status set to which status ID belongs.';
$string['privacy:metadata:studentid'] = 'ID of student having attendance recorded.';
$string['privacy:metadata:takenby'] = 'User ID of the user who took attendance for the student.';
$string['privacy:metadata:timemodified'] = 'Timestamp of when session was last modified';
$string['privacy:metadata:timesent'] = 'Timestamp when warning was sent.';
$string['privacy:metadata:timetaken'] = 'Timestamp of when attendance was taken for the student.';
$string['privacy:metadata:userid'] = 'ID of user to send warning to.';
$string['processingfile'] = 'Processing file';
$string['qr_cookie_error'] = 'QR session has expired.';
$string['qr_pass_wrong'] = 'QR password is wrong or has expired.';
$string['qrcode'] = 'QR Code';
$string['randompassword'] = 'Random password';
$string['remark'] = 'Remark for: {$a}';
$string['remarks'] = 'Remarks';
$string['repeatasfollows'] = 'Repeat the session above as follows';
$string['repeatevery'] = 'Repeat every';
$string['repeaton'] = 'Repeat on';
$string['repeatuntil'] = 'Repeat until';
$string['report'] = 'Report';
$string['required'] = 'Required*';
$string['requiredentries'] = '  Temporary records overwrite participant attendance records';
$string['requiredentry'] = '  Temporary user merge help guide';
$string['requiredentry_help'] = '<p align="center"><b>Attendance</b></p>
<p align="left"><strong>Merge Accounts</strong></p>
<p align="left">
<table border="2" cellpadding="4">
<tr>
<th>Moodle User</th>
<th>Temporary User</th>
<th>Action</th>
</tr>
<tr>
<td>Attendance data</td>
<td>Attendance data</td>
<td>Temporary user will override Moodle user</td>
</tr>
<tr>
<td>No attendance data</td>
<td>Attendance data</td>
<td>Temporary user attendance will be transfered to Moodle user</td>
</tr>
<tr>
<td>Attendance data</td>
<td>No attendance data</td>
<td>Temporary user will be deleted</td>
</tr>
<tr>
<td>No attendance data</td>
<td>No attendance data</td>
<td>Temporary user will be deleted</td>
</tr>
</table>

</p>
<p align="left"><strong>Temporay user will be deleted in all cases after merge action</strong></p>';
$string['requiresubnet'] = 'Require network address';
$string['requiresubnet_help'] = 'Attendance recording may be restricted to particular subnets by specifying a comma-separated list of partial or full IP addresses.';
$string['resetcaledarcreate'] = 'Calendar events have been enabled but a number of existing sessions do not have events. Do you want to create calendar events for all existing sessions?';
$string['resetcaledardelete'] = 'Calendar events have been disabled but a number of existing sessions have events that should be deleted. Do you want to delete all existing events?';
$string['resetcalendar'] = 'Reset calendar';
$string['resetdescription'] = 'Remember that deleting attendance data will erase information from database. You can just hide older sessions having changed start date of course!';
$string['resetstatuses'] = 'Reset statuses to default';
$string['restoredefaults'] = 'Restore defaults';
$string['resultsperpage'] = 'Results per page';
$string['resultsperpage_desc'] = 'Number of students displayed on a page';
$string['rotateqrcode'] = 'Rotate QR code';
$string['rotateqrcode_cleartemppass_task'] = 'Task to clear temporary passwords generated by rotate QR code functionality.';
$string['rotateqrcodeexpirymargin'] = 'Rotate QR code/password expiry margin (seconds)';
$string['rotateqrcodeexpirymargin_desc'] = 'Time interval (seconds) to allow expired QR code/password by.';
$string['rotateqrcodeinterval'] = 'Rotate QR code/password interval (seconds)';
$string['rotateqrcodeinterval_desc'] = 'Time interval (seconds) to rotate QR code/password by.';
$string['save'] = 'Save attendance';
$string['scantime'] = 'Scan time';
$string['scantime_help'] = 'This allows a timestamp to be included in the import file - it will attempt to convert the timestamp passed using the PHP strtotime function and then use attendance status settings to decide which status to set for the user';
$string['search:activity'] = 'Attendance - activity information';
$string['session'] = 'Session';
$string['session_help'] = 'Session';
$string['sessionadded'] = 'Session successfully added';
$string['sessionalreadyexists'] = 'Session already exists for this date';
$string['sessiondate'] = 'Date';
$string['sessiondays'] = 'Session Days';
$string['sessiondeleted'] = 'Session successfully deleted';
$string['sessionduplicate'] = 'A duplicate session exists for course: {$a->course} in attendance: {$a->activity}';
$string['sessionexist'] = 'Session not added (already exists)!';
$string['sessiongenerated'] = 'One session was successfully generated';
$string['sessions'] = 'Sessions';
$string['sessionsallcourses'] = 'All courses';
$string['sessionsbyactivity'] = 'Attendance instance';
$string['sessionsbycourse'] = 'Course';
$string['sessionsbydate'] = 'Week';
$string['sessionscompleted'] = 'Taken sessions';
$string['sessionscurrentcourses'] = 'Current courses';
$string['sessionsgenerated'] = '{$a} sessions were successfully generated';
$string['sessionsids'] = 'IDs of sessions: ';
$string['sessionsnotfound'] = 'There is no sessions in the selected timespan';
$string['sessionstartdate'] = 'Session start date';
$string['sessionstotal'] = 'Total number of sessions';
$string['sessionsupdated'] = 'Sessions updated';
$string['sessiontype'] = 'Type';
$string['sessiontype_help'] = 'You can add sessions for all students or for a group of students. Ability to add different types depends on activity group mode.

* In group mode "No groups" you can add only sessions for all students.
* In group mode "Separate groups" you can add only sessions for a group of students.
* In group mode "Visible groups" you can add both types of sessions.
';
$string['sessiontypeshort'] = 'Type';
$string['sessionunknowngroup'] = 'A session specifies unknown group(s): {$a}';
$string['sessionupdated'] = 'Session successfully updated';
$string['set_by_student'] = 'Self-recorded';
$string['setallstatuses'] = 'Set status for';
$string['setallstatusesto'] = 'Set status to «{$a}»';
$string['setperiod'] = 'Specified time in minutes to release IP';
$string['settings'] = 'Settings';
$string['setunmarked'] = 'Automatically set when not marked';
$string['setunmarked_help'] = 'If enabled in the session, set this status if a student has not marked their own attendance.';
$string['showdefaults'] = 'Show defaults';
$string['showduration'] = 'Show duration';
$string['showextrauserdetails'] = 'Show extra user details';
$string['showqrcode'] = 'Show QR Code';
$string['showsessiondescriptiononreport'] = 'Show session description in report';
$string['showsessiondescriptiononreport_desc'] = 'Show the session description in the attendance report listing.';
$string['showsessiondetails'] = 'Show session details';
$string['somedisabledstatus'] = '(Some options have been removed as the session has started.)';
$string['sortedgrid'] = 'Sorted grid';
$string['sortedlist'] = 'Sorted list';
$string['startofperiod'] = 'Start of period';
$string['starttime'] = 'Start time';
$string['status'] = 'Status';
$string['statusall'] = 'all';
$string['statusdeleted'] = 'Status deleted';
$string['statuses'] = 'Statuses';
$string['statusset'] = 'Status set {$a}';
$string['statussetsettings'] = 'Status set';
$string['statusunselected'] = 'unselected';
$string['strftimedm'] = '%b %d';
$string['strftimedmw'] = '<nobr>%a %b %d</nobr>';
$string['strftimedmy'] = '%d %b %Y';
$string['strftimedmyhm'] = '%d %b %Y %I.%M%p'; // Line added to allow multiple sessions in the same day.
$string['strftimedmyw'] = '<nobr>%a %d %b %Y</nobr>';
$string['strftimeh'] = '%I%p';
$string['strftimehm'] = '%I:%M%p';
$string['strftimeshortdate'] = '%d.%m.%Y';
$string['studentavailability'] = 'Available for students (minutes)';
$string['studentavailability_help'] = 'When students are marking their own attendance, the number of minutes after session starts that this status is available.
 <br/>If empty, this status will always be available, If set to 0 it will always be hidden to students.';
$string['studentid'] = 'Student ID';
$string['studentmarked'] = 'Your attendance in this session has been recorded.';
$string['studentmarking'] = 'Student recording';
$string['studentpassword'] = 'Student password';
$string['studentrecordingexpanded'] = 'Student recording expanded';
$string['studentrecordingexpanded_desc'] = 'Show the "Student recording" settings as expanded by default when creating new sessions.';
$string['studentscanmark'] = 'Allow students to record own attendance';
$string['studentscanmark_desc'] = 'If checked, teachers will be able to allow students to mark their own attendance.';
$string['studentscanmark_help'] = 'If checked students will be able to change their own attendance status for the session.';
$string['studentscanmarksessiontime'] = 'Students record attendance during session time';
$string['studentscanmarksessiontime_desc'] = 'If checked students can only record their attendance during the session.';
$string['studentscanmarksessiontimeend'] = 'Session end (minutes)';
$string['studentscanmarksessiontimeend_desc'] = 'If the session does not have an end time, how many minutes should the session be available for students to record their attendance.';
$string['submit'] = 'Submit';
$string['submitattendance'] = 'Submit attendance';
$string['submitpassword'] = 'Submit password';
$string['subnet'] = 'Subnet';
$string['subnetactivitylevel'] = 'Allow subnet config at activity level';
$string['subnetactivitylevel_desc'] = 'If enabled, teachers can override the default subnet at the activity level when creating an attendance. Otherwise the site default will be used when creating a session.';
$string['subnetwrong'] = 'Attendance can only be recorded from certain locations, and this computer is not on the allowed list.';
$string['summary'] = 'Summary';
$string['tablerenamefailed'] = 'Rename of old attforblock table to attendance failed';
$string['tactions'] = 'Action';
$string['takeattendance'] = 'Take attendance';
$string['takensessions'] = 'Taken sessions';
$string['tcreated'] = 'Created';
$string['tempaddform'] = 'Add temporary user';
$string['tempexists'] = 'There is already a temporary user with this email address';
$string['temptable'] = 'List of temporary users';
$string['tempuser'] = 'Temporary user';
$string['tempusermerge'] = 'Merge temporary user';
$string['tempusers'] = 'Temporary users';
$string['tempusersedit'] = 'Edit temporary user';
$string['tempuserslist'] = 'Temporary users';
$string['thirdpartyemails'] = 'Notify other users';
$string['thirdpartyemails_help'] = 'List of other users who will be notified. (requires the capability mod/attendance:viewreports)';
$string['thirdpartyemailsubject'] = 'Attendance warning';
$string['thirdpartyemailtext'] = '{$a->firstname} {$a->lastname} attendance within {$a->coursename} {$a->aname} is lower than {$a->warningpercent} ({$a->percent})';
$string['thirdpartyemailtextfooter'] = 'You are receiving this because the teacher of this course has added your email to the recipient’s list';
$string['thiscourse'] = 'This course';
$string['time'] = 'Time';
$string['timeahead'] = 'Multiple sessions that exceed one year cannot be created, please adjust the start and end dates.';
$string['to'] = 'to:';
$string['todate'] = 'to date';
$string['triggered'] = 'First notified';
$string['tuseremail'] = 'Email';
$string['tusername'] = 'Full name';
$string['ungraded'] = 'Ungraded sessions';
$string['unknowngroup'] = 'Unknown group';
$string['update'] = 'Update';
$string['uploadattendance'] = 'Upload attendance by CSV';
$string['usedefaultsubnet'] = 'Use default';
$string['usemessageform'] = 'or use the form below to send a message to the selected students';
$string['userexists'] = 'There is already a real user with this email address';
$string['userid'] = 'User ID';
$string['userimportfield'] = 'External user field';
$string['userimportfield_help'] = 'Field from uploaded CSV that contains user identifier';
$string['userimportto'] = 'Moodle user field';
$string['userimportto_help'] = 'Moodle field that matches the data from the CSV export';
$string['users'] = 'Users to export';
$string['usestatusset'] = 'Status set';
$string['variable'] = 'variable';
$string['variablesupdated'] = 'Variables successfully updated';
$string['versionforprinting'] = 'version for printing';
$string['viewmode'] = 'View mode';
$string['warnafter'] = 'Number of sessions taken before warning';
$string['warnafter_help'] = 'Warnings will only be triggered when the user has had their attendance taken for at least this number of sessions.';
$string['warningdeleted'] = 'Warning deleted';
$string['warningdesc'] = 'These warnings will be automatically added to any new attendance activities. If more than one warning is triggered at exactly the same time, only the warning with the lower warning threshold will be sent.';
$string['warningdesc_course'] = 'Warnings thresholds set here affect the absentee report and allow students and third parties to be notified.  If more than one warning is triggered at exactly the same time, only the warning with the lower warning threshold will be sent.';
$string['warningfailed'] = 'You cannot create a warning that uses the same percentage and number of sessions.';
$string['warningpercent'] = 'Warn if percentage falls under';
$string['warningpercent_help'] = 'A warning will be triggered when the overall percentage falls below this number.';
$string['warnings'] = 'Warnings set';
$string['warningthreshold'] = 'Warning threshold';
$string['warningupdated'] = 'Updated warnings';
$string['week'] = 'week(s)';
$string['weekcommencing'] = 'Week commencing';
$string['weeks'] = 'Weeks';
$string['youcantdo'] = 'You can\'t do anything';
