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
 * Google Meet external functions and service definitions.
 *
 * @package     mod_googlemeet
 * @copyright   2020 Rone Santos <ronefel@hotmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'mod_googlemeet_sync_recordings' => array(
        'classname' => 'mod_googlemeet_external',
        'methodname' => 'sync_recordings',
        'description' => '',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'googlemeet:syncgoogledrive',
    ),
    'mod_googlemeet_recording_edit_name' => array(
        'classname' => 'mod_googlemeet_external',
        'methodname' => 'recording_edit_name',
        'description' => '',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'googlemeet:editrecording',
    ),
    'mod_googlemeet_showhide_recording' => array(
        'classname' => 'mod_googlemeet_external',
        'methodname' => 'showhide_recording',
        'description' => '',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'googlemeet:editrecording',
    ),
    'mod_googlemeet_delete_all_recordings' => array(
        'classname' => 'mod_googlemeet_external',
        'methodname' => 'delete_all_recordings',
        'description' => '',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'googlemeet:removerecording',
    ),
);
