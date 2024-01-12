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
 * Mobile app definition for BigBlueButton.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */

defined('MOODLE_INTERNAL') || die;
global $CFG;
$addons = [
    "mod_bigbluebuttonbn" => [
        "handlers" => [ // Different places where the add-on will display content.
            'coursebigbluebuttonbn' => [ // Handler unique name (can be anything).
                'displaydata' => [
                    'title' => 'pluginname',
                    'icon' => $CFG->wwwroot . '/mod/bigbluebuttonbn/pix/monologo.svg',
                    'class' => '',
                ],
                'delegate' => 'CoreCourseModuleDelegate', // Delegate (where to display the link to the add-on).
                'method' => 'mobile_course_view' // Main function in \mod_bigbluebuttonbn\output\mobile.
            ]
        ],
        'lang' => [
            ['pluginname', 'bigbluebuttonbn'],
            ['view_conference_action_join', 'bigbluebuttonbn'],
            ['view_message_conference_room_ready', 'bigbluebuttonbn'],
            ['view_mobile_message_reload_page_creation_time_meeting', 'bigbluebuttonbn']
        ]
    ]
];
