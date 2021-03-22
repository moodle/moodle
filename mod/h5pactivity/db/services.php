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
 * H5P activity external functions and service definitions.
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = [
    'mod_h5pactivity_get_h5pactivity_access_information' => [
        'classname'     => 'mod_h5pactivity\external\get_h5pactivity_access_information',
        'classpath'     => '',
        'description'   => 'Return access information for a given h5p activity.',
        'type'          => 'read',
        'capabilities'  => 'mod/h5pactivity:view',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'mod_h5pactivity_view_h5pactivity' => [
        'classname'     => 'mod_h5pactivity\external\view_h5pactivity',
        'classpath'     => '',
        'description'   => 'Trigger the course module viewed event and update the module completion status.',
        'type'          => 'write',
        'capabilities'  => 'mod/h5pactivity:view',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'mod_h5pactivity_get_attempts' => [
        'classname'     => 'mod_h5pactivity\external\get_attempts',
        'classpath'     => '',
        'description'   => 'Return the information needed to list a user attempts.',
        'type'          => 'read',
        'capabilities'  => 'mod/h5pactivity:view',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'mod_h5pactivity_get_results' => [
        'classname'     => 'mod_h5pactivity\external\get_results',
        'classpath'     => '',
        'description'   => 'Return the information needed to list a user attempt results.',
        'type'          => 'read',
        'capabilities'  => 'mod/h5pactivity:view',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'mod_h5pactivity_get_h5pactivities_by_courses' => [
        'classname'     => 'mod_h5pactivity\external\get_h5pactivities_by_courses',
        'classpath'     => '',
        'description'   => 'Returns a list of h5p activities in a list of
            provided courses, if no list is provided all h5p activities
            that the user can view will be returned.',
        'type'          => 'read',
        'capabilities'  => 'mod/h5pactivity:view',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'mod_h5pactivity_log_report_viewed' => [
        'classname'     => 'mod_h5pactivity\external\log_report_viewed',
        'classpath'     => '',
        'description'   => 'Log that the h5pactivity was viewed.',
        'type'          => 'write',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'mod_h5pactivity_get_user_attempts' => [
        'classname'     => 'mod_h5pactivity\external\get_user_attempts',
        'classpath'     => '',
        'description'   => 'Return the information needed to list all enrolled user attempts.',
        'type'          => 'read',
        'capabilities'  => 'mod/h5pactivity:reviewattempts',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
];
