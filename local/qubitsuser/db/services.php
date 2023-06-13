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
 * Web service function declarations for the plugintype_pluginname plugin.
 *
 * @package   local_qubitsuser
 * @author    Qubits Dev Team
 * @copyright 2023 <https://www.yardstickedu.com/>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = [
    'local_qubitsuser_get_potential_users' => [
        'classname' => 'local_qubitsuser_external',
        'methodname' => 'get_potential_users',
        'classpath' => 'local/qubitsuser/externallib.php',
        'description' => 'Get the list of potential users to enrol',
        'ajax' => true,
        'type' => 'read',
        'capabilities' => 'moodle/course:enrolreview'
    ]
];