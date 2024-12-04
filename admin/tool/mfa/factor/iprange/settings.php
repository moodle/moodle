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
 * Settings
 *
 * @package     factor_iprange
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $OUTPUT;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_heading('factor_iprange/description', '',
        new lang_string('settings:description', 'factor_iprange')));
    $settings->add(new admin_setting_heading('factor_iprange/settings', new lang_string('settings', 'moodle'), ''));

    $enabled = new admin_setting_configcheckbox('factor_iprange/enabled',
        new lang_string('settings:enablefactor', 'tool_mfa'),
        new lang_string('settings:enablefactor_help', 'tool_mfa'), 0);
    $enabled->set_updatedcallback(function () {
        \tool_mfa\manager::do_factor_action('iprange', get_config('factor_iprange', 'enabled') ? 'enable' : 'disable');
    });
    $settings->add($enabled);

    $settings->add(new admin_setting_configtext('factor_iprange/weight',
        new lang_string('settings:weight', 'tool_mfa'),
        new lang_string('settings:weight_help', 'tool_mfa'), 100, PARAM_INT));


    // Current IP validation against list for description.
    $allowedips = get_config('factor_iprange', 'safeips');
    if (trim($allowedips) == '') {
        $message = 'allowedipsempty';
        $type = 'notifyerror';
    } else if (remoteip_in_list($allowedips)) {
        $message = 'allowedipshasmyip';
        $type = 'notifysuccess';
    } else {
        $message = 'allowedipshasntmyip';
        $type = 'notifyerror';
    };
    $info = $OUTPUT->notification(get_string($message, 'factor_iprange', ['ip' => getremoteaddr()]), $type);

    $settings->add(new admin_setting_configiplist('factor_iprange/safeips',
        new lang_string('settings:safeips', 'factor_iprange'),
        new lang_string('settings:safeips_help', 'factor_iprange',
                ['info' => $info, 'syntax' => get_string('ipblockersyntax', 'admin')]), '', PARAM_TEXT));
}
