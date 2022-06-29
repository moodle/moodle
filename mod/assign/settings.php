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
 * This file adds the settings pages to the navigation menu
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/assign/adminlib.php');

$ADMIN->add('modsettings', new admin_category('modassignfolder', new lang_string('pluginname', 'mod_assign'), $module->is_enabled() === false));

$settings = new admin_settingpage($section, get_string('settings', 'mod_assign'), 'moodle/site:config', $module->is_enabled() === false);

if ($ADMIN->fulltree) {
    $menu = array();
    foreach (core_component::get_plugin_list('assignfeedback') as $type => $notused) {
        $visible = !get_config('assignfeedback_' . $type, 'disabled');
        if ($visible) {
            $menu['assignfeedback_' . $type] = new lang_string('pluginname', 'assignfeedback_' . $type);
        }
    }

    // The default here is feedback_comments (if it exists).
    $name = new lang_string('feedbackplugin', 'mod_assign');
    $description = new lang_string('feedbackpluginforgradebook', 'mod_assign');
    $settings->add(new admin_setting_configselect('assign/feedback_plugin_for_gradebook',
                                                  $name,
                                                  $description,
                                                  'assignfeedback_comments',
                                                  $menu));

    $name = new lang_string('showrecentsubmissions', 'mod_assign');
    $description = new lang_string('configshowrecentsubmissions', 'mod_assign');
    $settings->add(new admin_setting_configcheckbox('assign/showrecentsubmissions',
                                                    $name,
                                                    $description,
                                                    0));

    $name = new lang_string('sendsubmissionreceipts', 'mod_assign');
    $description = new lang_string('sendsubmissionreceipts_help', 'mod_assign');
    $settings->add(new admin_setting_configcheckbox('assign/submissionreceipts',
                                                    $name,
                                                    $description,
                                                    1));

    $name = new lang_string('submissionstatement', 'mod_assign');
    $description = new lang_string('submissionstatement_help', 'mod_assign');
    $default = get_string('submissionstatementdefault', 'mod_assign');
    $setting = new admin_setting_configtextarea('assign/submissionstatement',
                                                    $name,
                                                    $description,
                                                    $default);
    $setting->set_force_ltr(false);
    $settings->add($setting);

    $name = new lang_string('submissionstatementteamsubmission', 'mod_assign');
    $description = new lang_string('submissionstatement_help', 'mod_assign');
    $default = get_string('submissionstatementteamsubmissiondefault', 'mod_assign');
    $setting = new admin_setting_configtextarea('assign/submissionstatementteamsubmission',
        $name,
        $description,
        $default);
    $setting->set_force_ltr(false);
    $settings->add($setting);

    $name = new lang_string('submissionstatementteamsubmissionallsubmit', 'mod_assign');
    $description = new lang_string('submissionstatement_help', 'mod_assign');
    $default = get_string('submissionstatementteamsubmissionallsubmitdefault', 'mod_assign');
    $setting = new admin_setting_configtextarea('assign/submissionstatementteamsubmissionallsubmit',
        $name,
        $description,
        $default);
    $setting->set_force_ltr(false);
    $settings->add($setting);

    $name = new lang_string('maxperpage', 'mod_assign');
    $options = array(
        -1 => get_string('unlimitedpages', 'mod_assign'),
        10 => 10,
        20 => 20,
        50 => 50,
        100 => 100,
    );
    $description = new lang_string('maxperpage_help', 'mod_assign');
    $settings->add(new admin_setting_configselect('assign/maxperpage',
                                                    $name,
                                                    $description,
                                                    -1,
                                                    $options));

    $name = new lang_string('defaultsettings', 'mod_assign');
    $description = new lang_string('defaultsettings_help', 'mod_assign');
    $settings->add(new admin_setting_heading('defaultsettings', $name, $description));

    $name = new lang_string('alwaysshowdescription', 'mod_assign');
    $description = new lang_string('alwaysshowdescription_help', 'mod_assign');
    $setting = new admin_setting_configcheckbox('assign/alwaysshowdescription',
                                                    $name,
                                                    $description,
                                                    1);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('allowsubmissionsfromdate', 'mod_assign');
    $description = new lang_string('allowsubmissionsfromdate_help', 'mod_assign');
    $setting = new admin_setting_configduration('assign/allowsubmissionsfromdate',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_enabled_flag_options(admin_setting_flag::ENABLED, true);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('duedate', 'mod_assign');
    $description = new lang_string('duedate_help', 'mod_assign');
    $setting = new admin_setting_configduration('assign/duedate',
                                                    $name,
                                                    $description,
                                                    604800);
    $setting->set_enabled_flag_options(admin_setting_flag::ENABLED, true);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('cutoffdate', 'mod_assign');
    $description = new lang_string('cutoffdate_help', 'mod_assign');
    $setting = new admin_setting_configduration('assign/cutoffdate',
                                                    $name,
                                                    $description,
                                                    1209600);
    $setting->set_enabled_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('enabletimelimit', 'mod_assign');
    $description = new lang_string('enabletimelimit_help', 'mod_assign');
    $setting = new admin_setting_configcheckbox(
        'assign/enabletimelimit',
        $name,
        $description,
        0
    );
    $settings->add($setting);

    $name = new lang_string('gradingduedate', 'mod_assign');
    $description = new lang_string('gradingduedate_help', 'mod_assign');
    $setting = new admin_setting_configduration('assign/gradingduedate',
                                                    $name,
                                                    $description,
                                                    1209600);
    $setting->set_enabled_flag_options(admin_setting_flag::ENABLED, true);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('submissiondrafts', 'mod_assign');
    $description = new lang_string('submissiondrafts_help', 'mod_assign');
    $setting = new admin_setting_configcheckbox('assign/submissiondrafts',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('requiresubmissionstatement', 'mod_assign');
    $description = new lang_string('requiresubmissionstatement_help', 'mod_assign');
    $setting = new admin_setting_configcheckbox('assign/requiresubmissionstatement',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    // Constants from "locallib.php".
    $options = array(
        'none' => get_string('attemptreopenmethod_none', 'mod_assign'),
        'manual' => get_string('attemptreopenmethod_manual', 'mod_assign'),
        'untilpass' => get_string('attemptreopenmethod_untilpass', 'mod_assign')
    );
    $name = new lang_string('attemptreopenmethod', 'mod_assign');
    $description = new lang_string('attemptreopenmethod_help', 'mod_assign');
    $setting = new admin_setting_configselect('assign/attemptreopenmethod',
                                                    $name,
                                                    $description,
                                                    'none',
                                                    $options);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    // Constants from "locallib.php".
    $options = array(-1 => get_string('unlimitedattempts', 'mod_assign'));
    $options += array_combine(range(1, 30), range(1, 30));
    $name = new lang_string('maxattempts', 'mod_assign');
    $description = new lang_string('maxattempts_help', 'mod_assign');
    $setting = new admin_setting_configselect('assign/maxattempts',
                                                    $name,
                                                    $description,
                                                    -1,
                                                    $options);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('teamsubmission', 'mod_assign');
    $description = new lang_string('teamsubmission_help', 'mod_assign');
    $setting = new admin_setting_configcheckbox('assign/teamsubmission',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('preventsubmissionnotingroup', 'mod_assign');
    $description = new lang_string('preventsubmissionnotingroup_help', 'mod_assign');
    $setting = new admin_setting_configcheckbox('assign/preventsubmissionnotingroup',
        $name,
        $description,
        0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('requireallteammemberssubmit', 'mod_assign');
    $description = new lang_string('requireallteammemberssubmit_help', 'mod_assign');
    $setting = new admin_setting_configcheckbox('assign/requireallteammemberssubmit',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('teamsubmissiongroupingid', 'mod_assign');
    $description = new lang_string('teamsubmissiongroupingid_help', 'mod_assign');
    $setting = new admin_setting_configempty('assign/teamsubmissiongroupingid',
                                                    $name,
                                                    $description);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('sendnotifications', 'mod_assign');
    $description = new lang_string('sendnotifications_help', 'mod_assign');
    $setting = new admin_setting_configcheckbox('assign/sendnotifications',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('sendlatenotifications', 'mod_assign');
    $description = new lang_string('sendlatenotifications_help', 'mod_assign');
    $setting = new admin_setting_configcheckbox('assign/sendlatenotifications',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('sendstudentnotificationsdefault', 'mod_assign');
    $description = new lang_string('sendstudentnotificationsdefault_help', 'mod_assign');
    $setting = new admin_setting_configcheckbox('assign/sendstudentnotifications',
                                                    $name,
                                                    $description,
                                                    1);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('blindmarking', 'mod_assign');
    $description = new lang_string('blindmarking_help', 'mod_assign');
    $setting = new admin_setting_configcheckbox('assign/blindmarking',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('hidegrader', 'mod_assign');
    $description = new lang_string('hidegrader_help', 'mod_assign');
    $setting = new admin_setting_configcheckbox('assign/hidegrader',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('markingworkflow', 'mod_assign');
    $description = new lang_string('markingworkflow_help', 'mod_assign');
    $setting = new admin_setting_configcheckbox('assign/markingworkflow',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('markingallocation', 'mod_assign');
    $description = new lang_string('markingallocation_help', 'mod_assign');
    $setting = new admin_setting_configcheckbox('assign/markingallocation',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);
}

$ADMIN->add('modassignfolder', $settings);
// Tell core we already added the settings structure.
$settings = null;

$ADMIN->add('modassignfolder', new admin_category('assignsubmissionplugins',
    new lang_string('submissionplugins', 'assign'), !$module->is_enabled()));
$ADMIN->add('assignsubmissionplugins', new assign_admin_page_manage_assign_plugins('assignsubmission'));
$ADMIN->add('modassignfolder', new admin_category('assignfeedbackplugins',
    new lang_string('feedbackplugins', 'assign'), !$module->is_enabled()));
$ADMIN->add('assignfeedbackplugins', new assign_admin_page_manage_assign_plugins('assignfeedback'));

foreach (core_plugin_manager::instance()->get_plugins_of_type('assignsubmission') as $plugin) {
    /** @var \mod_assign\plugininfo\assignsubmission $plugin */
    $plugin->load_settings($ADMIN, 'assignsubmissionplugins', $hassiteconfig);
}

foreach (core_plugin_manager::instance()->get_plugins_of_type('assignfeedback') as $plugin) {
    /** @var \mod_assign\plugininfo\assignfeedback $plugin */
    $plugin->load_settings($ADMIN, 'assignfeedbackplugins', $hassiteconfig);
}
