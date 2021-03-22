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
 * Strings for component 'course', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   core_course
 * @copyright 2018 Adrian Greeve <adriangreeve.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['activitychoosercategory'] = 'Activity chooser';
$string['activitychooserrecommendations'] = 'Recommended activities';
$string['activitychoosersettings'] = 'Activity chooser settings';
$string['activitychooseractivefooter'] = 'Activity chooser footer';
$string['activitychooseractivefooter_desc'] = 'The activity chooser can support plugins that add items to the footer.';
$string['activitychooserhidefooter'] = 'No footer';
$string['activitychoosertabmode'] = 'Activity chooser tabs';
$string['activitychoosertabmode_desc'] = "The activity chooser enables a teacher to easily select activities and resources to add to their course. This setting determines which tabs should be displayed in it. Note that the starred tab is only displayed for a user if they have starred one or more activities and the recommended tab is only displayed if a site administrator has specified some recommended activities.";
$string['activitychoosertabmodeone'] = 'Starred, All, Activities, Resources, Recommended';
$string['activitychoosertabmodetwo'] = 'Starred, All, Recommended';
$string['activitychoosertabmodethree'] = 'Starred, Activities, Resources, Recommended';
$string['activitydate:closed'] = 'Closed:';
$string['activitydate:closes'] = 'Closes:';
$string['activitydate:opened'] = 'Opened:';
$string['activitydate:opens'] = 'Opens:';
$string['aria:coursecategory'] = 'Course category';
$string['aria:courseimage'] = 'Course image';
$string['aria:courseshortname'] = 'Course short name';
$string['aria:coursename'] = 'Course name';
$string['aria:defaulttab'] = 'Default activities';
$string['aria:favourite'] = 'Course is starred';
$string['aria:favouritestab'] = 'Starred activities';
$string['aria:recommendedtab'] = 'Recommended activities';
$string['aria:modulefavourite'] = 'Star {$a} activity';
$string['coursealreadyfinished'] = 'Course already finished';
$string['coursenotyetstarted'] = 'The course has not yet started';
$string['coursenotyetfinished'] = 'The course has not yet finished';
$string['coursetoolong'] = 'The course is too long';
$string['customfield_islocked'] = 'Locked';
$string['customfield_islocked_help'] = 'If the field is locked, only users with the capability to change locked custom fields (by default users with the default role of manager only) will be able to change it in the course settings.';
$string['customfield_notvisible'] = 'Nobody';
$string['customfield_visibility'] = 'Visible to';
$string['customfield_visibility_help'] = 'This setting determines who can view the custom field name and value in the list of courses or in the available custom field filter of the Dashboard.';
$string['customfield_visibletoall'] = 'Everyone';
$string['customfield_visibletoteachers'] = 'Teachers';
$string['customfieldsettings'] = 'Common course custom fields settings';
$string['downloadcourseconfirmation'] = 'You are about to download a zip file of course content (excluding items which cannot be downloaded and any files larger than {$a}).';
$string['downloadcoursecontent'] = 'Download course content';
$string['downloadcoursecontent_help'] = 'This setting determines whether course content may be downloaded by users with the download course content capability (by default users with the role of student or teacher).';
$string['enabledownloadcoursecontent'] = 'Enable download course content';
$string['errorendbeforestart'] = 'The end date ({$a}) is before the course start date.';
$string['favourite'] = 'Starred course';
$string['gradetopassnotset'] = 'This course does not have a grade to pass set. It may be set in the grade item of the course (Gradebook setup).';
$string['informationformodule'] = 'Information about the {$a} activity';
$string['module'] = 'Activity';
$string['nocourseactivity'] = 'Not enough course activity between the start and the end of the course';
$string['nocourseendtime'] = 'The course does not have an end time';
$string['nocoursesections'] = 'No course sections';
$string['nocoursestudents'] = 'No students';
$string['noaccesssincestartinfomessage'] = 'Hi {$a->userfirstname},
<p>A number of students in {$a->coursename} have never accessed the course.</p>';
$string['norecentaccessesinfomessage'] = 'Hi {$a->userfirstname},
<p>A number of students in {$a->coursename} have not accessed the course recently.</p>';
$string['noteachinginfomessage'] = 'Hi {$a->userfirstname},
<p>Courses with start dates in the next week have been identified as having no teacher or student enrolments.</p>';
$string['privacy:perpage'] = 'The number of courses to show per page.';
$string['privacy:completionpath'] = 'Course completion';
$string['privacy:favouritespath'] = 'Course starred information';
$string['privacy:metadata:activityfavouritessummary'] = 'The course system contains information about which items from the activity chooser have been starred by the user.';
$string['privacy:metadata:completionsummary'] = 'The course contains completion information about the user.';
$string['privacy:metadata:favouritessummary'] = 'The course contains information relating to the course being starred by the user.';
$string['recommend'] = 'Recommend';
$string['recommendcheckbox'] = 'Recommend activity: {$a}';
$string['searchactivitiesbyname'] = 'Search for activities by name';
$string['searchresults'] = 'Search results: {$a}';
$string['submitsearch'] = 'Submit search';
$string['studentsatriskincourse'] = 'Students at risk in {$a} course';
$string['studentsatriskinfomessage'] = 'Hi {$a->userfirstname},
<p>Students in the {$a->coursename} course have been identified as being at risk.</p>';
$string['target:coursecompletion'] = 'Students at risk of not meeting the course completion conditions';
$string['target:coursecompletion_help'] = 'This target describes whether the student is considered at risk of not meeting the course completion conditions.';
$string['target:coursecompetencies'] = 'Students at risk of not achieving the competencies assigned to a course';
$string['target:coursecompetencies_help'] = 'This target describes whether a student is at risk of not achieving the competencies assigned to a course. This target considers that all competencies assigned to the course must be achieved by the end of the course.';
$string['target:coursedropout'] = 'Students at risk of dropping out';
$string['target:coursedropout_help'] = 'This target describes whether the student is considered at risk of dropping out.';
$string['target:coursegradetopass'] = 'Students at risk of not achieving the minimum grade to pass the course';
$string['target:coursegradetopass_help'] = 'This target describes whether the student is at risk of not achieving the minimum grade to pass the course.';
$string['target:noaccesssincecoursestart'] = 'Students who have not accessed the course yet';
$string['target:noaccesssincecoursestart_help'] = 'This target describes students who never accessed a course they are enrolled in.';
$string['target:noaccesssincecoursestartinfo'] = 'The following students are enrolled in a course which has started, but they have never accessed the course.';
$string['target:norecentaccesses'] = 'Students who have not accessed the course recently';
$string['target:norecentaccesses_help'] = 'This target identifies students who have not accessed a course they are enrolled in within the set analysis interval (by default the past month).';
$string['target:norecentaccessesinfo'] = 'The following students have not accessed a course they are enrolled in within the set analysis interval (by default the past month).';
$string['target:noteachingactivity'] = 'Courses at risk of not starting';
$string['target:noteachingactivity_help'] = 'This target describes whether courses due to start in the coming week will have teaching activity.';
$string['target:noteachingactivityinfo'] = 'The following courses due to start in the upcoming days are at risk of not starting because they don\'t have teachers or students enrolled.';
$string['targetlabelstudentcompletionno'] = 'Student who is likely to meet the course completion conditions';
$string['targetlabelstudentcompletionyes'] = 'Student at risk of not meeting the course completion conditions';
$string['targetlabelstudentcompetenciesno'] = 'Student who is likely to achieve the competencies assigned to a course';
$string['targetlabelstudentcompetenciesyes'] = 'Student at risk of not achieving the competencies assigned to a course';
$string['targetlabelstudentdropoutyes'] = 'Student at risk of dropping out';
$string['targetlabelstudentdropoutno'] = 'Not at risk';
$string['targetlabelstudentgradetopassno'] = 'Student who is likely to meet the minimum grade to pass the course.';
$string['targetlabelstudentgradetopassyes'] = 'Student at risk of not meeting the minimum grade to pass the course.';
$string['targetlabelteachingyes'] = 'Users with teaching capabilities who have access to the course';
$string['targetlabelteachingno'] = 'Courses at risk of not starting';
