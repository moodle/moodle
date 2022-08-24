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
 * Plugin capabilities are defined here.
 *
 * @package     mod_plugnmeet
 * @category    access
 * @copyright   2022 mynaparrot
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$functions = array(
    'mod_plugnmeet_isactive_room' => array(
        'classname' => 'mod_plugnmeet_isactive_room',
        'classpath' => 'mod/plugnmeet/classes/external/mod_plugnmeet_isactive_room.php',
        'methodname' => 'isactive_room',
        'description' => 'Check if room is active or not',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'mod/plugnmeet:view',
    ),
    'mod_plugnmeet_create_room' => array(
        'classname' => 'mod_plugnmeet_create_room',
        'classpath' => 'mod/plugnmeet/classes/external/mod_plugnmeet_create_room.php',
        'methodname' => 'create_room',
        'description' => 'create room',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'mod/plugnmeet:view',
    ),
    'mod_plugnmeet_get_join_token' => array(
        'classname' => 'mod_plugnmeet_get_join_token',
        'classpath' => 'mod/plugnmeet/classes/external/mod_plugnmeet_get_join_token.php',
        'methodname' => 'get_join_token',
        'description' => 'get join token',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'mod/plugnmeet:view',
    ),
    'mod_plugnmeet_end_room' => array(
        'classname' => 'mod_plugnmeet_end_room',
        'classpath' => 'mod/plugnmeet/classes/external/mod_plugnmeet_end_room.php',
        'methodname' => 'end_room',
        'description' => 'end room',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'mod/plugnmeet:edit',
    ),
    'mod_plugnmeet_get_recordings' => array(
        'classname' => 'mod_plugnmeet_get_recordings',
        'classpath' => 'mod/plugnmeet/classes/external/mod_plugnmeet_get_recordings.php',
        'methodname' => 'get_recordings',
        'description' => 'get recordings',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'mod/plugnmeet:view',
    ),
    'mod_plugnmeet_get_recording_download_link' => array(
        'classname' => 'mod_plugnmeet_get_recording_download_link',
        'classpath' => 'mod/plugnmeet/classes/external/mod_plugnmeet_get_recording_download_link.php',
        'methodname' => 'get_download_link',
        'description' => 'get download link',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'mod/plugnmeet:view',
    ),
    'mod_plugnmeet_delete_recording' => array(
        'classname' => 'mod_plugnmeet_delete_recording',
        'classpath' => 'mod/plugnmeet/classes/external/mod_plugnmeet_delete_recording.php',
        'methodname' => 'delete_recording',
        'description' => 'delete recordings',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'mod/plugnmeet:edit',
    ),
    'mod_plugnmeet_update_client' => array(
        'classname' => 'mod_plugnmeet_update_client',
        'classpath' => 'mod/plugnmeet/classes/external/mod_plugnmeet_update_client.php',
        'methodname' => 'update_client',
        'description' => 'update client',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'mod/plugnmeet:edit',
    ),
);
