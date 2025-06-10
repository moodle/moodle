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
 * Web service declarations.
 *
 * @package   report_lsusql
 * @copyright 2020 the Open University
 * @copyright 2022 Louisiana State University
 * @copyright 2022 Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'report_lsusql_get_users' => [
        'classname' => 'report_lsusql\external\get_users',
        'methodname' => 'execute',
        'classpath' => '',
        'description' => 'Use by form autocomplete for selecting users to receive emails.',
        'capabilities' => 'report/lsusql:definequeries',
        'type' => 'read',
        'ajax' => true,
    ],
];
