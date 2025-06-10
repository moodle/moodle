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
 * Strings for component 'tool_dynamicrule', language 'en_us', version '4.1'.
 *
 * @package     tool_dynamicrule
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['conditioncohortmemberdescription'] = 'Users who are members of cohort \'{$a}\'';
$string['conditioncohortmemberdescriptionwithdate'] = 'Users who are members of cohort \'{$a->name}\'<br />
Added to cohort on or after: \'{$a->conditiondate}\'';
$string['conditioncohortnotmemberdescription'] = 'Users who are not members of cohort \'{$a}\'';
$string['conditioncoursecompleteddescription'] = 'Users who have completed course \'{$a}\'';
$string['conditioncoursenotcompleteddescription'] = 'Users who have not completed course \'{$a}\'';
$string['conditionuserenrolleddescription'] = 'Users who are enrolled in course \'{$a->course}\'<br />
Enrolment method: \'{$a->enrol}\'';
$string['conditionuserenrolleddescriptionwithdate'] = 'Users who are enrolled in course \'{$a->course}\'<br />
Enrolment method: \'{$a->enrol}\'<br />
Enrolment start date on or after: \'{$a->conditiondate}\'';
$string['conditionusernotenrolleddescription'] = 'Users who are not enrolled in course \'{$a->course}\'<br />
Enrolment method: \'{$a->enrol}\'';
$string['conditionuserprofilefielddescription'] = 'Users whose value for profile field \'{$a->fieldname}\' is \'{$a->fieldvalue}\'';
$string['editdetails'] = 'Edit details of rule \'{$a}\'';
$string['editrulename'] = 'Edit name of rule \'{$a}\'';
$string['importlogerror'] = 'Couldn\'t import rule \'{$a}\'';
$string['newnameforrule'] = 'New name for rule \'{$a}\'';
$string['outcomecohortdescription'] = 'Add users to cohort \'{$a}\'';
