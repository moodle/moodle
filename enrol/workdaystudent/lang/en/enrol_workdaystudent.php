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
 * @package    enrol_workdaystudent
 * @copyright  2023 onwards LSU Online & Continuing Education
 * @copyright  2023 onwards Robert Russo
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.MoodleInternal.MoodleInternalNotNeeded

defined('MOODLE_INTERNAL') || die();

// The basics.
$string['pluginname'] = 'Workday Student Enrollment';
$string['pluginname_desc'] = 'LSU Workday Student Enrollment';
$string['workdaystudent:periodconfig'] = 'Period Configuration';
$string['reprocess'] = 'Reprocess Enrollment';
$string['workdaystudent:reprocess'] = 'Reprocess Enrollment';
$string['workdaystudent:delete'] = 'Delete Workday Student';
$string['workdaystudent:showhide'] = 'Show/Hide Workday Student';

// Basic Settings.
$string['workdaystudent:pluginsettings'] = 'Plugin Settings';
$string['workdaystudent:webservices'] = 'Webservice Settings';
$string['workdaystudent:emails'] = 'Administrative Contacts';
$string['workdaystudent:coursedefs'] = 'Course Defaults';

// Course Default Settings.
$string['workdaystudent:suspend'] = 'Suspend students';
$string['workdaystudent:suspend_desc'] = "Suspend students on unenroll instead of removing them from the course.";
$string['workdaystudent:course_grouping'] = 'Section grouping';
$string['workdaystudent:course_grouping_desc'] = "Moodle will pool course sections according to the primary instructor.";
$string['workdaystudent:createprior'] = 'Create Days';
$string['workdaystudent:createprior_desc'] = 'Create courses X days before the semester begins.';
$string['workdaystudent:enrollprior'] = 'Enroll Days';
$string['workdaystudent:enrollprior_desc'] = 'Enroll courses X days before the semester begins.';
$string['workdaystudent:numberthreshold'] = 'Course threshold';
$string['workdaystudent:numberthreshold_desc'] = "Moodle will NOT create courses with course numbers greater than and including the value specified here.";
$string['workdaystudent:format'] = 'Course format';
$string['workdaystudent:autoparent'] = 'Parent Category by Semester';
$string['workdaystudent:autoparent_desc'] = "Create all new course categories within semesterly categories.";
$string['workdaystudent:parentcat'] = 'Specified Parent Category';
$string['workdaystudent:parentcat_desc'] = "Create all new course categories within this category.";
$string['workdaystudent:visible'] = 'Course creation visibility';
$string['workdaystudent:visible_desc'] = "Create all new courses with them visible to students.";
$string['workdaystudent:coursenamingformat'] = 'Naming format';
$string['workdaystudent:coursenamingformat_desc'] = "Possible options:<br>{period_year}<br>{course_subject_abbreviation}<br>{course_number}<br>{class_type}<br>{firstname}<br>{lastname}<br>{delivery_mode}";

// The tasks.
$string['workdaystudent_full_enroll'] = 'Workday Student Enrollment';
$string['workdaystudent_quick_enroll'] = 'Quicker Workday Student Enrollment';

// Authentication Settings.
$string['workdaystudent:username'] = 'Username';
$string['workdaystudent:username_desc'] = 'Username supplied by the webservice creator.';
$string['workdaystudent:password'] = 'Password';
$string['workdaystudent:password_desc'] = 'Password supplied by the webservice creator.';

// Basic Config.
$string['workdaystudent:apiversion'] = 'API Version';
$string['workdaystudent:apiversion_desc'] = 'Workday Student API version.';
$string['workdaystudent:campus'] = 'Campus Code';
$string['workdaystudent:campus_desc'] = 'Campus code supplied by Workday Student contact.';
$string['workdaystudent:campusname'] = 'Campus Name';
$string['workdaystudent:campusname_desc'] = 'Campus name supplied by Workday Student contact.';
$string['workdaystudent:brange'] = 'Date Range';
$string['workdaystudent:brange_desc'] = 'Number of days ahead for grabbing future semesters.';
$string['workdaystudent:erange'] = 'Date Range';
$string['workdaystudent:erange_desc'] = 'Number of days behind for grabbing past semesters.';
$string['workdaystudent:metafields'] = 'Meta Fields';
$string['workdaystudent:metafields_desc'] = 'Comma separated list of meta fields to fetch.';
$string['workdaystudent:sportfield'] = 'Sport Field';
$string['workdaystudent:sportfield_desc'] = 'Sport field designator.';
$string['workdaystudent:primaryrole'] = 'Primary Role';
$string['workdaystudent:primaryrole_desc'] = 'The role you want to use for primary instructors in courses.';
$string['workdaystudent:nonprimaryrole'] = 'Non-primary Role';
$string['workdaystudent:nonprimaryrole_desc'] = 'The role you want to use for non-primary instructors in courses.';
$string['workdaystudent:studentrole'] = 'Student Role';
$string['workdaystudent:studentrole_desc'] = 'The role you want to use for students in the student courses.';
$string['workdaystudent:suspend_unenroll'] = 'Unenroll or Suspend';
$string['workdaystudent:suspend_unenroll_desc'] = 'Unenroll or suspend students.';
$string['workdaystudent:contacts'] = 'Email Contacts';
$string['workdaystudent:contacts_desc'] = 'Comma separated list of Moodle usernames you wish to email statuses and errors.';

