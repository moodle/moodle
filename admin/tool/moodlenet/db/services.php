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
 * Tool Moodle.Net webservice definitions.
 *
 * @package    tool_moodlenet
 * @copyright  2020 Mathew May {@link https://mathew.solutions}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'tool_moodlenet_verify_webfinger' => [
        'classname'   => 'tool_moodlenet\external',
        'methodname'  => 'verify_webfinger',
        'description' => 'Verify if the passed information resolves into a WebFinger profile URL',
        'type'        => 'read',
        'ajax'        => true,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE]
    ],
    'tool_moodlenet_search_courses' => [
        'classname'   => 'tool_moodlenet\external',
        'methodname'  => 'search_courses',
        'description' => 'For some given input search for a course that matches',
        'type'        => 'read',
        'ajax'        => true,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE]
    ],
];
