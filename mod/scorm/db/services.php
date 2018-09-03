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
 * SCORM external functions and service definitions.
 *
 * @package    mod_scorm
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

$functions = array(

    'mod_scorm_view_scorm' => array(
        'classname'     => 'mod_scorm_external',
        'methodname'    => 'view_scorm',
        'description'   => 'Trigger the course module viewed event.',
        'type'          => 'write',
        'capabilities'  => '',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_scorm_get_scorm_attempt_count' => array(
        'classname'     => 'mod_scorm_external',
        'methodname'    => 'get_scorm_attempt_count',
        'description'   => 'Return the number of attempts done by a user in the given SCORM.',
        'type'          => 'read',
        'capabilities'  => '',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_scorm_get_scorm_scoes' => array(
        'classname' => 'mod_scorm_external',
        'methodname' => 'get_scorm_scoes',
        'description' => 'Returns a list containing all the scoes data related to the given scorm id',
        'type' => 'read',
        'capabilities' => '',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_scorm_get_scorm_user_data' => array(
        'classname' => 'mod_scorm_external',
        'methodname' => 'get_scorm_user_data',
        'description' => 'Retrieves user tracking and SCO data and default SCORM values',
        'type' => 'read',
        'capabilities' => '',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_scorm_insert_scorm_tracks' => array(
        'classname' => 'mod_scorm_external',
        'methodname' => 'insert_scorm_tracks',
        'description' => 'Saves a scorm tracking record.
                          It will overwrite any existing tracking data for this attempt.
                          Validation should be performed before running the function to ensure the user will not lose any existing
                          attempt data.',
        'type' => 'write',
        'capabilities' => 'mod/scorm:savetrack',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_scorm_get_scorm_sco_tracks' => array(
        'classname' => 'mod_scorm_external',
        'methodname' => 'get_scorm_sco_tracks',
        'description' => 'Retrieves SCO tracking data for the given user id and attempt number',
        'type' => 'read',
        'capabilities' => '',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_scorm_get_scorms_by_courses' => array(
        'classname'     => 'mod_scorm_external',
        'methodname'    => 'get_scorms_by_courses',
        'description'   => 'Returns a list of scorm instances in a provided set of courses, if
                            no courses are provided then all the scorm instances the user has access to will be returned.',
        'type'          => 'read',
        'capabilities'  => '',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_scorm_launch_sco' => array(
        'classname'     => 'mod_scorm_external',
        'methodname'    => 'launch_sco',
        'description'   => 'Trigger the SCO launched event.',
        'type'          => 'write',
        'capabilities'  => '',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
);
