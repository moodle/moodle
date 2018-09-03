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

defined('MOODLE_INTERNAL') || die();

/**
 * External functions and service definitions.
 *
 * @package    message_popup
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$functions = array(
    'message_popup_get_popup_notifications' => array(
        'classname' => 'message_popup_external',
        'methodname' => 'get_popup_notifications',
        'classpath' => 'message/output/popup/externallib.php',
        'description' => 'Retrieve a list of popup notifications for a user',
        'type' => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'message_popup_get_unread_popup_notification_count' => array(
        'classname' => 'message_popup_external',
        'methodname' => 'get_unread_popup_notification_count',
        'classpath' => 'message/output/popup/externallib.php',
        'description' => 'Retrieve the count of unread popup notifications for a given user',
        'type' => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
);