// Webservice URL and endpoint suffixes.
$string['workdaystudent:wsurl'] = 'Webservice Endpoint';
$string['workdaystudent:wsurl_desc'] = 'Base URL for the webservice endpoint.';
$string['workdaystudent:units'] = 'Units Endpoint';
$string['workdaystudent:units_desc'] = 'URL suffix for the academic units endpoint.';
$string['workdaystudent:periods'] = 'Periods Endpoint';
$string['workdaystudent:periods_desc'] = 'URL suffix for the academic periods endpoint.';
$string['workdaystudent:programs'] = 'Programs Endpoint';
$string['workdaystudent:programs_desc'] = 'URL suffix for the programs of study endpoint.';
$string['workdaystudent:grading_schemes'] = 'Grading Schemes Endpoint';
$string['workdaystudent:grading_schemes_desc'] = 'URL suffix for the grading schemes endpoint.';
$string['workdaystudent:courses'] = 'Courses Endpoint';
$string['workdaystudent:courses_desc'] = 'URL suffix for the courses endpoint.';
$string['workdaystudent:sections'] = 'Sections Endpoint';
$string['workdaystudent:sections_desc'] = 'URL suffix for the course sections endpoint.';
$string['workdaystudent:dates'] = 'Dates Endpoint';
$string['workdaystudent:dates_desc'] = 'URL suffix for the academic dates endpoint.';
$string['workdaystudent:students'] = 'Students Endpoint';
$string['workdaystudent:students_desc'] = 'URL suffix for the students endpoint.';
$string['workdaystudent:registrations'] = 'Registrations Endpoint';
$string['workdaystudent:registrations_desc'] = 'URL suffix for the student section registrations endpoint.';
$string['workdaystudent:guild'] = 'GUILD Endpoint';
$string['workdaystudent:guild_desc'] = 'URL suffix for the GUILD associations endpoint.';

// Emails.
$string['workdaystudent:emailname'] = 'WorkdayStudent Enrollment Administrator';

// End user settings.
$string['workdaystudent:visibleonsemdate'] = 'Courses visibility';
$string['workdaystudent:visibleonsemdate_desc'] = "Courses will be made visible on the semester start date.";

// Config / extra page strings.
$string['wds:sshortname'] = 'WDS Settings';
$string['wds:cshortname'] = 'WDS Config';
$string['wds:academic_period'] = 'Period';
$string['wds:academic_period_id'] = 'Academic Period ID';
$string['wds:academic_year'] = 'Academic Year';
$string['wds:period_year'] = 'Year';
$string['wds:period_type'] = 'Type';
$string['wds:start_date'] = 'Start Date';
$string['wds:end_date'] = 'End Date';
$string['wds:enabled'] = 'Enabled';
$string['wds:updateusers'] = 'Update Users';
$string['wds:updatestudents'] = 'Update Students';
$string['wds:updatestudents_desc'] = 'Update moodle students en masse based on data in the interstitial database.';
$string['wds:updateteachers'] = 'Update Teachers';
$string['wds:updateteachers_desc'] = 'Update moodle teachers en masse based on data in the interstitial database.';
$string['wds:runstudentupdate'] = 'Student Updates';
$string['wds:runteacherupdate'] = 'Teacher Updates';
$string['wds:massupdate_ssuccess'] = 'Successfully updated all students to match their interstitial DB counterparts.';
$string['wds:massupdate_sfail'] = 'Student updates failed!';
$string['wds:massupdate_fsuccess'] = 'Successfully updated all faculty to match their interstitial DB counterparts.';
$string['wds:massupdate_ffail'] = 'Faculty updates failed!';
$string['wds:massupdate_dberror'] = 'Database Error!';
$string['wds:access_error'] = 'You do not have access to do this.';
