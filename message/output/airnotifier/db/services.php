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
 * Airnotifier external functions and service definitions.
 *
 * @package    message_airnotifier
 * @category   webservice
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(
    'message_airnotifier_add_user_device' => array(
        'classname'   => 'message_airnotifier_external',
        'methodname'  => 'add_user_device',
        'classpath'   => 'message/output/airnotifier/externallib.php',
        'description' => 'Add device to user device list',
        'type'        => 'write',
    ),

    'message_airnotifier_get_access_key' => array(
        'classname'   => 'message_airnotifier_external',
        'methodname'  => 'get_access_key',
        'classpath'   => 'message/output/airnotifier/externallib.php',
        'description' => 'Get the mobile device access key with specified permissions',
        'type'        => 'read',
    ),
);

