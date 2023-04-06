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
 * Local plugin "QubitsUser"
 *
 * @package   local_qubitsuser
 * @author    Qubits Dev Team
 * @copyright 2023 <https://www.yardstickedu.com/>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Qubits - Multitenancy User Management';
$string['createuser'] = 'Create user';
$string['createuseragain'] = 'Submit and create another user';
$string['createuserandback'] = 'Submit and back to dashboard';
$string['leavepasswordemptytogenerate'] = 'Leave empty to have a password generated.</br>If you are manually setting a password, for</br>security reasons, only select to send by email</br>if the force change password option is selected!';
$string['usercreated'] = 'User created successfully';
$string['userassigned'] = 'User assigned this domain successfully';
$string['assignexistinguser'] = 'Assign Existing user in this domain';
$string['emailnotexists'] = 'Given email doesn\'t exist';