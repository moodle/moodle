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
 * singleview grade report external functions and service definitions.
 *
 * @package   gradereport_singleview
 * @copyright 2022 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

$functions = [
    'gradereport_singleview_get_grade_items_for_search_widget' => [
        'methodname' => 'get_grade_items_for_search_widget',
        'classname' => 'gradereport_singleview\\external\\singleview',
        'description' => 'Get the gradeitem/(s) for a course',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'gradereport/singleview:view',
        'services' => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
];
