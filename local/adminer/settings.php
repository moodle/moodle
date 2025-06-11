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
 * Add page to admin menu.
 *
 * @package    local_adminer
 * @author Andreas Grabs <moodle@grabs-edv.de>
 * @copyright  Andreas Grabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $pluginname = get_string('pluginname', 'local_adminer');

    $adminersecret = $CFG->local_adminer_secret ?? '';
    $adminerdisabled = true;
    if ($adminersecret !== \local_adminer\util::DISABLED_SECRET) {
        $adminerdisabled = false;
        $ADMIN->add('server', new admin_externalpage(
            'local_adminer',
            $pluginname,
            \local_adminer\util::get_adminer_url(),
            'local/adminer:useadminer')
        );
    }

    $settings = new admin_settingpage('local_adminer_settings', $pluginname);
    $ADMIN->add('localplugins', $settings);

    $configs = [];

    if ($adminerdisabled) {
        $configs[] = new admin_setting_heading(
            'local_adminer_disabled_note',
            '',
            $OUTPUT->render_from_template('local_adminer/disabled_note', [])
        );
    }

    $templatecontext = [
        'disabledsecret' => \local_adminer\util::DISABLED_SECRET,
    ];
    $configs[] = new admin_setting_heading(
        'local_adminer_securitynote',
        '',
        $OUTPUT->render_from_template('local_adminer/security_note', $templatecontext)
    );

    $configs[] = new admin_setting_heading(
        'local_adminer_settings',
        get_string('settings'),
        ''
    );

    $options   = [0 => get_string('no'), 1 => get_string('yes')];
    $configs[] = new admin_setting_configselect(
        'startwithdb',
        get_string('config_startwithdb', 'local_adminer'),
        '',
        0,
        $options
    );

    $configs[] = new admin_setting_configcheckbox(
        'showquicklink',
        get_string('showquicklink', 'local_adminer'),
        get_string('showquicklink_help', 'local_adminer'),
        1
    );

    // Put all settings into the settings page.
    foreach ($configs as $config) {
        $config->plugin = 'local_adminer';
        $settings->add($config);
    }
}
