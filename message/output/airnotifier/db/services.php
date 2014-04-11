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
    'message_airnotifier_is_system_configured' => array(
        'classname'   => 'message_airnotifier_external',
        'methodname'  => 'is_system_configured',
        'classpath'   => 'message/output/airnotifier/externallib.php',
        'description' => 'Check whether the airnotifier settings have been configured',
        'type'        => 'read',
    ),

    'message_airnotifier_are_notification_preferences_configured' => array(
        'classname'   => 'message_airnotifier_external',
        'methodname'  => 'are_notification_preferences_configured',
        'classpath'   => 'message/output/airnotifier/externallib.php',
        'description' => 'Check if the users have notification preferences configured yet',
        'type'        => 'read',
    ),
);
