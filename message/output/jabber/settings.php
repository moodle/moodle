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
 * Jabber configuration page
 *
 * @package    message_jabber
 * @copyright  2011 Lancaster University Network Services Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('jabberhost', get_string('jabberhost', 'message_jabber'), get_string('configjabberhost', 'message_jabber'), '', PARAM_RAW));
    $settings->add(new admin_setting_configtext('jabberserver', get_string('jabberserver', 'message_jabber'), get_string('configjabberserver', 'message_jabber'), '', PARAM_RAW));
    $settings->add(new admin_setting_configtext('jabberusername', get_string('jabberusername', 'message_jabber'), get_string('configjabberusername', 'message_jabber'), '', PARAM_RAW));
    $settings->add(new admin_setting_configpasswordunmask('jabberpassword', get_string('jabberpassword', 'message_jabber'), get_string('configjabberpassword', 'message_jabber'), ''));
    $settings->add(new admin_setting_configtext('jabberport', get_string('jabberport', 'message_jabber'), get_string('configjabberport', 'message_jabber'), 5222, PARAM_INT));
}
