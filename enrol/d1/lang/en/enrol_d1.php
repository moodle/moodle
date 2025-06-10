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
 * @package    enrol_d1
 * @copyright  2022 onwards Louisiana State University
 * @copyright  2022 onwards Robert Russo
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.MoodleInternal.MoodleInternalNotNeeded

defined('MOODLE_INTERNAL') || die();

// The basics.
$string['pluginname'] = 'D1 Enrollment';
$string['pluginname_desc'] = 'LSU DestinyOne Enrollment';
$string['reprocess'] = 'Reprocess';

// The tasks.
$string['d1_full_enroll'] = 'D1 Enrollment';

// Settings.
$string['d1_studentrole'] = 'Student Role';
$string['d1_studentrole_help'] = 'From your Gradebook Roles, please select the role in which you want your D1 students enrolled.';
$string['d1_suspend_unenroll'] = 'Unenroll or supend';
$string['d1_suspend_unenroll_help'] = 'Unenroll or suspend the student.';
$string['d1_sendwelcome'] = 'Welcome Email';
$string['d1_sendwelcome_help'] = 'Send a welcome email to new users in addition to the standard Moodle welcome email.';
$string['d1_categories'] = 'Course Categories';
$string['d1_categories_help'] = 'The DestinyOne enrollment plugin will ONLY process ODL enrollments in these categories.';
$string['d1_username'] = 'D1 Username';
$string['d1_username_help'] = 'Web Services username for D1';
$string['d1_password'] = 'D1 Password';
$string['d1_password_help'] = 'Web Services password for the above username for D1';
$string['d1_extradebug'] = 'Extra Debugging';
$string['d1_extradebug_help'] = 'Write debug files out to the debug files location below.';
$string['d1_debugfiles'] = 'Debug Files Location';
$string['d1_debugfiles_help'] = 'File storage area for extra debugging.';
$string['d1_wsurl'] = 'WebService base url';
$string['d1_wsurl_help'] = 'The base webservice url used to connect to D1.';
$string['dd_calculated'] = 'Calculated Due Date';
$string['dd_specified'] = 'Specified Due Date';
$string['dd_none'] = 'No Due Date';
$string['d1_duedate'] = 'Due Date';
$string['d1_duedate_help'] = 'Enrollment end date as set either by adding the number of days to complete the section to the enrollment start date, using the specified enrollment due date in D1 (if one exists, otherwise fall back to the calculated date or no date depending on if a section duration exists), or not including a due date at all.';
$string['d1_fieldid'] = 'Field id';
$string['d1_fieldid_help'] = 'User profile field id used to house the secondary idnumber from D1.';
$string['d1_id_pre'] = 'Prefix';
$string['d1_id_pre_help'] = 'Custom user profile field prefix for pattern matching purposes.';

// Permissions.
$string['d1:showhide'] = 'Show / Hide enrollment instance';
$string['d1:reprocess'] = 'Reprocess enrollments';
$string['d1:delete'] = 'Delete enrollment instance';

