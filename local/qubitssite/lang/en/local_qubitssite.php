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
 * Local plugin "QubitsSite"
 *
 * @package   local_qubitssite
 * @author    Qubits Dev Team
 * @copyright 2023 <https://www.yardstickedu.com/>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Qubits - Multitenancy Site Management';
$string['accessdenied'] = 'Access denied. Please contact site administrator!';
$string['addnewsite'] = 'Add a new Qubits Tenant site';
$string['createsite'] = 'Create a Tenant Site';
$string['deletesite'] = 'Delete a Tenant Site';
$string['editsitesettings'] = 'Edit Qubits Tenant site settings';
$string['no_records_found'] = 'No Records Found!';
$string['missingqubitssitename'] = 'Missing Qubits Tenant Site Name';
$string['missingqubitssitehostname'] = 'Missing Qubits Tenant Site URL';
$string['qubitssitename'] = 'Qubits Tenant Site Name';
$string['qubitssitename_help'] = 'It is a Qubits Tenant Site Name, helps to quickly identify the tenant';
$string['qubitssitenametaken'] = 'Qubits Tenant Site Name is already used for another Tenant ({$a})';
$string['qubitssitehostname'] = 'Qubits Tenant Site URL';
$string['qubitssitehostname_help'] = 'It is a Qubits Tenant Site URL, you don\'t give the url with http or https. It will be automatically removed, while updating the details.';
$string['qubitssitehostnametaken'] = 'Qubits Tenant Site URL is already used for another Tenant ({$a})';
$string['qubitssitehostnamemaintaken'] = 'Don\'t give the main domain url. Main Domain : ({$a})';
$string['qubitssite:createtenantsite'] = 'Create Tenant Site';
$string['qubitssite:edittenantsite'] = 'Edit Tenant Site';
$string['qubitssite:deletetenantsite'] = 'Delete Tenant Site';
$string['qubitssite:viewtenantsite'] = 'View Tenant Sites';
$string['siteslist'] = 'Qubits - Multitenancy Sites List';
$string['title'] = 'Qubits Site {$a}';
$string['viewsite'] = 'View Site';
$string['managequbits'] = 'Manage Qubits Sites';