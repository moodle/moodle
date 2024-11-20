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
 * @package   block_mycourses
 * @copyright 2019-onward Mike Churchward (mike.churchward@poetopensource.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$addons = [
    'block_mycourses' => [
        'handlers' => [
            'mycoursesoutput' => [
                'displaydata' => [
                    'title' => '',
                    'class' => '',
                ],
                'delegate' => 'CoreBlockDelegate',
                'method' => 'mobile_view_block',
                'styles' => [
                    'url' => $CFG->wwwroot . '/blocks/mycourses/styles_app.css',
                    'version' => '1.7'
                ]
            ]
        ],
        'lang' => [
            ['pluginname', 'block_mycourses'],
            ['availableheader', 'block_mycourses'],
            ['inprogressheader', 'block_mycourses'],
            ['completedheader', 'block_mycourses'],
            ['startcourse', 'block_mycourses']
        ],
    ]
];