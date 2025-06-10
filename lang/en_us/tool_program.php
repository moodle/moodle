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
 * Strings for component 'tool_program', language 'en_us', version '4.1'.
 *
 * @package     tool_program
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allocationfor'] = 'Allocation for \'{$a}\'';
$string['conditionprogramcompleteddescription'] = 'Users that have status \'Completed\' in program \'{$a}\'';
$string['conditionprogramcompleteddescriptionwithdate'] = 'Users that have status \'Completed\' in program \'{$a->programname}\'<br />
Completion date is on or after \'{$a->conditiondate}\'';
$string['conditionprogramnotcompleteddescription'] = 'Users that do not have status \'Completed\' in program \'{$a}\'';
$string['conditionprogramoverduedescription'] = 'Users that have status \'Overdue\' in program \'{$a}\'';
$string['conditionprogramoverduedescriptionwithdate'] = 'Users that have status \'Overdue\' in program \'{$a->programname}\'<br />
Due date is on or after \'{$a->conditiondate}\'';
$string['conditionprogramsuspendeddescription'] = 'Users that have status \'Suspended\' in program \'{$a}\'';
$string['conditionprogramsuspendeddescriptionwithdate'] = 'Users that have status \'Suspended\' in program \'{$a->programname}\'<br />
Suspended date is on or after \'{$a->conditiondate}\'';
$string['conditionuserallocateddescription'] = 'Users allocated to program \'{$a}\'';
$string['conditionuserallocateddescriptionwithdate'] = 'Users allocated to program \'{$a->programname}\'<br />
Allocation date is on or after \'{$a->conditiondate}\'';
$string['conditionusernotallocateddescription'] = 'Users not allocated to program \'{$a}\'';
$string['editprogram'] = 'Edit program \'{$a}\'';
$string['errorcouldnotallocate'] = 'Could not allocate user \'{$a->originaluserfullname}\' to program \'{$a->program}\'';
$string['importlogfailed'] = 'Could not import program \'{$a->fullname}\'';
$string['importlogsuccessuserallocations'] = 'Allocated user \'{$a->userfullname}\' into program \'{$a->program}\'';
$string['newnameforset'] = 'New name for \'{$a}\'';
$string['notificationsubjectprogramuserallocated'] = 'Allocated to program \'{$a}\'';
$string['notificationsubjectprogramuserdeallocated'] = 'Deallocated from program \'{$a}\'';
$string['outcomeallocationdescriptionwithdate'] = 'Allocate users to program \'{$a->programname}\'<br />
Program start date: \'{$a->startdate}\'';
$string['outcomedeallocationdescription'] = 'Deallocate users from program \'{$a}\'';
