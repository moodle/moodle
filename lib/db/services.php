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
 * Core external functions and service definitions.
 *
 * @package    moodlecore
 * @subpackage webservice
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(

    // === group related functions ===

    'moodle_group_create_groups' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'create_groups',
        'classpath'   => 'group/externallib.php',
        'params'      => null, //TODO
        'returns'     => null, //TODO
    ),

    'moodle_group_get_groups' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'get_groups',
        'classpath'   => 'group/externallib.php',
        'params'      => null, //TODO
        'returns'     => null, //TODO
    ),

    'moodle_group_delete_groups' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'delete_groups',
        'classpath'   => 'group/externallib.php',
        'params'      => null, //TODO
        'returns'     => null, //TODO
    ),

    'moodle_group_get_groupmembers' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'get_groupmembers',
        'classpath'   => 'group/externallib.php',
        'params'      => null, //TODO
        'returns'     => null, //TODO
    ),

    'moodle_group_add_groupmembers' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'add_groupmembers',
        'classpath'   => 'group/externallib.php',
        'params'      => null, //TODO
        'returns'     => null, //TODO
    ),

    'moodle_group_delete_groupmembers' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'delete_groupmembers',
        'classpath'   => 'group/externallib.php',
        'params'      => null, //TODO
        'returns'     => null, //TODO
    ),

    // === user related functions ===

    'moodle_user_create_users' => array(
        'classname'   => 'moodle_user_external',
        'methodname'  => 'create_users',
        'classpath'   => 'user/externallib.php',
        'params'      => 'create_users_params',
        'returns'     => 'create_users_returns'
    ),

    'moodle_user_get_users' => array(
        'classname'   => 'moodle_user_external',
        'methodname'  => 'get_users',
        'classpath'   => 'user/externallib.php',
        'params'      => null, //TODO
        'returns'     => null, //TODO
    ),

    'moodle_user_delete_users' => array(
        'classname'   => 'moodle_user_external',
        'methodname'  => 'delete_users',
        'classpath'   => 'user/externallib.php',
        'params'      => null, //TODO
        'returns'     => null, //TODO
    ),

    'moodle_user_update_users' => array(
        'classname'   => 'moodle_user_external',
        'methodname'  => 'update_users',
        'classpath'   => 'user/externallib.php',
        'params'      => null, //TODO
        'returns'     => null, //TODO
    ),

);
