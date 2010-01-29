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
 * This file contains the mnet services for the mnet authentication plugin
 *
 * @since 2.0
 * @package moodlecore
 * @subpackage auth
 * @copyright 2010 Penny Leach
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$publishes = array(
    'sso_idp' => array(
        'apiversion' => 1,
        'classname'  => 'auth_plugin_mnet',
        'filename'   => 'auth.php',
        'methods'    => array(
            'user_authorise',
            'keepalive_server',
            'kill_children',
            'refresh_log',
            'fetch_user_image',
            'fetch_theme_info',
            'update_enrolments',
        ),
    ),
    'sso_sp' => array(
        'apiversion' => 1,
        'classname'  => 'auth_plugin_mnet',
        'filename'   => 'auth.php',
        'methods'    => array(
            'keepalive_client',
            'kill_child'
        )
    )
);
$subscribes = array(
    'sso_idp' => array(
        'user_authorise'    => 'auth/mnet/auth.php/user_authorise',
        'keepalive_server'  => 'auth/mnet/auth.php/keepalive_server',
        'kill_children'     => 'auth/mnet/auth.php/kill_children',
        'refresh_log'       => 'auth/mnet/auth.php/refresh_log',
        'fetch_user_image'  => 'auth/mnet/auth.php/fetch_user_image',
        'fetch_theme_info'  => 'auth/mnet/auth.php/fetch_theme_info',
        'update_enrolments' => 'auth/mnet/auth.php/update_enrolments',
    ),
    'sso_sp' => array(
        'keepalive_client' => 'auth/mnet/auth.php/keepalive_client',
        'kill_child'       => 'auth/mnet/auth.php/kill_child',
    ),
);
