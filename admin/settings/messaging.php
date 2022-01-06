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
 * Adds messaging related settings links for Messaging category to admin tree.
 *
 * @copyright 2019 Amaia Anabitarte <amaia@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $temp = new admin_settingpage('messages', new lang_string('messagingssettings', 'admin'));
    $temp->add(new admin_setting_configcheckbox('messaging',
        new lang_string('messaging', 'admin'),
        new lang_string('configmessaging', 'admin'),
        1));
    $temp->add(new admin_setting_configcheckbox('messagingallusers',
            new lang_string('messagingallusers', 'admin'),
            new lang_string('configmessagingallusers', 'admin'),
             0)
    );
    $temp->add(new admin_setting_configcheckbox('messagingdefaultpressenter',
            new lang_string('messagingdefaultpressenter', 'admin'),
            new lang_string('configmessagingdefaultpressenter', 'admin'),
            1)
    );
    $options = array(
        DAYSECS => new lang_string('secondstotime86400'),
        WEEKSECS => new lang_string('secondstotime604800'),
        2620800 => new lang_string('nummonths', 'moodle', 1),
        7862400 => new lang_string('nummonths', 'moodle', 3),
        15724800 => new lang_string('nummonths', 'moodle', 6),
        0 => new lang_string('never')
    );
    $temp->add(new admin_setting_configselect(
            'messagingdeletereadnotificationsdelay',
            new lang_string('messagingdeletereadnotificationsdelay', 'admin'),
            new lang_string('configmessagingdeletereadnotificationsdelay', 'admin'),
            604800,
            $options)
    );
    $temp->add(new admin_setting_configselect(
            'messagingdeleteallnotificationsdelay',
            new lang_string('messagingdeleteallnotificationsdelay', 'admin'),
            new lang_string('configmessagingdeleteallnotificationsdelay', 'admin'),
            2620800,
            $options)
    );
    $temp->add(new admin_setting_configcheckbox('messagingallowemailoverride',
        new lang_string('messagingallowemailoverride', 'admin'),
        new lang_string('configmessagingallowemailoverride', 'admin'),
        0));
    $ADMIN->add('messaging', $temp);
    $ADMIN->add('messaging', new admin_page_managemessageoutputs());

    // Notification outputs plugins.
    $plugins = core_plugin_manager::instance()->get_plugins_of_type('message');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\message $plugin */
        $plugin->load_settings($ADMIN, 'messaging', $hassiteconfig);
    }
}
