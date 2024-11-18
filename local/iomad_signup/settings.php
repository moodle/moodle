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
 * @package   local_iomad_signup
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig && !empty($USER->id)) {

    // Basic navigation settings
    require($CFG->dirroot . '/local/iomad/lib/basicsettings.php');

    $settings = new admin_settingpage('local_iomad_signup', get_string('pluginname', 'local_iomad_signup'));
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configcheckbox(
        'local_iomad_signup_enable',
        get_string('enable', 'local_iomad_signup'),
        get_string('enable_help', 'local_iomad_signup'),
        1)
    );

    $settings->add(new admin_setting_configcheckbox(
        'local_iomad_signup_showinstructions',
        get_string('showinstructions', 'local_iomad_signup'),
        get_string('showinstructions_help', 'local_iomad_signup'),
        1)
    );

    $settings->add(new admin_setting_configcheckbox(
        'local_iomad_signup_useemail',
        get_string('useemail', 'local_iomad_signup'),
        get_string('useemail_help', 'local_iomad_signup'),
        1)
    );

    $settings->add(new admin_setting_configcheckbox(
        'local_iomad_signup_autoenrol',
        get_string('autoenrol', 'local_iomad_signup'),
        get_string('autoenrol_help', 'local_iomad_signup'),
        1)
    );

    $settings->add(new admin_setting_configcheckbox(
        'local_iomad_signup_autoenrol_unassigned',
        get_string('autoenrol_unassigned', 'local_iomad_signup'),
        get_string('autoenrol_unassigned_help', 'local_iomad_signup'),
        0)
    );

    $siteauths = get_enabled_auth_plugins();
    $siteautharray = array();
    foreach ($siteauths as $siteauth) {
        if ($siteauth != 'manual') {
            $siteautharray[$siteauth] = $siteauth;
        }
    }

    // Add the available auth methods. IF, there are companies defined
    $sitecompanies = $DB->get_records_menu('company', array(), 'name', 'id,name');
    if ($sitecompanies) {
        $settings->add(new admin_setting_configmulticheckbox('local_iomad_signup_auth', get_string('authenticationtypes', 'local_iomad_signup'), get_string('authenticationtypes_desc', 'local_iomad_signup'), array(),$siteautharray));

        $siteroles = $DB->get_records_menu('role', array(), 'id', 'id,shortname');
        $availableroles = array('0' => 'none') + $siteroles;
        $settings->add(new admin_setting_configselect('local_iomad_signup_role', get_string('role', 'local_iomad_signup'),
                           get_string('configrole', 'local_iomad_signup'), 0, $availableroles));

        $availablecompanies = array('0' => 'none') + $sitecompanies;
        $settings->add(new admin_setting_configselect('local_iomad_signup_company', get_string('company', 'local_iomad_signup'),
                           get_string('configcompany', 'local_iomad_signup'), 0, $availablecompanies));
    } else {
        set_config('local_iomad_signup_auth', '');
        set_config('local_iomad_signup_role', 0);
        set_config('local_iomad_signup_company', 0);
    }
}
