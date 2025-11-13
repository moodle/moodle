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
 * @package    enrol_reenroller
 * @copyright  2025 Onwards LSU Online & Continuing Education
 * @author     2025 Onwards Robert Russo
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Basic strings.
$string['pluginname'] = 'Re-Enroller';
$string['privacy:metadata'] = 'The Re-Enroller plugin does not store personal data permanently.';
$string['reenroller:delete'] = 'Delete ReEnroller Instance';
$string['reenroller:showhide'] = 'Show/Hide ReEnroller Instance';

// Settings strings.
$string['setting:targetcategory'] = 'Target course categories';
$string['setting:targetcategory_desc'] = 'Only courses in these categories will be considered.';
$string['setting:sourcerole'] = 'Source role';
$string['setting:sourcerole_desc'] = 'Role to search for expired users.';
$string['setting:targetrole'] = 'Target role';
$string['setting:targetrole_desc'] = 'Role to assign when reenrolling users.';
$string['setting:instance_name'] = 'Enrollment method';
$string['setting:instance_name_desc'] = 'Search for expired students enrolled via this enrollment method.';
$string['setting:startdate'] = 'Enrollment expiration search date';
$string['setting:startdate_desc'] = 'The plugin seaches for expired enrollments AFTER this date in MM/DD/YYYY format.';
$string['setting:timelineheader'] = 'Enrollment Duration';
$string['setting:timelineheader_desc'] = 'The number (any number) of units (days, weeks, months, years) the new enrollment will remain active.';
$string['setting:timelinevalue'] = 'Number';
$string['setting:timelinevalue_desc'] = 'The number of whatever units you select below. This determines when the reenroller student is expired.';
$string['setting:timelineunit'] = 'Units';
$string['setting:timelineunit_desc'] = 'The units that determine the duration based on the above number entered.';

// Task strings.
$string['task:processexpired'] = 'Re-enroll expired d1 users who completed configured-category courses';
