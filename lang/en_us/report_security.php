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
 * Strings for component 'report_security', language 'en_us', version '4.1'.
 *
 * @package     report_security
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['check_passwordpolicy_details'] = '<p>It is recommended that a password policy is set, since password guessing is very often the easiest way to gain unauthorized access.
Do not make the requirements too strict though, as this can result in users not being able to remember their passwords and either forgetting them or writing them down.</p>';
$string['check_riskbackup_details_users'] = '<p>Because of the above roles or local overrides, the following user accounts currently have permission to make backups containing private data from any users enrolled in their course. Make sure they are (a) trusted and (b) protected by strong passwords:</p> {$a}';
