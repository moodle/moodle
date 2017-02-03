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
 * Defines the form for editing activity results block instances.
 *
 * @package    block_activity_results
 * @copyright  2016 Stephen Bourget
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Default high scores.
    $setting = new admin_setting_configtext('block_activity_results/config_showbest',
        new lang_string('defaulthighestgrades', 'block_activity_results'),
        new lang_string('defaulthighestgrades_desc', 'block_activity_results'), 3, PARAM_INT);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    // Default low scores.
    $setting = new admin_setting_configtext('block_activity_results/config_showworst',
        new lang_string('defaultlowestgrades', 'block_activity_results'),
        new lang_string('defaultlowestgrades_desc', 'block_activity_results'), 0, PARAM_INT);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    // Default group display.
    $yesno = array(0 => get_string('no'), 1 => get_string('yes'));
    $setting = new admin_setting_configselect('block_activity_results/config_usegroups',
        new lang_string('defaultshowgroups', 'block_activity_results'),
        new lang_string('defaultshowgroups_desc', 'block_activity_results'), 0, $yesno);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    // Default privacy settings.
    $nameoptions = array(
        B_ACTIVITYRESULTS_NAME_FORMAT_FULL => get_string('config_names_full', 'block_activity_results'),
        B_ACTIVITYRESULTS_NAME_FORMAT_ID => get_string('config_names_id', 'block_activity_results'),
        B_ACTIVITYRESULTS_NAME_FORMAT_ANON => get_string('config_names_anon', 'block_activity_results')
    );
    $setting = new admin_setting_configselect('block_activity_results/config_nameformat',
        new lang_string('defaultnameoptions', 'block_activity_results'),
        new lang_string('defaultnameoptions_desc', 'block_activity_results'), B_ACTIVITYRESULTS_NAME_FORMAT_FULL, $nameoptions);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    // Default grade display settings.
    $gradeoptions = array(
        B_ACTIVITYRESULTS_GRADE_FORMAT_PCT => get_string('config_format_percentage', 'block_activity_results'),
        B_ACTIVITYRESULTS_GRADE_FORMAT_FRA => get_string('config_format_fraction', 'block_activity_results'),
        B_ACTIVITYRESULTS_GRADE_FORMAT_ABS => get_string('config_format_absolute', 'block_activity_results')
    );
    $setting = new admin_setting_configselect('block_activity_results/config_gradeformat',
        new lang_string('defaultgradedisplay', 'block_activity_results'),
        new lang_string('defaultgradedisplay_desc', 'block_activity_results'), B_ACTIVITYRESULTS_GRADE_FORMAT_PCT, $gradeoptions);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    // Default decimal places.
    $places = array();
    for ($i = 0; $i <= 5; $i++) {
        $places[$i] = $i;
    }
    $setting = new admin_setting_configselect('block_activity_results/config_decimalpoints',
        new lang_string('defaultdecimalplaces', 'block_activity_results'),
        new lang_string('defaultdecimalplaces_desc', 'block_activity_results'), 2, $places);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

}
