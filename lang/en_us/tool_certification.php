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
 * Strings for component 'tool_certification', language 'en_us', version '4.1'.
 *
 * @package     tool_certification
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allocationfor'] = 'Allocation for \'{$a}\'';
$string['conditioncertificationcertifieddescriptionwithdate'] = 'Users that have status Certified in certification \'{$a->fullname}\'<br />
Certified date is on or after \'{$a->conditiondate}\'';
$string['conditioncertificationexpireddescriptionwithdate'] = 'Users that have status Expired in certification \'{$a->fullname}\'<br />
Expired date is on or after \'{$a->conditiondate}\'';
$string['conditioncertificationoverduedescriptionwithdate'] = 'Users that have status Overdue in certification \'{$a->fullname}\'<br />
Due date is on or after \'{$a->conditiondate}\'';
$string['conditioncertificationsuspendeddescriptionwithdate'] = 'Users that have status Suspended in certification \'{$a->fullname}\'<br />
Suspended date is on or after \'{$a->conditiondate}\'';
$string['conditionrecertificationgraceperiodendsdescription'] = 'Users whose grace period ends in certification \'{$a->fullname}\'';
$string['conditionrecertificationgraceperiodendsdescriptionwithdate'] = 'Users whose grace period ends in certification \'{$a->fullname}\'<br />
Recertification grace period ends on or before \'{$a->conditiondate}\'';
$string['conditionrecertificationstarteddescription'] = 'Users that have started a recertification period in certification \'{$a->fullname}\'';
$string['conditionrecertificationstarteddescriptionwithdate'] = 'Users that have started a recertification period in certification \'{$a->fullname}\'<br />
Recertification started on or after \'{$a->conditiondate}\'';
$string['conditionuserallocateddescriptionwithdate'] = 'Users allocated to certification {$a->fullname}<br />
Allocation date is on or after \'{$a->conditiondate}\'';
$string['editcertification'] = 'Edit certification \'{$a}\'';
$string['errorcouldnotallocate'] = 'Could not allocate user \'{$a->originaluserfullname}\' to certification \'{$a->certification}\'';
$string['importlogfailed'] = 'Could not import certification \'{$a->fullname}\'';
$string['importlogsuccess'] = 'Created new certification \'<a href="{$a->url}">{$a->fullname}</a>\'';
$string['importlogsuccessuserallocations'] = 'Allocated user \'{$a->userfullname}\' into certification \'{$a->certification}\'';
$string['notificationsubjectcertificationuserallocated'] = 'Allocated to certification \'{$a}\'';
$string['notificationsubjectcertificationuserdeallocated'] = 'Deallocated from certification \'{$a}\'';
$string['outcomeallocationdescriptionwithdate'] = 'Allocate users to certification {$a->certificationname}<br />
Certification start date: \'{$a->startdate}\'';
