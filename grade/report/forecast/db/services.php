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
 * Forecast grade report external functions and service definitions.
 *
 * @package    gradereport_forecast
 * @copyright  2016 Louisiana State University, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(

    'gradereport_forecast_get_grades_table' => array(
        'classname' => 'gradereport_forecast_external',
        'methodname' => 'get_grades_table',
        'classpath' => 'grade/report/forecast/externallib.php',
        'description' => 'Get the forecast report grades table for a course',
        'type' => 'read',
        'capabilities' => 'gradereport/forecast:view',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'gradereport_forecast_view_grade_report' => array(
        'classname' => 'gradereport_forecast_external',
        'methodname' => 'view_grade_report',
        'classpath' => 'grade/report/forecast/externallib.php',
        'description' => 'Trigger the report view event',
        'type' => 'write',
        'capabilities' => 'gradereport/forecast:view',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    )
);
