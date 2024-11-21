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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

$definitions = [
    'bb_collaborate_access_token' => [
        'mode' => cache_store::MODE_APPLICATION
    ],
    'reports_list' => [
        'mode' => cache_store::MODE_APPLICATION
    ],
    'instructor_course_data' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'ttl' => 900 // 15 minutes
    ],
    'tracking' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'requirelockingbeforewrite' => true,
        'overrideclass' => 'local_intelliboard\tools\cache_application',
        'overrideclassfile' => 'local/intelliboard/classes/tools/cache_application.php'
    ],
    'track_config' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true
    ]
];
