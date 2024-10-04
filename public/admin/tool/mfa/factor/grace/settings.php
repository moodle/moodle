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
 * @package     factor_grace
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_heading('factor_grace/description', '',
        new lang_string('settings:description', 'factor_grace')));
    $settings->add(new admin_setting_heading('factor_grace/settings', new lang_string('settings', 'moodle'), ''));

    $enabled = new admin_setting_configcheckbox('factor_grace/enabled',
        new lang_string('settings:enablefactor', 'tool_mfa'),
        new lang_string('settings:enablefactor_help', 'tool_mfa'), 0);
    $enabled->set_updatedcallback(function () {
        \tool_mfa\manager::do_factor_action('grace', get_config('factor_grace', 'enabled') ? 'enable' : 'disable');
    });
    $settings->add($enabled);

    $settings->add(new admin_setting_configtext('factor_grace/weight',
        new lang_string('settings:weight', 'tool_mfa'),
        new lang_string('settings:weight_help', 'tool_mfa'), 100, PARAM_INT));

    $settings->add(new admin_setting_configcheckbox('factor_grace/forcesetup',
        new lang_string('settings:forcesetup', 'factor_grace'),
        new lang_string('settings:forcesetup_help', 'factor_grace'), 0));

    $settings->add(new admin_setting_configduration('factor_grace/graceperiod',
        new lang_string('settings:graceperiod', 'factor_grace'),
        new lang_string('settings:graceperiod_help', 'factor_grace'), '604800'));

    $gracefactor = \tool_mfa\plugininfo\factor::get_factor('grace');
    $factors = $gracefactor->get_all_affecting_factors();
    $gracefactors = [];
    foreach ($factors as $factor) {
        $gracefactors[$factor->name] = $factor->get_display_name();
    }
    $settings->add(new admin_setting_configmultiselect('factor_grace/ignorelist',
        new lang_string('settings:ignorelist', 'factor_grace'),
        new lang_string('settings:ignorelist_help', 'factor_grace'), [], $gracefactors));

    $settings->add(new admin_setting_confightmleditor('factor_grace/customwarning',
        new lang_string('settings:customwarning', 'factor_grace'),
        new lang_string('settings:customwarning_help', 'factor_grace'), '', PARAM_RAW));
}
