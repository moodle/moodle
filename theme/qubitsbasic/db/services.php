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
 * @package   theme_qubitsbasic
 * @author    Qubits Dev Team
 * @copyright 2023 <https://www.yardstickedu.com/>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = [
    'theme_qubitsbasic_get_enrolled_courses_by_timeline_classification' => [
        'classname' => 'theme_qubitsbasic_external',
        'methodname' => 'get_enrolled_courses_by_timeline_classification',
        'classpath' => 'theme/qubitsbasic/externallib.php',
        'description' => 'List of enrolled courses for the given timeline classification (past, inprogress, or future).',
        'type' => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ]
];