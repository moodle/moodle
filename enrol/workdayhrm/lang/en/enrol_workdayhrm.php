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
 * @package    enrol_workdayhrm
 * @copyright  2023 onwards LSU Online & Continuing Education
 * @copyright  2023 onwards Robert Russo
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.MoodleInternal.MoodleInternalNotNeeded

defined('MOODLE_INTERNAL') || die();

// The basics.
$string['pluginname'] = 'Workday HRM Enrollment';
$string['pluginname_desc'] = 'LSU Workday HRM Enrollment';
$string['reprocess'] = 'Reprocess Workday HRM';
$string['workdayhrm:reprocess'] = 'Reprocess Workday HRM';
$string['workdayhrm:delete'] = 'Delete Workday HRM';
$string['workdayhrm:showhide'] = 'Show/Hide Workday HRM';

// The tasks.
$string['workdayhrm_full_enroll'] = 'Workday HRM Enrollment';

// Settings.
$string['workdayhrm_token'] = 'Token';
$string['workdayhrm_token_help'] = 'Base64 encoded username and password supplied by the webservice creator.';
$string['workdayhrm_wsurl'] = 'Webservice Endpoint';
$string['workdayhrm_wsurl_help'] = 'Complete URL for the webservice endpoint.';
$string['workdayhrm_studentrole'] = 'Student Role';
$string['workdayhrm_studentrole_help'] = 'The role you want to use for students in the HRM courses.';
$string['workdayhrm_suspend_unenroll'] = 'Unenroll or Suspend';
$string['workdayhrm_suspend_unenroll_help'] = 'Unenroll or suspend students.';
$string['workdayhrm_contacts'] = 'Email Contacts';
$string['workdayhrm_contacts_help'] = 'Comma separated list of Moodle usernames you wish to email statuses and errors.';
$string['homedomain'] = 'Home Domain';
$string['homedomain_desc'] = 'Your OAuth2 home domain. These emails will not be converted to #ext# usernames.';
$string['extdomain'] = 'External Domain';
$string['extdomain_desc'] = 'Your OAuth2 external domain used by your authentication source.';
$string['workdayhrm_courseids'] = 'HRM Courses';
$string['workdayhrm_courseids_help'] = 'Comma separated list of Moodle courseids you wish to populate with staff members.';

// Emails.
$string['workdayhrm_emailname'] = 'WorkdayHRM Enrollment Administrator';

