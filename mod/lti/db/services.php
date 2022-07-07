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
 * External tool external functions and service definitions.
 *
 * @package    mod_lti
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(

    'mod_lti_get_tool_launch_data' => array(
        'classname'     => 'mod_lti_external',
        'methodname'    => 'get_tool_launch_data',
        'description'   => 'Return the launch data for a given external tool.',
        'type'          => 'read',
        'capabilities'  => 'mod/lti:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_lti_get_ltis_by_courses' => array(
        'classname'     => 'mod_lti_external',
        'methodname'    => 'get_ltis_by_courses',
        'description'   => 'Returns a list of external tool instances in a provided set of courses, if
                            no courses are provided then all the external tool instances the user has access to will be returned.',
        'type'          => 'read',
        'capabilities'  => 'mod/lti:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_lti_view_lti' => array(
        'classname'     => 'mod_lti_external',
        'methodname'    => 'view_lti',
        'description'   => 'Trigger the course module viewed event and update the module completion status.',
        'type'          => 'read',
        'capabilities'  => 'mod/lti:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_lti_get_tool_proxies' => array(
        'classname'     => 'mod_lti_external',
        'methodname'    => 'get_tool_proxies',
        'description'   => 'Get a list of the tool proxies',
        'type'          => 'read',
        'capabilities'  => 'moodle/site:config',
        'ajax'          => true
    ),

    'mod_lti_create_tool_proxy' => array(
        'classname'     => 'mod_lti_external',
        'methodname'    => 'create_tool_proxy',
        'description'   => 'Create a tool proxy',
        'type'          => 'write',
        'capabilities'  => 'moodle/site:config',
        'ajax'          => true
    ),

    'mod_lti_delete_tool_proxy' => array(
        'classname'     => 'mod_lti_external',
        'methodname'    => 'delete_tool_proxy',
        'description'   => 'Delete a tool proxy',
        'type'          => 'write',
        'capabilities'  => 'moodle/site:config',
        'ajax'          => true
    ),

    'mod_lti_get_tool_proxy_registration_request' => array(
        'classname'     => 'mod_lti_external',
        'methodname'    => 'get_tool_proxy_registration_request',
        'description'   => 'Get a registration request for a tool proxy',
        'type'          => 'read',
        'capabilities'  => 'moodle/site:config',
        'ajax'          => true
    ),

    'mod_lti_get_tool_types' => array(
        'classname'     => 'mod_lti_external',
        'methodname'    => 'get_tool_types',
        'description'   => 'Get a list of the tool types',
        'type'          => 'read',
        'capabilities'  => 'moodle/site:config',
        'ajax'          => true
    ),

    'mod_lti_create_tool_type' => array(
        'classname'     => 'mod_lti_external',
        'methodname'    => 'create_tool_type',
        'description'   => 'Create a tool type',
        'type'          => 'write',
        'capabilities'  => 'moodle/site:config',
        'ajax'          => true
    ),

    'mod_lti_update_tool_type' => array(
        'classname'     => 'mod_lti_external',
        'methodname'    => 'update_tool_type',
        'description'   => 'Update a tool type',
        'type'          => 'write',
        'capabilities'  => 'moodle/site:config',
        'ajax'          => true
    ),

    'mod_lti_delete_tool_type' => array(
        'classname'     => 'mod_lti_external',
        'methodname'    => 'delete_tool_type',
        'description'   => 'Delete a tool type',
        'type'          => 'write',
        'capabilities'  => 'moodle/site:config',
        'ajax'          => true
    ),

    'mod_lti_is_cartridge' => array(
        'classname'     => 'mod_lti_external',
        'methodname'    => 'is_cartridge',
        'description'   => 'Determine if the given url is for a cartridge',
        'type'          => 'read',
        'capabilities'  => 'moodle/site:config',
        'ajax'          => true
    ),
);
