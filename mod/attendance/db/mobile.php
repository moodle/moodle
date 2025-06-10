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
 * Defines mobile handlers.
 *
 * @package   mod_attendance
 * @copyright 2018 Dan Marsdenb
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$addons = [
    'mod_attendance' => [
        'handlers' => [
            'view' => [
                'displaydata' => [
                    'icon' => $CFG->wwwroot . '/mod/attendance/pix/icon.png',
                    'class' => '',
                ],
                'delegate' => 'CoreCourseModuleDelegate',
                'method' => 'mobile_view_activity',
                'styles' => [
                    'url' => $CFG->wwwroot . '/mod/attendance/mobilestyles.css',
                    'version' => 22
                ]
            ]
        ],
        'lang' => [ // Language strings that are used in all the handlers.
            ['pluginname', 'attendance'],
            ['sessionscompleted', 'attendance'],
            ['pointssessionscompleted', 'attendance'],
            ['percentagesessionscompleted', 'attendance'],
            ['sessionstotal', 'attendance'],
            ['pointsallsessions', 'attendance'],
            ['percentageallsessions', 'attendance'],
            ['maxpossiblepoints', 'attendance'],
            ['maxpossiblepercentage', 'attendance'],
            ['submitattendance', 'attendance'],
            ['strftimeh', 'attendance'],
            ['strftimehm', 'attendance'],
            ['attendancesuccess', 'attendance'],
            ['attendance_no_status', 'attendance'],
            ['attendance_already_submitted', 'attendance'],
            ['somedisabledstatus', 'attendance'],
            ['invalidstatus', 'attendance'],
            ['preventsharederror', 'attendance'],
            ['closed', 'attendance'],
            ['subnetwrong', 'attendance'],
            ['enterpassword', 'attendance'],
            ['incorrectpasswordshort', 'attendance'],
            ['attendancesuccess', 'attendance'],
            ['setallstatuses', 'attendance']
        ],
    ]
];
