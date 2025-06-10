<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'enrol_imsenterprise', language 'en_us', version '4.1'.
 *
 * @package     enrol_imsenterprise
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allowunenrol'] = 'Allow the IMS data to <strong>unenroll</strong> students/teachers';
$string['allowunenrol_desc'] = 'If enabled, course enrollments will be removed when specified in the Enterprise data.';
$string['createnewcategories_desc'] = 'If the <org><orgunit> element is present in a course\'s incoming data, its content will be used to specify a category if the course is to be created from scratch. The plugin will NOT re-categorize existing courses.

If no category exists with the desired name, then a hidden category will be created.';
$string['createnewcourses_desc'] = 'If enabled, the IMS Enterprise enrollment plugin can create new courses for any it finds in the IMS data but not in Moodle\'s database. Any newly-created courses are initially hidden.';
$string['createnewusers_desc'] = 'IMS Enterprise enrollment data typically describes a set of users. If enabled, accounts can be created for any users not found in Moodle\'s database.

Users are searched for first by their "idnumber", and second by their Moodle username. Passwords are not imported by the IMS Enterprise plugin. The use of an authentication plugin is recommended for authenticating users.';
$string['deleteusers_desc'] = 'If enabled, IMS Enterprise enrollment data can specify the deletion of user accounts (if the "recstatus" flag is set to 3, which represents deletion of an account). As is standard in Moodle, the user record isn\'t actually deleted from Moodle\'s database, but a flag is set to mark the account as deleted.';
$string['filelockedmail'] = 'The text file you are using for IMS-file-based enrollments ({$a}) can not be deleted by the cron process.  This usually means the permissions are wrong on it.  Please fix the permissions so that Snap can delete the file, otherwise it might be processed repeatedly.';
$string['filelockedmailsubject'] = 'Important error: Enrollment file';
$string['imsenterprise:config'] = 'Configure IMS Enterprise enroll instances';
$string['imsenterprisecrontask'] = 'Enrollment file processing';
$string['messageprovider:imsenterprise_enrolment'] = 'IMS Enterprise enrollment messages';
$string['privacy:metadata'] = 'The IMS Enterprise file enrollment plugin does not store any personal data.';
$string['updatecourses_desc'] = 'If enabled, the IMS Enterprise enrollment plugin can update course full and short names (if the "recstatus" flag is set to 2, which represents an update).';
$string['updateusers_desc'] = 'If enabled, IMS Enterprise enrollment data can specify changes to user accounts (if the "recstatus" flag is set to 2, which represents an update).';
