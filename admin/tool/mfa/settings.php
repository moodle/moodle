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
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('tools', new admin_category('toolmfafolder', new lang_string('pluginname', 'tool_mfa'), false));
    $ADMIN->add('toolmfafolder', new admin_externalpage('tool_mfa_resetfactor',
    get_string('resetfactor', 'tool_mfa'),
    new moodle_url('/admin/tool/mfa/reset_factor.php')));

    $settings = new admin_settingpage('managemfa', new lang_string('mfasettings', 'tool_mfa'));
    $settings->add(new \tool_mfa\local\admin_setting_managemfa());

    $heading = new lang_string('settings:general', 'tool_mfa');
    $settings->add(new admin_setting_heading('tool_mfa/settings', $heading, ''));

    $name = new lang_string('settings:enabled', 'tool_mfa');
    $settings->add(new admin_setting_configcheckbox('tool_mfa/enabled', $name, '', false));

    $name = new lang_string('settings:lockout', 'tool_mfa');
    $description = new lang_string('settings:lockout_help', 'tool_mfa');
    $settings->add(new admin_setting_configtext('tool_mfa/lockout', $name, $description, 10, PARAM_INT));

    $name = new lang_string('settings:debugmode', 'tool_mfa');
    $description = new lang_string('settings:debugmode_help', 'tool_mfa');
    $settings->add(new admin_setting_configcheckbox('tool_mfa/debugmode', $name, $description, false));

    $name = new lang_string('settings:redir_exclusions', 'tool_mfa');
    $description = new lang_string('settings:redir_exclusions_help', 'tool_mfa');
    $settings->add(new admin_setting_configtextarea('tool_mfa/redir_exclusions', $name, $description, ''));

    $name = new lang_string('settings:guidancecheck', 'tool_mfa');
    $description = new lang_string('settings:guidancecheck_help', 'tool_mfa');
    $settings->add(new admin_setting_configcheckbox('tool_mfa/guidance', $name, $description, false));

    $name = new lang_string('settings:guidancepage', 'tool_mfa');
    $description = new lang_string('settings:guidancepage_help', 'tool_mfa');
    $settings->add(new admin_setting_confightmleditor('tool_mfa/guidancecontent', $name, $description, '', PARAM_RAW));

    $name = new lang_string('settings:guidancefiles', 'tool_mfa');
    $description = new lang_string('settings:guidancefiles_help', 'tool_mfa');
    $settings->add(new admin_setting_configstoredfile('tool_mfa/guidancefiles', $name, $description, 'guidance', 0, [
        'maxfiles' => -1,
            ]));

    $ADMIN->add('toolmfafolder', $settings);

    foreach (core_plugin_manager::instance()->get_plugins_of_type('factor') as $plugin) {
        $plugin->load_settings($ADMIN, 'toolmfafolder', $hassiteconfig);
    }

    $ADMIN->add('reports', new admin_category('toolmfareports', get_string('mfareports', 'tool_mfa')));

    $ADMIN->add('toolmfareports',
            new admin_externalpage('factorreport', get_string('factorreport', 'tool_mfa'),
            new moodle_url('/admin/tool/mfa/factor_report.php')));
}
