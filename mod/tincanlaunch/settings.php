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
 * Tincanlaunch admin settings.
 *
 * @package mod_tincanlaunch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot . '/mod/tincanlaunch/locallib.php');
    require_once($CFG->dirroot . '/mod/tincanlaunch/settingslib.php');

    // Default display settings.
    $settings->add(new admin_setting_heading(
        'tincanlaunch/tincanlaunchlrsfieldset',
        get_string('tincanlaunchlrsfieldset', 'tincanlaunch'),
        get_string('tincanlaunchlrsfieldset_help', 'tincanlaunch')
    ));

    $settings->add(new admin_setting_configtext_mod_tincanlaunch(
        'tincanlaunch/tincanlaunchlrsendpoint',
        get_string('tincanlaunchlrsendpoint', 'tincanlaunch'),
        get_string('tincanlaunchlrsendpoint_help', 'tincanlaunch'),
        get_string('tincanlaunchlrsendpoint_default', 'tincanlaunch'),
        PARAM_URL
    ));

    $options = array(
        1 => get_string('tincanlaunchlrsauthentication_option_0', 'tincanlaunch'),
        2 => get_string('tincanlaunchlrsauthentication_option_1', 'tincanlaunch'),
        0 => get_string('tincanlaunchlrsauthentication_option_2', 'tincanlaunch')
    );
    // Note the numbers above are deliberately mis-ordered for reasons of backwards compatibility with older settings.

    $setting = new admin_setting_configselect(
        'tincanlaunch/tincanlaunchlrsauthentication',
        get_string('tincanlaunchlrsauthentication', 'tincanlaunch'),
        get_string('tincanlaunchlrsauthentication_help', 'tincanlaunch') . '<br/>'
            . get_string('tincanlaunchlrsauthentication_watershedhelp', 'tincanlaunch'),
        1,
        $options
    );
    $settings->add($setting);

    $setting = new admin_setting_configtext(
        'tincanlaunch/tincanlaunchlrslogin',
        get_string('tincanlaunchlrslogin', 'tincanlaunch'),
        get_string('tincanlaunchlrslogin_help', 'tincanlaunch'),
        get_string('tincanlaunchlrslogin_default', 'tincanlaunch')
    );
    $settings->add($setting);

    $setting = new admin_setting_configtext(
        'tincanlaunch/tincanlaunchlrspass',
        get_string('tincanlaunchlrspass', 'tincanlaunch'),
        get_string('tincanlaunchlrspass_help', 'tincanlaunch'),
        get_string('tincanlaunchlrspass_default', 'tincanlaunch')
    );
    $settings->add($setting);

    $settings->add(new admin_setting_configtext(
        'tincanlaunch/tincanlaunchlrsduration',
        get_string('tincanlaunchlrsduration', 'tincanlaunch'),
        get_string('tincanlaunchlrsduration_help', 'tincanlaunch'),
        get_string('tincanlaunchlrsduration_default', 'tincanlaunch')
    ));

    $settings->add(new admin_setting_configtext(
        'tincanlaunch/tincanlaunchcustomacchp',
        get_string('tincanlaunchcustomacchp', 'tincanlaunch'),
        get_string('tincanlaunchcustomacchp_help', 'tincanlaunch'),
        get_string('tincanlaunchcustomacchp_default', 'tincanlaunch')
    ));

    $settings->add(new admin_setting_configcheckbox(
        'tincanlaunch/tincanlaunchuseactoremail',
        get_string('tincanlaunchuseactoremail', 'tincanlaunch'),
        get_string('tincanlaunchuseactoremail_help', 'tincanlaunch'),
        1
    ));

    $customfieldrecords = $DB->get_records('user_info_field');
    if ($customfieldrecords) {
        $customfields = [];
        foreach ($customfieldrecords as $customfieldrecord) {
            $customfields[$customfieldrecord->shortname] = $customfieldrecord->name;
        }
        asort($customfields);
        $settings->add(new admin_setting_configmultiselect(
            'tincanlaunch/profilefields',
            get_string('profilefields', 'tincanlaunch'),
            get_string('profilefields_desc', 'tincanlaunch'),
            [],
            $customfields
        ));
    }
}
