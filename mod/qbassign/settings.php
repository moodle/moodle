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
 * @package   mod_qbassign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/qbassign/adminlib.php');

$ADMIN->add('modsettings', new admin_category('modqbassignfolder', new lang_string('pluginname', 'mod_qbassign'), $module->is_enabled() === false));

$settings = new admin_settingpage($section, get_string('settings', 'mod_qbassign'), 'moodle/site:config', $module->is_enabled() === false);

if ($ADMIN->fulltree) {
    $menu = array();
    foreach (core_component::get_plugin_list('qbassignfeedback') as $type => $notused) {
        $visible = !get_config('qbassignfeedback_' . $type, 'disabled');
        if ($visible) {
            $menu['qbassignfeedback_' . $type] = new lang_string('pluginname', 'qbassignfeedback_' . $type);
        }
    }

    // The default here is feedback_comments (if it exists).
    $name = new lang_string('feedbackplugin', 'mod_qbassign');
    $description = new lang_string('feedbackpluginforgradebook', 'mod_qbassign');
    $settings->add(new admin_setting_configselect('qbassign/feedback_plugin_for_gradebook',
                                                  $name,
                                                  $description,
                                                  'qbassignfeedback_comments',
                                                  $menu));

    $name = new lang_string('showrecentsubmissions', 'mod_qbassign');
    $description = new lang_string('configshowrecentsubmissions', 'mod_qbassign');
    $settings->add(new admin_setting_configcheckbox('qbassign/showrecentsubmissions',
                                                    $name,
                                                    $description,
                                                    0));

    $name = new lang_string('sendsubmissionreceipts', 'mod_qbassign');
    $description = new lang_string('sendsubmissionreceipts_help', 'mod_qbassign');
    $settings->add(new admin_setting_configcheckbox('qbassign/submissionreceipts',
                                                    $name,
                                                    $description,
                                                    1));

    $name = new lang_string('submissionstatement', 'mod_qbassign');
    $description = new lang_string('submissionstatement_help', 'mod_qbassign');
    $default = get_string('submissionstatementdefault', 'mod_qbassign');
    $setting = new admin_setting_configtextarea('qbassign/submissionstatement',
                                                    $name,
                                                    $description,
                                                    $default);
    $setting->set_force_ltr(false);
    $settings->add($setting);

    $name = new lang_string('submissionstatementteamsubmission', 'mod_qbassign');
    $description = new lang_string('submissionstatement_help', 'mod_qbassign');
    $default = get_string('submissionstatementteamsubmissiondefault', 'mod_qbassign');
    $setting = new admin_setting_configtextarea('qbassign/submissionstatementteamsubmission',
        $name,
        $description,
        $default);
    $setting->set_force_ltr(false);
    $settings->add($setting);

    $name = new lang_string('submissionstatementteamsubmissionallsubmit', 'mod_qbassign');
    $description = new lang_string('submissionstatement_help', 'mod_qbassign');
    $default = get_string('submissionstatementteamsubmissionallsubmitdefault', 'mod_qbassign');
    $setting = new admin_setting_configtextarea('qbassign/submissionstatementteamsubmissionallsubmit',
        $name,
        $description,
        $default);
    $setting->set_force_ltr(false);
    $settings->add($setting);

    $name = new lang_string('maxperpage', 'mod_qbassign');
    $options = array(
        -1 => get_string('unlimitedpages', 'mod_qbassign'),
        10 => 10,
        20 => 20,
        50 => 50,
        100 => 100,
    );
    $description = new lang_string('maxperpage_help', 'mod_qbassign');
    $settings->add(new admin_setting_configselect('qbassign/maxperpage',
                                                    $name,
                                                    $description,
                                                    -1,
                                                    $options));

    $name = new lang_string('defaultsettings', 'mod_qbassign');
    $description = new lang_string('defaultsettings_help', 'mod_qbassign');
    $settings->add(new admin_setting_heading('defaultsettings', $name, $description));

    $name = new lang_string('alwaysshowdescription', 'mod_qbassign');
    $description = new lang_string('alwaysshowdescription_help', 'mod_qbassign');
    $setting = new admin_setting_configcheckbox('qbassign/alwaysshowdescription',
                                                    $name,
                                                    $description,
                                                    1);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('allowsubmissionsfromdate', 'mod_qbassign');
    $description = new lang_string('allowsubmissionsfromdate_help', 'mod_qbassign');
    $setting = new admin_setting_configduration('qbassign/allowsubmissionsfromdate',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_enabled_flag_options(admin_setting_flag::ENABLED, true);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('duedate', 'mod_qbassign');
    $description = new lang_string('duedate_help', 'mod_qbassign');
    $setting = new admin_setting_configduration('qbassign/duedate',
                                                    $name,
                                                    $description,
                                                    604800);
    $setting->set_enabled_flag_options(admin_setting_flag::ENABLED, true);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('cutoffdate', 'mod_qbassign');
    $description = new lang_string('cutoffdate_help', 'mod_qbassign');
    $setting = new admin_setting_configduration('qbassign/cutoffdate',
                                                    $name,
                                                    $description,
                                                    1209600);
    $setting->set_enabled_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('enabletimelimit', 'mod_qbassign');
    $description = new lang_string('enabletimelimit_help', 'mod_qbassign');
    $setting = new admin_setting_configcheckbox(
        'qbassign/enabletimelimit',
        $name,
        $description,
        0
    );
    $settings->add($setting);

    $name = new lang_string('gradingduedate', 'mod_qbassign');
    $description = new lang_string('gradingduedate_help', 'mod_qbassign');
    $setting = new admin_setting_configduration('qbassign/gradingduedate',
                                                    $name,
                                                    $description,
                                                    1209600);
    $setting->set_enabled_flag_options(admin_setting_flag::ENABLED, true);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('submissiondrafts', 'mod_qbassign');
    $description = new lang_string('submissiondrafts_help', 'mod_qbassign');
    $setting = new admin_setting_configcheckbox('qbassign/submissiondrafts',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('requiresubmissionstatement', 'mod_qbassign');
    $description = new lang_string('requiresubmissionstatement_help', 'mod_qbassign');
    $setting = new admin_setting_configcheckbox('qbassign/requiresubmissionstatement',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    // Constants from "locallib.php".
    $options = array(
        'none' => get_string('attemptreopenmethod_none', 'mod_qbassign'),
        'manual' => get_string('attemptreopenmethod_manual', 'mod_qbassign'),
        'untilpass' => get_string('attemptreopenmethod_untilpass', 'mod_qbassign')
    );
    $name = new lang_string('attemptreopenmethod', 'mod_qbassign');
    $description = new lang_string('attemptreopenmethod_help', 'mod_qbassign');
    $setting = new admin_setting_configselect('qbassign/attemptreopenmethod',
                                                    $name,
                                                    $description,
                                                    'none',
                                                    $options);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    // Constants from "locallib.php".
    $options = array(-1 => get_string('unlimitedattempts', 'mod_qbassign'));
    $options += array_combine(range(1, 30), range(1, 30));
    $name = new lang_string('maxattempts', 'mod_qbassign');
    $description = new lang_string('maxattempts_help', 'mod_qbassign');
    $setting = new admin_setting_configselect('qbassign/maxattempts',
                                                    $name,
                                                    $description,
                                                    -1,
                                                    $options);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('teamsubmission', 'mod_qbassign');
    $description = new lang_string('teamsubmission_help', 'mod_qbassign');
    $setting = new admin_setting_configcheckbox('qbassign/teamsubmission',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('preventsubmissionnotingroup', 'mod_qbassign');
    $description = new lang_string('preventsubmissionnotingroup_help', 'mod_qbassign');
    $setting = new admin_setting_configcheckbox('qbassign/preventsubmissionnotingroup',
        $name,
        $description,
        0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('requireallteammemberssubmit', 'mod_qbassign');
    $description = new lang_string('requireallteammemberssubmit_help', 'mod_qbassign');
    $setting = new admin_setting_configcheckbox('qbassign/requireallteammemberssubmit',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('teamsubmissiongroupingid', 'mod_qbassign');
    $description = new lang_string('teamsubmissiongroupingid_help', 'mod_qbassign');
    $setting = new admin_setting_configempty('qbassign/teamsubmissiongroupingid',
                                                    $name,
                                                    $description);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('sendnotifications', 'mod_qbassign');
    $description = new lang_string('sendnotifications_help', 'mod_qbassign');
    $setting = new admin_setting_configcheckbox('qbassign/sendnotifications',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('sendlatenotifications', 'mod_qbassign');
    $description = new lang_string('sendlatenotifications_help', 'mod_qbassign');
    $setting = new admin_setting_configcheckbox('qbassign/sendlatenotifications',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('sendstudentnotificationsdefault', 'mod_qbassign');
    $description = new lang_string('sendstudentnotificationsdefault_help', 'mod_qbassign');
    $setting = new admin_setting_configcheckbox('qbassign/sendstudentnotifications',
                                                    $name,
                                                    $description,
                                                    1);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('blindmarking', 'mod_qbassign');
    $description = new lang_string('blindmarking_help', 'mod_qbassign');
    $setting = new admin_setting_configcheckbox('qbassign/blindmarking',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('hidegrader', 'mod_qbassign');
    $description = new lang_string('hidegrader_help', 'mod_qbassign');
    $setting = new admin_setting_configcheckbox('qbassign/hidegrader',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('markingworkflow', 'mod_qbassign');
    $description = new lang_string('markingworkflow_help', 'mod_qbassign');
    $setting = new admin_setting_configcheckbox('qbassign/markingworkflow',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('markingallocation', 'mod_qbassign');
    $description = new lang_string('markingallocation_help', 'mod_qbassign');
    $setting = new admin_setting_configcheckbox('qbassign/markingallocation',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);
}

$ADMIN->add('modqbassignfolder', $settings);
// Tell core we already added the settings structure.
$settings = null;

$ADMIN->add('modqbassignfolder', new admin_category('qbassignsubmissionplugins',
    new lang_string('submissionplugins', 'qbassign'), !$module->is_enabled()));
$ADMIN->add('qbassignsubmissionplugins', new qbassign_admin_page_manage_qbassign_plugins('qbassignsubmission'));
$ADMIN->add('modqbassignfolder', new admin_category('qbassignfeedbackplugins',
    new lang_string('feedbackplugins', 'qbassign'), !$module->is_enabled()));
$ADMIN->add('qbassignfeedbackplugins', new qbassign_admin_page_manage_qbassign_plugins('qbassignfeedback'));

foreach (core_plugin_manager::instance()->get_plugins_of_type('qbassignsubmission') as $plugin) {
    /** @var \mod_qbassign\plugininfo\qbassignsubmission $plugin */
    $plugin->load_settings($ADMIN, 'qbassignsubmissionplugins', $hassiteconfig);
}

foreach (core_plugin_manager::instance()->get_plugins_of_type('qbassignfeedback') as $plugin) {
    /** @var \mod_qbassign\plugininfo\qbassignfeedback $plugin */
    $plugin->load_settings($ADMIN, 'qbassignfeedbackplugins', $hassiteconfig);
}
