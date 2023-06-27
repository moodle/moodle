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
 * Plugin administration pages are defined here.
 *
 * @package     local_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */

defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__) . '/lib.php');

global $CFG, $PAGE;

$PAGE->requires->js(new moodle_url('/local/edwiserbridge/js/eb_settings.js'));
$PAGE->requires->js_call_amd('local_edwiserbridge/eb_settings', 'init');
$stringmanager = get_string_manager();
$strings = $stringmanager->load_component_strings('local_edwiserbridge', 'en');
$PAGE->requires->strings_for_js(array_keys($strings), 'local_edwiserbridge');

$ADMIN->add(
    'modules',
    new admin_category(
        'edwisersettings',
        new lang_string(
            'edwiserbridge',
            'local_edwiserbridge'
        )
    )
);

$ADMIN->add(
    'edwisersettings',
    new admin_externalpage(
        'edwiserbridge_conn_synch_settings',
        new lang_string(
            'nav_name',
            'local_edwiserbridge'
        ),
        "$CFG->wwwroot/local/edwiserbridge/edwiserbridge.php?tab=settings",
        array(
            'moodle/user:update',
            'moodle/user:delete'
        )
    )
);

$ADMIN->add(
    'edwisersettings',
    new admin_externalpage(
        'edwiserbridge_setup',
        new lang_string(
            'run_setup',
            'local_edwiserbridge'
        ),
        "$CFG->wwwroot/local/edwiserbridge/setup_wizard.php",
        array(
            'moodle/user:update',
            'moodle/user:delete'
        )
    )
);

// Adding settings page.
$settings = new admin_settingpage('edwiserbridge_settings', new lang_string('pluginname', 'local_edwiserbridge'));
$ADMIN->add('localplugins', $settings);

$settings->add(
    new admin_setting_heading(
        'local_edwiserbridge/eb_settings_msg',
        '',
        '<div class="eb_settings_btn_cont" style="padding:20px;">' . get_string('eb_settings_msg', 'local_edwiserbridge')
            . '<a target="_blank" class="eb_settings_btn" style="padding: 7px 18px; border-radius: 4px; color: white;
        background-color: #2578dd; margin-left: 5px;" href="' . $CFG->wwwroot . '/local/edwiserbridge/setup_wizard.php'
            . '" >' . get_string('click_here', 'local_edwiserbridge') . '</a></div>'
    )
);

// Adding this field so that the setting page will be shown after installation.
$settings->add(
    new admin_setting_configcheckbox(
        'local_edwiserbridge/eb_setup_wizard_field',
        get_string(
            'eb_dummy_msg',
            'local_edwiserbridge'
        ),
        ' ',
        1
    )
);
