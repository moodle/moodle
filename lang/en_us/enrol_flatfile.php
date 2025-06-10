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
 * Strings for component 'enrol_flatfile', language 'en_us', version '4.1'.
 *
 * @package     enrol_flatfile
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['expiredaction'] = 'Enrollment expiration action';
$string['expiredaction_help'] = 'Select action to carry out when user enrollment expires. Please note that some user data and settings are purged from course during course unenrollment.';
$string['filelockedmail'] = 'The text file you are using for file-based enrollments ({$a}) can not be deleted by the cron process.  This usually means the permissions are wrong on it.  Please fix the permissions so that Snap can delete the file, otherwise it might be processed repeatedly.';
$string['filelockedmailsubject'] = 'Important error: Enrollment file';
$string['flatfile:manage'] = 'Manage user enrollments manually';
$string['flatfile:unenrol'] = 'Unenroll users from the course manually';
$string['flatfileenrolments'] = 'Flat file (CSV) enrollments';
$string['flatfilesync'] = 'Flat file enrollment sync';
$string['location_desc'] = 'Specify full path to the enrollment file. The file is automatically deleted after processing.';
$string['messageprovider:flatfile_enrolment'] = 'Flat file enrollment messages';
$string['notifyenrolled'] = 'Notify enrolled users';
$string['notifyenroller'] = 'Notify user responsible for enrollments';
$string['privacy:metadata:enrol_flatfile'] = 'The Flat file (CSV) enrollment plugin may store personal data relating to future enrollments in the enrol_flatfile table.';
$string['privacy:metadata:enrol_flatfile:action'] = 'The enrollment action expected at the given date.';
$string['privacy:metadata:enrol_flatfile:courseid'] = 'The courseid to which the enrollment relates.';
$string['privacy:metadata:enrol_flatfile:timeend'] = 'The time at which the enrollment change ends.';
$string['privacy:metadata:enrol_flatfile:timemodified'] = 'The modification time of this enrollment change.';
$string['privacy:metadata:enrol_flatfile:timestart'] = 'The time at which the enrollment change starts.';
